<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query)) {
            return redirect()->back()->with('error', 'Please enter a search term.');
        }

        // Search in users table (you can extend this to search other models)
        $users = User::where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%")
                    ->limit(10)
                    ->get();

        // You can add more search results from other models here
        $results = [
            'users' => $users,
            'query' => $query
        ];

        return view('content.search.search-results', compact('results'));
    }

    public function autocomplete(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = User::where('name', 'LIKE', "%{$query}%")
                          ->orWhere('email', 'LIKE', "%{$query}%")
                          ->limit(5)
                          ->get(['id', 'name', 'email']);

        return response()->json($suggestions);
    }
}
