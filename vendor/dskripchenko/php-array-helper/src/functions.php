<?php
if (!function_exists('array_merge_deep')) {

    /**
     * @param array $a
     * @param array $b
     * @return mixed
     */
    function array_merge_deep($a, $b)
    {
        $args = func_get_args();
        $res = array_shift($args);
        while (!empty($args)) {
            foreach (array_shift($args) as $k => $v) {
                if (is_int($k)) {
                    if (array_key_exists($k, $res)) {
                        $res[] = $v;
                    } else {
                        $res[$k] = $v;
                    }
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = array_merge_deep($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }
        return $res;
    }
}

if (!function_exists('array_sort_deep')) {

    /**
     * @param array $array
     * @param int $flags
     * @return array
     */
    function array_sort_deep(array $array, int $flags = SORT_REGULAR): array
    {
        $sorting = static function (&$array, int $flags) use (&$sorting) {
            if (is_array($array)) {
                ksort($array, $flags);
                foreach ($array as $key => &$value) {
                    $sorting($value, $flags);
                }
            }
        };

        $sorting($array, $flags);

        return $array;
    }
}

if (!function_exists('array_is_associative')) {

    /**
     * @param array $array
     * @return bool
     */
    function array_is_associative(array $array): bool
    {
        return is_array($array) && array_diff_key($array, array_keys(array_keys($array)));
    }
}

if (!function_exists('array_get_signature')) {

    /**
     * @param array $array
     * @param string|null $secret
     * @param string|null $salt
     * @param callable|null $hashing
     * @param int $flags
     *
     * @return string
     */
    function array_get_signature(
        array $array,
        ? string $secret = null,
        ? string $salt = null,
        ? callable $hashing = null,
        int $flags = JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_NUMERIC_CHECK
    ): string {
        $hashing = $hashing ?: static function(string $str) {
            return md5($str);
        };

        $array = array_sort_deep($array);
        $raw = json_encode($array, $flags);

        $rawString = "{$secret}{$raw}{$salt}";
        return $hashing($rawString);
    }
}