<?php

// use Modules\MakerModule\Http\Controllers\MakerModuleController;

// Route::get('/maker-modules', [MakerModuleController::class, 'index'])->name('maker-modules.index');
// Route::get('/maker-modules/create', [MakerModuleController::class, 'create'])->name('maker-modules.create');
// Route::post('/maker-modules', [MakerModuleController::class, 'store'])->name('maker-modules.store');
// Route::get('/maker-modules/{maker-module}', [MakerModuleController::class, 'show'])->name('maker-modules.show');
// Route::get('/maker-modules/{maker-module}/edit', [MakerModuleController::class, 'edit'])->name('maker-modules.edit');
// Route::put('/maker-modules/{maker-module}', [MakerModuleController::class, 'update'])->name('maker-modules.update');
// Route::delete('/maker-modules/{maker-module}', [MakerModuleController::class, 'destroy'])->name('maker-modules.destroy');
Route::name('maker-modules.')
    ->middleware(['locale', 'auth', 'web'])
    ->prefix('{lang}/maker-modules')
    ->group(function () {

    });


