<?php
$config=require("./Application/Common/Conf/config.php");
$config2=array(
'TOKEN_ON' => true,
'TOKEN_NAME' => '__hash__',
'TOKEN_TYPE' => 'md5',
'TOKEN_RESET' => true
);
return array_merge($config,$config2);