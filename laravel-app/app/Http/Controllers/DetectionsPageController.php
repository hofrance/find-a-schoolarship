<?php

namespace App\Http\Controllers;

use App\Models\Detection;
use Illuminate\Http\Request;

class DetectionsPageController extends Controller
{
    public function index(Request $request)
    {
        $q = Detection::query();

        if ($search = trim((string) $request->get('search'))) {
            $q->where(function($w) use ($search) {
                $w->where('title', 'like', "%$search%")
                  ->orWhere('source_name', 'like', "%$search%")
                  ->orWhere('country', 'like', "%$search%")
                  ->orWhere('level', 'like', "%$search%")
                  ->orWhere('language', 'like', "%$search%")
                  ->orWhere('amount', 'like', "%$search%")
                  ->orWhere('item_url', 'like', "%$search%");
            });
        }
        if ($country = $request->get('country')) { $q->where('country', $country); }
        if ($level = $request->get('level')) { $q->where('level', $level); }
        if ($lang = $request->get('language')) { $q->where('language', $lang); }
        if ($minScore = $request->get('min_score')) { $q->where('score', '>=', (int)$minScore); }

        $q->orderByRaw("CASE WHEN deadline IS NULL THEN 1 ELSE 0 END, deadline ASC")
          ->orderByDesc('score');

        $perPage = min(100, max(10, (int) $request->get('per_page', 25)));
        $detections = $q->paginate($perPage)->withQueryString();

        return view('detections.index', compact('detections'));
    }

    public function show(Detection $detection)
    {
        return view('detections.show', compact('detection'));
    }
}
