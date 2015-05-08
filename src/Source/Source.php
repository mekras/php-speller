<?php
/**
 * PHP Speller
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Speller\Source;

/**
 * Text source interface
 *
 * @since 1.00
 */
interface Source
{
    /**
     * Return text as one string
     *
     * @return string
     *
     * @since 1.00
     */
    public function getAsString();
}
