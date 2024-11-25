<?php


namespace App\Modules\Keyword;


use App\Models\Live\PageArticles;
use App\Modules\ParagraphDetect;
use DonatelloZa\RakePlus\RakePlus;
use http\Exception\RuntimeException;
use JetBrains\PhpStorm\Pure;

class ExtractKeywords
{
    #[Pure] public static function convertLanguage(string $language):string
    {
        return match ($language) {
            "de" => "de_DE",
            "en" => "en_US",
            default => throw new \RuntimeException("Cant detect stopwords"),
        };
    }

    public static function runForString(string $content, $minLength = 5,$language = 'de')
    {

        $language = self::convertLanguage($language);

        $keywords = RakePlus::create($content, $language)->keywords();
        $keywords  = collect($keywords);
        $keywords = $keywords->unique()->filter(function($str) use ($minLength){
            return strlen($str)>$minLength;
        });

        $keywords = $keywords->take(20)->flatten()->toArray();
        return $keywords;
    }

    /**
     * @param PageArticles $article
     * @param string $content
     * @param int $minLength
     * @param string $language
     * @return array
     */
    public static function runForArticle(PageArticles $article, string $content,$minLength = 8,string $language = "en"):array
    {
        $language = self::convertLanguage($language);

        $pa = new ParagraphDetect();
        $pa->splitIntoParagraphs($content);
        $keywords = [];

        foreach($pa->getParagraphs() as $paragraph){
            $title = $paragraph["heading"];
            $keywords = [...RakePlus::create($title, $language)->keywords(),...$keywords];
        }

        $keywords = collect($keywords);
        $keywords = $keywords->unique()->filter(function($str) use ($minLength){
            return strlen($str)>$minLength;
        });

        $keywords = $keywords->take(20)->flatten()->toArray();

        $article->options()->create(["key"=>"keywords",'value'=>json_encode($keywords)]);
        return $keywords;

    }

    public function workOnArticle(PageArticles $articles)
    {





    }


}
