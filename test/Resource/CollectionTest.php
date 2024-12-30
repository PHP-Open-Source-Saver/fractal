<?php namespace PHPOpenSourceSaver\Fractal\Test\Resource;

use PHPOpenSourceSaver\Fractal\Pagination\Cursor;
use PHPOpenSourceSaver\Fractal\Resource\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    protected $simpleCollection = [
        ['foo' => 'bar'],
        ['baz' => 'ban'],
    ];

    public function testGetData()
    {
        $resource = new Collection($this->simpleCollection, function (array $data) {
            return $data;
        });

        $this->assertSame($resource->getData(), $this->simpleCollection);
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\Resource\Collection::setData
     */
    public function testSetData()
    {
        $collection = Mockery::mock('PHPOpenSourceSaver\Fractal\Resource\Collection')->makePartial();
        $collection->setData('foo');
        $this->assertSame('foo', $collection->getData());
    }

    public function testGetTransformer()
    {
        $resource = new Collection($this->simpleCollection, function () {
        });
        $this->assertTrue(is_callable($resource->getTransformer()));

        $resource = new Collection($this->simpleCollection, 'SomeClass');
        $this->assertSame($resource->getTransformer(), 'SomeClass');
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\Resource\Collection::setTransformer
     */
    public function testSetTransformer()
    {
        $collection = Mockery::mock('PHPOpenSourceSaver\Fractal\Resource\Collection')->makePartial();
        $collection->setTransformer('foo');
        $this->assertSame('foo', $collection->getTransformer());
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\Resource\Collection::setCursor
     */
    public function testSetCursor()
    {
        $cursor = Mockery::mock('PHPOpenSourceSaver\Fractal\Pagination\Cursor');
        $collection = Mockery::mock('PHPOpenSourceSaver\Fractal\Resource\Collection')->makePartial();
        $this->assertInstanceOf('PHPOpenSourceSaver\Fractal\Resource\Collection', $collection->setCursor($cursor));
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\Resource\Collection::getCursor
     */
    public function testGetCursor()
    {
        $cursor = new Cursor();
        $collection = Mockery::mock('PHPOpenSourceSaver\Fractal\Resource\Collection')->makePartial();
        $collection->setCursor($cursor);
        $this->assertInstanceOf('PHPOpenSourceSaver\Fractal\Pagination\Cursor', $collection->getCursor());
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\Resource\Collection::setPaginator
     * @covers \PHPOpenSourceSaver\Fractal\Resource\Collection::getPaginator
     */
    public function testGetSetPaginator()
    {
        $paginator = Mockery::mock('PHPOpenSourceSaver\Fractal\Pagination\IlluminatePaginatorAdapter');
        $collection = Mockery::mock('PHPOpenSourceSaver\Fractal\Resource\Collection')->makePartial();
        $this->assertInstanceOf('PHPOpenSourceSaver\Fractal\Resource\Collection', $collection->setPaginator($paginator));
        $this->assertInstanceOf('PHPOpenSourceSaver\Fractal\Pagination\PaginatorInterface', $collection->getPaginator());
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\Resource\Collection::setMetaValue
     * @covers \PHPOpenSourceSaver\Fractal\Resource\Collection::getMetaValue
     */
    public function testGetSetMeta()
    {
        $collection = Mockery::mock('PHPOpenSourceSaver\Fractal\Resource\Collection')->makePartial();
        $this->assertInstanceOf('PHPOpenSourceSaver\Fractal\Resource\Collection', $collection->setMetaValue('foo', 'bar'));

        $this->assertSame(['foo' => 'bar'], $collection->getMeta());
        $this->assertSame('bar', $collection->getMetaValue('foo'));
        $collection->setMeta(['baz' => 'bat']);
        $this->assertSame(['baz' => 'bat'], $collection->getMeta());
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\Resource\Collection::setResourceKey
     */
    public function testSetResourceKey()
    {
        $collection = Mockery::mock('PHPOpenSourceSaver\Fractal\Resource\Collection')->makePartial();
        $this->assertInstanceOf('PHPOpenSourceSaver\Fractal\Resource\Collection', $collection->setResourceKey('foo'));
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\Resource\Collection::getResourceKey
     */
    public function testGetResourceKey()
    {
        $collection = Mockery::mock('PHPOpenSourceSaver\Fractal\Resource\Collection')->makePartial();
        $collection->setResourceKey('foo');
        $this->assertSame('foo', $collection->getResourceKey());
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
