<?php 

// 如果两个或更多个数组元素有相同的键名，则最后的元素会覆盖其他元素。
var_dump(array_merge(['k1' => 'v1', 'k2' => 'v2'], ['k2' => 'vv2']));
var_dump(array_merge(['k1' => 'v1', 'k2' => 'v2'], ['k2' => 'vv2'], ['k2' => 'vvv2']));

