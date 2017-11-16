<?php
/*
 *  将大文件分割成小文件
 *  以词条数分文件
 */


// 单个文件词条数
$cNum = 1000;   // 默认每个文件300条
// 过滤小于几个字节的词条  一个汉字做两个字节
$cBit = 3;  // 三个字母或2个汉字以上

// 文件分割整理


// 获取目录下所有的txt文件
$txtList = glob('*.txt'); // 获取所有文件

foreach ($txtList as $txtName){
    $txtStr = file_get_contents($txtName);  // 读入PHP文件
    $txtStr = str_ireplace(array("\r\n", "\r"), array("\n", "\n"), $txtStr);  // 替换换行标记
    $txtArr = explode("\n", $txtStr);   // 分割成数组

    $i = 0; // 行数
    $fileID = 1;    // 文件编号
    $fileName = basename($txtName, '.txt'); // 文件名
    $fileStr = '';  // 文件内容
    foreach ($txtArr as $txtStrS){  // 循环每一行
        // 判断文件长度
        if(strlen($txtStrS) > $cBit){
            $fileStr.= $txtStrS . "\n";
        }
        // 如果词条数量足够了
        if($i++ >= $cNum){
            $fileNameT = $fileName . '_' . $fileID++ . '.txt';
            file_put_contents($fileNameT, $fileStr);
            $i = 0; $fileStr = '';  // 重置
        }
    }

    
}
