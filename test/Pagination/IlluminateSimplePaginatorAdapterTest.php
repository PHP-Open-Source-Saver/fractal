<?php

namespace PHPOpenSourceSaver\Fractal\Test\Pagination;

use PHPOpenSourceSaver\Fractal\Pagination\IlluminateSimplePaginatorAdapter;
use Mockery;
use PHPUnit\Framework\TestCase;

class IlluminateSimplePaginatorAdapterTest extends TestCase
{
    public function testPaginationAdapter()
    {
        $total = 0;
        $count = 10;
        $perPage = 10;
        $currentPage = 2;
        $lastPage = 0;
        $url = 'http://example.com/foo?page=1';

        $paginator = Mockery::mock('Illuminate\Contracts\Pagination\Paginator');
        $paginator->shouldReceive('currentPage')->andReturn($currentPage);
        $paginator->shouldReceive('lastPage')->andReturn($lastPage);
        $paginator->shouldReceive('count')->andReturn($count);
        $paginator->shouldReceive('total')->andReturn($total);
        $paginator->shouldReceive('perPage')->andReturn($perPage);
        $paginator->shouldReceive('url')->with(1)->andReturn($url);

        $adapter = new IlluminateSimplePaginatorAdapter($paginator);

        $this->assertInstanceOf('PHPOpenSourceSaver\Fractal\Pagination\PaginatorInterface', $adapter);
        $this->assertInstanceOf('Illuminate\Contracts\Pagination\Paginator', $adapter->getPaginator());

        $this->assertSame($currentPage, $adapter->getCurrentPage());
        $this->assertSame($lastPage, $adapter->getLastPage());
        $this->assertSame($count, $adapter->getCount());
        $this->assertSame($total, $adapter->getTotal());
        $this->assertSame($perPage, $adapter->getPerPage());
        $this->assertSame('http://example.com/foo?page=1', $adapter->getUrl(1));
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
