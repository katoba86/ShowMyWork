<?php


namespace App\Modules;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class AzureService
{
    private string $language;


    private Client $client;
    private string $content;

    public function __construct()
    {
        $this->client = new Client([
            'headers'=>[
                'Ocp-Apim-Subscription-Key'=>'632897c1be914717aab1cbaf7d0292fe'
            ]
        ]);


    }

    /**
     * @param mixed $language
     */
    public function setLanguage($language): void
    {
        $this->language = $language;
    }

    public function getKeywords():?array
    {

        $endpoint = "https://multitranslate.cognitiveservices.azure.com";
        $path = $endpoint."/text/analytics/v3.1/keyPhrases";


        $input = [
            'query'=>[
                'showStats'=>true,
            ],
            'json'=>[
                'documents'=>[
                    [
                        'id'=>'testId',
                        'text'=>$this->content,
                        'language'=>$this->language
                    ]
                ]
            ]
        ];
        $key = md5(__METHOD__.json_encode($input));
        $cached = Cache::get($key);
        if(null === $cached) {
            $request = $this->client->post($path, $input);
            $data = json_decode($request->getBody()->getContents(),true);
            try{
                $data = $data["documents"][0]["keyPhrases"];
                Cache::set($key,$data,3600*24*31);
                return $data;
            }catch(\Exception $e){
                return null;
            }
        }
        return $cached;
    }

    public function setContent(string $content)
    {
        $this->content = strip_tags($content);
        if(strlen($this->content)>2500){
            $this->content =  substr($this->content, 0, strpos($this->content, ' ', 2500));
        }
    }


}
