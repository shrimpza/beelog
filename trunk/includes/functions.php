<?php

    /**
     * Converts a PDO result set to a simple array
     */
    function rsToArray($rs) {
        $arr = $rs->fetchAll(PDO::FETCH_ASSOC);
        $res = array();
        for ($i = 0; $i < count($arr); $i++)
            $res[] = array_change_key_case($arr[$i], CASE_LOWER);
        return $res;
    }

    /**
     * Convert any object structure to an array
     */
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
        } else {
            $arr = null;
        }
        return $arr;
    }

    /**
     * Returned a suitably escaped string for use as a "slug"
     */
    function slugify($text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    /**
     * Frequently a JSON module is not available for some or other reason in certain
     * installations, so use a pure PHP drop-in solution. 
     */
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