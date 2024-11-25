<?php


namespace App\Modules\Transform\Chains;


use App\Modules\Transform\Models\TransformRequest;

abstract class AbstractChain implements ChainInterface
{
    /**
     * @var array|ChainInterface[]
     */
    protected array $leafs = [];


    public function hasLeaves(): bool
    {
        return (count($this->leafs)>0);
    }

    protected function addLeaf(ChainInterface $leaf){
        $this->leafs[] = $leaf;
    }

    protected function runLeafs(TransformRequest $request){
        foreach($this->leafs as $leaf){
            $request = $leaf->handle($request);
        }
        return $request;
    }


}
