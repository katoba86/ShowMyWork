<?php


namespace App\Modules\Scan\Chains;


use App\Modules\Scan\ScanChainInterface;
use App\Modules\Scan\ScanService;

class ScanIsBigEnough implements ScanChainInterface
{
    const TEST_PATH = 'wp-json/wp/v2/posts';
    const MIN_ITEMS = 20;
    const WP_TOTAL_HEADERS = 'X-WP-Total';

    /**
     * @param string $domain
     * @return bool
     */
    public function check(string $domain): bool
    {
        $headers = ScanService::fetch($domain.DIRECTORY_SEPARATOR.self::TEST_PATH,true);

        if(!isset($headers[self::WP_TOTAL_HEADERS]) || !isset($headers[self::WP_TOTAL_HEADERS][0])){
            return false;
        }
        if($headers[self::WP_TOTAL_HEADERS][0] < self::MIN_ITEMS){
            return false;
        }
        return true;
    }
}
