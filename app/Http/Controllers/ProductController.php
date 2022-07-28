<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\HttpRepsonseTrait;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    use HttpRepsonseTrait;

    public function index(Product $product)
    {
        $products = $product->all();

        return response([
            'success'      =>    true,
            'message'      =>    'Products retrieved successfully',
            'data'         =>    $products,
        ], 200);
    }
 
   
    public function store(Request $request)
    {
        try {
            $validated  = $request->validate([
                'name'          =>    'required|string|max:255',
                'description'   =>    'nullable|string',
                'image'         =>    'nullable',
                'category_id'   =>    'nullable|integer',
                'price'         =>    'required|numeric',
            ]);
         
            if (isset($request->image)) {
                $imagePath = time() . $request->name . '.'. $request->image->extension();
                $request->image->move(public_path('images'), $imagePath);
            }
           
    
            $product = Product::create($validated);
           
            
            return $this->success('Product created successfully', $product);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

   
    public function show(Product $product)
    {
        return $this->success('Product retrieved successfully', $product);
    }


    public function update(Request $request, Product $product)
    {
        try {
            $validated  = $request->validate([
                'name'          =>    'required|string|max:255',
                'description'   =>    'nullable|string|max:255',
                'image'         =>    'nullable',
                'category_id'   =>    'nullable|integer',
                'price'         =>    'required|numeric',
            ]);
         
            if (isset($request->image)) {
                $imagePath = time() . $request->name . '.'. $request->image->extension();
                $request->image->move(public_path('images'), $imagePath);
            }
           
    
            $product->update($validated);
            
            return $this->success('Product updated successfully', $product);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

  
    public function destroy(Product $product)
    {
        $product->delete();
        return $this->success('Product deleted successfully', $product);
    }

    public function productByCategory(Category $category)
    {
        $product = Product::where('category_id', $category->id)->get();
        return $product;
    }
}
