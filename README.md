paging of php
====
![preview](https://github.com/PengYilong/Paging/raw/master/preview.png)

## Installation

Use [composer](http://getcomposer.org) to install nezumi/paging in your project:
```
composer require nezumi/paging
```


## Usage
```php
use Nezumi\paing;

$file = './template/paging.html';
$go_page_file = './template/go_page.html';
$paging = new Paging($file, $go_page_file);	
$paging->page_name = 'p';
$paging->init($count, $pagesize);
$html = $paging->html().$paging->go_page();
```

## custrom template like smarty

- paing.html
```html
<span id="PageContent" class="default">
<span>共{$t_page}页,{$t_records}条</span>{$prev}{$page_list}{$next}</span>
```

- go_page.html
```
<script type="text/javascript">
function go_page_button()
{
	var page = document.getElementById('pageInput');
	var gpage = (page.value > {$t_page}) ? {$t_page} : page.value;
	location.href = "{$url}&{$page_name}="+gpage;
}
</script>
<input class='search-btn fr' type='button' onclick="go_page_button()"  value="确定" />
<span class="go-to-page">
	<span>跳到</span>
	<input class='pagenum' type='text' id="pageInput"   value='{$current}' />
	<span>页</span> 
</span>

```