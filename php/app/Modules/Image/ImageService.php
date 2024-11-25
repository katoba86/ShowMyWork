<?php


namespace App\Modules\Image;


use Averta\Pixabay\Pixabay;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;


class ImageService
{
    /**
     * @var Collection
     */
    private $keywords;
    private int $loops = 5;


    const PIXABAY = 'pixabay';
    const PEXEL = 'pexel';

    const PIXA_API = "21957527-37c31139f77d9dac04c7f46ca";
    const PEXEL_KEY = "563492ad6f9170000100000146a78ea2b1ff442cb35e169773a9434c";

    private $fixedProvider = null;

    /**
     * @param null $fixedProvider
     * @return ImageService
     */
    public function setFixedProvider($fixedProvider)
    {
        $this->fixedProvider = $fixedProvider;
        return $this;
    }

    public function getDirectImage($id)
    {

        if(null === $this->fixedProvider){
            throw new \RuntimeException('Cant run without provider');
        }

        if($this->fixedProvider === self::PEXEL){
            $pexel = new Pexel(self::PEXEL_KEY);
            return $pexel->direct($id);
        }elseif ($this->fixedProvider === self::PIXABAY){
            $pixa = new Pixabay(self::PIXA_API);
            $test = $pixa->image([
                'id' => $id,
                'image_type' => 'photo',
                'per_page' => 20
            ]);
            if (isset($test["hits"]) && is_array($test["hits"])) {
                $hits = collect($test["hits"]);
                return $hits->map(function ($hit) {
                    $r = new ImageResponse();
                    $r->provider = "pixabay";
                    $r->preview = $hit["previewURL"];
                    $r->full = $hit["largeImageURL"];
                    $r->copyrightUrl = $hit["userImageURL"];
                    $r->copyrightTitle = $hit["user"];
                    return $r;
                });
            }
            return collect([]);
        }else{
            throw new \RuntimeException('Cant retrive photo');
        }

    }


    public function run(): Collection
    {
        $ret = collect([]);


        $attempts = 0;

        $key = md5($this->keywords->implode('_') . __METHOD__);
        $cached = Cache::get($key);
        if (null !== $cached) {
            return $cached;
        } else {
            $this->keywords->shuffle();
            do {
                $keyword = $this->keywords->skip($attempts)->first();
                if(!is_string($keyword) || trim($keyword)===""){
                    break;
                }
                $pexel = $this->getFromPexels($keyword);
                $pixa = $this->getFromPixabay($keyword);

                if ($pexel->count() + $pixa->count() >= 2) {
                    break;
                }
                $attempts++;
            } while ($attempts < $this->loops);

            $ret = $ret->merge($pixa);
            $ret = $ret->merge($pexel);
        }
        if ($ret->count() > 2) {
            Cache::set($key, $ret, 3600 * 24 * 365);
        }
        return $ret;
    }


    private function getFromPexels(string $search): Collection
    {
        $pexel = new Pexel(self::PEXEL_KEY);
        return $pexel->search($search);
    }

    private function getFromPixabay(string $search): Collection
    {
        $pixa = new Pixabay(self::PIXA_API);
        $test = $pixa->image([
            'q' => $this->keywords[0],
            'image_type' => 'photo',
            'orientation' => 'horizontal',
            'per_page' => 20
        ]);
        if (isset($test["hits"]) && is_array($test["hits"])) {
            $hits = collect($test["hits"]);
            return $hits->map(function ($hit) {
                $r = new ImageResponse();
                $r->provider = "pixabay";
                $r->preview = $hit["previewURL"];
                $r->full = $hit["largeImageURL"];
                $r->copyrightUrl = $hit["userImageURL"];
                $r->copyrightTitle = $hit["user"];
                return $r;
            });
        }
        return collect([]);
    }


    /**
     * @param array $keywords
     */
    public function setKeywords(mixed $keywords): void
    {
        if (is_array($keywords)) {
            $this->keywords = collect($keywords);
        } elseif ($keywords instanceof Collection) {
            $this->keywords = $keywords;
        } else {
            throw new \RuntimeException('Cant add keywords of unknown type');

        }
    }


}
