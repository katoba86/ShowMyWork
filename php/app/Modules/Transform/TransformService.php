<?php

namespace App\Modules\Transform;

use App\Models\Pool\PoolModel;
use App\Modules\Transform\Chains\ChainInterface;
use App\Modules\Transform\Models\TransformRequest;
use Illuminate\Support\Str;

class TransformService{


    /**
     * @var array|ChainInterface[]
     */
    private $runners = [];


    private mixed $runArtifacts;


    protected array $options = [];

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return mixed
     */
    public function getRunArtifacts(): mixed
    {
        return $this->runArtifacts;
    }




    public function transform(PoolModel $model,string $field):PoolModel{


        $request = new TransformRequest();
        $request->setContent($model->$field);
        $request->setOriginal($request->getContent());
        if(count($this->options)>0){
            $request->setOptions($this->options);
        }


        foreach ($this->runners as $runner){
            $request = $runner->handle($request);
        }

        $model->$field = $request->getContent();
        $this->runArtifacts = $request->getAllData();
        return $model;
    }

    /**
     * @return ChainInterface[]|array
     */
    public function getRunners(): array
    {
        return $this->runners;
    }




    public function addRunner(ChainInterface $runner):TransformService
    {
        $this->runners[] = $runner;
        return $this;
    }

    public function buildSlug(string $content,string $targetLanguage):string
    {
        $content = html_entity_decode($content);
        $file = storage_path('stop_words_'.strtolower($targetLanguage).".txt");
        $stop_words = file($file);
        foreach ($stop_words as &$word) {
            $word = '/\b' . preg_quote(trim($word), '/') . '\b/iu';
        }
        $content =  preg_replace($stop_words, '', strtolower($content));
        return Str::slug($content,"-",$targetLanguage);
    }


}
