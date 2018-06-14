<link rel="stylesheet" href="./css/normalize.css" type="text/css">
<link rel="stylesheet" href="./css/paging.css" type="text/css">
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

