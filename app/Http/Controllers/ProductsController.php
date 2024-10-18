<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function order(Request $request)
    {
        try{
            DB::beginTransaction();

            $request->validate([
                "product_id" => "required|numeric",
                "quantity" => "required|numeric"
            ]);

            $product = Products::find($request->product_id);

            if($request->quantity <= $product->available_stock){
                $remainingStock = $product->available_stock - intval($request->quantity);

                $product->update([
                    "available_stock" => $remainingStock
                ]);

                DB::commit();

                return response()->json(["message" => "You have successfully ordered this product."], 201);
            }else{

                return response()->json(["message" => "Failed to order this product due to unavailability of the stock"], 400);
            }


        }catch(Exception $e){
            DB::rollback();

            return response()->json(["message" => $e->getMessage()],400);
            throw $e;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Products $products)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Products $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Products $products)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Products $products)
    {
        //
    }
}
