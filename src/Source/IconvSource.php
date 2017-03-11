<?php
/**
 * PHP Speller.
 *
 * @copyright 2015, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Source;

/**
 * Convert text encoding using iconv.
 *
 * @since x.x
 */
class IconvSource implements EncodingAwareSource
{
    /**
     * Original source.
     *
     * @var EncodingAwareSource
     */
    private $source;

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
        $this->source = $source;
        $this->encoding = (string) $encoding;
    }

    /**
     * Return text in the specified encoding.
     *
     * @return string
     *
     * @since 1.0
     */
    public function getAsString()
    {
        return iconv(
            $this->source->getEncoding(),
            $this->getEncoding(),
            $this->source->getAsString()
        );
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
