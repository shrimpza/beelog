<?php

function rsToArray($rs) {
    $arr = $rs->fetchAll(PDO::FETCH_ASSOC);
    $res = array();
    for ($i = 0; $i < count($arr); $i++)
        $res[] = array_change_key_case($arr[$i], CASE_LOWER);
    return $res;
}

function objectToArray($obj, $ignoreClasses = array()) {
    return _objectToArray($obj, $ignoreClasses);
}

function _objectToArray($obj, $ignoreClasses = array()) {
    $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
    if (is_array($_arr)) {
        $arr = array();
        foreach ($_arr as $key => $val) {
            if (is_object($val) && in_array(get_class($val), $ignoreClasses))
                continue;
            else {
                $val = (is_array($val) || is_object($val)) ? _objectToArray($val, $ignoreClasses) : $val;
                $arr[$key] = $val;
            }
        }
    } else
        $arr = null;
    return $arr;
}

if (!function_exists('json_encode')) {
    require_once('json.php');

    function json_encode($value) {
        $json = new Services_JSON();
        return $json->encode($value);
    }

    function json_decode($value) {
        $json = new Services_JSON();
        return $json->decode($value);
    }

}
?>