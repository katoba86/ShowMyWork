<?php


namespace App\Actions\Main;


use App\Models\Live\Page;
use App\Models\Live\PageArticles;
use App\Models\Pool\PoolArticle;
use App\Modules\Image\ImageResponse;
use App\Modules\Image\ImageService;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class SetImage
{


    use AsAction;

    public string $commandSignature = 'app:image {article} {provider} {id}';
    public string $commandDescription = 'Set image for article';



    public static function handle(int $article,string $provider,string $id){

        $article = PageArticles::where('id',$article)->firstOrFail();

        $provider = match ($provider) {
            'p' => ImageService::PIXABAY,
            'x' => ImageService::PEXEL,
        };


        $image = new ImageService();
        $image->setFixedProvider($provider);
        $targetImage = $image->getDirectImage($id);


        if($targetImage instanceof ImageResponse){
            $media = $article->assignImage($targetImage);
            DownloadImage::dispatch($media);
        }else{
          return null;
        }
    }

    public function asCommand(Command $command)
    {

        $article =  (int)$command->argument('article');
        $provider = $command->argument('provider');
        $id = $command->argument('id');


        $this->handle($article,$provider,$id);
    }

}
