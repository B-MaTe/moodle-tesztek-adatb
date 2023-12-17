<?php

namespace util\pageable;

class Pageable
{
    /**
     * Default Page size.
     */
    public const DEFAULT_PAGE_SIZE = 4;
    public const DEFAULT_PAGE_SIZE_QUESTIONS = 8;
    private int $pageSize;
    private int $page;
    private int $totalRecords;

    public function __construct(int $pageSize, int $page, int $totalRecords)
    {
        $this->pageSize = $pageSize;
        $this->page = $page;
        $this->totalRecords = $totalRecords;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getTotalRecords(): int
    {
        return $this->totalRecords;
    }

    public function getOffset(): int {
        return $this->page * $this->pageSize;
    }

    public static function builder(): PageableBuilder
    {
        return new PageableBuilder();
    }
}