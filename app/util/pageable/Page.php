<?php

namespace util\pageable;

class Page
{
    private array $items;
    private int $currentPage;
    private int $pageSize;
    private int $totalRecords;

    public function __construct(array $items, int $currentPage, int $pageSize, int $totalRecords)
    {
        $this->items = $items;
        $this->currentPage = $currentPage;
        $this->pageSize = $pageSize;
        $this->totalRecords = $totalRecords;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getTotalRecords(): int
    {
        return $this->totalRecords;
    }

    public function setItems(array $items): void {
        $this->items = $items;
    }
}