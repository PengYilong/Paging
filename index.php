<style type="text/css">
/*===========================paging style start==========================*/
.fr{float:right;}
.pagelist{ clear:both; display:block; float:left;}
.pagelist:after{ clear:both; content:"."; display:block; height:0; visibility:hidden; }
.pagelist .l-btns{ display:block; float:right; margin:0 5px 0 0; padding:0 5px; border:1px solid #dbdbdb; height:22px; overflow:hidden; }
.pagelist .l-btns span{ font-size:12px; color:#333; line-height:22px; }
.pagelist .l-btns .pagenum{ display:inline-block; margin:0 5px; padding:0 5px; border:1px solid #dbdbdb; border-top:0; border-bottom:0; width:30px; height:22px; line-height:20px; font-size:12px; color:#333; text-align:center; vertical-align:top; overflow:hidden; }
span.default{ margin:0; padding:0; font-family:"Microsoft YaHei",Verdana; font-size:12px; }
span.default a,span.default span{ display:inline-block; margin:0 -2px 0 -2px; padding:1px 5px; line-height:20px; height:20px; border:1px solid #e1e1e1; background:#fff; color:#333; text-decoration:none; }
span.default span:first-child{ border-left:1px solid #e1e1e1; }
span.default a:hover{ color:#666; background:#eee; }
span.default span.current{ color:#fff; background:#1e71b1; border-color:#1e71b1; }
span.default span.disabled{ color:#999; background:#fff; }
/*===========================paging style end==========================*/

/*===========================search button==========================*/
.search-btn{ background:#1e71b1; border:none; color:#fff; cursor:pointer; display:inline-block; font-family:"Microsoft Yahei"; font-size:12px; height:24px; line-height:22px; margin:0 1px 0 0; padding:0px 15px; vertical-align:middle; }
.search-btn:hover{ background:#105488; }
.search-btn.green{ background:#52A152; }
.search-btn.green:hover{ background:#328032; }
.search-btn.yellow{ background:#FF9C30;}
.search-btn.yellow:hover{ background:#c87316; }
.search-btn.violet{ background:#993333 ; }
.search-btn.violet:hover{ background:#990033; }
/*===========================search button==========================*/
</style>
<?php
//load composer
if( file_exists(__DIR__.'/vendor/autoload.php') ){
	require __DIR__.'/vendor/autoload.php';
}
include './Loader.php';
spl_autoload_register('Loader::_autoload');

use Nezumi\MySQLi;
use Nezumi\Paging;

//load config
$config = include './configs/database.php';

$mysql = new MySQLi();
$mysql->open($config['master']);

$data = getList('p', 8, '*', 'people');
echo $data['table'].'<br/>';
echo $data['page'];

function getList($page = 'page', $pagesize, $fields='*', $table, $where = '', $limit = '', $order = '', $group = '', $key = '', $having = '')
{
	global $mysql;
	//get total records
	$array = $mysql->get_one('count(*) AS num', $table, $where, $limit = '', $order ='', $group, $key, $having);
	$count = $array['num'];

	$file = './template/paging.html';
	$go_page_file = './template/go_page.html';
	$paging = new Paging($file, $go_page_file);	
	$paging->page_name = $page;
	$paging->init($count, $pagesize);
	$limit = $paging->limit;
	$result = $mysql->select($fields, $table, $where, $limit, $order, $group, $key, $having);
	return [
		'data' => $result,
		'table' => $mysql->display_table($result),
		'page' => '<div class="pagelist">'.$paging->html().$paging->go_page().'</div>',
	];
}

