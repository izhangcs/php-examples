<?php 

function test_strpos() {
    var_dump(strpos("http://test-mvp.baidu.com/shares?utm_source=zhidao_app", "mvp.baidu.com/shares"));
}

//test_strpos();

function test_iconv()
{
    $res = iconv("UTF-8", "GBK", "公司");
    var_dump($res);
    $res = iconv("UTF-8", "GBK//IGNORE", "公司");
    var_dump($res);
    $res = iconv("UTF-8", "GBK//TRANSLIT", "公司");
    var_dump($res);
}

test_iconv();