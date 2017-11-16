<?php
/*
 *  模板替换标记方法文件
 *
 */

// 返回替换标签
return array(
    /* 重要 关键字对应 */
    '主关键词'      =>  'getMainKeyword',  // 取当前页面主关键词 主关键词()  如果传递参数 则此页面的关键词 固定
    '文章关键字'    =>  'getPostKey',   // 取文章关键词    文章关键字(文章ID/地址Url)
    '文章标题'      =>  'getPostKey',   //   文章关键字 的别名
    '文章地址'      =>  'getPostUrl',   // 取文章Url   文章地址(文章ID)
    '随机关键词'    =>  'getKeywordRand',   // 随机取一个关键词  随机关键词()
    '随机描述'      =>  'getRandDescription',   // 取随机描述
    '随机文章ID'    =>  'getRandPostID', // 获取已经生成过的文章ID    随机文章ID()
    '取域名'        =>  'getDomain',   // 取域名 参数为 主 或子 不写则获取完整
    '随机域名'      =>  'getRandDomain',   // 随机取域名
    '随机外链'      =>  'getRandExternalLink',   // 从外链列表中随机取出一条外链

    /* 辅助 常用功能函数 */
    '随机句子'      =>  'getRandStr',  // 随机取N条句子   随机句子(句子条数) 默认随机使用，。分隔
    '随机段落'      =>  'getRandParagraph',  // 随机取一条记录 有一个参数 插入内容 为空不插入
    '随机数字'      =>  'getRandNum',  // 取随机数字 根据长度范围  随机数字(最小位数, 最大长度) 都可选 默认3-5
    '随机字母'      =>  'getRandLetter',    // 取随机字母 随机字母(最小长度, 最大长度, Aa0)   默认3-5 全小写
    '随机人名'      =>  'getRandUName',    // 取随机人名 随机人名() 不带参数
    '随机成语'      =>  'getRandProverb',    // 取随机人名 随机人名() 不带参数
    '时间'          =>  'getTime',    // 取时间(时间格式,加减操作) 时间格式可写为 年/月/日 周 时:分:秒  +10时
    '随机图片'      =>  'getRandImg',   // 取随机图片 无参数
    '随机视频'      =>  'getRandVideo',   // 取随机视频 无参数
    '随机地名'      =>  'getRandPlaceName',   // 取随机地名 无参数
    '随机网站类别'  =>  'getRandSiteType',   // 取随机网站类型 无参数
    '随机网站名称'  =>  'getRandSiteName',   // 取随机网站名 无参数
    '编码'          =>  'str2Unicode', // 输出编码后的文本
    '随机栏目名'    =>  'getRandColumnName',  // 随机返回一个栏目名称
    '取栏目标题'    =>  'getRandColumnTtile',  // 返回一个栏目的标题，参数一为要取标题的拼音标示，不填为随机
    '输出链接'      =>  'echoALink',   // 输出a链接标签
    '蜘蛛_主关键词' =>  'echoBoldTitle',   // 蜘蛛模式专用 如果是 蜘蛛模式 则输出 参数的内容 并加粗 否则随机取一个关键词
    '短网址'        =>  'getShortURL', // 取短网址 有一个参数 需要取短网址的地址，默认为当前域名

    /* 便捷方法 */
    '取随机文章ID_按日期'    =>  'easy_getRandPostIDinDate', // 输出类似 2015100912345 这样的文章ID
    '主关键词_编码'          =>  'easy_getMainKeywordUnicode', // 输出经过编码的主关键词
    '文章关键字_编码'        =>  'easy_getPostKeyUnicode', // 获取文章关键字 经过编码的
    '随机关键词_编码'        =>  'easy_getKeywordRandUnicode',   // 随机取一个关键词  编码
    '随机描述_编码'          =>  'easy_getRandDescriptionUnicode',   // 取随机描述 编码

    /* 蜘蛛模式专用代码 */

    /* 扩展功能 */
    '输出'          =>  'echo',   // 输出文本 参数可无限添加,会依次输出
    'php'           =>  'EvalPHP',  // 直接运行原生PHP 代码
);

//**** End ****//
