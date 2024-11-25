<?php


namespace App\Modules\Scan;

use App\Models\Live\PageSerp;
use App\Models\Live\PageKeywords;
use App\Modules\Scan\Chains\ScanDomain;
use App\Modules\Scan\Chains\ScanIsBigEnough;
use App\Modules\Scan\Chains\ScanIsAgency;
use App\Modules\Scan\Chains\ScanIsEcommerce;
use App\Modules\Scan\Chains\ScanIsWordpress;
use Cache;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use Http;
use Illuminate\Console\OutputStyle;
use Illuminate\Http\Client\ConnectionException;

class ScanService
{


    private bool $noSave = false;

    private  bool $isCli = false;


    private OutputStyle $cliOutput;



    const SEARCH_BY_GOOGLE = 0;

    const LOCATION_USA = 0;
    const LOCATION_GERMANY = 1;

    public function getLinksOfSerps(string $keyword,int $location = ScanService::LOCATION_USA,int $searchEngine = ScanService::SEARCH_BY_GOOGLE):array{

        switch($searchEngine){
            case ScanService::SEARCH_BY_GOOGLE:
                return $this->getLinksOfGoogleSerp($keyword,$location);
            default:
                throw new \RuntimeException('Not implemented...yet');
        }

    }

    /**
     * @return bool
     */
    public function isNoSave(): bool
    {
        return $this->noSave;
    }

    /**
     * @param bool $noSave
     */
    public function setNoSave(bool $noSave): void
    {
        $this->noSave = $noSave;
    }



    public function getLinksofGoogleSerp(PageKeywords $keyword,int $startIndex = 1,int $count = 10):array
    {


        if($count > 10){

            $numLoops = ceil($count/10);
            $ret = [];
            for($i=0;$i<$numLoops;$i++){
                $results = $this->getLinksofGoogleSerp($keyword,($i*10)+1,10);
                $ret = [...$ret,...$results];
            }
            $ret = array_unique($ret);
            return $ret;
        }



        $hash = implode("..",[
            'SERP',
            md5($keyword->keyword),
            $keyword->language,
            $count,
            $startIndex
        ]);

        $result = Cache::get($hash);




        if($result){
            $apiResult = $result;



        }else {

            $curl = curl_init();
            $url = 'https://www.googleapis.com/customsearch/v1?start='.($startIndex).'&count='.$count.'&cx=partner-pub-9140878519889734:3683835407&key=AIzaSyCdTch8NQhKXyud9NHXQnw4iY05hISO7fM&q='.urlencode($keyword->keyword).'&gl='.$keyword->language;
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 0,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            if($this->isCli()){
                $this->cliOutput->text("Query Google at position\t".($startIndex)."\t for Keyword \t".$keyword);
            }
            $apiResult = curl_exec($curl);
            curl_close($curl);

            Cache::set($hash,$apiResult,3600*24*365);


        }

        $data = json_decode($apiResult,true);
        if(!isset($data["items"])){
            $data["items"]=[];
        }
        if(!$this->isNoSave()) {
            $this->saveGoogleSerp($keyword, $data["items"]);
        }
        $records = collect($data["items"])->map(function($item){
            $parsed = parse_url($item["link"]);
           return $parsed["scheme"]."://".$parsed["host"];
        });

        return $records->unique()->toArray();
    }

    public function validate(string $page)
    {
        /* @var $chains ScanChainInterface[] */
        $chains = [
            ScanDomain::class,
            ScanIsWordpress::class,
            ScanIsBigEnough::class,
            ScanIsAgency::class,
          //  ScanIsEcommerce::class,
            //ScanIsNotImageBlog::class,
            //ScanIsNoAgency::class
        ];
        foreach ($chains as $chain) {
            $chain = new $chain();
            if(!$chain->check($page)){
                return false;
            }
        }
        return true;
    }



    public static function fetch(string $url,$returnHeaders = false):int|array
    {
        $hash = implode("__",['STATUS',md5($url),(int)$returnHeaders]);
        $cached = Cache::get($hash);
        if($cached){
            return $cached;
        }

        try{
            $result = Http::get($url);
        }catch(TooManyRedirectsException $e){
            if(!$returnHeaders) {
                Cache::set($hash, 5000, 3600 * 24 * 365); //my custom error code
                return 5000;
            }else{
                return [];
            }
        }catch (ConnectionException $e){
            if(!$returnHeaders) {
                Cache::set($hash, 5001, 3600 * 24 * 365); //my custom error code
                return 5001;
            }else{
                return [];
            }
        }catch (RequestException $e){
            if(!$returnHeaders) {
                Cache::set($hash, 5002, 3600 * 24 * 365); //my custom error code
                return 5002;
            }else{
                return [];
            }
        }

        if(!$returnHeaders) {
            $httpCode = $result->status();
            Cache::set($hash, (int)$httpCode, 3600 * 24 * 365);
            return (int)$httpCode;
        }else{


            $headers = $result->headers();
            Cache::set($hash, $headers, 3600 * 24 * 365);
            return $headers;
        }
    }

    /**
     * @return bool
     */
    public function isCli(): bool
    {
        return $this->isCli;
    }

    /**
     * @param bool $isCli
     */
    public function setIsCli(bool $isCli): void
    {
        $this->isCli = $isCli;
    }

    /**
     * @param OutputStyle $cliOutput
     */
    public function setCliOutput(OutputStyle $cliOutput): void
    {
        $this->cliOutput = $cliOutput;
    }

    private function saveGoogleSerp(PageKeywords $keyword, array $data)
    {
        foreach($data as $item){


            $description = "";
            $locale = null;

            if(isset($item["pagemap"]) && isset($item["pagemap"]["metatags"]) && is_array($item["pagemap"]["metatags"])){
                $metatags = $item["pagemap"]["metatags"][0];
                foreach ($metatags as $key => $metatag) {
                    if(preg_match("/description/",$key) && strlen($metatag)>strlen($description)){
                        $description = $metatag;
                    }
                    if(preg_match("/locale/",$key) && strlen($metatag)>strlen($description)){
                        $locale = $metatag;
                    }
                }
            }

            PageSerp::firstOrCreate(['keyword'=>$keyword->keyword,'link'=>$item["link"]],[
                'description'=>!empty($description)?$description:$item["snippet"],
                'title'=>$item["title"],
                'locale'=>(null !== $locale)?$locale:null,
                'snippet'=>$item['snippet']
            ]);


        }
    }


}
