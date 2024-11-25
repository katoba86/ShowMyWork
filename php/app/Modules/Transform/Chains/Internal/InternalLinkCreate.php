<?php


namespace App\Modules\Transform\Chains\Internal;


use App\Actions\Main\GetPossibleArticle;
use App\Models\Live\PageArticles;
use App\Models\Pool\PoolArticle;
use App\Modules\Keyword\ExtractKeywords;
use App\Modules\ParagraphDetect;
use App\Modules\ParagraphReplace;
use App\Modules\Transform\Chains\AbstractChain;
use App\Modules\Transform\Chains\ChainInterface;
use App\Modules\Transform\Models\TransformRequest;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\Node\HtmlNode;
use PHPHtmlParser\DTO\Tag\AttributeDTO;
use PHPHtmlParser\Exceptions\ChildNotFoundException;

class InternalLinkCreate extends AbstractChain implements ChainInterface
{


    const NOT_ALLOWED = ['www.flickr.com','amzn.com','google.com','google.de','awin.com','amazon.com','www.amazon.de','www.amazon.com'];

    private function workOnHref(AttributeDTO $href)
    {
        $target = $href->getValue();
        if(substr($target,-1)==="/"){
            $target = substr($target,0,strlen($target)-1);
        }
        $parsed = parse_url($target);

        if(isset($parsed["host"]) && in_array($parsed["host"],self::NOT_ALLOWED)){
            return false;
        }


        if(isset($parsed["path"])) {
            $parsed["path"] = pathinfo($parsed["path"]);

            if (isset($parsed["path"]["filename"])){


                $exists = PoolArticle::where(["slug"=>$parsed["path"]["filename"]])->first();

                if($exists ){
                    return "/".$exists->slug;
                }

            }
        }
        return '#';

        //return $href->getValue();
    }


    public function handle(TransformRequest $transformRequest): TransformRequest
    {
        $transformRequest = $this->checkLinks($transformRequest);
        $transformRequest = $this->getInternalLinks($transformRequest);
        return $transformRequest;
    }


    public function loop($node){
        /* @var \PHPHtmlParser\Dom\Node\HtmlNode $node */
        if(!$node->hasChildren()){
            return;
        }

        $child = $node->firstChild();
        while($child !== null){
           if($child instanceof HtmlNode){


               if($child->getTag()->name() === "a"){
                    $href = $child->getTag()->getAttribute('href');
                    $href = $this->workOnHref($href);
                    if(false !== $href) {
                        $child->getTag()->setAttribute('href', $href);
                    }else{
                        $newTag = new Dom\Tag("span");
                        $child->setTag($newTag);
                    }



               }

                    $this->loop($child);

            }
            try{
                $child = $node->nextChild($child->id());
            }catch (ChildNotFoundException $e){
                unset($e);
                $child = null;
            }
        }

    }


    public function checkLinks(TransformRequest $transformRequest)
    {

        $dom = new Dom;
        $dom->loadStr($transformRequest->getContent());
        /* @var Dom\Node\AbstractNode $link */
        $first = $dom->root;
        $this->loop($first);
        $transformRequest->setContent(strval($dom));
        return $transformRequest;

    }



    private function getInternalLinks(TransformRequest $transformRequest):TransformRequest
    {
        $numberOfLinks = ceil(sqrt(strlen(strip_tags($transformRequest->getContent()))/1500));

        $article = $transformRequest->getOption('article');

        $articleKeywords = $article->getOption('keywords');
        if(null === $articleKeywords){
            $articleKeywords = ExtractKeywords::runForArticle($article,$transformRequest->getContent());
        }else{
            $articleKeywords = json_decode($articleKeywords,true);
        }
        $n = 0;
        foreach($articleKeywords as $articleKeyword){
            $linkableArticle = $this->getPossibleArticleForKeyword($articleKeyword,$article);
            if($linkableArticle instanceof PageArticles) {
                $transformRequest = $this->linkArticleWithText($articleKeyword,$linkableArticle, $transformRequest);
                $n++;
                if($n>=$numberOfLinks){break;}
            }
        }

        return $transformRequest;
    }

    private function getPossibleArticleForKeyword(string $articleKeyword,PageArticles $pa)
    {
        /* @var $linkpa \Illuminate\Support\Collection */

        $linkpa = PageArticles::where('page','=',$pa->page)->where('content', 'LIKE', '%'.$articleKeyword.'%')->where('id','!=',$pa->getKey())->get();
        return $linkpa->shuffle($pa->getKey())->first();

    }

    private function linkArticleWithText(string $articleKeyword,PageArticles $linkableArticle, TransformRequest $transformRequest):TransformRequest
    {

        $paDetect = new ParagraphDetect();
        $paDetect->splitIntoParagraphs($transformRequest->getContent());

        $content = $transformRequest->getContent();

        foreach($paDetect->getParagraphs() as &$pa){
            $matches = null;
            $regex =   "#(?:\S+\s)?\S*".$articleKeyword."\S*(?:\s\S+)?#";
            preg_match($regex,$pa["paragraph"],$matches);
            if(is_array($matches) && count($matches)>0){
                $content = preg_replace("/".$matches[0]."/","<a href='/".$linkableArticle->slug."'>".$matches[0]."</a>",$content,1);
                break;
            }
        }
        $transformRequest->setContent($content);
        return $transformRequest;
    }


}
