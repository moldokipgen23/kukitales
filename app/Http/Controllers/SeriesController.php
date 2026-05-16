<?php

namespace App\Http\Controllers;

use App\Models\Series;

class SeriesController extends Controller
{
    public function show(string $slug)
    {
        $series = Series::where('slug', $slug)
            ->with(['posts' => fn ($q) => $q->where('status', 'published')->orderBy('episode_number')])
            ->withCount('posts')
            ->firstOrFail();

        return view('series.show', compact('series'));
    }
}
