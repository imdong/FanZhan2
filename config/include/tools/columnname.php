<?php
// 获取文件地址

$fileNameA = C('columnname');


$fileName = ROOT_PATH .  $fileNameA;


$fileSaveName = basename($fileName);
$fileSaveName = explode('.', $fileSaveName);
if($fileSaveName['1'] != 'txt'){
    die('No txt!');
}

// 读入文件内容
$fileStr = file_get_contents($fileName);

$keywordStr = str_ireplace(array("\r\n", "\r"), array("\n", "\n"), $fileStr);  // 替换换行标记
$keywordList = explode("\n", $keywordStr);

$retArr = array();
foreach ($keywordList as $key => $value){
    $keyInfo = explode('|', $value);
    $retArr[$keyInfo['1']] = $keyInfo['0'];
}

// 序列化文件
$serializeStr = serialize($retArr);

// 获取保存文件地址



$fileSaveName = $fileSaveName['0'] . '.serialize';

// 写到文件
file_put_contents($fileSaveName, $serializeStr);

// 修改配置文件
$configStr = file_get_contents(ROOT_PATH . 'config.php');

$fileSaveName = dirname($fileNameA) .'/'. $fileSaveName;



// 对文件名转义 防止出错
$fileNameStr = str_ireplace(array('\\','*','.'), array('\\\\', '\*', '\.'), $fileNameA);

$configStr = preg_replace('#((?:\'|\")columnname(?:\'|\")\s*=>\s*(?:\'|\"))'.$fileNameStr.'((?:\'|\"),)#', '$1'.$fileSaveName.'$2', $configStr);

file_put_contents(ROOT_PATH . 'config.php', $configStr);


echo 'OK!';