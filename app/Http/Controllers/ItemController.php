<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    // GET /api/items?search=choco&category_id=1&per_page=10
    public function index(Request $request)
    {
        $items = Item::with('category')
            ->when($request->query('search'), fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->query('category_id'), fn ($q, $c) => $q->where('category_id', $c))
            ->orderBy('name')
            ->paginate($request->integer('per_page', 15));

        return response()->json($items);
    }

    public function show(Item $item)
    {
        return response()->json($item->load('category'));
    }

    public function store(Request $request)
    {
        $item = Item::create($this->validated($request));

        return response()->json($item->load('category'), 201);
    }

    public function update(Request $request, Item $item)
    {
        $item->update($this->validated($request));

        return response()->json($item->load('category'));
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return response()->noContent();
    }

    private function validated(Request $request): array
    {
        // PUT = all fields required, PATCH = partial update
        $req = $request->isMethod('put') || $request->isMethod('post') ? 'required' : 'sometimes';

        return $request->validate([
            'category_id' => "$req|integer|exists:categories,id",
            'name'        => "$req|string|max:255",
            'description' => 'nullable|string',
            'price'       => "$req|numeric|min:0",
            'quantity'    => "$req|integer|min:0",
        ]);
    }
}
