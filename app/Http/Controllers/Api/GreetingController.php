<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalnestGreeting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GreetingController extends Controller
{
    /**
     * Greetings template library (GET /api/calnest/greetings)
     * Read-only, no auth needed (public content), token accepted.
     */
    public function index(Request $request): JsonResponse
    {
        $query = CalnestGreeting::query();

        if ($request->has('category') && !empty($request->query('category'))) {
            $cat = $request->query('category');
            $camelCat = Str::camel($cat);
            $snakeCat = Str::snake($cat);

            $query->where(function ($q) use ($cat, $camelCat, $snakeCat) {
                $q->where('category', $cat)
                  ->orWhere('category', $camelCat)
                  ->orWhere('category', $snakeCat);
            });
        }

        if ($request->has('language') && !empty($request->query('language'))) {
            $query->where('language', $request->query('language'));
        }

        if ($request->has('q') && !empty($request->query('q'))) {
            $search = '%' . $request->query('q') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('message', 'like', $search);
            });
        }

        $greetings = $query->orderBy('id', 'asc')->get();

        return response()->json([
            'items' => $greetings->map(fn (CalnestGreeting $g) => $g->toResponseArray())->values(),
        ], 200);
    }
}
