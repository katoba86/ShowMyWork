<?php


namespace App\Modules;


use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\Node\AbstractNode;
use PHPHtmlParser\Dom\Node\HtmlNode;
use PHPHtmlParser\Exceptions\ChildNotFoundException;

class ParagraphDetect
{

    const HEADINGS = ['h1','h2','h3','h4','h5','h6'];
    public  array $paragraphs = [];
    private bool $paragraphStarted = false;
    private int $curParagraph = 0;

    public function splitIntoParagraphs(string $content):void
    {

        $dom = new Dom();
        $dom->loadStr($content);
        /* @var Dom\Node\AbstractNode $link */
        $first = $dom->root;
        $this->loop($first);





        $this->paragraphs = array_filter($this->paragraphs,function($paragraph){
            return (strlen($paragraph["heading"]) < strlen($paragraph["paragraph"]));
        });

        $this->paragraphs = array_values($this->paragraphs);


    }

    /**
     * @return array
     */
    public function getParagraphs(): array
    {
        return $this->paragraphs;
    }




    public function loop(AbstractNode $node)
    {
        /* @var \PHPHtmlParser\Dom\Node\HtmlNode $node */
        if(!$node->hasChildren()){
            //End of document
            return;
        }

        $child = $node->firstChild();
        while($child !== null){
            if($child instanceof HtmlNode){

                if(in_array($child->getTag()->name(),self::HEADINGS)){
                    $this->curParagraph++;
                    $this->paragraphStarted = true;
                    $this->paragraphs[$this->curParagraph] = ["type"=>$child->getTag()->name(),"heading"=>$child->innerText(),"paragraph"=>"","lastChild"=>null];

                }
                $this->paragraphs[$this->curParagraph]["paragraph"] .= $child->innerText;
                $this->paragraphs[$this->curParagraph]["lastChild"] = $child->id();

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
