<?php


namespace App\Modules\Scan\Chains;


use App\Modules\Scan\ScanChainInterface;

class ScanIsNotImageBlog implements ScanChainInterface
{

    /**
     * @param string $domain
     * @return bool
     */
    public function check(string $domain): bool
    {
        // TODO: Implement check() method.
    }
}
