<?php
use App\Modules\Proxy\ProxyController;
use Illuminate\Support\Facades\Route;



Route::group([
    'prefix'=>'proxy/wp-json',
    'middleware'=>[\App\Modules\Proxy\ProxyMiddleware::class]
], function () {
    Route::get('',[ProxyController::class,'info']);
    Route::get('wp/v2/posts',[ProxyController::class,'articles']);
    Route::get('wp/v2/categories',[ProxyController::class,'categories']);
    Route::get('wp/v2/tags', [ProxyController::class, 'tags']);
    Route::get('wp/v2/media', [ProxyController::class, 'media']);
    Route::get('wp/v2/pages', [ProxyController::class, 'pages']);



});
