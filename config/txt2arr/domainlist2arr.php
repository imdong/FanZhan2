<?php

$serializeFileName = 'domainlist.serialize';    // 序列化文件
$txtFileName = 'domainlist.txt';    // 文本列表

// 判断序列化文件是否存在
if(file_exists($serializeFileName) && is_file($serializeFileName)){
    $fileStr = file_get_contents($serializeFileName);
    $serializeArray = unserialize($fileStr);
}else{
    $serializeArray = array();
}

// 判断文本列表是否存在
if(file_exists($txtFileName) && is_file($txtFileName)){
    $fileStr = file_get_contents($txtFileName);
    // 统一替换行标记
    $txtStr = str_ireplace(array("\r\n", "\r"), array("\n", "\n"), $fileStr);
    // 分割类型
    $domainTypeList = explode("\n\n", $txtStr);

    foreach ($domainTypeList as $domainList) {
        $domainSinfo = explode(":\n", $domainList);
        $tName = $domainSinfo['0'];
        $domainListB = explode("\n", $domainSinfo['1']);
        foreach ($domainListB as $vDomain){
            //if(strlen($vDomain) > 1)
            $arrayName[$vDomain] = $tName;
        }
    }
}else{
    $arrayName = array();
}

var_dump($arrayName);


// 合并两个数组
$returnArray = array_merge($serializeArray, $arrayName);

$fileStr = serialize($returnArray);
file_put_contents('domainlist.serialize', $fileStr);

echo "domainlist OK!<br />\n<pre>";
print_r($returnArray);
echo "</pre>";