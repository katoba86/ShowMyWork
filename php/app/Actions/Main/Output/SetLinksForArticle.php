<?php


namespace App\Actions\Main\Output;


use App\Models\Live\PageArticles;
use Lorisleiva\Actions\Concerns\AsAction;

class SetLinksForArticle
{
    use AsAction;

    private $domain = null;


    private $replaceLinks = [];

    public function handle(PageArticles $article)
    {


        $articleObject = $article->articleObject;
        $site = $articleObject->site;
        $this->domain = $site->getDomain();



        $regexp = "/<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>/isU";
        $content = preg_replace_callback($regexp,[$this,'replaceLink'],$article->content);





        return [
            'num'=>1,
            'link'=>$this->replaceLinks,
            'domain'=>$this->domain
        ];
    }


    private function replaceLink($match)
    {
        if(count($match)>=4) {

            $parsed = parse_url($match[2]);

            if (isset($parsed["host"]) && $parsed["host"] === $this->domain) {
                $parsed["path"] = pathinfo($parsed["path"]);
                $this->replaceLinks[] = $parsed["path"]["filename"];
                $link = '#link|' . $parsed["path"]["filename"];
                return "<a href='".$link."'>".$match[3]."</a>";
            }
        }

        return $match[0];
    }

}
