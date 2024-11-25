<?php


namespace App\Modules\Transform\Chains\Internal;


use App\Modules\Transform\Chains\AbstractChain;
use App\Modules\Transform\Models\TransformRequest;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\Node\AbstractNode;
use PHPHtmlParser\Dom\Node\HtmlNode;
use PHPHtmlParser\Exceptions\ChildNotFoundException;

class InternalImage extends AbstractChain
{


    private $parsed = [];
    /**
     * @var array|HtmlNode[]
     */
    private $imageNodes = [];

    /**
     * @param TransformRequest $transformRequest
     * @return TransformRequest
     */
    public function handle(TransformRequest $transformRequest): TransformRequest
    {

        $newContent = $this->checkImages($transformRequest->getContent());
        $transformRequest->setContent($newContent);



        foreach($this->imageNodes as $imageNode){


            $target = $imageNode;

            $parent = $target->getParent();

            











        }
        exit;

        return $transformRequest;
    }


    public function checkImages(string $content){
        $dom = new Dom();
        $dom->loadStr($content);
        /* @var Dom\Node\AbstractNode $link */
        $first = $dom->root;
        $this->loop($first);
        return strval($dom);
    }


    public function loop(AbstractNode $node)
    {
        /* @var \PHPHtmlParser\Dom\Node\HtmlNode $node */
        if(!$node->hasChildren()){
            // End of document
            return;
        }

        $child = $node->firstChild();
        while($child !== null){
            if($child instanceof HtmlNode){
                $this->parsed[(int)$child->id()] = $child;
                if($child->getTag()->name() === 'img'){
                    $this->imageNodes[$child->id()] = $child;
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
}
