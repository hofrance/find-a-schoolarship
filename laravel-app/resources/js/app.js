import './bootstrap';

(function() {
  // Particules (réduit si prefers-reduced-motion)
  const canvas = document.createElement('canvas');
  canvas.className = 'particle-canvas';
  document.addEventListener('DOMContentLoaded', () => document.body.appendChild(canvas));
  const ctx = canvas.getContext('2d');
  const particles = [];
  const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  
  function resize(){ canvas.width = innerWidth; canvas.height = innerHeight; }
  addEventListener('resize', resize); resize();
  
  function spawn(n=50){ 
    for(let i=0;i<n;i++){ 
      particles.push({
        x: Math.random()*canvas.width, 
        y: Math.random()*canvas.height,
        vx:(Math.random()-0.5)*(reduceMotion?0.15:0.4), 
        vy:(Math.random()-0.5)*(reduceMotion?0.15:0.4),
        r: Math.random()*(reduceMotion?1.5:2)+1, 
        a: Math.random()*0.5+0.1
      });
    }
  }
  spawn(reduceMotion?25:60);
  
  function tick(){
    if (!ctx) return;
    ctx.clearRect(0,0,canvas.width,canvas.height);
    particles.forEach(p=>{ 
      p.x+=p.vx; p.y+=p.vy;
      if(p.x<0||p.x>canvas.width) p.vx*=-1;
      if(p.y<0||p.y>canvas.height) p.vy*=-1;
      ctx.beginPath(); ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
      ctx.fillStyle = `rgba(0,212,255,${p.a})`; ctx.fill();
    });
    if (!reduceMotion) requestAnimationFrame(tick);
  }
  tick();

  // Thèmes
  const THEMES = ['theme-cyan','theme-purple','theme-matrix','theme-sunset','theme-darkpurple'];
  function setTheme(name){ 
    document.documentElement.classList.remove(...THEMES); 
    document.documentElement.classList.add(name); 
    localStorage.setItem('fui-theme',name); 
  }
  function initTheme(){ 
    const saved = localStorage.getItem('fui-theme'); 
    if(saved) setTheme(saved); 
    else setTheme('theme-cyan');
  }
  initTheme();

  // Toasts
  function toast(message, type='info', timeout=3000){
    const root = document.getElementById('toast-root'); if(!root) return;
    const el = document.createElement('div');
    el.className = `toast ${type==='success'?'toast-success':''} ${type==='error'?'toast-error':''}`;
    el.textContent = message; root.appendChild(el);
    setTimeout(()=>{ 
      el.style.opacity='0'; 
      el.style.transition='opacity .3s'; 
      setTimeout(()=>el.remove(), 350); 
    }, timeout);
  }

  // Filtre chips
  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('chip')) {
      e.target.classList.toggle('is-active');
      
      // Déclencher l'événement de filtre
      const filterEvent = new CustomEvent('chipFilter', {
        detail: {
          filter: e.target.dataset.filter,
          active: e.target.classList.contains('is-active')
        }
      });
      document.dispatchEvent(filterEvent);
    }
  });

  // Recherche en temps réel
  document.addEventListener('input', (e) => {
    if (e.target.classList.contains('search-input')) {
      const searchTerm = e.target.value.toLowerCase();
      const cards = document.querySelectorAll('.searchable-card');
      
      cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(searchTerm) ? 'block' : 'none';
      });
    }
  });

  // Smooth scroll pour les ancres
  document.addEventListener('click', (e) => {
    if (e.target.matches('a[href^="#"]')) {
      e.preventDefault();
      const target = document.querySelector(e.target.getAttribute('href'));
      if (target) {
        target.scrollIntoView({ 
          behavior: 'smooth',
          block: 'start'
        });
      }
    }
  });

  // API globale
  window.FUI = {
    setTheme,
    toggleTheme() {
      const current = localStorage.getItem('fui-theme') || THEMES[0];
      const idx = THEMES.indexOf(current);
      const next = THEMES[(idx+1)%THEMES.length];
      setTheme(next);
    },
    toast
  };
})();
