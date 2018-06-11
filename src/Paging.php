<?php

namespace Nezumi;

class Paging
{
    /**
     * @var
     */
    protected $pagesize;           //每页显示多少条记录

    /**
     * @var current page
     */
    protected $page;

    /**
     * @var total records
     */
    protected $total_records;

    /**
     * @var total pages
     */
    protected $t_page;

    /**
     * @var
     */
    public $url;

    /**
     * @var
     */
    public $limit;

    /**
     * @var
     */
    protected $page_listnum = 6;     //显示列表页数

    public function __construct($total_records, $pagesize = 8, $pa = '')
    {
        $this->page = isset($_GET['page']) ? $_GET['page'] : 1;
        $this->pagesize = $pagesize;
        $this->limit = $this->set_limit();
        $this->total_records = $total_records;
        $this->url = $this->get_url($pa);
        $this->t_page = ceil($this->total_records / $this->pagesize);
    }

    public function set_limit()
    {
        return ($this->page - 1) * $this->pagesize.','.$this->pagesize;
    }

    //解析地址并将多于的page去掉
    protected function get_url($pa)
    {
        $url = $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'], '?') ? '' : '?').$pa;
        $parse_url = parse_url($url);
        if (isset($parse_url['query'])) {
            parse_str($parse_url['query'], $parse_arr);
            if (isset($parse_arr['page'])) {
                unset($parse_arr['page']);
            }
            $url = $parse_url['path'].'?'.http_build_query($parse_arr);
        }

        return $url;
    }

    /**
     * first page
     */
    protected function first()
    {
        $html = "<a href='{$this->url}&page=1'>首页</a>&nbsp;&nbsp;";

        return $html;
    }

    /**
     * last page
     * 
     */
    protected function last()
    {
        $html = "<a href='{$this->url}&page=$this->t_page'>尾页</a>&nbsp;&nbsp;";

        return $html;
    }

    /**
     * prev page
     * 
     */
    protected function prev()
    {
        $html = "<a href='{$this->url}&page=".($this->page > 1 ? $this->page - 1 : $this->page)."'>上一页</a>&nbsp;&nbsp;";

        return $html;
    }

    /**
     * next page 
     * 
     */
    public function next()
    {
        $html = "<a href='{$this->url}&page=".($this->page + 1 < $this->t_page ? $this->page + 1 : $this->t_page)."'>下一页</a>&nbsp;&nbsp;";

        return $html;
    }

    //分页列表的数目等于(page_listnum/2)+1
    protected function page_list()
    {
        $link_page = '';
        $page_cur = floor($this->page_listnum / 2);
        for ($i = $page_cur; $i >= 1; --$i) {
            $page = $this->page - $i;
            if ($page <= 0) {
                continue;
            }
            $link_page .= " <a href='{$this->url}&page={$page}'>$page</a> ";
        }
        $link_page .= " $this->page ";
        for ($i = 1; $i <= $page_cur; ++$i) {
            $page = $this->page + $i;
            if ($page > $this->t_page) {
                break;
            }
            $link_page .= " <a href='{$this->url}&page={$page}'>$page</a> ";
        }

        return $link_page;
    }

    /**
     * 跳转页面,按回车键去跳转，或者按GO,都是通过location实现的.
     */
    protected function go_page()
    {
        $html = "&nbsp;&nbsp;&nbsp;到第<input class='go_page' type='text' onkeydown='javascript:if(event.keyCode==13){var gpage=(this.value>".$this->t_page.')?'.$this->t_page.":this.value;location=\"{$this->url}&page=\"+gpage+\"\";}'  value='{$this->page}' />页 &nbsp;<input class='go_page_sure' type='button' onclick='javascript:var gpage=(this.previousSibling.previousSibling.value>".$this->t_page.')?'.$this->t_page.":this.previousSibling.previousSibling.value;location=\"{$this->url}&page=\"+gpage+\"\";'  value='确定' />";

        return $html;
    }

    public function fpage($fpagearr = ['total', 'current', 'first', 'last', 'prev', 'next', 'page_list', 'go_page'])
    {
        $fhtml = ''; 
        $fhtml['total'] = '共'.$this->total_records.'条 &nbsp;';
        $fhtml['current'] = '页次'.$this->page.'/'.$this->t_page.' &nbsp;';
        $fhtml['epage'] = '每页'.($this->pagesize > $this->total_records ? $this->total_records : $this->pagesize).'条  ';
        $fhtml['first'] = $this->first();
        $fhtml['last'] = $this->last();
        $fhtml['prev'] = $this->prev();
        $fhtml['next'] = $this->next();
        $fhtml['page_list'] = $this->page_list();
        $fhtml['go_page'] = $this->go_page();
        $fpage = '';
        foreach ($fpagearr as $index) {
            $fpage .= $fhtml[$index];
        }

        return $fpage;
    }
}
