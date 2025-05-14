<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FoodMenu;

class FoodMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Breakfast items
        $breakfastItems = [
            [
                'name' => 'Oatmeal',
                'description' => 'Hot oatmeal with choice of toppings',
                'meal_type' => 'Breakfast',
                'dietary_tags' => 'Vegetarian, Low Fat, Calories: 250',
                'available' => true,
            ],
            [
                'name' => 'Scrambled Eggs',
                'description' => 'Freshly scrambled eggs with toast',
                'meal_type' => 'Breakfast',
                'dietary_tags' => 'High Protein, Calories: 320',
                'available' => true,
            ],
            [
                'name' => 'Pancakes',
                'description' => 'Stack of pancakes with maple syrup',
                'meal_type' => 'Breakfast',
                'dietary_tags' => 'Vegetarian, Calories: 450',
                'available' => true,
            ],
        ];

        // Lunch items
        $lunchItems = [
            [
                'name' => 'Grilled Chicken',
                'description' => 'Herb-grilled chicken with vegetables',
                'meal_type' => 'Lunch',
                'dietary_tags' => 'High Protein, Low Carb, Calories: 380',
                'available' => true,
            ],
            [
                'name' => 'Caesar Salad',
                'description' => 'Fresh romaine with chicken and dressing',
                'meal_type' => 'Lunch',
                'dietary_tags' => 'Low Carb, Calories: 310',
                'available' => true,
            ],
            [
                'name' => 'Turkey Sandwich',
                'description' => 'Turkey with lettuce, tomato on whole grain',
                'meal_type' => 'Lunch',
                'dietary_tags' => 'Balanced, Calories: 350',
                'available' => true,
            ],
        ];

        // Dinner items
        $dinnerItems = [
            [
                'name' => 'Baked Salmon',
                'description' => 'Fresh salmon with lemon butter sauce',
                'meal_type' => 'Dinner',
                'dietary_tags' => 'High Protein, Omega-3, Calories: 420',
                'available' => true,
            ],
            [
                'name' => 'Pasta Primavera',
                'description' => 'Pasta with vegetables in light sauce',
                'meal_type' => 'Dinner',
                'dietary_tags' => 'Vegetarian, Calories: 380',
                'available' => true,
            ],
            [
                'name' => 'Vegetable Stir Fry',
                'description' => 'Mixed vegetables with tofu in sauce',
                'meal_type' => 'Dinner',
                'dietary_tags' => 'Vegan, Gluten-Free, Calories: 320',
                'available' => true,
            ],
        ];

        // Snack items
        $snackItems = [
            [
                'name' => 'Fresh Fruit Cup',
                'description' => 'Seasonal fresh fruit assortment',
                'meal_type' => 'Snack',
                'dietary_tags' => 'Vegan, Gluten-Free, Calories: 110',
                'available' => true,
            ],
            [
                'name' => 'Greek Yogurt',
                'description' => 'Greek yogurt with honey',
                'meal_type' => 'Snack',
                'dietary_tags' => 'Vegetarian, High Protein, Calories: 180',
                'available' => true,
            ],
            [
                'name' => 'Mixed Nuts',
                'description' => 'Assorted nuts and dried fruits',
                'meal_type' => 'Snack',
                'dietary_tags' => 'Vegan, High Protein, Calories: 210',
                'available' => true,
            ],
        ];

        // Insert all items
        foreach (array_merge($breakfastItems, $lunchItems, $dinnerItems, $snackItems) as $item) {
            FoodMenu::create($item);
        }
    }
} 