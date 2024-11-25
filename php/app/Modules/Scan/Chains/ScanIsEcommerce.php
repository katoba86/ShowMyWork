<?php


namespace App\Modules\Scan\Chains;


use App\Modules\Scan\HtmlMetaParser;
use App\Modules\Scan\ScanChainInterface;

class ScanIsEcommerce  implements ScanChainInterface
{

    /**
     * @param string $domain
     * @return bool
     */
    public function check(string $domain): bool
    {

        $htmlParser = new HtmlMetaParser();
        $parser = $htmlParser->scan($domain);



        return true;
    }
}
