<?php


namespace App\Transformers;


use League\Fractal\Pagination\CursorInterface;
use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Serializer\SerializerAbstract;

class WpSerializer extends ArraySerializer
{

    public function collection($resourceKey, array $data)
    {
        return $data;
    }
}
