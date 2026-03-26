<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::all(), 200);
    }

    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product, 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'description' => 'required|string',
            'features' => 'nullable',
            'rating' => 'nullable|integer|min:0|max:5',
            'reviews' => 'nullable|integer|min:0',
            'qty' => 'required|integer|min:0',
            'brand' => 'required|string|max:255',
        ]);

        $imageUrls = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('images', 'public');
                $imageUrls[] = url('storage/' . $path);
            }
        }

        $data['images'] = $imageUrls;
        $data['features'] = $this->parseFeatures($data['features'] ?? []);
        $data['in_stock'] = $data['qty'] > 0;

        $product = Product::create($data);

        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    // Update normal fields except images and features
    $product->update($request->except('images', 'features'));

    // Update features array
    if ($request->has('features')) {
        $product->features = $request->features;
    }

    // Handle images array
    $images = $product->images ?? []; // existing images

    // Keep old images that admin didn't remove
    if ($request->old_images) {
        $images = $request->old_images; // old_images should be array of URLs
    } else {
        $images = [];
    }

    // Append new uploaded images
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $file) {
            $path = $file->store('images', 'public');
            $images[] = url("storage/$path");
        }
    }

    $product->images = $images;
    $product->save();

    return response()->json($product);
}
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $images = $product->images ?? [];
        foreach ($images as $imgUrl) {
            $oldPath = str_replace(url('storage') . '/', '', $imgUrl);
            Storage::disk('public')->delete($oldPath);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }

    //  Safely parse features input (array or JSON)
    private function parseFeatures($features)
    {
        if (is_array($features)) return $features;
        if (is_string($features)) return json_decode($features, true) ?? [];
        return [];
    }
}
