<?php namespace PHPOpenSourceSaver\Fractal\Test;

use BadMethodCallException;
use Exception;
use PHPOpenSourceSaver\Fractal\Manager;
use PHPOpenSourceSaver\Fractal\Resource\Collection;
use PHPOpenSourceSaver\Fractal\Resource\Item;
use PHPOpenSourceSaver\Fractal\Scope;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class TransformerAbstractTest extends TestCase
{
    /**
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::setAvailableIncludes
     */
    public function testSetAvailableIncludes()
    {
        $transformer = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract')->makePartial();
        $this->assertInstanceOf('PHPOpenSourceSaver\Fractal\TransformerAbstract', $transformer->setAvailableIncludes(['foo']));
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::getAvailableIncludes
     */
    public function testGetAvailableIncludes()
    {
        $transformer = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract')->makePartial();

        $transformer->setAvailableIncludes(['foo', 'bar']);
        $this->assertSame(['foo', 'bar'], $transformer->getAvailableIncludes());
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::setDefaultIncludes
     */
    public function testSetDefaultIncludes()
    {
        $transformer = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract')->makePartial();
        $this->assertInstanceOf('PHPOpenSourceSaver\Fractal\TransformerAbstract', $transformer->setDefaultIncludes(['foo']));
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::getDefaultIncludes
     */
    public function testGetDefaultIncludes()
    {
        $transformer = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract')->makePartial();

        $transformer->setDefaultIncludes(['foo', 'bar']);
        $this->assertSame(['foo', 'bar'], $transformer->getDefaultIncludes());
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::setCurrentScope
     */
    public function testSetCurrentScope()
    {
        $transformer = $this->getMockForAbstractClass('PHPOpenSourceSaver\Fractal\TransformerAbstract');
        $manager = new Manager();
        $scope = new Scope($manager, m::mock('PHPOpenSourceSaver\Fractal\Resource\ResourceAbstract'));
        $this->assertInstanceOf('PHPOpenSourceSaver\Fractal\TransformerAbstract', $transformer->setCurrentScope($scope));
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::getCurrentScope
     */
    public function testGetCurrentScope()
    {
        $transformer = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract')->makePartial();
        $manager = new Manager();
        $scope = new Scope($manager, m::mock('PHPOpenSourceSaver\Fractal\Resource\ResourceAbstract'));
        $transformer->setCurrentScope($scope);
        $this->assertSame($transformer->getCurrentScope(), $scope);
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::getCurrentScope
     */
    public function testCanAccessScopeBeforeInitialization()
    {
        $transformer = $this->getMockForAbstractClass('PHPOpenSourceSaver\Fractal\TransformerAbstract');
        $currentScope = $transformer->getCurrentScope();
        $this->assertNull($currentScope);
    }

    public function testProcessEmbeddedResourcesNoAvailableIncludes()
    {
        $transformer = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract')->makePartial();

        $manager = new Manager();
        $manager->parseIncludes('foo');

        $scope = new Scope($manager, m::mock('PHPOpenSourceSaver\Fractal\Resource\ResourceAbstract'));
        $this->assertFalse($transformer->processIncludedResources($scope, ['some' => 'data']));
    }

    public function testProcessEmbeddedResourcesNoDefaultIncludes()
    {
        $transformer = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract')->makePartial();

        $manager = new Manager();
        $manager->parseIncludes('foo');

        $scope = new Scope($manager, m::mock('PHPOpenSourceSaver\Fractal\Resource\ResourceAbstract'));
        $this->assertFalse($transformer->processIncludedResources($scope, ['some' => 'data']));
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::processIncludedResources
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::callIncludeMethod
     */
    public function testProcessEmbeddedResourcesInvalidAvailableEmbed()
    {
		$this->expectException(BadMethodCallException::class);

        $transformer = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract')->makePartial();

        $manager = new Manager();
        $manager->parseIncludes('book');

        $scope = new Scope($manager, m::mock('PHPOpenSourceSaver\Fractal\Resource\ResourceAbstract'));
        $transformer->setCurrentScope($scope);

        $transformer->setAvailableIncludes(['book']);
        $transformer->processIncludedResources($scope, []);
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::processIncludedResources
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::callIncludeMethod
     */
    public function testProcessEmbeddedResourcesInvalidDefaultEmbed()
    {
		$this->expectException(BadMethodCallException::class);

        $transformer = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract')->makePartial();

        $manager = new Manager();

        $scope = new Scope($manager, m::mock('PHPOpenSourceSaver\Fractal\Resource\ResourceAbstract'));

        $transformer->setDefaultIncludes(['book']);
        $transformer->processIncludedResources($scope, []);
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::processIncludedResources
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::callIncludeMethod
     */
    public function testProcessIncludedAvailableResources()
    {
        $manager = new Manager();
        $manager->parseIncludes('book');
        $transformer = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract[transform]');

        $transformer->shouldReceive('includeBook')->once()->andReturnUsing(function ($data) {
            return new Item(['included' => 'thing'], function ($data) {
                return $data;
            });
        });

        $transformer->setAvailableIncludes(['book', 'publisher']);
        $scope = new Scope($manager, new Item([], $transformer));
        $included = $transformer->processIncludedResources($scope, ['meh']);
        $this->assertSame(['book' => ['data' => ['included' => 'thing']]], $included);
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::processIncludedResources
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::figureOutWhichIncludes
     */
    public function testProcessExcludedAvailableResources()
    {
        $manager = new Manager();
        $transformer = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract[transform]');
        $scope = new Scope($manager, new Item([], $transformer));

        $transformer->shouldReceive('includeBook')->never();

        $transformer->shouldReceive('includePublisher')->once()->andReturnUsing(function ($data) {
            return new Item(['another' => 'thing'], function ($data) {
                return $data;
            });
        });

        // available includes that have been requested are excluded
        $manager->parseIncludes('book,publisher');
        $manager->parseExcludes('book');

        $transformer->setAvailableIncludes(['book', 'publisher']);

        $included = $transformer->processIncludedResources($scope, ['meh']);
        $this->assertSame(['publisher' => ['data' => ['another' => 'thing']]], $included);
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::processIncludedResources
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::figureOutWhichIncludes
     */
    public function testProcessExcludedDefaultResources()
    {
        $manager = new Manager();
        $transformer = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract[transform]');
        $scope = new Scope($manager, new Item([], $transformer));

        $transformer->shouldReceive('includeBook')->never();

        $transformer->shouldReceive('includePublisher')->once()->andReturnUsing(function ($data) {
            return new Item(['another' => 'thing'], function ($data) {
                return $data;
            });
        });

        $manager->parseIncludes('book,publisher');
        $manager->parseExcludes('book');

        $transformer->setDefaultIncludes(['book', 'publisher']);

        $included = $transformer->processIncludedResources($scope, ['meh']);
        $this->assertSame(['publisher' => ['data' => ['another' => 'thing']]], $included);
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::processIncludedResources
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::callIncludeMethod
     */
    public function testProcessIncludedAvailableResourcesEmptyEmbed()
    {
        $manager = new Manager();
        $manager->parseIncludes(['book']);
        $transformer = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract[transform]');

        $transformer->shouldReceive('includeBook')->once()->andReturn(null);

        $transformer->setAvailableIncludes(['book']);
        $scope = new Scope($manager, new Item([], $transformer));
        $included = $transformer->processIncludedResources($scope, ['meh']);

        $this->assertFalse($included);
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::callIncludeMethod
     */
    public function testCallEmbedMethodReturnsCrap()
    {
		$this->expectExceptionObject(new Exception('Invalid return value from PHPOpenSourceSaver\Fractal\TransformerAbstract::includeBook().'));

        $manager = new Manager();
        $manager->parseIncludes('book');
        $transformer = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract[transform]');

        $transformer->shouldReceive('includeBook')->once()->andReturn(new \stdClass());

        $transformer->setAvailableIncludes(['book']);
        $scope = new Scope($manager, new Item([], $transformer));
        $transformer->processIncludedResources($scope, ['meh']);
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::processIncludedResources
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::callIncludeMethod
     */
    public function testProcessEmbeddedDefaultResources()
    {
        $manager = new Manager();
        $transformer = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract[transform]');

        $transformer->shouldReceive('includeBook')->once()->andReturnUsing(function ($data) {
            return new Item(['included' => 'thing'], function ($data) {
                return $data;
            });
        });

        $transformer->setDefaultIncludes(['book']);
        $scope = new Scope($manager, new Item([], $transformer));
        $included = $transformer->processIncludedResources($scope, ['meh']);
        $this->assertSame(['book' => ['data' => ['included' => 'thing']]], $included);
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::processIncludedResources
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::callIncludeMethod
     */
    public function testIncludedItem()
    {
        $manager = new Manager();
        $manager->parseIncludes('book');

        $transformer = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract[transform]');
        $transformer->shouldReceive('includeBook')->once()->andReturnUsing(function ($data) {
            return new Item(['included' => 'thing'], function ($data) {
                return $data;
            });
        });

        $transformer->setAvailableIncludes(['book']);
        $scope = new Scope($manager, new Item([], $transformer));
        $included = $transformer->processIncludedResources($scope, ['meh']);
        $this->assertSame(['book' => ['data' => ['included' => 'thing']]], $included);
    }

    public function testParamBagIsProvidedForIncludes()
    {
        $manager = new Manager();
        $manager->parseIncludes('book:foo(bar)');

        $transformer = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract')->makePartial();

        $transformer->shouldReceive('includeBook')
            ->with(m::any(), m::type('\PHPOpenSourceSaver\Fractal\ParamBag'))
            ->once();

        $transformer->setAvailableIncludes(['book']);
        $scope = new Scope($manager, new Item([], $transformer));

        $this->assertFalse($transformer->processIncludedResources($scope, []));
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::processIncludedResources
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::callIncludeMethod
     */
    public function testIncludedCollection()
    {
        $manager = new Manager();
        $manager->parseIncludes('book');

        $collectionData = [
            ['included' => 'thing'],
            ['another' => 'thing'],
        ];

        $transformer = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract[transform]');
        $transformer->shouldReceive('includeBook')->once()->andReturnUsing(function ($data) use ($collectionData) {
            return new Collection($collectionData, function ($data) {
                return $data;
            });
        });

        $transformer->setAvailableIncludes(['book']);
        $scope = new Scope($manager, new Collection([], $transformer));
        $included = $transformer->processIncludedResources($scope, ['meh']);
        $this->assertSame(['book' => ['data' => $collectionData]], $included);
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::processIncludedResources
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::callIncludeMethod
     */
    public function testProcessEmbeddedDefaultResourcesEmptyEmbed()
    {
        $transformer = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract[transform]');
        $transformer->shouldReceive('includeBook')->once()->andReturn(null);

        $transformer->setDefaultIncludes(['book']);
        $scope = new Scope(new Manager(), new Item([], $transformer));
        $included = $transformer->processIncludedResources($scope, ['meh']);

        $this->assertFalse($included);
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::item
     */
    public function testItem()
    {
        $mock = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract');
        $item = $mock->item([], function () {
        });
        $this->assertInstanceOf('PHPOpenSourceSaver\Fractal\Resource\Item', $item);
    }

    /**
     * @covers \PHPOpenSourceSaver\Fractal\TransformerAbstract::collection
     */
    public function testCollection()
    {
        $mock = m::mock('PHPOpenSourceSaver\Fractal\TransformerAbstract');
        $collection = $mock->collection([], function () {
        });
        $this->assertInstanceOf('PHPOpenSourceSaver\Fractal\Resource\Collection', $collection);
    }

    public function tearDown(): void
    {
        m::close();
    }
}
