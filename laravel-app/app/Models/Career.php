<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Career extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'requirements',
        'skills',
        'salary_range',
        'education_levels',
        'sectors',
        'career_prospects',
        'featured_image',
        'is_featured',
        'locale',
        'views_count'
    ];

    protected $casts = [
        'education_levels' => 'array',
        'sectors' => 'array',
        'is_featured' => 'boolean',
        'views_count' => 'integer'
    ];

    // Auto-génération du slug
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($career) {
            $career->slug = Str::slug($career->title);
        });
    }

    // Scope pour les métiers en vedette
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Scope pour la locale
    public function scopeLocale($query, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $query->where('locale', $locale);
    }

    // Scope pour un secteur
    public function scopeBySector($query, $sector)
    {
        return $query->whereJsonContains('sectors', $sector);
    }

    // Incrémenter les vues
    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
