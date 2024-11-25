<?php


namespace App\Modules\Transform\Chains;


use App\Modules\Transform\Models\TransformRequest;

interface ChainInterface
{
    public function handle(TransformRequest $transformRequest):TransformRequest;

    public function hasLeaves():bool;



}
