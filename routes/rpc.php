<?php
use Illuminate\Http\Request;

Route::post('/rpc/v1/', static function (Request $request) {
    $jsonRpcServer = app(\Upgate\LaravelJsonRpc\Contract\ServerInterface::class);
    $jsonRpcServer
        ->router();

    return $jsonRpcServer->run($request);
})->middleware([
    'basic-auth:application-rpc,username',
]);