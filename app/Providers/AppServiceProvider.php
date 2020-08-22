<?php

namespace App\Providers;

use App\Mail\UserCreated;
use App\Mail\UserMailChanged;
use App\Product;
use App\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // Se modifica el estado del producto a NO DISPONIBLE cuando su existencia sea cero
        Product::updated(function($product) {
            if ($product->quantity == 0 && $product->estaDisponible()) {
                $product->status = Product::PRODUCTO_NO_DISPONIBLE;

                $product->save();
            }
        });

        /* Se envía correo electrónico al insertar un nuevo usuario
        con el token de verificación */
        User::created(function($user) {
            // Se usa el fasar Mail
            Mail::to($user)->send(new UserCreated($user));
        });

        // Uso de isDirty para saber si una propiedad ha cambiado
        User::updated(function($user) {
            if ($user->isDirty('email')) {
                Mail::to($user)->send(new UserMailChanged($user));
            }
        });

    }
}
