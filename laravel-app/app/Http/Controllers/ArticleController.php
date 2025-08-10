<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::published()
            ->locale()
            ->orderByDesc('published_at');

        // Filtrer par catégorie
        if ($category = $request->get('category')) {
            $query->category($category);
        }

        // Recherche
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $articles = $query->paginate(12);

        // Récupérer les catégories pour le filtre
        $categories = Article::published()
            ->locale()
            ->distinct()
            ->pluck('category');

        return view('articles.index', compact('articles', 'categories'));
    }

    public function show($slug)
    {
        $article = Article::published()
            ->locale()
            ->where('slug', $slug)
            ->firstOrFail();

        // Incrémenter les vues
        $article->incrementViews();

        // Articles similaires
        $relatedArticles = Article::published()
            ->locale()
            ->where('category', $article->category)
            ->where('id', '!=', $article->id)
            ->limit(3)
            ->get();

        return view('articles.show', compact('article', 'relatedArticles'));
    }

    public function category($category)
    {
        $articles = Article::published()
            ->locale()
            ->category($category)
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('articles.category', compact('articles', 'category'));
    }
}
