<?php namespace PHPOpenSourceSaver\Fractal\Test\Stub\Transformer;

use PHPOpenSourceSaver\Fractal\TransformerAbstract;

class NullIncludeBookTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'author',
    ];

    public function transform(): array
    {
        return ['a' => 'b'];
    }

    public function includeAuthor()
    {
        return $this->null();
    }
}
