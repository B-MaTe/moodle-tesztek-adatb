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

    public function getTotalPages(): int
    {
        return ceil($this->totalRecords / $this->pageSize);
    }

    public function setItems(array $items): void {
        $this->items = $items;
    }

    public function getParams($uri, $pageSize, $currentPage): string {
        return $uri . '?pageSize=' . $pageSize . '&page=' . min(max(0, $currentPage), $this->getTotalPages() - 1);
    }

    public function getPreviousPageParams($uri): string
    {
        return $this->getParams($uri, $this->pageSize, $this->currentPage-1);
    }

    public function getNextPageParams($uri): string
    {
        return $this->getParams($uri, $this->pageSize, $this->currentPage+1);
    }

    public function getParamsDefault($uri): string
    {
        return $this->getParams($uri, $this->pageSize, $this->currentPage);
    }
}