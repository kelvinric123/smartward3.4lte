<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FoodOrder;
use App\Models\Patient;
use App\Models\Ward;
use App\Models\Bed;

class FoodOrderController extends Controller
{
    /**
     * Display a listing of all food orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = FoodOrder::with(['patient', 'bed', 'ward'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.food-orders.index', compact('orders'));
    }

    /**
     * Display breakfast orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function breakfastOrders()
    {
        $orders = FoodOrder::with(['patient', 'bed', 'ward'])
            ->where('meal_type', 'Breakfast')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.food-orders.meal_type', [
            'orders' => $orders,
            'mealType' => 'Breakfast'
        ]);
    }

    /**
     * Display lunch orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function lunchOrders()
    {
        $orders = FoodOrder::with(['patient', 'bed', 'ward'])
            ->where('meal_type', 'Lunch')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.food-orders.meal_type', [
            'orders' => $orders,
            'mealType' => 'Lunch'
        ]);
    }

    /**
     * Display dinner orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function dinnerOrders()
    {
        $orders = FoodOrder::with(['patient', 'bed', 'ward'])
            ->where('meal_type', 'Dinner')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.food-orders.meal_type', [
            'orders' => $orders,
            'mealType' => 'Dinner'
        ]);
    }

    /**
     * Display snack orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function snackOrders()
    {
        $orders = FoodOrder::with(['patient', 'bed', 'ward'])
            ->where('meal_type', 'Snack')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.food-orders.meal_type', [
            'orders' => $orders,
            'mealType' => 'Snacks'
        ]);
    }

    /**
     * Update the status of a food order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        $order = FoodOrder::findOrFail($id);
        $order->status = $request->status;
        $order->save();
        
        return redirect()->back()->with('success', 'Order status updated successfully');
    }

    /**
     * Remove the specified food order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = FoodOrder::findOrFail($id);
        $order->delete();
        
        return redirect()->back()->with('success', 'Order deleted successfully');
    }
} 