<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FoodMenu;

class FoodMenuController extends Controller
{
    /**
     * Display a listing of food menu items.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menuItems = FoodMenu::orderBy('meal_type')->paginate(15);
        return view('admin.food-menu.index', compact('menuItems'));
    }

    /**
     * Show the form for creating a new food menu item.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.food-menu.create');
    }

    /**
     * Store a newly created food menu item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'meal_type' => 'required|string|in:Breakfast,Lunch,Dinner,Snack',
            'dietary_tags' => 'nullable|string',
            'available' => 'boolean',
        ]);

        FoodMenu::create($validated);
        
        return redirect()->route('admin.food-menu.index')
            ->with('success', 'Menu item created successfully');
    }

    /**
     * Display the specified food menu item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $menuItem = FoodMenu::findOrFail($id);
        return view('admin.food-menu.show', compact('menuItem'));
    }

    /**
     * Show the form for editing the specified food menu item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $menuItem = FoodMenu::findOrFail($id);
        return view('admin.food-menu.edit', compact('menuItem'));
    }

    /**
     * Update the specified food menu item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'meal_type' => 'required|string|in:Breakfast,Lunch,Dinner,Snack',
            'dietary_tags' => 'nullable|string',
            'available' => 'boolean',
        ]);
        
        $menuItem = FoodMenu::findOrFail($id);
        $menuItem->update($validated);
        
        return redirect()->route('admin.food-menu.index')
            ->with('success', 'Menu item updated successfully');
    }

    /**
     * Remove the specified food menu item from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $menuItem = FoodMenu::findOrFail($id);
        $menuItem->delete();
        
        return redirect()->route('admin.food-menu.index')
            ->with('success', 'Menu item deleted successfully');
    }
} 