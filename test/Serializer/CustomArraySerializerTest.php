<?php

namespace PHPOpenSourceSaver\Fractal\Test\Serializer;

use PHPOpenSourceSaver\Fractal\Manager;
use PHPOpenSourceSaver\Fractal\Resource\Item;
use PHPOpenSourceSaver\Fractal\Scope;
use PHPOpenSourceSaver\Fractal\Serializer\ArraySerializer;
use PHPOpenSourceSaver\Fractal\Test\Stub\Serializer\RootSerializer;
use PHPOpenSourceSaver\Fractal\Test\Stub\Transformer\GenericBookNoResourceKeyTransformer;
use PHPUnit\Framework\TestCase;

class CustomArraySerializerTest extends TestCase
{
    public function testAllowNullResourceKey()
    {
        $manager = new Manager();
        $manager->parseIncludes('author');
        $manager->setSerializer(new RootSerializer());

        $bookData = [
            'title' => 'Foo',
            'year' => '1991',
            '_author' => [
                'name' => 'Dave',
            ],
        ];

        $resource = new Item($bookData, new GenericBookNoResourceKeyTransformer(), 'data');
        $scope = new Scope($manager, $resource);

        $expected = [
            'data' => [
                'title' => 'Foo',
                'year' => 1991,
                'author' => [
                    'name' => 'Dave',
                ],
            ],
        ];

        $this->assertSame($expected, $scope->toArray());
    }

    public function testMismatchedResourceKey()
    {
        $manager = new Manager();
        $manager->parseIncludes('author');
        $manager->setSerializer(new RootSerializer());

        $bookData = [
            'title' => 'Foo',
            'year' => '1991',
            '_author' => [
                'name' => 'Dave',
            ],
        ];

        $resource = new Item($bookData, new GenericBookNoResourceKeyTransformer(), 'data');
        $scope = new Scope($manager, $resource);

        $expected = [
            'data' => [
                'title' => 'Foo',
                'year' => 1991,
                'author' => [
                    'data' => [
                        'name' => 'Dave',
                    ]
                ],
            ],
        ];

        $this->assertNotSame($expected, $scope->toArray());
    }
}
