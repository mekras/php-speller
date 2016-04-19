<?php
/**
 * PHP Speller
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Mekras\Speller\Source;

/**
 * File as text source
 *
 * @since 1.2
 */
class FileSource implements Source
{
    /**
     * File name
     *
     * @var string
     * @since 1.2
     */
    protected $filename;

    /**
     * Create new source
     *
     * @param string $filename
     *
     * @since 1.2
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Return text as one string
     *
     * @return string
     *
     * @since 1.2
     */
    public function getAsString()
    {
        return file_get_contents($this->filename);
    }

    /**
     * Return file name with text to check
     *
     * This can be used for backends with file checking support
     *
     * @return string
     *
     * @since 1.2
     */
    public function getFilename()
    {
        return (string) $this->filename;
    }
}
