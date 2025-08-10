<?php

namespace App\Http\Controllers;

use App\Models\Career;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    public function index(Request $request)
    {
        $query = Career::locale()
            ->orderBy('title');

        // Filtrer par secteur
        if ($sector = $request->get('sector')) {
            $query->bySector($sector);
        }

        // Recherche
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('requirements', 'like', "%{$search}%");
            });
        }

        $careers = $query->paginate(15);

        // Récupérer tous les secteurs pour le filtre
        $sectors = Career::locale()
            ->whereNotNull('sectors')
            ->get()
            ->pluck('sectors')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        // Métiers en vedette
        $featuredCareers = Career::featured()
            ->locale()
            ->limit(6)
            ->get();

        return view('careers.index', compact('careers', 'sectors', 'featuredCareers'));
    }

    public function show($slug)
    {
        $career = Career::locale()
            ->where('slug', $slug)
            ->firstOrFail();

        // Incrémenter les vues
        $career->incrementViews();

        // Métiers similaires (même secteur)
        $relatedCareers = Career::locale()
            ->where('id', '!=', $career->id);

        if (!empty($career->sectors)) {
            $relatedCareers->where(function($q) use ($career) {
                foreach ($career->sectors as $sector) {
                    $q->orWhereJsonContains('sectors', $sector);
                }
            });
        }

        $relatedCareers = $relatedCareers->limit(4)->get();

        return view('careers.show', compact('career', 'relatedCareers'));
    }

    public function sector($sector)
    {
        $careers = Career::locale()
            ->bySector($sector)
            ->orderBy('title')
            ->paginate(15);

        return view('careers.sector', compact('careers', 'sector'));
    }
}
