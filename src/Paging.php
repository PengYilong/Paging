<?php

namespace Nezumi;

class Paging
{

    /**
     * @var int display records of per page
     */
    protected $pagesize;  

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
    protected $total;

    /**
     * @var string
     */
    public $url;

    /**
     * @var
     */
    public $limit;
 

    /**
     * @var string
     */
    protected $file;

    /**
     * @var string
     */
    protected $go_page_file;

    /**
     * @var string 
     */
    public $page_name = 'p';

    /**
     * @var string 
     */
    public $first_symbol = '首页';

    /**
     * @var string 
     */
    public $last_symbol = '尾页';

    /**
     * @var string
     */
    public $prev_symbol = '«';

    /**
     * @var string
     */
    public $next_symbol = '»';

    /**
     * @var int display page nums
     */
    public $page_listnum = 6;    

    public function __construct($file, $go_page_file)
    {
        $this->file = $file;
        $this->go_page_file = $go_page_file;
    }

    public function init($total_records, $pagesize = 8, $param = '')
    {
        $this->page =  isset($_GET[$this->page_name]) ? $_GET[$this->page_name]: 1;
        $this->pagesize = $pagesize;
        $this->limit = $this->set_limit();
        $this->total_records = $total_records;
        $this->url = $this->get_url($param);
        $this->total = ceil($this->total_records / $this->pagesize);     
    }

    public function set_limit()
    {
        return ($this->page - 1) * $this->pagesize.','.$this->pagesize;
    }

    /**
     * parse url and strip redundance page 
     * 
     */
    protected function get_url($param)
    {
        if( empty($_SERVER['QUERY_STRING']) && empty($param)  ){
            $url = $_SERVER['REQUEST_URI'].'?';
        } else {
            $url = $_SERVER['REQUEST_URI'].$param;
            $parse_url = parse_url($url);
            parse_str($parse_url['query'], $parse_arr);
            if (isset($parse_arr[$this->page_name]) ) {
                unset($parse_arr[$this->page_name]);
            }
            //only p
            if( empty($parse_arr) ){
                $url = $parse_url['path'].'?';
            } else {
                $url = $parse_url['path'].'?'.http_build_query($parse_arr).'&';
            }
         }
        return $url;
    }

    /**
     * first page
     */
    protected function first()
    {
        $html = '<a href="'.$this->url.$this->page_name.'=1">'.$this->first_symbol.'</a>';
        return $html;
    }

    /**
     * last page
     * 
     */
    protected function last()
    {
        $html = '<a href="'.$this->url.$this->page_name.'='.$this->total.'">'.$this->last_symbol.'</a>';
        return $html;
    }

    /**
     * prev page
     * 
     */
    protected function prev()
    {
        if( $this->page > 1 ){
            $html = '<a href="'.$this->url.$this->page_name.'='.($this->page -1 ).'">'.$this->prev_symbol.'</a>';
        } else {
            $html = '<span >'.$this->prev_symbol.'</span>';
        }
        return $html;
    }

    /**
     * next page 
     * 
     */
    public function next()
    {
        if( $this->page >= $this->total ){
            $html = '<span >'.$this->next_symbol.'</span>';
        } else {
            $html = '<a href="'.$this->url.$this->page_name.'='.($this->page + 1 < $this->total ? $this->page + 1 : $this->total).'" >'.$this->next_symbol.'</a>';
        }
        
        return $html;
    }

    /**
     * display nums of paging
     * page_offset = (page_listnum-1)/2
     * 
     */
    protected function page_list()
    {
        $link_page = '';
        $page_offset = ceil( ($this->page_listnum-1) / 2);
        if( $this->page-$page_offset > 1 ){
            $link_page .= '<span>...</span>';
        }
        for ($i = $page_offset; $i >= 1; $i--) {
            $page = $this->page - $i;
            if ($page <= 0) {
                continue;
            }
            $link_page .=  '<a href="'.$this->url.$this->page_name.'='.$page.'" >'.$page.'</a> ';
        }

        $link_page .= '<span class="current">'.$this->page.'</span>';

        for ($i = 1; $i <= $page_offset; $i++) {
            $page = $this->page + $i;
            if ($page > $this->total) {
                break;
            }
            $link_page .= ' <a href="'.$this->url.$this->page_name.'='.$page.'">'.$page.'</a> ';
        }
        if( $this->total-$this->page > $page_offset){
            $link_page .= '<span>...</span>';
        }        
        return $link_page;
    }

    /**
     * go to page via press Enter or go
     */
    public function go_page()
    {
        if( !file_exists($this->go_page_file) ){
            $this->error = $this->go_page_file.' doesn\'t exiset!';
            return false;
        } 
        $html = [];
        $html['t_page'] = $this->total;
        $html['page_name'] = $this->page_name;
        $html['url'] = $this->url;
        $html['current'] = $this->page;
        $str = $this->parsor($html, $this->go_page_file);
        return $str;             
    }

    public function html()
    {
        if( !file_exists($this->file) ){
            $this->error = $this->file.' doesn\'t exiset!';
            return false;
        } 
        $html = []; 
        $html['total'] = $this->total;
        $html['t_records'] = $this->total_records;
        $html['current'] = $this->page;
        $html['epage'] = ($this->pagesize > $this->total_records ? $this->total_records : $this->pagesize);
        $html['first'] = $this->first();
        $html['last'] = $this->last();
        $html['prev'] = $this->prev();
        $html['next'] = $this->next();
        $html['page_list'] = $this->page_list();
        $str = $this->parsor($html, $this->file);
        return $str;
    }


    protected function parsor($html, $file)
    {
        $str = file_get_contents($file);
        $keys = array_keys($html);
        array_walk($keys, [$this, 'add_special']);
        $values = array_values($html);
        //replace variables
        $str = str_replace($keys, $values, $str);
        return $str;
    }

    private function add_special(&$value)
    {
        $value = '{$'.$value.'}';
        return $value;
    }

    public function get_errors()
    {
        return $this->error;
    }
}
