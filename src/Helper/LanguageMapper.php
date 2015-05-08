<?php
/**
 * PHP Speller
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Speller\Helper;

/**
 * Map given list of language tags to supported ones
 *
 * @since x.xx
 */
class LanguageMapper
{
    /**
     * Map given list of language tags to supported ones
     *
     * @param string[] $requested list of requested languages
     * @param string[] $supported list of supported languages
     *
     * @return string[]
     *
     * @link  http://tools.ietf.org/html/bcp47
     * @since x.xx
     */
    public static function map(array $requested, array $supported)
    {
        $index = [];
        foreach ($supported as $tag) {
            $key = strtolower(preg_replace('/_-\./', '', $tag));
            $index[$key] = $tag;
        }

        $result = [];
        foreach ($requested as $source) {
            if (in_array($source, $supported, true)) {
                $result []= $source;
                continue;
            }

            $tag = strtolower(preg_replace('/_-\./', '', $source));
            foreach ($index as $key => $target) {
                if (substr($key, 0, strlen($tag)) == $tag) {
                    $result []= $target;
                    break;
                }
            }
        }
        return $result;
    }
}
