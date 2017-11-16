<?php
/*
 *  模板替换标记功能实现方法
 *
 */


/*
 *  功能: 取当前页面主关键词
 *  无参数
 */
function tag_getMainKeyword($param_arr){
    // 如果传递了空的 参数 则返回文章关键字
    if(empty($param_arr['0'])) return tag_getPostKey(SITE_URI);

    // 创建静态的关键字信息
    static $keywordList = array();

    // 获取要查询的关键字
    $key = $param_arr['0'];
    if(empty($keywordList[$key])){
        $keyword = tag_getKeywordRand(array());    // 随机取关键词
        $keywordList[$key] = $keyword;  // 保存到数组
    }else{
        $keyword = $keywordList[$key];  // 读取关键词
    }
    return $keyword;
}
/*
 *  功能: 根据文章ID取文章关键字
 *  参数1: 文章ID
 */
function tag_getPostKey($param_arr){
    // 数组则为标签传递 非数组则为内部调用
    if(is_array($param_arr)){
        if(empty($param_arr['0'])) return '';
        $post_id = $param_arr['0'];
    }else{
        $post_id = $param_arr;
    }

    if(preg_match('/^[a-zA-Z0-9]+$/', $post_id)){
        $post_id = SITE_DOMAIN . '/' . $post_id . '.html';
    }
    // 查询缓存是否有此文章记录
    $postKeyword = S('PostKey/' . $post_id);
    if($postKeyword == false){
        $postKeyword = tag_getKeywordRand($post_id);
        S('PostKey/' . $post_id, $postKeyword);
    }
    // 保存文章ID列表 只保存纯数字ID
    if(is_array($param_arr) && is_int($param_arr['0'])){
        $postList = S('PostID/List');
        $id = $param_arr['0'];
        $postList[$id] = $postKeyword;
        S('PostID/List', $postList);
    }
    return $postKeyword;
}

/*
 *  功能: 根据文章ID取文章Url(相对)
 *  参数1: 文章ID
 */
function tag_getPostUrl($param_arr){
    $Str = $param_arr['0'] . '.html';
    return $Str;
}

/*
 *  功能: 取随机文章ID 根据已有的文章列表
 *  无参数
 */
function tag_getRandPostID($param_arr){
    $postList = S('PostID/List');
    if(count($postList) <= 0) return '';
    $post = array_rand($postList);
    return $post;
}


/*
 *  功能: 取随机关键字
 *  无参数
 */
function tag_getKeywordRand($param_arr){
    // 获取字符串数组文件
    static $keywordList = null;
    if($keywordList === null){
        $keywordList = readFile2Array(C('keywords'));
    }
    return array2RandValue($keywordList);
}

/*
 *  功能: 取随机描述
 *  无参数
 */
function tag_getRandDescription($param_arr){
    // 获取字符串数组文件
    static $descriptionList = null;
    if($descriptionList === null){
        $descriptionList = readFile2Array(C('description'));
    }
    return array2RandValue($descriptionList);
}


/*
 *  功能: 取随机数字(按长度范围)
 *  参数1 最小长度 默认3
 *  参数2 最大长度 默认5
 */
function tag_getRandNum($param_arr){
    $leastLenght = (empty($param_arr['0']) || !is_numeric($param_arr['0'])) ? 3 : intval($param_arr['0']);
    $maxLenght = (empty($param_arr['1']) || !is_numeric($param_arr['1'])) ? 5 : intval($param_arr['1']);
    $leastStr = '1';
    for ($i=1; $i < $leastLenght; $i++) { 
        $leastStr.='0';
    }
    $leastNumber = intval($leastStr);

    $maxStr = '9';
    for ($i=1; $i < $maxLenght; $i++) { 
        $maxStr.='9';
    }
    $maxNumber = intval($maxStr);

    $intStr = rand($leastNumber, $maxNumber);
    return $intStr;
}
/*
 *  功能: 执行PHP函数
 *  参数1 PHP代码
 */
function tag_EvalPHP($param_arr){
    return eval($param_arr['0']);
}

/*
 *  功能: 输出任意文本
 *  参数1 文本内容 可无限添加
 */
function tag_echo($param_arr){
    $str = '';
    foreach ($param_arr as $value){
        // 如果带有引用 & 则直接读取配置文件的对应内容
        if($value['0'] == '&'){
            $configName = substr($value, 1);    // 获取要读取的配置项名
            // 判断
            switch ($configName) {
                case '网站目录':
                    $str.= SITE_NAME;
                    break;

                default:  // 都不存在就读取配置文件内容
                    $str.= C($configName);
                    break;
            }
        }else{
            $str.= $value;
        }
    }
    return $str;
}

/*
 *  功能: 取随机句子
 *  参数1 句子数量 默认1
 */
function tag_getRandStr($param_arr){
    $strNum = (empty($param_arr['0']) || is_numeric($param_arr['0'])) ? 1 : intval($param_arr['0']);

    // 获取字符串数组文件
    static $strList = null;
    if($strList === null){
        $strListArr = C('confused');
        $strPathList = array2RandValue($strListArr[C('type')]);
        $strList = readFile2Array($strPathList);
    }

    $retStr = '';
    for ($i=0; $i < $strNum; $i++){
        // 随机加 句号 逗号 逗号几率高
        if($i >= 1) $retStr.= mt_rand(2, 5) == 2 ? '。' : '，';
        $retStr.= array2RandValue($strList);
    }
    return $retStr;
}

/*
 *  功能: 随机取一段话
 *  参数1 插入内容  不写则不插入
 */
function tag_getRandParagraph($param_arr){
    $insertStr = empty($param_arr['0']) ? '' : $param_arr['0'];

    // 获取字符串数组文件
    static $strList = null;
    if($strList === null){
        $strListArr = C('paragraph');
        $strPathList = array2RandValue($strListArr);
        $strList = readFile2Array($strPathList);
    }
    $retStr = array2RandValue($strList);

    // 随机插入 不乱码
    if(!empty($insertStr) && isSpider()){
        $len = intval(mb_strlen($retStr, 'utf-8') * 0.75);
        $strA = mb_substr($retStr, 0, $len, 'utf-8');
        $strB = mb_substr($retStr, $len, 99999, 'utf-8');
        $retStr = $strA . $insertStr . $strB;
    }
    return $retStr;
}

/*
 *  功能: 取随机字母
 *  参数1 最少字母长度 默认3
 *  参数2 最长字母长度 默认5
 *  参数3 字母组合 0aA  默认 a 全小写
 */
function tag_getRandLetter($param_arr){
    $leastLenght = (empty($param_arr['0']) || is_numeric($param_arr['0'])) ? 3 : intval($param_arr['0']);
    $maxLenght = (empty($param_arr['1']) || is_numeric($param_arr['1'])) ? 5 : intval($param_arr['1']);
    $constitute = empty($param_arr['2']) ? 'a' : $param_arr['2'];

    $letterStr = '';
    for ($i=0; $i < strlen($constitute); $i++) {
        switch ($constitute[$i]) {
            case 'a':
                $letterStr.= 'qwertyuiopasdfghjklzxcvbnm';  // 所有小写字母
                break;
            case 'A':
                $letterStr.= 'QWERTYUIOPASDFGHJKLZXCVBNM';  // 所有大写字母
                break;
            case '0':
                $letterStr.= '1234567890';  // 所有数字
                break;
        }
    }

    $letterStrLen = strlen($letterStr) - 1; // 字符串长度
    $seq = mt_rand($leastLenght, $maxLenght);  // 根据范围生成随机长度
    $retStr = '';
    for ($i=0; $i < $seq; $i++) { 
        $retStr.= $letterStr[mt_rand(0, $letterStrLen)];
    }
    return $retStr;
}

/*
 *  功能: 取随机人名
 *  无参数
 */
function tag_getRandUName($param_arr){
    // 获取字符串数组文件
    static $nameList = null;
    if($nameList === null){
        $nameList = readFile2Array(C('firstname'));
    }

    $nameStr = array2RandValue($nameList['x']);
    $nameStr.= array2RandValue($nameList['m']);
    return $nameStr;
}

/*
 *  功能: 取随机图片
 *  无参数
 */
function tag_getRandImg($param_arr){
    // 获取所有文件
    static $imgList = null;
    if($imgList === null){
        $imgList = glob(C('imgpath').'/*');
    }

    // 随机取一名成员
    return basename(array2RandValue($imgList));
}

/*
 *  功能: 取时间
 *  参数1 返回时间格式
 *  参数2 对时间加减操作
 */
function tag_getTime($param_arr){
    $tileFormat = empty($param_arr['0']) ? '年/月/日 周 时:分:秒' : $param_arr['0'];
    $decrease = empty($param_arr['1']) ? '+0秒' : $param_arr['1'];

    $time = time(); // 获取当前时间戳

    // 处理时间加减
    if(preg_match('#(?<u>\+|\-)(?<d>[0-9]+)(?<m>[^<]+)#', $decrease, $decreaseT)){
        // 计算 单位时间操作范围
        switch ($decreaseT['m']) {
            case '年':
                $timeLen = 31536000;
                break;
            case '月':
                $timeLen = 2592000;
                break;
            case '周':
                $timeLen = 604800;
                break;
            case '日':
                $timeLen = 86400;
                break;
            case '时':
                $timeLen = 3600;
                break;
            case '分':
                $timeLen = 60;
                break;
            case '秒':
                $timeLen = 1;
                break;
            default:
                $timeLen = 0;
                break;
        }
        // 计算总操作范围
        $timeLen*= $decreaseT['d'];
        if($decreaseT['u'] == '+'){
            $time+= $timeLen;
        }else{
            $time-= $timeLen;
        }
    }

    // 计算周
    $weekArr = array('周','一','二','三','四','五','六','日');
    $week = $weekArr[date('N', $time)];
    // 转换时间格式
    $tileFormat = str_ireplace(
        array('年','月','日','周','时','分','秒'),
        array('Y','m','d',$week,'H','i','s'),
        $tileFormat
    );
    // 格式化时间
    $timeStr = date($tileFormat, $time);
    return $timeStr;
}

/*
 *  功能: 取随机视频
 *  无参数
 */
function tag_getRandVideo($param_arr){
    // 获取字符串数组文件
    static $videoList = null;
    if($videoList === null){
        $videoList = readFile2Array(C('videourl'));
    }
    return array2RandValue($videoList);
}

/*
 *  功能: 取随机网站名
 *  无参数
 */
function tag_getRandSiteName($param_arr){
    // 获取字符串数组文件
    static $siteNameList = null;
    if($siteNameList === null){
        $siteNameList = readFile2Array(C('sitename'));
    }
    return array2RandValue($siteNameList);
}

/*
 *  功能: 返回编码后的文本
 *  参数一 需要编码的文本
 */
function tag_str2Unicode($param_arr){
    $string = empty($param_arr['0']) ? '' : $param_arr['0'];
    $string = unicode_encode($string);  // 对文本编码
    $string = str_ireplace(';&#', ';&#x0020;&#', $string);
    return unicode_encode($string);
}

/*
 *  功能: 取随机成语
 *  无参数
 */
function tag_getRandProverb($param_arr){
    // 获取字符串数组文件
    static $proverbList = null;
    if($proverbList === null){
        $proverbList = readFile2Array(C('proverb'));
    }
    $proverbStr = array2RandValue($proverbList);
    return $proverbStr;
}


/*
 *  取域名
 *  参数一 域名类型 默认为完整域名 可选参数 主 / 子
 */
function tag_getDomain($param_arr){
    $string = empty($param_arr['0']) ? '整' : $param_arr['0'];
    switch ($string) {
        case '主':
            $rDomain = SITE_MAIN_DOMAIN;
            break;
        case '子':
            $rDomain = SITE_SUB_DOMAIN;
            break;
        default:
            $rDomain = SITE_DOMAIN;
            break;
    }
    return $rDomain;
}

/*
 *  功能: 取随机地名
 *  无参数
 */
function tag_getRandPlaceName($param_arr){
    // 获取字符串数组文件
    static $proverbList = null;
    if($proverbList === null){
        $proverbList = readFile2Array(C('placename'));
    }
    $proverbStr = array2RandValue($proverbList);
    return $proverbStr;
}

/*
 *  功能: 取随机地名
 *  无参数
 */
function tag_getRandSiteType($param_arr){
    // 获取字符串数组文件
    static $proverbList = null;
    if($proverbList === null){
        $proverbList = readFile2Array(C('sitetype'));
    }
    $proverbStr = array2RandValue($proverbList);
    return $proverbStr;
}

/*
 *  功能: 取随机栏目名(拼音标示)
 *  无参数
 */
function tag_getRandColumnName($param_arr){
    // 获取字符串数组文件
    static $proverbList = null;
    if($proverbList === null){
        $proverbList = readFile2Array(C('columnname'));
    }
    // 随机取出一个键名来
    $proverbStr = array_rand($proverbList);
    return $proverbStr;
}

/*
 *  功能: 取栏目中文名
 *  参数一 要取栏目的拼音标示(不写则随机)
 */
function tag_getRandColumnTtile($param_arr){
    $string = empty($param_arr['0']) ? '' : $param_arr['0'];
    // 获取字符串数组文件
    static $proverbList = null;
    if($proverbList === null){
        $proverbList = readFile2Array(C('columnname'));
    }
    if(empty($proverbList[$string])){
        $proverbStr = array2RandValue($proverbList);
    }else{
        $proverbStr = $proverbList[$string];
    }
    return $proverbStr;
}

/*
 *  功能: 取随机域名
 *  无参数
 */
function tag_getRandDomain($param_arr){
    // 获取字符串数组文件
    static $proverbList = null;
    if($proverbList === null){
        $proverbList = readFile2Array(C('domainlist'));
    }

    $proverbStr = array_rand($proverbList);
 
    return $proverbStr;
}

/*
 *  功能: 取随机外链
 *  无参数
 */
function tag_getRandExternalLink($param_arr){
    // 获取字符串数组文件
    static $proverbList = null;
    if($proverbList === null){
        $proverbList = readFile2Array(C('externallink'));
    }
    $proverbStr = array2RandValue($proverbList);
    return $proverbStr;
}

/*
 *  功能: 输出A标签
 *  参数一 连接地址
 *  参数二 连接标题
 */
function tag_echoALink($param_arr){
    $string = empty($param_arr['0']) ? '内' : $param_arr['0'];
    switch ($string) {
        case '内':
            $link = tag_getRandLetter(array('5','7')) . '.' . tag_getRandDomain(array());
            $title = tag_getRandPlaceName(array()) . tag_getRandSiteType(array());
            break;
        case '外':
            $link = tag_getRandExternalLink(array());
            $title = $link;
            break;
    }
    $retStr = "<a href=\"http://www.{$link}\" >{$title}</a>";
    return $retStr;
}

/*
 *  功能: 输出对应短网址
 *  参数一 链接地址 默认不填为当前域名
 */
function tag_getShortURL($param_arr){
    $string = empty($param_arr['0']) ? 'http://'.$_SERVER['HTTP_HOST'] : $param_arr['0'];
    // 先查询缓存
    $shortUrlList = json_decode(file_get_contents(ROOT_PATH . '/config/Cache/ShortURL.json'), true);
    if(empty($shortUrlList[$string])){
        $sUrl = getShortURL_tinyurl($string);
        if(!$sUrl) return $string;
        $shortUrlList[$string] = getShortURL_tinyurl($string);
        file_put_contents(ROOT_PATH . '/config/Cache/ShortURL.json', json_encode($shortUrlList));
    }
    return $shortUrlList[$string];
}

/*
 *  功能: 蜘蛛模式下输出加粗连接
 *  参数一 连接标题 蜘蛛模式下会输出并加粗
 */
function tag_echoBoldTitle($param_arr){
    $string = empty($param_arr['0']) ? tag_getMainKeyword(array()) : $param_arr['0'];
    // 判断是否蜘蛛模式
    if(isSpider()){
        $retStr = "<b>{$string}</b>";
    }else{
        $retStr = tag_getKeywordRand(array());
    }
    return $retStr;
}




//**** End ****//
