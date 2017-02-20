<?php


namespace AppBundle\Service;


class PaginationService
{
    private $page;
    private $maxResults;
    private $totalItems;
    private $path;

    public function __construct($path = '')
    {
        $this->path = $path;
    }

    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    public function setMaxResults($maxResults)
    {
        $this->maxResults = $maxResults;
        return $this;
    }

    public function setTotalItems($totalItems)
    {
        $this->totalItems = $totalItems;
        return $this;
    }

    public function getPaginationValues()
    {
        return [
            'current' => $this->page,
            'next' => $this->getNextPage(),
            'prev' => $this->getPrevPage(),
            'total' => $this->getLastPage()];
    }

    public function getLastPage()
    {
        return $this->totalItems/$this->maxResults;
    }

    public function getNextPage()
    {
        return ($this->page + 1) <= $this->getLastPage() ?  $this->path . ($this->page + 1) : 0;
    }

    public function getPrevPage()
    {
        return ($this->page - 1) <= 0 ? 0 : $this->path . ($this->page - 1);
    }
}