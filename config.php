<?php
// 默认配置 * 号会被替换为网站名称
$diyConfig = array(
    'sitemode'      =>  0,    // 运行模式 0 为 二级目录  1 为 泛站
    'keywords'      =>  'config/data/keywords/keywords.serialize',  // 随机关键词抽取文件
    'description'   =>  'config/data/description/description.serialize',   // 随机描述抽取文件

    'externallink'  =>  'config/data/externallink/externallink.txt', // 外链列表
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
);

// 网站单独配置
$siteConfig = array(
    'default' => array(
        'type'          =>  'nk',
        '医院名称'      =>  '默认医院',
        '桌面网址'      =>  'http://www.adload.cc/pc?sznk',
        '手机网址'      =>  'http://www.adload.cc/mobile?sznk',
        '头部通用脚本'  =>  '<!-- 预留代码 -->',
    )
);

//**** End ****//
