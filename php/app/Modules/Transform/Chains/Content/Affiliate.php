<?php


namespace App\Modules\Transform\Chains\Content;


use App\Modules\Transform\Chains\AbstractChain;
use App\Modules\Transform\Chains\ChainInterface;
use App\Modules\Transform\Models\TransformRequest;

class Affiliate extends AbstractChain implements ChainInterface
{

    public function handle(TransformRequest $transformRequest): TransformRequest
    {
        return $transformRequest;
    }
}
