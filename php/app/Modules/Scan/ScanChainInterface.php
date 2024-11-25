<?php


namespace App\Modules\Scan;


interface ScanChainInterface
{
    public function check(string $domain):bool;
}
