<?php


namespace App\Modules\Proxy;


use App\Models\Live\Page;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProxyMiddleware
{
    public function handle(Request $request,Closure $next)
    {
        $getProxyPage = function(string $str,$detect='Basic '):?string{
            if (Str::startsWith($str, $detect)) {
                $target = Str::substr($str, strlen($detect));
                $target = explode(":",base64_decode($target,true));
                if(!is_array($target) || count($target)!==2){
                    return null;
                }
                return $target[0];
            }
            return null;
        };

        $authorization = $request->headers->get('authorization');

        $detected = ($authorization!==null)?$getProxyPage($authorization):null;



        if(null === $detected) {
            abort(response()->json(['error' => 'Unauthenticated just for you.'], 401));
        }
        $pages = Page::get();


        $first = Page::where('domain',$detected)->first();

        if($first instanceof Model){
            $request->query->set('page',$first);
            return $next($request);
        }

        abort(response()->json(['error' => 'Unauthenticated because of keine ahnung.'], 401));
    }
}
