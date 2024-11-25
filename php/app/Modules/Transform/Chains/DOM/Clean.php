<?php


namespace App\Modules\Transform\Chains\DOM;


use App\Modules\Transform\Chains\AbstractChain;
use App\Modules\Transform\Chains\ChainInterface;
use App\Modules\Transform\Models\TransformRequest;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\Node\HtmlNode;
use PHPHtmlParser\Exceptions\ChildNotFoundException;

class Clean extends AbstractChain implements ChainInterface
{


    private array $removeAttributes = [
        'class',
        'id',
        'name',
        'data.*',
        'aria.*',
        'srcset',
        'alt',
        'style',
        'loading',
        'color',
        'width',
        'sizes',
        'height',
        'data-src',
        'data-sizes',
        'data-srcset',
    ];
    private array $removeTags = [

        'svg',
        'iframe',
        'style',
        'script',
        'noscript',
        'form',
        'input',
        'button',
        'object',
        'video',
        'embed',
        'frame',
        'link',
        'applet',
        'area',
        'meta',
        'code',
    ];

    /**
     * @var array|ChainInterface[]
     */
    protected array $leafs = [];

    public function handle(TransformRequest $transformRequest): TransformRequest
    {

        $this->addLeaf(new ShortCodes());
        $transformRequest = $this->runLeafs($transformRequest);
        $content = $transformRequest->getContent();
        $content = $this->removeTagsAndAttributes($content);
        $transformRequest->setContent($content);
        return $transformRequest;
    }


    public function removeTagsAndAttributes(string $str)
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
                if(in_array($child->getTag()->name(),$this->removeTags)){
                    $child->delete();
                }else {


                    $attributes = $child->getTag()->getAttributes();

                    if(count($attributes)>0) {

                        foreach ($this->removeAttributes as $toRemove) {
                            foreach ($attributes as $attributeKey=>$attributeValue) {

                                if (preg_match('#^' . $toRemove . '$#i', $attributeKey)) {

                                    $child->getTag()->removeAttribute($attributeKey);
                                }
                            }


                        }
                    }

                    $this->loop($child);
                }
            }
            try{
                $child = $node->nextChild($child->id());
            }catch (ChildNotFoundException $e){
                unset($e);
                $child = null;
            }
        }

    }

    /**
     * @param array|string[] $removeTags
     */
    public function setRemoveTags(array $removeTags): void
    {
        $this->removeTags = $removeTags;
    }




}
