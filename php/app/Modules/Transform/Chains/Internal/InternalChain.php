<?php


namespace App\Modules\Transform\Chains\Internal;


use App\Modules\Transform\Chains\AbstractChain;
use App\Modules\Transform\Models\TransformRequest;

class InternalChain extends AbstractChain
{

    /**
     * @param TransformRequest $transformRequest
     * @return TransformRequest
     */
    public function handle(TransformRequest $transformRequest): TransformRequest
    {

        $links = new InternalImage();
        $transformRequest = $links->handle($transformRequest);


        return $transformRequest;
    }
}
