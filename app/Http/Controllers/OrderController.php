<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartProduct;
use Illuminate\Http\Request;
use App\Traits\HttpRepsonseTrait;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    use HttpRepsonseTrait;
    
    public function orderPlace(Request $request)
    {
        $cart_id = $request->session()->get('cart_id');
          
        if (!$cart_id) {
            return $this->failure('Cart not found');
        }

        $order = Order::create([
            "payment_method" => $request->payment_method,
            "delivery_address" => $request->delivery_address,
            "user_id" => 1,
        ]);
        
        $cart_id        =   $request->session()->get('cart_id');
        $cartProducts   =   CartProduct::where('cart_id', $cart_id)->get();
        
        
        $orderProducts = [];
        foreach ($cartProducts as $cartProduct) {
            $orderProducts[] = [
                "order_id" => $order->id,
                "product_id" => $cartProduct->product_id,
                "quantity" => $cartProduct->quantity,
                'price' => $cartProduct->product->price * $cartProduct->quantity,
                // 'product_details' => $cartProduct['product'],
            ];

            $orderProducts = OrderProduct::insert($orderProducts);
        }

        
        CartProduct::where('cart_id', $cart_id)->delete();
        Cart::where('id', $cart_id)->delete();
        $request->session()->flush('cart_id');
        return $this->success('Order placed successfully', $order);
    }

    public function updateStatusToProcesseing(Order $order)
    {
        try {
            if ($order->status == 'processing') {
                return $this->failure('Order already in processing');
            } else {
                $order->update(['status' => 'processing',]);
                return $this->success('Order updated successfully', $order);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }
    
    public function updateStatusToCompleted(Request $request, Order $order)
    {
        if ($order->status == 'completed') {
            return $this->failure('Order already completed');
        } else {
            $order->update(['status' => 'completed']);
            return $this->success('Order updated successfully', $order);
        }
    }

    public function updateStatusToCancelled(Request $request, Order $order)
    {
        if ($order->status == 'cancelled') {
            return $this->failure('Order already cancelled');
        } else {
            $order->update(['status' => 'cancelled']);
            return $this->success('Order updated successfully', $order);
        }
    }
}
