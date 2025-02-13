<?php namespace PHPOpenSourceSaver\Fractal\Test\Resource;

use PHPOpenSourceSaver\Fractal\Manager;
use PHPOpenSourceSaver\Fractal\Resource\Collection;
use PHPOpenSourceSaver\Fractal\Resource\Item;
use PHPOpenSourceSaver\Fractal\Resource\NullResource;
use PHPOpenSourceSaver\Fractal\Scope;
use PHPOpenSourceSaver\Fractal\Serializer\DataArraySerializer;
use PHPOpenSourceSaver\Fractal\Test\Stub\Serializer\RootSerializer;
use PHPOpenSourceSaver\Fractal\Test\Stub\Transformer\GenericBookNoResourceKeyTransformer;
use PHPOpenSourceSaver\Fractal\Test\Stub\Transformer\GenericBookTransformer;
use Mockery;
use PHPUnit\Framework\TestCase;

class DataArraySerializerTest extends TestCase
{
    public function testSerializingItemResource()
    {
        $manager = new Manager();
        $manager->parseIncludes('author');
        $manager->setSerializer(new DataArraySerializer());

        $bookData = [
            'title' => 'Foo',
            'year' => '1991',
            '_author' => [
                'name' => 'Dave',
            ],
        ];

        // Try without metadata
        $resource = new Item($bookData, new GenericBookTransformer(), 'book');
        $scope = new Scope($manager, $resource);

        $expected = [
            'data' => [
                'title' => 'Foo',
                'year' => 1991,
                'author' => [
                    'data' => [
                        'name' => 'Dave',
                    ],
                ],
            ],
        ];

        $this->assertSame($expected, $scope->toArray());

        //Test single field
        $manager->parseFieldsets(['book' => 'title']);
        $expected = [
            'data' => [
                'title' => 'Foo',
            ]
        ];
        $this->assertSame($expected, $scope->toArray());

        //Test multiple field
        $manager->parseFieldsets(['book' => 'title,year']);
        $expected = [
            'data' => [
                'title' => 'Foo',
                'year' => 1991
            ]
        ];
        $this->assertSame($expected, $scope->toArray());

        //Test with relationship field
        $manager->parseFieldsets(['book' => 'title,author', 'author' => 'name']);
        $expected = [
            'data' => [
                'title' => 'Foo',
                'author' => [
                    'data' => [
                        'name' => 'Dave'
                    ]
                ]
            ]
        ];
        $this->assertSame($expected, $scope->toArray());

        //Clear all sparse fieldsets
        $manager->parseFieldsets([]);

        // Same again with metadata
        $resource = new Item($bookData, new GenericBookTransformer(), 'book');
        $resource->setMetaValue('foo', 'bar');

        $scope = new Scope($manager, $resource);

        $expected = [
            'data' => [
                'title' => 'Foo',
                'year' => 1991,
                'author' => [
                    'data' => [
                        'name' => 'Dave',

                    ],
                ],
            ],
            'meta' => [
                'foo' => 'bar',
            ],
        ];

        $this->assertSame($expected, $scope->toArray());

        //Test with relationship field
        $manager->parseFieldsets(['book' => 'title,author', 'author' => 'name']);
        $expected = [
            'data' => [
                'title' => 'Foo',
                'author' => [
                    'data' => [
                        'name' => 'Dave'

                    ]
                ]
            ],
            'meta' => [
                'foo' => 'bar'
            ],
        ];
        $this->assertSame($expected, $scope->toArray());
    }

    public function testSerializingCollectionResource()
    {
        $manager = new Manager();
        $manager->parseIncludes('author');
        $manager->setSerializer(new DataArraySerializer());

        $booksData = [
            [
                'title' => 'Foo',
                'year' => '1991',
                '_author' => [
                    'name' => 'Dave',
                ],
            ],
            [
                'title' => 'Bar',
                'year' => '1997',
                '_author' => [
                    'name' => 'Bob',
                ],
            ],
        ];

        // Try without metadata
        $resource = new Collection($booksData, new GenericBookTransformer(), 'books');

        $scope = new Scope($manager, $resource);

        $expected = [
            'data' => [
                [
                    'title' => 'Foo',
                    'year' => 1991,
                    'author' => [
                        'data' => [
                            'name' => 'Dave',
                        ],
                    ],
                ],
                [
                    'title' => 'Bar',
                    'year' => 1997,
                    'author' => [
                        'data' => [
                            'name' => 'Bob',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertSame($expected, $scope->toArray());

        $expectedJson = '{"data":[{"title":"Foo","year":1991,"author":{"data":{"name":"Dave"}}},{"title":"Bar","year":1997,"author":{"data":{"name":"Bob"}}}]}';
        $this->assertSame($expectedJson, $scope->toJson());

        //Test single field
        $manager->parseFieldsets(['books' => 'title']);
        $expected = [
            'data' => [
                ['title' => 'Foo'],
                ['title' => 'Bar']
            ],
        ];
        $this->assertSame($expected, $scope->toArray());

        //Test multiple field
        $manager->parseFieldsets(['books' => 'title,year']);
        $expected = [
            'data' => [
                [
                    'title' => 'Foo',
                    'year' => 1991
                ],
                [
                    'title' => 'Bar',
                    'year' => 1997
                ]
            ],
        ];
        $this->assertSame($expected, $scope->toArray());

        //Test with relationship field
        $manager->parseFieldsets(['books' => 'title,author', 'author' => 'name']);
        $expected = [
            'data' => [
                [
                    'title' => 'Foo',
                    'author' => [
                        'data' => [
                            'name' => 'Dave'
                        ]
                    ]
                ],
                [
                    'title' => 'Bar',
                    'author' => [
                        'data' => [
                            'name' => 'Bob'
                        ]
                    ]
                ]
            ]
        ];
        $this->assertSame($expected, $scope->toArray());

        //Clear all sparse fieldsets
        $manager->parseFieldsets([]);

        // Same again with meta
        $resource = new Collection($booksData, new GenericBookTransformer(), 'books');
        $resource->setMetaValue('foo', 'bar');

        $scope = new Scope($manager, $resource);


        $expected = [
            'data' => [
                [
                    'title' => 'Foo',
                    'year' => 1991,
                    'author' => [
                        'data' => [
                            'name' => 'Dave',
                        ],
                    ],
                ],
                [
                    'title' => 'Bar',
                    'year' => 1997,
                    'author' => [
                        'data' => [
                            'name' => 'Bob',
                        ],
                    ],
                ],
            ],
            'meta' => [
                'foo' => 'bar',
            ],
        ];

        $this->assertSame($expected, $scope->toArray());

        $expectedJson = '{"data":[{"title":"Foo","year":1991,"author":{"data":{"name":"Dave"}}},{"title":"Bar","year":1997,"author":{"data":{"name":"Bob"}}}],"meta":{"foo":"bar"}}';
        $this->assertSame($expectedJson, $scope->toJson());

        $manager->parseFieldsets(['books' => 'title,author', 'author' => 'name']);
        $expected = [
            'data' => [
                [
                    'title' => 'Foo',
                    'author' => [
                        'data' => [
                            'name' => 'Dave'
                        ]
                    ]
                ],
                [
                    'title' => 'Bar',
                    'author' => [
                        'data' => [
                            'name' => 'Bob'
                        ]
                    ]
                ]
            ],
            'meta' => [
                'foo' => 'bar'
            ]
        ];

        $this->assertSame($expected, $scope->toArray());
    }

    public function testSerializingNullResource()
    {
        $manager = new Manager();
        $manager->parseIncludes('author');
        $manager->setSerializer(new DataArraySerializer());

        $bookData = [
            'title' => 'Foo',
            'year' => '1991',
            '_author' => [
                'name' => 'Dave',
            ],
        ];

        // Try without metadata
        $resource = new NullResource($bookData, new GenericBookTransformer(), 'book');
        $scope = new Scope($manager, $resource);

        $expected = [
            'data' => [],
        ];
        $this->assertSame($expected, $scope->toArray());

        //Test single field
        $manager->parseFieldsets(['book' => 'title']);
        $this->assertSame($expected, $scope->toArray());

        //Test multiple fields
        $manager->parseFieldsets(['book' => 'title,year']);
        $this->assertSame($expected, $scope->toArray());

        //Test with relationship
        $manager->parseFieldsets(['book' => 'title,author', 'author' => 'name']);
        $this->assertSame($expected, $scope->toArray());

        //Clear all sparse fieldsets
        $manager->parseFieldsets([]);

        // Same again with metadata
        $resource = new NullResource($bookData, new GenericBookTransformer(), 'book');
        $resource->setMetaValue('foo', 'bar');

        $scope = new Scope($manager, $resource);

        $expected = [
            'data' => [],
            'meta' => [
                'foo' => 'bar',
            ],
        ];
        $this->assertSame($expected, $scope->toArray());

        //Test with relationship
        $manager->parseFieldsets(['book' => 'title,author', 'author' => 'name']);
        $this->assertSame($expected, $scope->toArray());
    }

    public function testCanPassNullValueToSerializer()
    {
        $testClass = new \stdClass();
        $testClass->name = 'test';
        $testClass->email = 'test@test.com';


    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
