<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SetMenu;
use App\Models\Cuisine;

class SetMenuController extends Controller
{
    

    public function getFilterMenus(Request $request)
    {
        $cuisineSlug = $request->query('cuisineSlug');
        $perPage = $request->query('perPage', 10);

        $setMenusQuery = SetMenu::where('status', true)
            ->when($cuisineSlug, function ($query, $cuisineSlug) {
                $query->whereHas('cuisines', function ($cuisineQuery) use ($cuisineSlug) {
                    $cuisineQuery->where('name', $cuisineSlug);
                });
            })
            ->orderByDesc('number_of_orders');

        $setMenus = $setMenusQuery->paginate($perPage);
        $cuisines = Cuisine::withCount(['setMenus as live_set_menus_count' => function ($query) {
            $query->where('status', 'live');
        }])
            ->withSum('setMenus as total_orders', 'number_of_orders')
            ->orderByDesc('total_orders')
            ->get();

        return response()->json([
            'setMenus' => $setMenus,
            'cuisines' => $cuisines,
        ]);
    }
}
