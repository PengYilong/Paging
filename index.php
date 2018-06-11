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


$data = getList(8, '*', 'people');
echo $data['table'].'<br/>';
echo $data['page'];

function getList($pagesize, $fields='*', $table, $where = '', $limit = '', $order = '', $group = '', $key = '', $having = '')
{
	global $mysql;
	//get total records
	$array = $mysql->get_one('count(*) AS num', $table, $where, $limit = '', $order ='', $group, $key, $having);
	$count = $array['num'];
	$paging = new Paging($count, $pagesize);
	$limit = $paging->limit;	
	$result = $mysql->select($fields, $table, $where, $limit, $order, $group, $key, $having);
	return [
		'data' => $result,
		'table' => $mysql->display_table($result),
		'page' => $paging->fpage(['total', 'current', 'first', 'last', 'prev', 'next', 'page_list']),
	];
}

