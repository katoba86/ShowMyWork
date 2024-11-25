<?php


namespace App\Modules\Image;


use GuzzleHttp\Client;

class Pexel
{


    private string $API_KEY;
    /**
     * @var Client
     */
    private Client $client;

    public function __construct(string $API_KEY)
    {
        $this->API_KEY = $API_KEY;
        $this->client = new Client([
            'headers'=>[
                'Authorization'=>$API_KEY
            ]
        ]);
    }


    public function direct($id)
    {
        $ret = $this->client->get("https://api.pexels.com/v1/photos/".$id);
        $response = $ret->getBody()->getContents();
        $response = json_decode($response,true);
        if(isset($response["id"])) {
            return $this->convert($response);
        }else{
            throw new \RuntimeException('Cant retrive photo in conversion');
        }

    }

    public function search(string $keyword)
    {
        $ret = $this->client->get("https://api.pexels.com/v1/search",[
            "query"=>[
                "query"=>$keyword,
                "orientation"=>'landscape'
            ]
        ]);

        $response = $ret->getBody()->getContents();
        $response = json_decode($response,true);


        $photos = collect($response["photos"]);
        return $photos->map(function($photoObject){
            return $this->convert($photoObject);
        });

    }

    private function convert($photoObject)
    {


            $r = new ImageResponse();
            $r->provider = "Pexel";
            $r->copyrightTitle = $photoObject["photographer"];
            $r->copyrightUrl = $photoObject["photographer_url"];
            $r->full = $photoObject["src"]["large"];
            $r->preview = $photoObject["src"]["small"];
            return $r;


    }

}
