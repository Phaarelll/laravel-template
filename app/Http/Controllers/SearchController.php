<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\File;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query)) {
            return redirect()->back()->with('error', 'Please enter a search term.');
        }

        // Search in users table
        $users = User::where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%")
                    ->limit(10)
                    ->get();

        // Search in menu items
        $menuItems = $this->searchMenuItems($query);

        $results = [
            'users' => $users,
            'menuItems' => $menuItems,
            'query' => $query
        ];

        return view('content.search.search-results', compact('results'));
    }

    private function searchMenuItems($query)
    {
        $menuPath = resource_path('menu/verticalMenu.json');
        
        if (!File::exists($menuPath)) {
            return collect([]);
        }

        $menuData = json_decode(File::get($menuPath), true);
        $results = collect([]);

        if (isset($menuData['menu'])) {
            $this->searchInMenuArray($menuData['menu'], $query, $results);
        }

        return $results->take(10);
    }

    private function searchInMenuArray($menuArray, $query, &$results, $parentName = null)
    {
        foreach ($menuArray as $item) {
            // Skip menu headers
            if (isset($item['menuHeader'])) {
                continue;
            }

            // Check if current item matches search query
            if (isset($item['name']) && stripos($item['name'], $query) !== false) {
                $results->push([
                    'name' => $item['name'],
                    'url' => $item['url'] ?? '#',
                    'icon' => $item['icon'] ?? 'bx bx-circle',
                    'slug' => $item['slug'] ?? '',
                    'parent' => $parentName,
                    'target' => $item['target'] ?? '_self'
                ]);
            }

            // Search in submenu items
            if (isset($item['submenu']) && is_array($item['submenu'])) {
                $this->searchInMenuArray($item['submenu'], $query, $results, $item['name'] ?? null);
            }
        }
    }

    public function autocomplete(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Get user suggestions
        $users = User::where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%")
                    ->limit(3)
                    ->get(['id', 'name', 'email']);

        // Get menu suggestions
        $menuItems = $this->searchMenuItems($query)->take(5);

        $suggestions = [
            'users' => $users,
            'menus' => $menuItems
        ];

        return response()->json($suggestions);
    }
}
