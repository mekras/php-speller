<?php
/**
 * PHP Speller.
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Source;

use Mekras\Speller\Exception\SourceException;

/**
 * Convert text encoding using iconv.
 *
 * @since x.x
 */
class IconvSource extends MetaSource
{
    /**
     * Output encoding.
     *
     * @var string
     */
    private $encoding;

    /**
     * Create new converter.
     *
     * @param EncodingAwareSource $source   Original source.
     * @param string              $encoding Output encoding (default to "UTF-8").
     *
     * @since x.x
     */
    public function __construct(EncodingAwareSource $source, $encoding = 'UTF-8')
    {
        parent::__construct($source);
        $this->encoding = (string) $encoding;
    }

    /**
     * Return text in the specified encoding.
     *
     * @return string
     *
     * @throws SourceException
     *
     * @since x.x
     */
    public function getAsString()
    {
        $text = iconv(
            $this->source->getEncoding(),
            $this->getEncoding(),
            $this->source->getAsString()
        );
        if (false === $text) {
            throw new SourceException('iconv failed to convert source text');
        }

        return $text;
    }

    /**
     * Return output text encoding.
     *
     * @return string
     *
     * @since x.x
     */
    public function getEncoding()
    {
        return $this->encoding;
    }
}
