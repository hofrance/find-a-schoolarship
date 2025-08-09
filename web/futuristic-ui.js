// Micro-interactions: ripple effect on .btn and reveal on scroll for .card
(function(){
  function addRipple(e){
    const btn = e.currentTarget;
    const r = document.createElement('span');
    r.className = 'ripple';
    const rect = btn.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    r.style.width = r.style.height = size + 'px';
    r.style.left = (e.clientX - rect.left - size/2) + 'px';
    r.style.top = (e.clientY - rect.top - size/2) + 'px';
    btn.appendChild(r);
    setTimeout(()=>r.remove(), 600);
  }
  const style = document.createElement('style');
  style.textContent = `.ripple{ position:absolute; border-radius:50%; background:rgba(255,255,255,.35); transform:scale(0); animation:ripple .6s ease-out forwards; pointer-events:none; mix-blend:overlay; }`;
  document.head.appendChild(style);

  document.addEventListener('click', (e)=>{
    const t = e.target.closest('.btn');
    if (t) addRipple(e);
  });

  const io = new IntersectionObserver((entries)=>{
    entries.forEach(en=>{
      if (en.isIntersecting){
        en.target.style.animation = 'fadeInUp .5s ease both';
        io.unobserve(en.target);
      }
    });
  }, { threshold:.08 });
  document.querySelectorAll('.card').forEach(el=>{
    el.style.opacity = .001;
    io.observe(el);
  });

  // Sync flatpickr theme on theme toggle
  const key = 'ui.theme';
  const btnTheme = document.getElementById('btn-theme');
  function sync(){
    const t = document.documentElement.getAttribute('data-theme')||'dark';
    document.documentElement.classList.toggle('auto-theme', t==='light');
  }
  sync();
  if (btnTheme) btnTheme.addEventListener('click', ()=>setTimeout(sync, 0));
})();
