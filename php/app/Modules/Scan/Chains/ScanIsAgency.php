<?php


namespace App\Modules\Scan\Chains;



use App\Modules\Scan\HtmlMetaParser;
use App\Modules\Scan\ScanChainInterface;

use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ScanIsAgency implements ScanChainInterface
{
    const AGENCY_WORDS = [
        'Agency',
        'therapist',
        'organization',
        'factory',
        'designer',
        'artist',
        'online shop',
        'doktor',
        'psychologe',
        'beratungsstelle',
        'author',
        'stiftung',
        'autor',
        'autorin',
        'Bestseller',
        'rezension',
        'Association',
        'national',
        'government',
        'department',
        'authority',
        'administration',
        'public authority',
        'sign up',
        'game',
        'download',
        'official site',
        'ebook',
        'dentist',
        'service',
        'funding',
        'shop',
        'medical',
        'commerce',
        'commerce',
        'college',
        'university'
    ];

    /**
     * @param string $domain
     * @return bool
     */
    public function check(string $domain): bool
    {

        $htmlParser = new HtmlMetaParser();
        $parser = $htmlParser->scan($domain);
        if(!is_array($parser)){
            return false;
        }
        foreach($parser as $key=>$item){

            if(preg_match('#description|title#i',$key)){
                if(preg_match('#'.implode("|",self::AGENCY_WORDS).'#i',$item)){
                    return false;
                }
            }
        }
        return true;
    }




}
