<?php


namespace App\Actions\Main;


use App\AppConfig;
use App\Models\Pool\PoolArticle;
use App\Modules\AzureService;

class ExtractKeywords
{
    public static function run(PoolArticle $article,string $field='title'):?array{


        $azure = new AzureService();
       // $article->switchVersion(AppConfig::STATUS_PROCESSING);
        $azure->setLanguage($article->language);
        $azure->setContent($article->$field);
        $keywords = $azure->getKeywords();
        return $keywords;

    }
}
