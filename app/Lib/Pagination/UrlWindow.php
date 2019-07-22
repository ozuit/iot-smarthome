<?php

namespace App\Lib\Pagination;

use Illuminate\Pagination\UrlWindow as Base;

class UrlWindow extends Base
{
    public function getAdjacentUrlRange($onEachSide)
    {
        $from = $this->currentPage() - $onEachSide;
        $to = $this->currentPage() + $onEachSide;
        if ($from <= 2) {
            $from++;
            $to++;
        }
        if ($to >= ($this->lastPage() - 1)) {
            $from--;
            $to--;
        }

        return $this->paginator->getUrlRange(
            $from, $to
        );
    }

    public function getStart()
    {
        return ['1' => $this->paginator->url(1)];
    }

    public function getFinish()
    {
        return [$this->lastPage() => $this->paginator->url($this->lastPage())];
    }
}