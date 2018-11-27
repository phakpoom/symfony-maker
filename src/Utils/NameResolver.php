<?php

namespace Bonn\Maker\Utils;

class NameResolver
{
    /**
     * @param string $string
     * @return string|null
     */
    public static function resolveOnlyClassName(string $string): string
    {
        $arr = explode('\\', $string);

        return end($arr);
    }

    /**
     * @param string $string
     * @return string
     */
    public static function resolveNamespace(string $string): string
    {
        $explodeClassName = explode('\\', $string);

        return implode('\\', array_slice($explodeClassName, 0, count($explodeClassName) - 1));
    }

    /**
     * @param string $string
     * @return string
     */
    public static function camelToUnderScore(string $string): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $string, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }
}
