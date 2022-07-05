<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CartProduct;
use App\Traits\HttpRepsonseTrait;

class CartProductController extends Controller
{
    use HttpRepsonseTrait;

    public function addToCart(Request $request, Product $product)
    {
        try {
            $cart_id = $request->session()->get('cart_id');
            
            if (!$cart_id) {
                $cart = Cart::create();
                $cart_id = $cart->id;
                $request->session()->put('cart_id', $cart_id);
            }
           


            $cartProduct = CartProduct::where('cart_id', $cart_id)
        ->where('product_id', $product->id)
        ->first();
        
          
            if (!$cartProduct) {
                $cartProduct   =   CartProduct::create([
            'cart_id'      =>  $cart_id,
            'product_id'   =>  $product->id,
            'quantity'     =>  1
        ]);
                return $this->success('Product added to cart successfully', $cartProduct);
            } else {
                $cartProduct->update(['quantity' => $cartProduct->quantity + 1]);
                return $this->success('Product added to cart successfully', $cartProduct);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

   
    public function index()
    {
        //id cart products are same display incremented prouct qauntity
        $cartProduct = CartProduct::get();

        if (!$cartProduct) {
            return $this->failure('No products in cart');
        }
        
        return $this->success('Cart products retrieved successfully', $cartProduct);
    }

    public function destroy(Product $product)
    {
        $cartProduct = CartProduct::where('product_id', $product->id)->first();
       
        if ($cartProduct->quantity > 1) {
            $cartProduct->update(['quantity' => $cartProduct->quantity - 1]);
            return $this->success('Product quantity updated successfully', $cartProduct);
        }
        $cartProduct->delete();
        return $this->success('Product removed from cart successfully');
    }
}
