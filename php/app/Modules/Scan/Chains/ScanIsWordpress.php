<?php


namespace App\Modules\Scan\Chains;


use App\Modules\Scan\ScanChainInterface;
use App\Modules\Scan\ScanService;

class ScanIsWordpress implements ScanChainInterface
{

    const TEST_PATH = 'wp-json/wp/v2/posts';

    public function check(string $domain): bool
    {
        $result = ScanService::fetch($domain.DIRECTORY_SEPARATOR.self::TEST_PATH);
        if($result !== 200){
            return false;
        }
        return true;
    }
}
