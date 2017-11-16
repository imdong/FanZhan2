<?php
// 开关定义说明  false = 否   true = 是
define('DEBUG', true);  // 调试模式开关
define('GEN_STATIC', false); // 全部生成静态
define('PAGE_COMPRES', false);   // 对生成的页面进行换行压缩(删除换行)

// 默认配置 * 号会被替换为网站名称
$defaultConfig = array(
    // 需要自动序列化的配置项
    'autoserialize' =>  array(
        'domainlist',   'keywords', 'description',  'firstname',    'proverb',
        'videourl',     'sitename',
    ),

    'domainlist'    =>  'config/data/domainlist.serialize',  // 网站域名关系表
    'firstname'     =>  'config/data/xingming.serialize',  // 姓名列表
    'proverb'       =>  'config/data/proverb.serialize',  // 成语列表
    'videourl'      =>  'config/data/video.serialize',    // 视频列表
    'sitename'      =>  'config/data/sitename.serialize',   // 网站名
    'placename'     =>  'config/data/placename.txt',   // 地名
    'sitetype'      =>  'config/data/sitetype.txt',   // 网站类型
    'columnname'    =>  'config/data/columnname.serialize',    // 网站分类关系
    'imgpath'       =>  'asset/images/',          // 图片列表文件夹

    'keywords'      =>  'config/data/keywords/keywords.serialize',  // 随机关键词抽取文件
    'description'   =>  'config/data/description/description.txt',   // 随机描述抽取文件

    /* Url路由匹配规则 */
    'route'         =>  array(
        // 'index'     =>  '(/|index.html)$',  // 首页Url断定规则
        // 'list'      =>  '/list(?:-(?<page>[0-9]+))?\.html', // 列表页Url断定规则
        'index'     =>  "^{$_SERVER['HTTP_HOST']}/(index\.html)?$",  // 首页Url断定规则
        'list'      =>  '(/|index\.html|list-[0-9]+\.html)$', // 列表页Url断定规则
        'post'      =>  '/(?<id>[a-zA-Z0-9\-/]+)(?<=[^/])\.html$',  // 文章页Url断定规则
    ),
    'route_404'     =>  '404',  // 404页面指定页面类型

    /* 模板文件设置 */    // 依据 Url路由规则的结果来执行的
    'tpl_index'     =>  array(  // 首页模板
        'config/tpl/index.html',
    ),

    'tpl_list'      =>  array(  // 列表页模板
        'config/tpl/list.html',
    ),

    'tpl_post'      =>  array(  // 文章页模板
        'config/tpl/post.html',
    ),

    'tpl_404'       =>  array(  // 404错误页模板
        'config/tpl/404.html',
    ),

    /* 替换标签解析标记 */
    'tpl_rel_ls'    =>  '<@',   // 标记左部 固定
    'tpl_rel_lt'    =>  '<#',   // 标记左部 临时
    'tpl_rel_lr'    =>  '<~',   // 标记左部 蜘蛛
    'tpl_rel_r'     =>  '>',    // 固定替换标记

    /* 模板生成编码 */
    'tpl_charset'   =>  'utf-8',    // 全小写 默认utf-8 不转码

    /* 是否区别蜘蛛显示 */
    'discern_spider' =>  false, // 此模式为蜘蛛池专用 如果开启 则 蜘蛛 显示 和 非蜘蛛显示不同

    /* 泛站用 */
    'type'          =>  'nk',
);

// 混淆段落文件

$defaultConfig['paragraph'] = glob(ROOT_PATH.'config/paragraph/*.*');

// 混淆句子文件
$defaultConfig['confused'] = array(
    // 男科句子
    'nk'    =>  array(
        'config/ciku/nk_1.serialize'
    )
);

//**** 自定义配置结束 下面无需修改 ****//

$defaultConfig = array_merge($defaultConfig, $diyConfig);

// 模板替换标记列表
$ReplaceTagS = require ROOT_PATH.'replaceTag.php';

//**** 引用function ****//
require ROOT_PATH.'config/include/Cache.class.php';   // 引入缓存类
require ROOT_PATH.'config/include/Functions.php';   // 引入扩展方法
require ROOT_PATH.'config/include/TagFunctions.php';   // 引入模板替换方法
require ROOT_PATH.'config/include/EasyTagFunctions.php';   // 引入模板替换方法

// 设置全局变量
define('TPL_TAG_LS', $defaultConfig['tpl_rel_ls']);
define('TPL_TAG_LT', $defaultConfig['tpl_rel_lt']);
define('TPL_TAG_LR', $defaultConfig['tpl_rel_lr']);

define('TPL_TAG_R', $defaultConfig['tpl_rel_r']);
