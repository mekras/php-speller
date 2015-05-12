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
 * File as text source
 *
 * @since x.xx
 */
class FileSource implements Source
{
    /**
     * File name
     *
     * @var string
     * @since x.xx
     */
    protected $filename;

    /**
     * Create new source
     *
     * @param string $filename
     *
     * @since x.xx
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
     * @since x.xx
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
     * @since x.xx
     */
    public function getFilename()
    {
        return (string) $this->filename;
    }
}
