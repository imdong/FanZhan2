<?php
/*
 *  名称: 站群管理系统(基于多站改)
 *
 *  作者: 青石 (www.qs5.org)
 *  版本: v1.2
 *  最后更新时间: 2015年10月10日11:16:00
 *
 *  ========================================
 *
 *  说明，使用站点URI做文章唯一ID主键
 */

//**** 初始化配置 ****//
    define('START_TIME', microtime(true));  // 记录脚本开始执行时间
    define('ROOT_PATH', dirname(__FILE__).'/');
    
    require 'config.php';   // 载入用户配置文件
    // 导入核心配置文件
    require 'config/Core.config.php';

//**** Url路径解析处理 ****//
    // 非正常结尾默认按目录处理
    if(!preg_match('/\.[a-z]+|\/$/', $_SERVER['PATH_INFO'])) $_SERVER['PATH_INFO'].= '/';
    // 如果是 '/' 结尾则加上 index.html
    $sitePath = $_SERVER['PATH_INFO'];
    $sitePath.= substr($_SERVER['PATH_INFO'], -1) == '/' ? 'index.html' : '';
    define('SITE_PATH', $sitePath); // 网站路径
    $stieUrl = $_SERVER['HTTP_HOST'] . SITE_PATH;   // 路径地址
    // 域名后缀列表 二级域名一定要放在其根域名前面 比如 com.cn 必须在 .cn 前面否则 com 会认为是 域名部分 而不是后缀 
    $cDoaminSuffixList = 'com\.cn|net\.cn|org\.cn|gov\.cn|cn|com|net|org|gov|mobi|me|info|name|biz|cc|tv|asia|hk|tk';
    // 生成匹配域名的正则表达式
    $cDomainRegex = "#(?:(?<subdomain>[a-zA-Z0-9\-\.]+?)\.)??(?<domain>(?<name>[a-zA-Z0-9\-]+)\.(?<suffix>{$cDoaminSuffixList}))$#";
    // 进行正则匹配 域名信息
    if(!preg_match($cDomainRegex, $_SERVER['HTTP_HOST'], $cDomainInfo)){
        die('DomainE');   // 域名不正确 理论上永远不会走到这里 除非直接IP访问 或不存在的后缀
    }

    // 检查是否有静态缓存文件 有则直接输出并结束
    $ext = SITE_PATH;
    $sHtmlPath = (isSpider() && C('discern_spider')) ? 'spider/' : 'html/';
    $sHtmlPath.= $cDomainInfo['domain'] . '/' . $_SERVER['HTTP_HOST'] . $ext;
    if(file_exists($sHtmlPath)){
        die(file_get_contents($sHtmlPath));
    }

//**** 生成流程 初始化 ****//
    define('SITE_DOMAIN', $_SERVER['HTTP_HOST']);   // 完整域名
    
    define('SITE_MAIN_DOMAIN', $cDomainInfo['domain']); // 网站主域名
    define('SITE_SUB_DOMAIN', $cDomainInfo['subdomain']); // 网站子域名

//**** Url关系解析处理 ****//
    // 判断网站模式
    if($defaultConfig['sitemode'] == 0){    // 二级目录模式
        // 匹配Url中的关系
        if(!preg_match('#^/(?<name>[^/]+)?(?<path>/.+)?#', strtolower(SITE_PATH), $tSiteInfo)){
            die('Url Error');   // 理论上永远不可能执行到这里
        }

        if(empty($tSiteInfo['name'])) die('Not SiteE!');    // 直接访问根目录
        if(!isset($siteConfig[$tSiteInfo['name']])) die('Not Site!');   // 不存在的网站
        define('SITE_NAME', $tSiteInfo['name']);    // 将网站名存到常量
    }else if($defaultConfig['sitemode'] == 1){  // 泛站模式
        $cDomainList = readFile2Array($defaultConfig['domainlist']); // 获取域名列表
        // 判断主域名是否存在
        if(empty($cDomainList[SITE_MAIN_DOMAIN])) $cDomainList[SITE_MAIN_DOMAIN] = 'default';
        define('SITE_NAME', $cDomainList[SITE_MAIN_DOMAIN]);    // 将网站名存到常量
    }
    define('SITE_URI', SITE_DOMAIN . SITE_PATH);   // 将网站Url存入常量

// 每家医院的每个域名单独建立缓存文件夹
Cache::$Config['Cache_Dir'] = './config/Cache/'.SITE_NAME.'/'.SITE_MAIN_DOMAIN.'/'.SITE_SUB_DOMAIN;  // 修改默认缓存目录

//**** 读取站点匹配设置 判断当前页面类型 ****//
    $cRoute = C('route');   // 读取路由配置
    foreach($cRoute as $tRouteType => $tRouteRegex) {
        if(preg_match("#$tRouteRegex#", SITE_URI, $tRouteResult) == true){
            $tPageType = $tRouteType;
            $tplHtmlPath = C('tpl_'.$tRouteType);
            break;  // 找到结果则跳出循环
        }
    }
    // 判断类型为空则表示没找到 走404流程
    if(empty($tPageType)){
        $tPageType = '404';
        $tplHtmlPath = C('tpl_' . C('route_404'));
    }

//**** 模板标记生成处理 ****//

    // 根据是否蜘蛛进入不同模式
    if(isSpider() && C('discern_spider')){
        $cacheTag = 'Spider/';
    }else{
        $cacheTag = 'Html/';
    }

    // 检查生成缓存 如果没有 进入生成流程 有则跳过
    $pageTplHtml = S($cacheTag.SITE_URI);  // 首先读取缓存
    if(DEBUG) $pageTplHtml = false; // 调试模式不开启缓存
    if(!$pageTplHtml){
        $pageTplHtml = file_get_contents(array2RandValue($tplHtmlPath)); // 读取模板

        // 对模板标记进行正则替换
        $pageTplHtml = tplTagReplace($pageTplHtml, TPL_TAG_LS, TPL_TAG_R);
        // 替换蜘蛛标记
        $pageTplHtml = tplTagReplace($pageTplHtml, TPL_TAG_LR, TPL_TAG_R, 'spider');
        // 将生成结果输出到缓存
        S($cacheTag.SITE_URI, $pageTplHtml, null);
    }

    // 重新替换每次都会不同的标记
    $pageTplHtml = tplTagReplace($pageTplHtml, TPL_TAG_LT, TPL_TAG_R, 'temp');

// 处理404页面信息
$pageType = C('route_404');
if($tPageType == '404' && $pageType == '404'){
    // 说明未对404做正常页面处理 返回404
    header("HTTP/1.1 404 Not Found"); 
}

if(PAGE_COMPRES) $pageTplHtml = compress_html($pageTplHtml); // 页面压缩清除换行

// 获取输出编码
$tpl_charset = C('tpl_charset');
if($tpl_charset != 'utf-8') $pageTplHtml = iconv('utf-8', $tpl_charset.'//IGNORE', $pageTplHtml);

header('Content-Type:text/html;charset='.$tpl_charset);   // 定义协议头与编码

$pageTplHtml.= '<!-- Page Generation Time: '.date('c').' -->';    // 加入页面生成时间
/**** 生成静态文件 ****/
if(GEN_STATIC && !DEBUG){
    if($tPageType == 'list' || $tPageType == 'post' || $tPageType == 'tlist' ){
        if(_mkDir(dirname($sHtmlPath))){
            file_put_contents($sHtmlPath, $pageTplHtml);
        }
    }
}

// 获取 内存占用 运行时间
$exeTime = microtime(true) - START_TIME;
$pageTplHtml.= '<!-- Memory Size: '.memory_get_usage().'; Execution Time: '.$exeTime.'; -->';
exit($pageTplHtml); // 输出Html到页面

//**** 主程序结束 ****//
