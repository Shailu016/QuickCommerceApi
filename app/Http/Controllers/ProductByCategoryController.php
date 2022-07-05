<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductByCategoryController extends Controller
{
    public function t(Category $category)
    {
        $product = Product::where('category_id', $category->id)->get();
        return $product;
    }
}
