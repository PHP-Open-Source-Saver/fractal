<?php namespace PHPOpenSourceSaver\Fractal\Test\Stub\Transformer;

use PHPOpenSourceSaver\Fractal\TransformerAbstract;

class GenericAuthorTransformer extends TransformerAbstract
{
    public function transform(array $author)
    {
        return $author;
    }
}
