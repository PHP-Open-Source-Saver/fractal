<?php namespace PHPOpenSourceSaver\Fractal\Test\Stub\Transformer;

use PHPOpenSourceSaver\Fractal\TransformerAbstract;

class JsonApiEmptyTransformer extends TransformerAbstract
{
    public function transform(array $resource)
    {
        return $resource;
    }
}
