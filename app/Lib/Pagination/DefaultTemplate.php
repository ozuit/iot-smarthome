<?php

namespace App\Lib\Pagination;

use Illuminate\Pagination\LengthAwarePaginator as PaginatorContract;

class DefaultTemplate
{
    const DOT_BEFORE = 1;
    const DOT_AFTER = 2;

    protected $paginator;
    protected $window;

    public function __construct(PaginatorContract $paginator, UrlWindow $window = null)
    {
        $this->paginator = $paginator;
        $this->window = is_null($window) ? UrlWindow::make($paginator) : $window->get();
    }

    protected function hasPages()
    {
        return $this->paginator->lastPage() > 1;
    }

    protected function currentPage()
    {
        return $this->paginator->currentPage();
    }

    protected function lastPage()
    {
        return $this->paginator->lastPage();
    }

    protected function getPreviousButton($text = '&laquo;')
    {
        if ($this->paginator->currentPage() <= 1) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->paginator->url(
            $this->paginator->currentPage() - 1
        );

        return $this->getPageLinkWrapper($url, $text, 'prev');
    }

    public function getNextButton($text = '&raquo;')
    {
        if (! $this->paginator->hasMorePages()) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->paginator->url($this->paginator->currentPage() + 1);

        return $this->getPageLinkWrapper($url, $text, 'next');
    }

    protected function getUrlLinks(array $urls)
    {
        $html = '';

        foreach ($urls as $page => $url) {
            $html .= $this->getPageLinkWrapper($url, $page);
        }

        return $html;
    }

    protected function getPageLinkWrapper($url, $page, $rel = null)
    {
        if ($page == $this->paginator->currentPage()) {
            return $this->getActivePageWrapper($page);
        }

        return $this->getAvailablePageWrapper($url, $page, $rel);
    }

    public function render()
    {
        if ($this->hasPages()) {
            return sprintf(
                '<ul class="%s">%s%s%s</ul>',
                'pagination',
                $this->getPreviousButton('<i class="fa fa-angle-left"></i>'),
                $this->getLinks(),
                $this->getNextButton('<i class="fa fa-angle-right"></i>')
            );
        }

        return '';
    }

    protected function getAvailablePageWrapper($url, $page, $rel = null)
    {
        $rel = is_null($rel) ? '' : ' rel="'.$rel.'"';

        return '<li><a href="'.htmlentities($url).'"'.$rel.'>'.$page.'</a></li>';
    }

    protected function getDisabledTextWrapper($text)
    {
        $class = ($text != '...' ? 'disabled' : 'disabled dot');
        return '<li class="'.$class.'"><a>'.$text.'</a></li>';
    }

    protected function getActivePageWrapper($text)
    {
        return '<li><span>'.$text.'</span></li>';
    }

    protected function getDots($pos = null)
    {
        $page = $this->currentPage();
        $last = $this->lastPage();
        if ($pos === DefaultTemplate::DOT_AFTER && ($page == $last - 2 || $page == $last - 3)) {
            return $this->getUrlLinks([($last - 1) => $this->paginator->url($last - 1)]);
        } elseif ($pos === DefaultTemplate::DOT_BEFORE && ($page == 3 || $page == 4)) {
            return $this->getUrlLinks(['2' => $this->paginator->url(2)]);
        }

        return $this->getDisabledTextWrapper('...');
    }

    protected function getLinks()
    {
        $html = '';

        if (is_array($this->window['first'])) {
            $html .= $this->getUrlLinks($this->window['first']);
        }

        if (is_array($this->window['slider'])) {
            $html .= $this->getDots(DefaultTemplate::DOT_BEFORE);
            $html .= $this->getUrlLinks($this->window['slider']);
        }

        if (is_array($this->window['last'])) {
            $html .= $this->getDots(DefaultTemplate::DOT_AFTER);
            $html .= $this->getUrlLinks($this->window['last']);
        }

        return $html;
    }
}