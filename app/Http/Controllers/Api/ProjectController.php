<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['type', 'technologies']);
        if ($request->has('type_id')) {
            $query->where('type_id', $request->type_id);
        }

        if ($request->has('technology_id')) {
            $query->whereHas('technologies', function ($q) use ($request) {
                $q->whereIn('id', [$request->technology_id]);
            });
        }

        $projects = $query->paginate(20);

        return response()->json([
            'success' => true,
            'results' => $projects
        ]);
    }

    public function show($slug)
    {
        $project = Project::with('type', 'technologies')->where('slug', $slug)->first();

        if ($project) {
            return response()->json([
                'success' => true,
                'results' => $project
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Nessun progetto trovato'
            ])->setStatusCode(404);
        }
    }
}
