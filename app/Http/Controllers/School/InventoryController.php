<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $school     = app('school');
        $categories = InventoryCategory::withCount('items')->get();
        $items      = InventoryItem::with('category')
            ->when($request->category_id, fn($q) => $q->where('inventory_category_id', $request->category_id))
            ->when($request->status,      fn($q) => $q->where('status', $request->status))
            ->when($request->search,      fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->paginate(20);
        return view('school.inventory.index', compact('items', 'categories', 'school'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                  => 'required|string',
            'inventory_category_id' => 'required|exists:inventory_categories,id',
            'code'                  => 'nullable|string',
            'quantity'              => 'required|integer|min:0',
            'min_quantity'          => 'required|integer|min:0',
            'unit_price'            => 'required|numeric|min:0',
            'unit'                  => 'nullable|string',
        ]);
        $data['school_id'] = app('school')->id;
        $item = InventoryItem::create($data);
        $item->updateStatus();
        return redirect()->back()->with('success', 'Item added');
    }

    public function transaction(Request $request)
    {
        $data = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'type'              => 'required|in:in,out,adjustment',
            'quantity'          => 'required|integer|min:1',
            'unit_price'        => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string',
            'transaction_date'  => 'required|date',
        ]);
        $data['school_id'] = app('school')->id;
        $data['user_id']   = auth()->id();
        InventoryTransaction::create($data);

        $item = InventoryItem::findOrFail($data['inventory_item_id']);
        match ($data['type']) {
            'in'         => $item->increment('quantity', $data['quantity']),
            'out'        => $item->decrement('quantity', $data['quantity']),
            'adjustment' => $item->update(['quantity' => $data['quantity']]),
        };
        $item->updateStatus();
        return redirect()->back()->with('success', 'Transaction recorded');
    }

    public function categories(Request $request)
    {
        $school     = app('school');
        $categories = InventoryCategory::withCount('items')->paginate(20);
        return view('school.inventory.categories', compact('categories', 'school'));
    }

    public function storeCategory(Request $request)
    {
        $data              = $request->validate(['name' => 'required|string', 'description' => 'nullable|string']);
        $data['school_id'] = app('school')->id;
        InventoryCategory::create($data);
        return redirect()->back()->with('success', 'Category added');
    }
}
