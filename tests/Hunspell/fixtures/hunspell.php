<?php
/**
 * hunspell binary stub
 */
if ($argc > 1 && '-D' === $argv[1]) {
    fwrite(STDERR, file_get_contents(__DIR__ . '/dicts.txt'));
    exit(0);
}

if (in_array('-a', $argv, true)) {
    fwrite(STDOUT, file_get_contents(__DIR__ . '/check.txt'));
    exit(0);
}
