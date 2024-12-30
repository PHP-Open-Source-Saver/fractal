<?php namespace PHPOpenSourceSaver\Fractal\Test\Stub\Transformer;

use PHPOpenSourceSaver\Fractal\TransformerAbstract;

class JsonApiAuthorTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'published',
    ];

    public function transform(array $author): array
    {
        unset($author['_published']);

        return $author;
    }

    public function includePublished(array $author)
    {
        if (! isset($author['_published'])) {
            return;
        }

        return $this->collection(
            $author['_published'],
            new JsonApiBookTransformer(),
            'books'
        );
    }
}
