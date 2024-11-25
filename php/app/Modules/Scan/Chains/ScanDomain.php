<?php


namespace App\Modules\Scan\Chains;


use App\Modules\Scan\ScanChainInterface;
use App\Modules\Scan\ScanService;

class ScanDomain implements ScanChainInterface
{

    const NOW_ALLOWED = [
      'gov',
      'edu',
      'academy',
      'adult',
      'amazon',
      'app',
      'art',
      'mil',
      'bible',
      'biz',
      'capital',
        'cars',
        'cfd',
        'coop',
        'duck',
        'eco',
        'esq',
        'fiat',
        'fire',
        'fly',
        'gle',
        'google',
        'fb',
        'facebook',
        'green',
        'ripe',
        'hangout',
        'homesense',
        'hosting',
        'inc',
        'hotel',
        'hosting',
        'institute',
        'ice',
        'jobs',
        'law',
        'leclerc',
        'lgbt',
        'mobi',
        'mobily',
        'museum',
        'news',
        'ntt',
        'pharmacy',
        'med',
        'physio',
        'porn',
        'post',
        'pub',
        'review',
        'shop',
        'sex',
        'tel',
        'university',
        'vacations',
        'voyage',
        'travel',

        //Geo
        'asia',
        'krd',
        'tatar',

        'london',
        'bzh',

        'eus',
        'paris',

        'cat',
        'gal',
        'madrid',

        'ac',
        'ad',
        'ag',
        'ai',
        'aq',

        'as',
        'fm',

        'gg',
        'hu',
        'kp',
        'biz',
        'pro',
        'edu',
        'int',
        'aero',
        'cn',






    ];

    public function check(string $domain): bool
    {
        $split = explode(".", parse_url($domain, PHP_URL_HOST));
       $domain =  end($split);
       return !in_array($domain,self::NOW_ALLOWED);
    }
}
