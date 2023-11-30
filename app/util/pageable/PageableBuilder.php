<?php

namespace util\pageable;

class PageableBuilder
{
    private int $pageSize = 10;
    private int $page = 0;
    private int $totalRecords = PHP_INT_MAX;

    public function withPageSize(int $pageSize): self
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    public function withPage(int $page): self
    {
        $this->page = $page;
        return $this;
    }

    public function withTotalRecords(int $totalRecords): self
    {
        $this->totalRecords = $totalRecords;
        return $this;
    }

    public function build(): Pageable
    {
        return new Pageable($this->pageSize, $this->page, $this->totalRecords);
    }
}