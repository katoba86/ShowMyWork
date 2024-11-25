<?php


namespace App\Modules\Proxy;


use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class ProxyAuth implements UserProvider
{

    public function retrieveById($identifier)
    {
        // TODO: Implement retrieveById() method.
    }

    public function retrieveByToken($identifier, $token)
    {
        // TODO: Implement retrieveByToken() method.
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        // TODO: Implement updateRememberToken() method.
    }

    public function retrieveByCredentials(array $credentials)
    {
        echo "<pre>2";print_r($credentials);exit();
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        echo "<pre>2";print_r($user);exit();
    }
}
