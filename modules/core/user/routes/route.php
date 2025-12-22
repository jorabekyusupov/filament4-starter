<?php

// use Modules\User\Http\Controllers\UserController;

// Route::get('/users', [UserController::class, 'index'])->name('users.index');
// Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
// Route::post('/users', [UserController::class, 'store'])->name('users.store');
// Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
// Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
// Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
// Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
Route::name('users.')
    ->middleware(['locale', 'auth', 'web'])
    ->prefix('/users')
    ->group(function () {
        Route::get('/', function (){
            dd('User Module is working fine.');
        })
            ->name('index');
    });


