<?php

namespace App\Http\Controllers;

use App\Models\FoodCategory;
use App\Models\FoodItem;
use App\Models\Ingredient;
use App\Models\Location;
use App\Models\Modifier;
use App\Models\Meal;
use App\Models\QueueManagement;
use App\Models\EKiosk;
use App\Models\PaymentMethod;
use App\Models\PositionAsset;
use App\Models\EKioskAsset;
use App\Models\ServiceTable;
use App\Models\Tax;
use Ramsey\Uuid\Uuid;

class SeederController extends Controller
{
    public function seeder()
    {
//        $locations = [
//            [
//                'name' => 'Albi Mall',
//                'location' => 'Prishtine'
//            ]
//        ];
//
//        foreach ($locations as $location) {
//            Location::create($location);
//        }


        $eKiosks = [
            [
                'eKioskid' => Uuid::uuid4(),
            ],
        ];

        foreach ($eKiosks as $eKiosk) {
            EKiosk::create([
                'id' => $eKiosk['eKioskid'],
                'e_kiosk_id' => $eKiosk['eKioskid'],
                'location_id' => Location::first()->id,
                'name' => 'E-Kiosk',
                'location' => 'Doloribus aliquip qu',
                'status' => 1,
                'authentication_code' => 'Pa$$w0rd!',
            ]);
        }

        $assetsEkiosks = [
            [
                'id' => Uuid::uuid4(),

            ]
        ];

        foreach ($assetsEkiosks as $assetsEkiosk) {
            $id = Uuid::uuid4()->toString();
            EKiosk::create([
                'id' => $eKiosk['eKioskid'],
                'e_kiosk_id' => $eKiosk['eKioskid'],
                'location_id' => Location::first()->id,
                'name' => 'E-Kiosk1',
                'location' => 'Doloribus aliquip qu',
                'status' => 1,
                'authentication_code' => 'Pa$$w0rd!',
            ]);
//            PositionAsset::create([
//                'id' => Uuid::uuid4(),
//                'name' => 'Position 1',
//                'description' => 'Dolore irure omnis q',
//                'status' => 1,
//                'url' => 'Cupidatat deleniti r',
//            ]);
        }


        $manageAssets = [
            [
                'id' => Uuid::uuid4(),

            ]
        ];

        foreach ($manageAssets as $manageAsset) {
            $id = Uuid::uuid4()->toString();
            EKiosk::create([
                'id' => $eKiosk['eKioskid'],
                'location_id' => Location::first()->id,
                'name' => 'E-Kiosk2',
                'location' => 'Doloribus aliquip qu',
                'status' => 1,
                'authentication_code' => 'Pa$$w0rd!',
            ]);
//            PositionAsset::create([
//                'id' => Uuid::uuid4(),
//                'name' => 'Position 1',
//                'description' => 'Dolore irure omnis q',
//                'status' => 1,
//                'url' => 'Cupidatat deleniti r',
//                'url' => 'Cupidatat deleniti r',
//            ]);
//            EKioskAsset::create([
//                'e_kiosk_id',
//                'position_id',
//                'status' => 1,
//                'name' => 'Patrick Nguyen',
//            ]);
        }


        $ingredients = [
            [
                'id' => Uuid::uuid4(),
                'name' => 'Milk',
                'price' => 2,
                'cost' => 1,
                'unit' => 'ltr',
                'quantity' => 5,
                'alert_quantity' => 20,
                'description' => 'milk description'
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'Meat',
                'price' => 3,
                'cost' => 2,
                'unit' => 'piece',
                'quantity' => 3,
                'alert_quantity' => 20,
                'description' => 'meat description'
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'Onion',
                'price' => 1,
                'cost' => 1,
                'unit' => 'g',
                'quantity' => 4,
                'alert_quantity' => 20,
                'description' => 'onion description'
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'Potato',
                'price' => 6,
                'cost' => 2,
                'unit' => 'piece',
                'quantity' => 20,
                'alert_quantity' => 20,
                'description' => 'potato description'
            ],
        ];

        foreach ($ingredients as $ingredient) {
            $id = Uuid::uuid4()->toString();
            $price = rand(10, 100);
            $cost = rand(1, $price - 3);
            $unit = ['ltr', 'kg', 'piece', 'g', 'packet'];
            $unit = $unit[array_rand($unit)];
            Ingredient::create([
                'id' => Uuid::uuid4()->toString(),
                'location_id' => Location::first()->id,
                'name' => $ingredient['name'],
                'price' => $price,
                'cost' => $cost,
                'unit' => $unit,
            ]);
        }


        $categoriesWithProducts = [
            [
                'name' => "Appetizers",
                'products' => [
                    "Spinach Artichoke Dip",
                    "Chicken Wings",
                    "Mozzarella Sticks",
                    "Nachos",
                    "Bruschetta",
                    "Potato Skins",
                    "Stuffed Mushrooms",
                    "Buffalo Cauliflower",
                    "Fried Calamari",
                    "Jalapeno Poppers",
                ],
            ],
            [
                'name' => "Soups",
                'products' => [
                    "Tomato Soup",
                    "Chicken Noodle Soup",
                    "Lentil Soup",
                    "Clam Chowder",
                    "Miso Soup",
                    "Minestrone Soup",
                    "French Onion Soup",
                    "Creamy Mushroom Soup",
                    "Spicy Seafood Soup",
                    "Thai Coconut Soup",
                ],
            ],
            [
                'name' => "Salads",
                'products' => [
                    "Caesar Salad",
                    "Greek Salad",
                    "Cobb Salad",
                    "Caprese Salad",
                    "Spinach Salad",
                    "Waldorf Salad",
                    "Mediterranean Salad",
                    "Chicken Caesar Salad",
                    "Quinoa Salad",
                    "Asian Sesame Salad",
                ],
            ],
            [
                'name' => "Burgers",
                'products' => [
                    "Classic Cheeseburger",
                    "Bacon Avocado Burger",
                    "Mushroom Swiss Burger",
                    "BBQ Burger",
                    "Spicy Jalapeno Burger",
                    "Veggie Burger",
                    "Turkey Burger",
                    "Blue Cheese Burger",
                    "Hawaiian Burger",
                    "Pulled Pork Burger",
                ],
            ],
            [
                'name' => "Sandwiches",
                'products' => [
                    "Club Sandwich",
                    "BLT Sandwich",
                    "Grilled Cheese Sandwich",
                    "Turkey Avocado Sandwich",
                    "Chicken Caesar Wrap",
                    "Reuben Sandwich",
                    "Philly Cheesesteak",
                    "Tuna Salad Sandwich",
                    "Veggie Wrap",
                    "French Dip Sandwich",
                ],
            ],
            [
                'name' => "Pizzas",
                'products' => [
                    "Margherita Pizza",
                    "Pepperoni Pizza",
                    "Vegetarian Pizza",
                    "Meat Lovers Pizza",
                    "Hawaiian Pizza",
                    "BBQ Chicken Pizza",
                    "Mushroom Pizza",
                    "Buffalo Chicken Pizza",
                    "Four Cheese Pizza",
                    "Supreme Pizza",
                ],
            ],
            [
                'name' => "Pasta",
                'products' => [
                    "Spaghetti Bolognese",
                    "Fettuccine Alfredo",
                    "Penne Arrabiata",
                    "Lasagna",
                    "Carbonara",
                    "Pesto Pasta",
                    "Shrimp Scampi",
                    "Chicken Parmesan",
                    "Lobster Ravioli",
                    "Veggie Primavera",
                ],
            ],
            [
                'name' => "Seafood",
                'products' => [
                    "Grilled Salmon",
                    "Fish and Chips",
                    "Lobster Tail",
                    "Shrimp Scampi",
                    "Crab Cakes",
                    "Stuffed Flounder",
                    "Seafood Paella",
                    "Seared Tuna",
                    "Scallops with Garlic Butter",
                    "Miso-Glazed Black Cod",
                ],
            ],
            [
                'name' => "Steaks",
                'products' => [
                    "Filet Mignon",
                    "Ribeye Steak",
                    "New York Strip Steak",
                    "T-Bone Steak",
                    "Porterhouse Steak",
                    "Prime Rib",
                    "Sirloin Steak",
                    "Chateaubriand",
                    "Surf and Turf",
                    "Steak Frites",
                ],
            ],
            [
                'name' => "Chicken",
                'products' => [
                    "Grilled Chicken Breast",
                    "Roasted Chicken",
                    "Chicken Piccata",
                    "Lemon Herb Chicken",
                    "BBQ Chicken",
                    "Chicken Marsala",
                    "Teriyaki Chicken",
                    "Chicken Alfredo",
                    "Chicken Parmesan",
                    "Chicken Kebabs",
                ],
            ],
            [
                'name' => "Vegetarian",
                'products' => [
                    "Veggie Stir-Fry",
                    "Tofu Curry",
                    "Vegetable Biryani",
                    "Vegan Pad Thai",
                    "Quinoa Stuffed Peppers",
                    "Eggplant Parmesan",
                    "Falafel Wrap",
                    "Mushroom Risotto",
                    "Vegetable Lasagna",
                    "Vegan Burger",
                ],
            ],
            [
                'name' => "Sushi",
                'products' => [
                    "California Roll",
                    "Spicy Tuna Roll",
                    "Salmon Nigiri",
                    "Tuna Sashimi",
                    "Shrimp Tempura Roll",
                    "Rainbow Roll",
                    "Dragon Roll",
                    "Eel Avocado Roll",
                    "Yellowtail Roll",
                    "Cucumber Roll",
                ],
            ],
            [
                'name' => "Tacos",
                'products' => [
                    "Grilled Chicken Tacos",
                    "Fish Tacos",
                    "Beef Barbacoa Tacos",
                    "Shrimp Tacos",
                    "Vegetable Tacos",
                    "Carnitas Tacos",
                    "Lobster Tacos",
                    "Pork Belly Tacos",
                    "Steak Tacos",
                    "Spicy Tofu Tacos",
                ],
            ],
            [
                'name' => "Wraps",
                'products' => [
                    "Chicken Caesar Wrap",
                    "Mediterranean Wrap",
                    "BBQ Chicken Wrap",
                    "Turkey Club Wrap",
                    "Veggie Wrap",
                    "Buffalo Chicken Wrap",
                    "Falafel Wrap",
                    "Steak and Cheese Wrap",
                    "Grilled Veggie Wrap",
                    "Avocado and Bacon Wrap",
                ],
            ],
            [
                'name' => "Desserts",
                'products' => [
                    "Cheesecake",
                    "Chocolate Brownie",
                    "Apple Pie",
                    "Tiramisu",
                    "Ice Cream Sundae",
                    "Creme Brulee",
                    "Chocolate Mousse",
                    "Fruit Tart",
                    "Key Lime Pie",
                    "Red Velvet Cake",
                ],
            ],
            [
                'name' => "Beverages",
                'products' => [
                    "Soft Drinks",
                    "Iced Tea",
                    "Lemonade",
                    "Coffee",
                    "Hot Tea",
                    "Fruit Juice",
                    "Smoothies",
                    "Milkshakes",
                    "Mocktails",
                    "Mineral Water",
                ],
            ],
            [
                'name' => "Breakfast",
                'products' => [
                    "Pancakes",
                    "French Toast",
                    "Eggs Benedict",
                    "Omelette",
                    "Bagel with Cream Cheese",
                    "Breakfast Burrito",
                    "Waffle",
                    "Granola Parfait",
                    "Croissant",
                    "Breakfast Wrap",
                ],
            ],
            [
                'name' => "Drinks",
                'products' => [
                    "Tomato Soup",
                    "Minestrone Soup",
                    "Lentil Soup",
                    "Clam Chowder",
                    "Miso Soup",
                    "Chicken Noodle Soup",
                    "French Onion Soup",
                    "Creamy Mushroom Soup",
                    "Spicy Seafood Soup",
                    "Thai Coconut Soup",
                ],
            ],
        ];
        foreach ($categoriesWithProducts as $category) {
            $id = Uuid::uuid4()->toString();
            FoodCategory::create([
                'id' => $id,
                'status' => true,
                'location_id' => Location::first()->id,
                'name' => $category['name'],
            ]);
            foreach ($category['products'] as $productName) {
                $idPr = Uuid::uuid4()->toString();
                $price = rand(10, 100);
                $cost = rand(1, $price - 3);
                $foodItem = FoodItem::create([
                    'id' => $idPr,
                    'location_id' => Location::first()->id,
                    'name' => $productName,
                    'sku' => (int)round(microtime(true) * 1000),
                    'price' => $price,
                    'cost' => $cost,
                    'status' => true,
                    'food_category_id' => $id,
                    'size' => [
                        'small' => 15,
                        'medium' => 35,
                        'large' =>null
                    ]
                ]);
                $foodItem = FoodItem::find($idPr);
                $foodItem->ingredients()->sync([]);
                $ingredients = Ingredient::get();
                foreach ($ingredients as $ingredient) {
                    $foodItem->ingredients()->attach($ingredient->id, ['quantity' => 1]);
                }
            }
        }


        $modifiers = [
            [
                'id' => Uuid::uuid4(),
                'name' => 'Ketchup',
                'price' => 2,
                'cost' => 1,
                'unit' => 'g',
                'quantity' => 2,
                'alert_quantity' => 12,
                'description' => 'ketchup description',
                'title' => 'Cap katchup',
                'products' => [
                    "Pancakes",
                    "French Toast",
                ],
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'Sugar',
                'price' => 2,
                'cost' => 1,
                'unit' => 'g',
                'quantity' => 2,
                'alert_quantity' => 12,
                'description' => 'sugar description',
                'title' => 'Sugar',
                'products' => [
                    "Pancakes",
                    "French Toast",
                ],
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'schrimp',
                'price' => 2,
                'cost' => 1,
                'unit' => 'ltr',
                'quantity' => 5,
                'alert_quantity' => 20,
                'title' => 'Pesto',
                'description' => 'milk description',
                'products' => [
                    "Pancakes",
                    "French Toast",
                ],
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'Pesto',
                'price' => 2,
                'cost' => 1,
                'unit' => 'ltr',
                'quantity' => 5,
                'alert_quantity' => 20,
                'title' => 'Hot sauce',
                'description' => 'milk description',
                'products' => [
                    "Pancakes",
                    "French Toast",
                ],
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'schrimp',
                'price' => 2,
                'cost' => 1,
                'unit' => 'ltr',
                'quantity' => 5,
                'alert_quantity' => 20,
                'title' => 'Tahini',
                'description' => 'milk description',
                'products' => [
                    "Pancakes",
                    "French Toast",
                ],
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'schrimp',
                'price' => 2,
                'cost' => 1,
                'unit' => 'ltr',
                'quantity' => 5,
                'alert_quantity' => 20,
                'title' => 'Honey',
                'description' => 'milk description',
                'products' => [
                    "Pancakes",
                    "French Toast",
                ],
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'schrimp',
                'price' => 2,
                'cost' => 1,
                'unit' => 'ltr',
                'quantity' => 5,
                'alert_quantity' => 20,
                'title' => 'Garlic aioli',
                'description' => 'milk description',
                'products' => [
                    "Pancakes",
                    "French Toast",
                ],
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'schrimp',
                'price' => 2,
                'cost' => 1,
                'unit' => 'ltr',
                'quantity' => 5,
                'alert_quantity' => 20,
                'title' => 'Soy glaze',
                'description' => 'milk description',
                'products' => [
                    "Pancakes",
                    "French Toast",
                ],
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'schrimp',
                'price' => 2,
                'cost' => 1,
                'unit' => 'ltr',
                'quantity' => 5,
                'alert_quantity' => 20,
                'title' => 'Salsa',
                'description' => 'milk description',
                'products' => [
                    "Pancakes",
                    "French Toast",
                ],
            ],
            [
                'id' => Uuid::uuid4(),
                'name' => 'schrimp',
                'price' => 2,
                'cost' => 1,
                'unit' => 'ltr',
                'quantity' => 5,
                'alert_quantity' => 20,
                'title' => 'Harissa',
                'description' => 'milk description',
                'products' => [
                    "Pancakes",
                    "French Toast",
                ],
            ],
        ];

        foreach ($modifiers as $key => $modifier) {
            $categories = FoodCategory::get()->toArray();
            if ($key <= count($categories)){
                $categoryId = $categories[$key]['id'];
            }else{
                $categoryId = null;
            }
            $id = Uuid::uuid4()->toString();
            $price = rand(10, 100);
            $cost = rand(1, $price - 3);
            Modifier::create([
                'id' => $id,
                'location_id' => Location::first()->id,
                'category_id' => $categoryId,
                'name' => $modifier['name'],
                'title' => $modifier['title'],
                'price' => $price,
                'status' => true,
                'cost' => $cost,
                'sku' => (int)round(microtime(true) * 1000),
            ]);
            $modifier = Modifier::find($id);
            $modifier->ingredients()->sync([]);
            $ingredients = Ingredient::get();
            foreach ($ingredients as $ingredient) {
                $modifier->ingredients()->attach($ingredient->id, ['quantity' => 1]);
            }
        }


        $meals = [
            [
                'id' => Uuid::uuid4(),
                'name' => [
                    "veggie-primavera",
                    "shrimp-scampi",
                    "t-bone-steak",
                    "prime-rib",
                    "steak-frites",
                ],
                'price' => 2,
                'cost' => 1,
                'unit' => 'g',
                'quantity' => 2,
                'alert_quantity' => 12,
                'name' => 'veggie-primavera',
                'products' => [
                    "Pancakes",
                    "French Toast",
                ],
            ],
            [
                'id' => Uuid::uuid4(),
                'price' => 2,
                'cost' => 1,
                'name' => 'Hamburger',
                'unit' => 'ltr',
                'quantity' => 5,
                'alert_quantity' => 20,
                'description' => 'milk description',
            ]
        ];
        $dealCategoryId = Uuid::uuid4();
        FoodCategory::create([
            'id' => $dealCategoryId,
            'location_id' => Location::first()->id,
            'name' => "Deals"
        ]);
        $i = 0;
        foreach ($meals as $meal) {
            $mealId = Uuid::uuid4()->toString();

            Meal::create([
                'id' => $mealId,
                'food_category_id' => $dealCategoryId,
                'location_id' => Location::first()->id,
                'sku' => (int)round(microtime(true) * 1000),
                'name' =>  $meal['name'],
                'price' => $price,
                'title' => 'title',
                'cost' => $cost,
                'status' => true,
            ]);
            $deal = Meal::find($mealId);
            $deal->foodItems()->sync([]);
            $deal->foodItems()->attach(FoodItem::get()[1 + $i], ['quantity' => 1]);
            $deal->foodItems()->attach(FoodItem::get()[2 + $i], ['quantity' => 1]);
            $deal->foodItems()->attach(FoodItem::get()[3 + $i], ['quantity' => 1]);
            $deal->foodItems()->attach(FoodItem::get()[4 + $i], ['quantity' => 1]);
            $i++;
        }



        $serviceTables = [
            [
                'id' => Uuid::uuid4()->toString(),
                'title' => 'table center'
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'title' => 'table vip'
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'title' => 'table one'
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'title' => 'table two'
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'title' => 'table three'
            ],
        ];

        foreach ($serviceTables as $serviceTable) {
            ServiceTable::create([
                'id' => $serviceTable['id'],
                'location_id' => Location::first()->id,
                'title' => $serviceTable['title'],
            ]);
        }




        $taxes = [
            [
                'name' => 'Dine In Tax',
                'type' => 'dine_in',
                'tax_rate' => 18,
                'tax_id' => 49874647498684889,
                'description' => 'test description tax'
            ],
            [
                'name' => 'Take away Tax',
                'type' => 'take_away',
                'tax_rate' => 10,
                'tax_id' => 35324534534534534,
                'description' => 'test description tax'
            ],
            [
                'name' => 'Delivery Tax',
                'type' => 'delivery',
                'tax_rate' => 28,
                'tax_id' => 54353425345345455,
                'description' => 'test description tax'
            ]
        ];

        foreach ($taxes as $tax) {
            Tax::create([
                'name' => $tax['name'],
                'type' => $tax['type'],
                'tax_rate' => $tax['tax_rate'],
                'tax_id' => $tax['tax_id'],
                'description' => $tax['description'],
                'location_id' => Location::first()->id,
            ]);
        }
        dd('done');
    }
}
