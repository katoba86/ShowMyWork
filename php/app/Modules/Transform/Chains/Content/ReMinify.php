<?php


namespace App\Modules\Transform\Chains\Content;


use App\Modules\Transform\Chains\AbstractChain;
use App\Modules\Transform\Chains\ChainInterface;
use App\Modules\Transform\Models\TransformRequest;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\Node\HtmlNode;
use PHPHtmlParser\Exceptions\ChildNotFoundException;

class ReMinify extends AbstractChain implements ChainInterface
{


    protected $tagReplacement = [];

    /**
     * @param TransformRequest $transformRequest
     * @return TransformRequest
     */
    public function handle(TransformRequest $transformRequest): TransformRequest
    {

        $data = $transformRequest->getData(Minify::class);
        if(null === $data){
            return $transformRequest;
        }

        $this->tagReplacement = $data;

        $transformRequest->setContent(
            $this->doReMinify($transformRequest->getContent())
        );

        return $transformRequest;
    }





    public function doReMinify(string $str)
    {
        $dom = new Dom;
        $dom->loadStr($str);
        /* @var Dom\Node\AbstractNode $link */
        $first = $dom->root;
        $this->loop($first);
        return strval($dom);
    }

    public function getReplacement(string $tag)
    {
        if(!isset($this->tagReplacement[$tag])){
            throw new \RuntimeException('Cant detet replacement key');
        }
        return $this->tagReplacement[$tag];
    }

    public function loop($node){
        /* @var \PHPHtmlParser\Dom\Node\HtmlNode $node */
        if(!$node->hasChildren()){
            return;
        }

        $child = $node->firstChild();
        while($child !== null){
            if($child instanceof HtmlNode){

                $replacement = $this->getReplacement($child->getTag()->name());
                $newTag = new Dom\Tag($replacement["tag"]);
                if($child->getTag()->isSelfClosing()){
                    $newTag->selfClosing();
                }

                if(isset($replacement["attributes"]) && is_array($replacement["attributes"])){
                    $newTag->setAttributes($replacement["attributes"]);
                }
                $child->setTag($newTag);
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
