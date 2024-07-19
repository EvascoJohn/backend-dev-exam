<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $request)
    {

        $query = Category::query();

        // This query searches all the matching keywords from name and desc.

        if ($request) {
            $query->where('name', 'like', '%' . $request . '%')
                  ->orWhere('description', 'like', '%' . $request . '%');
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
            ]);

            $category = Category::create($validated);
            return response()->json($category, 201);
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
        $category = Category::find($id);

        if ($category) {
            return response()->json($category);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        }
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
            ]);

            $category = Category::findOrFail($id);
            $category->update($validated);
            return response()->json($category, 200);
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
        Category::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
