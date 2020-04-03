<?php
$config=require './Common/config.php';
$array=array(
	'TMPL_L_DELIM' => '{?',
	'TMPL_R_DELIM' => '?}',
	//'SHOW_PAGE_TRACE'=>true
	"ISLOG"=>1
);
return array_merge($config,$array);

