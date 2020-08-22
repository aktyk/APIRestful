<?php

namespace App\Http\Controllers\Product;

use App\Buyer;
use App\Http\Controllers\ApiController;
use App\Product;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductBuyerTransactionController extends ApiController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product, Buyer $buyer)
    {
        // Se verifica que el comprador no sea igual al vendedor
        if ($request->id == $product->seller_id) {
            return $this->errorResponse('El vendedor debe ser diferente al vendedor', 409);
        }

        // Se verifica que el comprador esté verificado en el sistema
        if (!$buyer->esVerificado()) {
            return $this->errorResponse('El comprador debe estar verificado en el sistema', 409);
        }

        // Se verifica que el vendedor esté verificado en el sistema
        if (!$product->seller->esVerificado()) {
            return $this->errorResponse('El vendedor debe ser un usuario verificado en el sistema', 409);
        }

        // Se verifica que el producto esté disponible
        if (!$product->estaDisponible()) {
            return $this->errorResponse('El producto para esta transacción no está disponible', 409);
        }

        // Se verifica que haya existencias disponibles
        if ($product->quantity < $request->quantity) {
            return $this->errorResponse('No hay existencias disponibles para esta transacción', 409);
        }

        // Se registra la transacción encapsulandola en una "transaction"
        return DB::transaction(function() use($request, $product, $buyer) {
            $product->quantity -= $request->quantity;
            $product->save();

            $transaction = Transaction::create([
                'quantity' => $request->quantity,
                'buyer_id' => $buyer->id,
                'product_id' => $product->id,
            ]);

            return $this->showOne($transaction, 201);

        });

    }

}
