<?php


namespace App\Modules;


use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\Node\AbstractNode;
use PHPHtmlParser\Dom\Node\HtmlNode;
use PHPHtmlParser\Exceptions\ChildNotFoundException;

class ParagraphReplace
{


    public function replaceOnParagraph(string $content,int $targetId,$replaceFunction):string
    {
        $dom = new Dom();
        $dom->loadStr($content);
        /* @var Dom\Node\AbstractNode $link */
        $first = $dom->root;
        $this->loop($first,$targetId,$replaceFunction);
        return strval($dom);
    }






    public function loop(AbstractNode $node,int $targetId,mixed $replaceFunction)
    {
        /* @var \PHPHtmlParser\Dom\Node\HtmlNode $node */
        if(!$node->hasChildren()){
            //End of document
            return;
        }

        $child = $node->firstChild();
        while($child !== null){
            if($child instanceof HtmlNode){

                if($child->id() === $targetId){
                  $child =  $child->replaceChild($child->id(),$replaceFunction($child));
                }
                $this->loop($child,$targetId,$replaceFunction);
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
