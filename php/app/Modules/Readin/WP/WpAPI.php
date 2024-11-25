<?php


namespace App\Modules\Readin\WP;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class WpAPI
{

    const WP_BASE = '/wp-json/wp/v2/';

    protected $client;
    private $endpoint;

    public function __construct($endpoint, Client $client )
    {
        $this->endpoint = $endpoint;
        $this->client   = $client;

    }


    public function infos()
    {
        return $this->_getData('/wp-json',[],true);
    }

    public function posts()
    {
        return $this->_get('posts');
    }


    public function categories()
    {
        return $this->_get('categories');
    }

    public function tags()
    {
        return $this->_get('tags');
    }

    public function buildHash($type)
    {
        return implode(".",[
           $type,
           $this->endpoint
        ]);
    }

    public function _get($type)
    {

        $cached = Cache::get($this->buildHash($type));
        if($cached){
            return $cached;
        }

        $result = $this->_getData($type);

        if((int)$result["pages"]>1){


            for($x=2;$x<=$result["pages"];$x++){
                $newData = $this->_getData($type,["page"=>$x]);
                $result["results"] = array_merge($result["results"],$newData["results"]);
            }

        }
        Cache::set($this->buildHash($type),$result,3600*24);
        return $result;
    }


    public function _getData($method, array $query = array(),$withoutBase = false)
    {
        echo ".";
        try {
            if(!array_key_exists('per_page',$query)){
                $query['per_page']=100;
            }
            $query = ['query' => $query];
            $response = $this->client->get($this->endpoint .(($withoutBase)?'':self::WP_BASE). $method, $query);



            $total = $response->getHeader('X-WP-Total');
            $pages = $response->getHeader('X-WP-TotalPages');
            $total = (is_array($total) && count($total)>0)?$total[0]:0;



            $pages = (is_array($pages) && count($pages)>0)?$pages[0]:0;


            $return = [
                'results' => json_decode($response->getBody()->getContents(),true),
                'total'   => $total,
                'pages'   => $pages
            ];

        } catch (\GuzzleHttp\Exception\TransferException $e) {

            $error['message'] = $e->getMessage();

            if ($e->getResponse()) {
                $error['code'] = $e->getResponse()->getStatusCode();
            }

            $return = [
                'error'   => $error,
                'results' => [],
                'total'   => 0,
                'pages'   => 0
            ];

        }

        return $return;

    }
    public function post(int $iid)
    {
        return $this->_getData('posts/'.$iid);
    }


    public function relatedMedia(int $iid)
    {
        return $this->_getData('media',['parent'=>$iid]);
    }

    public function getMedia(int $featured_mediaId)
    {
        return $this->_getData('media/'.$featured_mediaId);
    }

}
