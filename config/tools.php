<?php
header('Content-Type:text/html;charset=utf-8 ');   // 定义协议头与编码
require 'include/UserAuth.php'; // 鉴权

// 命令对应功能文件列表
$cmdList = array(
    'columnname' => 'columnname',   // 处理columnname
);

// 判断是否传递的 cmd 指令
if(empty($_GET['cmd'])){
    // 输出功能列表
    foreach ($cmdList as $key => $value) {
        echo "<a href=\"?cmd={$key}\">{$key}</a> | ";
    }
    exit;
}

// 获取命令并判断命令是否正确
$cmd = strtolower($_GET['cmd']);
if(empty($cmdList[$cmd])) die('Cmd Error!');


// 初始化环境
define('CONFIG_PATH', dirname(__FILE__) . '/');
define('ROOT_PATH', dirname(CONFIG_PATH) . '/');

// 配置文件需要变量
define('SITE_NAME', 'Tools');    // 将网站名存到常量


// 导入配置文件
require  ROOT_PATH . 'config.php';

require 'include/tools/' . $cmdList[$cmd] . '.php';
