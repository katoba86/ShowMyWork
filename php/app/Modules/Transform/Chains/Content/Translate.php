<?php


namespace App\Modules\Transform\Chains\Content;


use App\AppConfig;
use App\Models\Translation;
use App\Modules\Transform\Chains\AbstractChain;
use App\Modules\Transform\Chains\ChainInterface;
use App\Modules\Transform\Models\TransformRequest;
use Illuminate\Support\Facades\Cache;
use Scn\DeeplApiConnector\DeeplClient;
use Scn\DeeplApiConnector\Model\Translation as DeeplTranslation;

class Translate extends AbstractChain implements ChainInterface
{

    /**
     * @param TransformRequest $transformRequest
     * @return TransformRequest
     */
    public function handle(TransformRequest $transformRequest): TransformRequest
    {



        if(AppConfig::FAKE_TRANSLATION){
            return $transformRequest;
        }

        $client = DeeplClient::create(AppConfig::DEEPL_KEY);
        $language = $transformRequest->getOption('language');
        if($language===null){
            throw new \RuntimeException('Cant translate to null value');
        }




        $translationObject = Translation::where(
            [
                'sourceMd5'=>md5($transformRequest->getContent()),
                'targetLanguage'=>$language
            ])->firstOrNew();



        if(!is_numeric($translationObject->id) && (int)$translationObject->id===0) {

            $cacheKey = implode(".", [
                md5($transformRequest->getContent()),
                $language
            ]);

            $cached = Cache::get($cacheKey);
            if (null === $cached) {
                $translation = $client->translate($transformRequest->getContent(), $language);
                Cache::set($cacheKey, $translation, 3600 * 24 * 30);
            } else {
                $translation = $cached;
            }

            /* @var $translation DeeplTranslation */

            $translationObject->sourceMd5 = md5($transformRequest->getContent());
            $translationObject->targetLanguage = $language;
            $translationObject->sourceLanguage = $translation->getDetectedSourceLanguage();
            $translationObject->content = $transformRequest->getContent();
            $translationObject->translation = $translation->getText();
            $translationObject->saveQuietly();
        }
        $transformRequest->setContent($translationObject->translation);

        return $transformRequest;
    }
}
