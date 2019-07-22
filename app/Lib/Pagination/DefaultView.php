<?php

namespace App\Lib\Pagination;

use Illuminate\Pagination\LengthAwarePaginator as PaginatorContract;

class DefaultView extends View
{
    protected $template;

    public function __construct($paginator)
    {
        if ($paginator instanceof PaginatorContract) {
            $paginator->onEachSide = 1;
            $this->template = new DefaultTemplate($paginator);
        }
    }

    public function render()
    {
        if ($this->template instanceof DefaultTemplate) {
            return strval($this->template->render());
        }
        return '';
    }
}