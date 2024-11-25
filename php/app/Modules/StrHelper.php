<?php


namespace App\Modules;


class StrHelper
{
    public static function contains(array $needles,string $haystack,bool $ignoreCase = false):bool
    {
        if($ignoreCase){
            $haystack = mb_strtolower($haystack);
            $needles = array_map('mb_strtolower',$needles);
        }
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
}
