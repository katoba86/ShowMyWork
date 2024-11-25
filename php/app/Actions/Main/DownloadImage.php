<?php


namespace App\Actions\Main;


use App\Models\Live\PageMedia;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

class DownloadImage
{
 use AsAction;

    public function handle(PageMedia $media):void
    {

        //$dest = Storage::path($media->getKey().".jpg");
        $data = file_get_contents($media->link);
        Storage::put($media->getKey().".jpg",$data);
    }


    public function asJob(PageMedia $media):void
    {
        $this->handle($media);
    }

}
