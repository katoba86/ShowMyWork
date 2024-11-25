<?php


namespace App\Modules\Transform\Chains\Content;


use App\Modules\StrHelper;
use App\Modules\Transform\Chains\AbstractChain;
use App\Modules\Transform\Models\TransformRequest;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\Node\AbstractNode;
use PHPHtmlParser\Dom\Node\HtmlNode;
use PHPHtmlParser\Dom\Node\TextNode;
use PHPHtmlParser\Exceptions\ChildNotFoundException;

class RemoveImageCopyright extends AbstractChain
{
    const PhotoTrigger = [
        'Photo','Foto','Image','Copyright'
    ];

    const PhotoHosts = [
      'flickr.com',
      'unsplash.com',
        'pixabay.com',
        'pexels.com',
        'placehold.it',
        'stocksnap.com',
        'istockphoto',
        'depositphotos',
        'shutterstock',
        'stockvault',
    ];

    private string $content;


    public function handle(TransformRequest $request):TransformRequest
    {
        $this->content = $request->getContent();
        $dom = new Dom();
        $dom->loadStr($this->content);
        $this->removeStandaloneReference($dom);
        $this->removeByFollowingPTag($dom);
        $request->setContent($this->content);
        return $request;
    }

    private function removeStandaloneReference(Dom $dom)
    {
        //First check for em tags

        $emTags = $dom->find("em a");
        //Second check for small tags
        $smallTags = $dom->find("small a");

        //$pTagsWithSmallContent = $dom->find("p a"); //@todo no example found

        /* @var $tags HtmlNode */
        foreach([...$emTags,...$smallTags] as &$tags){

            $href = $tags->getAttribute('href');
            if(StrHelper::contains(self::PhotoHosts,$href)){

                $count = 0;

                if($tags->getParent()->countChildren()<=3) {

                   /* @var $children HtmlNode */
                    foreach($tags->getParent()->getChildren() as $children){
                        $count+=strlen($children->text);
                    }
                    if($count < 150 && StrHelper::contains(self::PhotoTrigger,$tags->getParent()->text,true)){
                        $tags->getParent()->delete();
                    }
                }
            }
        }
        $this->content = strval($dom);
    }


    public function removeByFollowingPTag(Dom $dom)
    {
        /* @var Dom\Node\HtmlNode $domNode */
        /* @var Dom\Node\HtmlNode $domNode */
        /* @var Dom\Node\HtmlNode $child */
        $allImages = $dom->find('img');
        foreach($allImages as $domNode) {
            $parent = $domNode->getParent();
            if($parent->countChildren() <=3) {
                foreach ($parent->getChildren() as $child) {
                    if ($child->getTag()->name() === "p") {

                        if( StrHelper::contains(self::PhotoTrigger,$child->text)
                        ){
                            $child->delete();
                            $domNode->delete();
                            break;
                        }
                    }
                }
            }
        }
        $this->setContent(strval($dom));
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): RemoveImageCopyright
    {
        $this->content = $content;
        return $this;
    }




}
