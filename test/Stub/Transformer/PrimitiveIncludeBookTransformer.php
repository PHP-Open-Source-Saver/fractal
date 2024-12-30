<?php namespace PHPOpenSourceSaver\Fractal\Test\Stub\Transformer;

use PHPOpenSourceSaver\Fractal\Resource\Primitive;
use PHPOpenSourceSaver\Fractal\TransformerAbstract;

class PrimitiveIncludeBookTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'price'
    ];

    public function transform(): array
    {
        return ['a' => 'b'];
    }

    public function includePrice(array $book): Primitive
    {
        return $this->primitive($book['price'], function ($price) {return (int) $price;});
    }
}
