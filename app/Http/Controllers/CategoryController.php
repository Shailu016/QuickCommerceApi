<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Traits\HttpRepsonseTrait;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    use HttpRepsonseTrait;
   
    public function index()
    {
        return $this->success('Categories retrieved successfully', Category::all());
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          =>    'required|string|max:255',
            'description'   =>    'nullable|string|max:255',
            'image'         =>    'nullable',
        ]);

        if (isset($request->image)) {
            $imagePath = time() . $request->name . '.'. $request->image->extension();
            $request->image->move(public_path('images'), $imagePath);
        }
        
         
        $category = Category::create($validated);
        return $this->success('Category created successfully', $category);
    }

    
    public function show(Category $category)
    {
        return $this->success('Category retrieved successfully', $category);
    }

    
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'          =>    'required',
            'description'   =>    'nullable',
            'image'         =>    'nullable',
        ]);

        if (isset($request->image)) {
            $imagePath = time() . $request->name . '.'. $request->image->extension();
            $request->image->move(public_path('images'), $imagePath);
            $oldImagePath = public_path('images') . "\\" . $category->image_path;
    
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }
        }

        $category->update($validated);
        return $this->success('Category updated successfully', $category);
    }

    
    public function destroy(Category $category)
    {
        $category->delete();
        return $this->success('Category deleted successfully', $category);
    }
}
