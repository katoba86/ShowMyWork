<?php


namespace App\Transformers;


use App\Models\Live\PageMedia;
use League\Fractal\TransformerAbstract;

class MediaTransformer extends TransformerAbstract
{
    public function transform(PageMedia $media)
    {
        return [
            'guid'=>["rendered"=>$media->createLink()],
            'id'=>$media->getKey(),
            'wid'=>$media->getKey(),
            'date'=>$media->getUpdated(),
            'date_gmt'=>$media->getUpdated(),
            'modified'=>$media->getUpdated(),
            'modified_gmt'=>$media->getUpdated(),
            'status'=>'inherit',
            'type'=>'attachment',
            'title'=>["rendered"=>$media->getKey()],
            "source_url"=>$media->createLink(),
            "media_details"=>[
                "file"=>$media->createLink(),
            ]
        ];
    }
}
