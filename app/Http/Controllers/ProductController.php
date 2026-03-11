<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::latest()->paginate(10);

        return Inertia::render('Products/index', [
            'products' => $products
        ]);
    }

    public function store(Request $request)
    {
        Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'category' => $request->category,
            'image_main' => $request->image_main,
            'images_sub' => json_encode($request->images_sub), // Lưu dạng JSON
            'specs' => json_encode($request->specs), // Lưu dạng JSON
            'description' => $request->description,
            'document' => $request->document,
        ]);

        return redirect()->route('products.index')->with('message', 'Thêm thành công!');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Nó sẽ đi tìm file resources/js/Pages/Products/Form.vue
        return inertia('Products/form'); 
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
