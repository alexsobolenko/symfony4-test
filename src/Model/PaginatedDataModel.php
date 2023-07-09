<?php

declare(strict_types=1);

namespace App\Model;

final class PaginatedDataModel
{
    /**
     * @var int
     */
    public int $pages;

    /**
     * @var int|null
     */
    public ?int $prev;

    /**
     * @var int|null
     */
    public ?int $next;

    /**
     * @var array
     */
    public array $pageItems;

    /**
     * @param int $total
     * @param int $limit
     * @param int $page
     * @param array $items
     */
    public function __construct(
        public int $total = 0,
        public int $limit = 10,
        public int $page = 1,
        public array $items = []
    ) {
        $this->pageItems = [1];
        $this->pages = 0;
        $this->prev = null;
        $this->next = null;

        if ($this->total > 0) {
            $this->pages = (int) ceil($this->total / $this->limit);

            if ($this->pages > 1) {
                if ($this->page !== $this->pages) {
                    $this->next = $this->page + 1;
                }

                if ($this->page !== 1) {
                    $this->prev = $this->page - 1;
                }
            } else {
                $this->page = 1;
            }

            if ($this->pages < 5) {
                for ($i = 2; $i <= $this->pages; $i++) {
                    $this->pageItems[] = $i;
                }
            } else {
                if ($this->page >= 5) {
                    $this->pageItems[] = '...';
                }

                if ($this->page + 4 <= $this->pages && $this->page >= 5) {
                    for ($i = $this->page - 2; $i <= $this->page + 2; $i++) {
                        $this->pageItems[] = $i;
                    }
                } else {
                    if ($this->page > 5) {
                        $start = $this->pages - 4;
                        $end = $this->pages;
                    } else {
                        $start = 2;
                        $end = 5;
                    }

                    if ($start < 2) {
                        $start = 2;
                    }

                    if ($end > $this->pages) {
                        $end = $this->pages;
                    }

                    for ($i = $start; $i <= $end; $i++) {
                        $this->pageItems[] = $i;
                    }
                }

                if ($this->page <= $this->pages - 4) {
                    $this->pageItems[] = '...';
                }

                if (!in_array($this->pages, $this->pageItems, true)) {
                    $this->pageItems[] = $this->pages;
                }
            }
        }
    }
}
