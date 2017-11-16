<?php
/*
 *  便捷标签方法
 *
 */

// 取随机文章ID_按日期
function tag_easy_getRandPostIDinDate($param_arr){
    $dateStr = date('Ymd'); // 获取当前时间
    $randInt = tag_getRandNum($param_arr);  // 取随机数
    return $dateStr . $randInt;
}
// 主关键词_编码
function tag_easy_getMainKeywordUnicode($param_arr){
    $keyword = tag_getMainKeyword($param_arr);  // 获取关键词
    $keyword = tag_str2Unicode(array($keyword));    // 进行编码
    return $keyword;
}
// 文章关键字_编码
function tag_easy_getPostKeyUnicode($param_arr){
    $keyword = tag_getPostKey($param_arr);  // 获取关键词
    $keyword = tag_str2Unicode(array($keyword));    // 进行编码
    return $keyword;
}
// 随机关键词_编码
function tag_easy_getKeywordRandUnicode($param_arr){
    $keyword = tag_getKeywordRand($param_arr);  // 获取关键词
    $keyword = tag_str2Unicode(array($keyword));    // 进行编码
    return $keyword;
}
// 随机描述_编码
function tag_easy_getRandDescriptionUnicode($param_arr){
    $keyword = tag_getRandDescription($param_arr);  // 获取关键词
    $keyword = tag_str2Unicode(array($keyword));    // 进行编码
    return $keyword;
}
