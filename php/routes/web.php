<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');



Route::group([
    'middleware'=>['auth:sanctum','verified']
],function(){

    Route::get('pages',\App\Http\Livewire\Crud::class)->name('pages');
    Route::get('page/overview',\App\Http\Livewire\Pageoverview::class);
    Route::get('page/article/{id}',\App\Http\Livewire\Edit\Article::class)->name('article.edit');
    Route::get('page/articles',\App\Http\Livewire\List\Article::class);
    Route::get('page/categories/{id}',\App\Http\Livewire\Edit\Category::class)->name('category.edit');
    Route::get('page/categories',\App\Http\Livewire\Categories::class)->name('category.list');

});


Route::get('/media/photos/{id}.jpg',function($id){

    try {
        $file = Storage::get("photos/".$id . ".jpg");
    }catch(\Exception $e){
        abort(404);
    }
    $type = "image/jpg";
    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
});

Route::get('/media/{id}.jpg',function($id){

    try {
        $file = Storage::get($id . ".jpg");
    }catch(\Exception $e){
        abort(404);
    }
    $type = "image/jpg";
    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
});
