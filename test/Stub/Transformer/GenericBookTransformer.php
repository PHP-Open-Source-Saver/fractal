<?php namespace PHPOpenSourceSaver\Fractal\Test\Stub\Transformer;

use PHPOpenSourceSaver\Fractal\TransformerAbstract;

class GenericBookTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'author',
    ];

    public function transform(array $book): array
    {
        $book['year'] = (int) $book['year'];
        unset($book['_author']);

        return $book;
    }

    public function includeAuthor(array $book)
    {
        if (! isset($book['_author'])) {
            return;
        }

        return $this->item($book['_author'], new GenericAuthorTransformer(), 'author');
    }
}
