<?php

declare(strict_types = 1);

if (!function_exists('array_has_wildcard')) {
    /**
     * Analogue of function "array_has" with ability to search with wildcard.
     *
     * @param array $array
     * @param string $keys
     * @param bool $searchWithSegment
     * @return bool
     */
    function array_has_wildcard(
        array $array,
        string $keys,
        $searchWithSegment = false
    ): bool {
        if (null === $keys || !$array) {
            return false;
        }

        if (!\Illuminate\Support\Str::contains($keys, '*')) {
            return array_has($array, $keys);
        }

        $segments = explode('.', $keys);
        if ($segments === []) {
            return false;
        }
        $countSegments = count($segments);

        foreach ($segments as $i => $segment) {
            if (!is_array($array)) {
                return false;
            }
            $flag = false;
            if ($searchWithSegment &&
                $i !== ($countSegments - 1) &&
                $segments[$i + 1] === '*'
            ) {
                $grepKeys = preg_grep(
                    '/'.preg_quote($segment, '/').'\..*/',
                    array_keys($array)
                );
                if ($grepKeys) {
                    foreach ($grepKeys as $grepKey) {
                        $flag = array_has_wildcard(
                            $array[$grepKey],
                            implode('.', array_slice($segments, $i + 2)),
                            $searchWithSegment
                        );
                        if ($flag) {
                            return true;
                        }
                    }
                    return false;
                }
            }
            if (!$flag) {
                if ($segment === '*') {
                    if ($i + 1 === $countSegments) {
                        return !empty($array);
                    }
                    foreach ($array as $item) {
                        $flag = array_has_wildcard(
                            $item,
                            implode('.', array_slice($segments, $i + 1)),
                            $searchWithSegment
                        );
                        if ($flag) {
                            return true;
                        }
                    }
                    return false;
                } elseif (array_key_exists($segment, $array)) {
                    $flag = true;
                    $array = $array[$segment];
                }
            }
            if (!$flag) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('array_get_random')) {
    /**
     * @param array $array
     * @return mixed
     */
    function array_get_random(array $array)
    {
        $countArray = count($array);
        if ($countArray === 1) {
            return reset($array);
        }
        $key = array_rand($array);
        return $array[$key];
    }
}
