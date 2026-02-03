<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ClamAV Binary Path
    |--------------------------------------------------------------------------
    |
    | The path to the clamscan executable. By default, it assumes clamscan
    | is in your system PATH. If you have a custom installation, provide
    | the full path to the binary.
    |
    */

    'binary' => env('CLAMAV_BINARY', 'clamscan'),

    /*
    |--------------------------------------------------------------------------
    | Scan Timeout
    |--------------------------------------------------------------------------
    |
    | The maximum time in seconds to wait for a scan to complete. Large
    | files may require more time. Set to null for no timeout (not recommended).
    |
    */

    'timeout' => (int) env('CLAMAV_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Scan Options
    |--------------------------------------------------------------------------
    |
    | Additional options to pass to clamscan. These are passed directly
    | to the command line. Common options include:
    |
    | --no-summary: Suppress summary output
    | --infected: Only print infected files
    | --remove: Remove infected files (use with caution)
    |
    */

    'scan_options' => [
        '--no-summary',
    ],

];
