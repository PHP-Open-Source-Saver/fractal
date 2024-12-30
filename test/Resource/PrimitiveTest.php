<?php namespace PHPOpenSourceSaver\Fractal\Test\Resource;

use PHPOpenSourceSaver\Fractal\Resource\Primitive;
use Mockery;
use PHPUnit\Framework\TestCase;

class PrimitiveTest extends TestCase
{
    protected $simplePrimitive = 'sample string';

    public function testGetData()
    {
        $primitive = new Primitive($this->simplePrimitive);

        $this->assertSame($primitive->getData(), $this->simplePrimitive);
    }

    public function testGetTransformer()
    {
        $primitive = new Primitive($this->simplePrimitive, function () {});

        $this->assertTrue(is_callable($primitive->getTransformer()));

        $transformer = 'thismightbeacallablestring';
        $primitive = new Primitive($this->simplePrimitive, $transformer);

        $this->assertSame($primitive->getTransformer(), $transformer);
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\Resource\Primitive::setResourceKey
     */
    public function testSetResourceKey()
    {
        $primitive = Mockery::mock('PHPOpenSourceSaver\Fractal\Resource\Primitive')->makePartial();

        $this->assertSame($primitive, $primitive->setResourceKey('foo'));
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\Resource\Primitive::getResourceKey
     */
    public function testGetResourceKey()
    {
        $primitive = Mockery::mock('PHPOpenSourceSaver\Fractal\Resource\Primitive')->makePartial();
        $primitive->setResourceKey('foo');

        $this->assertSame('foo', $primitive->getResourceKey());
    }
}
