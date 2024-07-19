<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

// I pasted the sample data for reference.
// {
//     "name": "testing",
//     "description": "testing",
//     "price": 69.69,
//     "category_id": 2
// }

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $search)
    {
        $query = Item::query();

        // This query searches all the matching keywords from name and description.
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
        }

        // dd($query);

        $items = $query->get();
        return response()->json($items);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'category_id' => 'required|integer|exists:categories,id',
            ]);

            $item = Item::create($validated);
            return response()->json($item, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $item = Item::findOrFail($id);
        return response()->json($item);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'category_id' => 'required|integer|exists:categories,id',
            ]);

            $item = Item::findOrFail($id);
            $item->update($validated);
            return response()->json($item, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $item = Item::findOrFail($id);
        $item->delete();
        return response()->json(null, 204);
    }
    public function indexWithCategory()
    {
        //pipeline
        $items = Item::raw(function($collection) {
            // Perform the lookup to join items with categories
            return $collection->aggregate([
                [
                    '$lookup' => [
                        'from' => 'categories',       // join with the categories collection
                        'localField' => 'category_id', // field in the items collection
                        'foreignField' => '_id',      // field in the categories collection
                        'as' => 'category'            // field to output the joined data
                    ]
                ],
                [
                    '$unwind' => '$category'          // create a document for each array for easier use.
                ]
            ]);
        });

        // Return the results as JSON
        return response()->json($items);
    }
}
