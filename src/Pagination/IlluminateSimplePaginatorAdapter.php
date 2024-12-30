<?php

/*
 * This file is part of the PHPOpenSourceSaver\Fractal package.
 *
 * (c) Phil Sturgeon <me@philsturgeon.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPOpenSourceSaver\Fractal\Pagination;

use Illuminate\Contracts\Pagination\Paginator;

/**
 * A paginator adapter for illuminate/pagination.
 *
 * @author Maxime Beaudoin <firalabs@gmail.com>
 * @author Marc Addeo <marcaddeo@gmail.com>
 */
class IlluminateSimplePaginatorAdapter implements PaginatorInterface
{
    protected Paginator $paginator;

    /**
     * Create a new illuminate simple pagination adapter.
     */
    public function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentPage(): int
    {
        return $this->paginator->currentPage();
    }

    /**
     * {@inheritDoc}
     */
    public function getLastPage(): int
    {
        return 0;
    }

    /**
     * {@inheritDoc}
     */
    public function getTotal(): int
    {
        return 0;
    }

    /**
     * {@inheritDoc}
     */
    public function getCount(): int
    {
        return $this->paginator->count();
    }

    /**
     * {@inheritDoc}
     */
    public function getPerPage(): int
    {
        return $this->paginator->perPage();
    }

    /**
     * {@inheritDoc}
     */
    public function getUrl(int $page): string
    {
        return $this->paginator->url($page);
    }

    /**
     * Get the paginator instance.
     */
    public function getPaginator(): Paginator
    {
        return $this->paginator;
    }
}
