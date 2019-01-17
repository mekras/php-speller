<?php
declare(strict_types=1);

/**
 * PHP Speller.
 *
 * @copyright 2017, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author    Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Mekras\Speller\Exception;

/**
 * External program execution failed.
 *
 * @since 1.6
 */
class ExternalProgramFailedException extends RuntimeException
{
    /**
     * Create new exception.
     *
     * @param string          $command  Failed command
     * @param string          $message  Error output.
     * @param int             $code     Exit code
     * @param \Exception|null $previous Previous exception if any.
     *
     * @since 1.6
     */
    public function __construct($command, $message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct(
            sprintf('Failed to execute "%s": %s', $command, $message),
            $code,
            $previous
        );
    }
}
