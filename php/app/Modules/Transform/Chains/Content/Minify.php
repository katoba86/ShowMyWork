<?php


namespace App\Modules\Transform\Chains\Content;


use App\Modules\Transform\Chains\AbstractChain;
use App\Modules\Transform\Chains\ChainInterface;
use App\Modules\Transform\Models\TransformRequest;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\Node\HtmlNode;
use PHPHtmlParser\Exceptions\ChildNotFoundException;

class Minify extends AbstractChain implements ChainInterface
{


    private $tagReplacement = [];
    private $counter1 = 0;

    public static function getNextShortTag($counter):string
    {
        $alphabet = range('a','z');
        $num = count($alphabet);
        if($counter < $num){
            return $alphabet[$counter];
        }

        $end = ($counter-$num)%10;
        $number = floor(($counter-$num)/10);
        return "".$alphabet[$number].$end;
    }

    public function handle(TransformRequest $transformRequest): TransformRequest
    {
        $this->tagReplacement = [];
        $content = $transformRequest->getContent();

        $content = $this->doMinify($content);
        $transformRequest->setData(__CLASS__,$this->tagReplacement);
        $transformRequest->setContent($content);






        return $transformRequest;

    }


    public function doMinify(string $str)
    {
        $dom = new Dom;
        $dom->loadStr($str);
        /* @var Dom\Node\AbstractNode $link */
        $first = $dom->root;
        $this->loop($first);
        return strval($dom);
    }

    public function loop($node){
        /* @var \PHPHtmlParser\Dom\Node\HtmlNode $node */
        if(!$node->hasChildren()){
            return;
        }

        $child = $node->firstChild();
        while($child !== null){
            if($child instanceof HtmlNode){

                    $newKey = self::getNextShortTag($this->counter1);
                    $newTag = new Dom\Tag($newKey);
                    if($child->getTag()->isSelfClosing()){
                        $newTag->selfClosing();
                    }

                    $attributes = $child->getAttributes();
                    $this->tagReplacement[$newKey] = [
                        'attributes'=>$attributes,
                        'tag'=>$child->getTag()->name()
                    ];
                    $child->setTag($newTag);
                    $this->counter1++;
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

}
