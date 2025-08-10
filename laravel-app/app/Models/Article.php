<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'category',
        'tags',
        'is_published',
        'published_at',
        'locale',
        'author_id',
        'views_count'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'views_count' => 'integer'
    ];

    // Auto-génération du slug
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($article) {
            $article->slug = Str::slug($article->title);
        });
    }

    // Scope pour les articles publiés
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Scope pour la locale
    public function scopeLocale($query, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $query->where('locale', $locale);
    }

    // Scope pour une catégorie
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Relation avec l'auteur
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Incrémenter les vues
    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
