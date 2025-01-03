<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\SetMenu;
use App\Models\Cuisine;
use App\Models\MenuGroup;

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
        $baseUrl = 'https://staging.yhangry.com/booking/test/set-menus';
        $currentPage = 1;

        do {
            $response = Http::get("$baseUrl?page=$currentPage");

            if ($response->failed()) {
                $this->error("Failed to fetch data for page $currentPage.");
                break;
            }

            $data = $response->json();

//            echo "<pre>"; print_r($data); echo "</pre>";
//            die();
            $menus = $data['data'];
            $nextPage = $data['links']['next'] ?? null;

            foreach ($menus as $menuData) {

                $setMenu = SetMenu::updateOrCreate(
                    ['id' => $menuData['name']],
                    [
                        'name' => $menuData['name'],
                        'description' => $menuData['description'],
                        'image' => $menuData['image'],
                        'thumbnail' => $menuData['thumbnail'],
                        'price_per_person' => $menuData['price_per_person'],
                        'min_spend' => $menuData['min_spend'],
                        'is_vegan' => $menuData['is_vegan'],
                        'is_vegetarian' => $menuData['is_vegetarian'],
                        'is_seated' => $menuData['is_seated'],
                        'is_standing' => $menuData['is_standing'],
                        'is_canape' => $menuData['is_canape'],
                        'is_mixed_dietary' => $menuData['is_mixed_dietary'],
                        'is_meal_prep' => $menuData['is_meal_prep'],
                        'is_halal' => $menuData['is_halal'],
                        'is_kosher' => $menuData['is_kosher'],
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

                $normalizedGroups = normalizeGroupKeys($groupData['groups']);

                MenuGroup::updateOrCreate(
                    ['set_menu_id' => $setMenu->id],
                    [
                        'dishes_count' => $groupData['dishes_count'],
                        'selectable_dishes_count' => $groupData['selectable_dishes_count'],
                        'group_ungrouped' => $groupData['groups']['ungrouped'],
                        'group_mains' => $groupData['groups']['mains'],
                        'group_starter' => $groupData['groups']['starter'],
                        'group_desserts' => $groupData['groups']['desserts'],
                    ]
                );
            }

            $this->info("Processed page $currentPage.");
            $currentPage++;
            sleep(1);
        } while ($nextPage);

        $this->info('Set menu harvesting completed.');
    }
}
