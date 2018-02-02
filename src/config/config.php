<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dirs
    |--------------------------------------------------------------------------
    |
    | Folders in which you need to check code style.
    |
    */

    'dirs' => [
        'app/'
    ],

    /*
    |--------------------------------------------------------------------------
    | phpCs Arguments
    |--------------------------------------------------------------------------
    |
    | Additional argument for php code sniffer.
    | @see https://github.com/squizlabs/PHP_CodeSniffer/wiki/Usage#getting-help-from-the-command-line
    |
    */

    'arguments' => [
        '--standard=PSR2',
        '--colors',
    ]
];
