<?php
/*
 *  将目录下的txt文件全部转为序列化过的文件
 *
 */

$strLowLen = 3; // 设置词条有效长度 小于此长度的会被忽略 一个汉字算两个


$bm = empty($_GET['bm']) ? '' : strtoupper($_GET['bm']);    // 获取默认编码

// 枚举目录下的所有 txt 文件 全部转为对应的 数组文件
$txtList = glob('*.txt'); // 获取所有文件

// 没找到txt文件则返回
if(!$txtList) die('No .txt file!');

// 循环处理每一个txt文件
foreach ($txtList as $key => $txtFileName){
    // 读取文件
    $txtStr = file_get_contents($txtFileName);
    // 设置了源编码
    if(!empty($bm)){
        $txtStr = mb_convert_encoding($txtStr, 'UTF-8', $bm);
    }
    $txtStr = str_ireplace(array("\r\n", "\r"), array("\n", "\n"), $txtStr);  // 替换换行标记
    $txtArr = explode("\n", $txtStr);   // 分割字符

    // 整理文件内容
    $retArr = array();
    foreach ($txtArr as $value){
        // 字符长度应该大于0
        if(strlen($value) >= $strLowLen){
            $retArr[] = $value;
        }
    }
    // 数组序列化
    $serializeStr = serialize($txtArr);
    // 文件名
    $fileName = basename($txtFileName, '.txt') . '.serialize';

    // 写到文件
    file_put_contents($fileName, $serializeStr);
    echo $fileName . "<br />\n";
}
echo 'OK!';

/*
 *  功能: txt文件转phpArray文件
 *  参数一: txt文件名
 */
function txtFile2ArrFile($txtFileName){

    
/*
    // 生成 Array.php
    $arrStr = '<?php return array(';
    // 循环每一行
    foreach ($txtArr as $value){
        // 字符长度应该大于0
        if(strlen($value) > 0){
            $arrStr.= "'{$value}',";
        }
    }
    // 去掉最后一个 多于的,
    $arrStr = substr($arrStr, 0, strlen($arrStr)-1);
    $arrStr.= ");\n";
    file_put_contents(basename($txtFileName, '.txt') . '.php', $arrStr);

    // 生成Json文件
    $jsonStr = json_encode($txtArr);
    file_put_contents(basename($txtFileName, '.txt') . '.json', $jsonStr);
    
    // 生成serialize php文件
    $serializeStr = serialize($txtArr);
    $serializeRetStr = "<?php return '{$serializeStr}';\n";
    file_put_contents(basename($txtFileName, '.txt') . '.serialize.php', $serializeRetStr);
*/
    // 生成serialize文件




}

