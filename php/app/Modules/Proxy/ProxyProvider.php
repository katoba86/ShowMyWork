<?php


namespace App\Modules\Proxy;




use Illuminate\Support\ServiceProvider;

class ProxyProvider extends ServiceProvider
{


    public function boot()
    {
        $this->app->router->group([
            'prefix'=>'proxy',
            'namespace' => 'App\Modules\Proxy',
            //'middleware' => [ProxyMiddleware::class]
        ], function ($router) {
            $router->get('/test',[ProxyController::class,'test']);
            //$router->get('/wp-json/wp/v2/categories',[ProxyController::class,'categories']);
            //$router->get('/wp-json/wp/v2/posts',[ProxyController::class,'articles']);
        });





        $this->app->singleton(ProxyService::class,ProxyService::class);
    }

}
