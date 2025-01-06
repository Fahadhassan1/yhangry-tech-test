<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\SetMenu;
use App\Models\Cuisine;
use App\Models\MenuGroup;
use Illuminate\Support\Facades\DB;

class HarvestSetMenus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'harvest:set-menus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and store set menus from the API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $apiUrl = 'https://staging.yhangry.com/booking/test/set-menus';
        $currentPage = 1;

        do {
            $response = Http::get("$apiUrl?page=$currentPage");

            if ($response->failed()) {
                $this->error("Failed to fetch data for page # $currentPage.");
                break;
            }

            $data = $response->json();
            $menus = $data['data'];
            $nextPage = $data['links']['next'] ?? null;

            try {
                DB::beginTransaction();

                foreach ($menus as $menuData) {
                    $setMenu = SetMenu::updateOrCreate(
                        ['name' => $menuData['name']],
                        [
                            'name' => $menuData['name'],
                            'description' => $menuData['description'],
                            'image' => $menuData['image'],
                            'thumbnail' => $menuData['thumbnail'],
                            'price_per_person' => $menuData['price_per_person'],
                            'min_spend' => $menuData['min_spend'],
                            'is_vegan' => $menuData['is_vegan'] ?? 0,
                            'is_vegetarian' => $menuData['is_vegetarian'] ?? 0,
                            'is_seated' => $menuData['is_seated'] ?? 0,
                            'is_standing' => $menuData['is_standing'] ?? 0,
                            'is_canape' => $menuData['is_canape'] ?? 0,
                            'is_mixed_dietary' => $menuData['is_mixed_dietary'] ?? 0,
                            'is_meal_prep' => $menuData['is_meal_prep'] ?? 0,
                            'is_halal' => $menuData['is_halal'] ?? 0,
                            'is_kosher' => $menuData['is_kosher'] ?? 0,
                            'available' => $menuData['available'],
                            'number_of_orders' => $menuData['number_of_orders'],
                            'status' => $menuData['status'],
                            'created_at' => $menuData['created_at'],
                        ]
                    );

                    if (!empty($menuData['cuisines'])) {
                        $cuisineIds = [];
                        foreach ($menuData['cuisines'] as $cuisineData) {
                            $cuisine = Cuisine::updateOrCreate(
                                ['id' => $cuisineData['id']],
                                ['name' => $cuisineData['name']]
                            );
                            $cuisineIds[] = $cuisine->id;
                        }
                        $setMenu->cuisines()->sync($cuisineIds);
                    }

                    $groupData = $menuData['groups'];

                    MenuGroup::updateOrCreate(
                        ['set_menu_id' => $setMenu->id],
                        [
                            'dishes_count' => $groupData['dishes_count'],
                            'selectable_dishes_count' => $groupData['selectable_dishes_count'],
                            'group_ungrouped' => $groupData['groups']['ungrouped'] ?? 0,
                            'group_mains' => $groupData['groups']['mains'] ?? 0,
                            'group_starter' => $groupData['groups']['starter'] ?? 0,
                            'group_desserts' => $groupData['groups']['desserts'] ?? 0,
                        ]
                    );
                }

                DB::commit();
                $this->info("Processed page $currentPage.");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Failed to process page $currentPage: " . $e->getMessage());
                break;
            }

            $currentPage++;
            sleep(1);
        } while ($nextPage);

        $this->info('Set menu harvesting completed.');
    }
}
