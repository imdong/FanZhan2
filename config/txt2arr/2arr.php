<?php
$domainStr = 'abcd:test.cn|teds.cn';


$domainListA = explode("\r\n", $domainStr);

foreach ($domainListA as $value) {
    $domainListB = explode(':', $value);
    $tName = $domainListB['0'];
    $domainListB = explode('|', $domainListB['1']);
    foreach ($domainListB as $vDomain){
        $arrayName[$vDomain] = $tName;
    }
}

$Str = serialize($arrayName);
file_put_contents('domainlist.serialize', $Str);
