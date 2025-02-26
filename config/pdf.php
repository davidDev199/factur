<?php

return [
    'wkhtmltopdf_bin' => strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'
        ? env('WKHTMLTOPDF_BINARIES_WINDOWS', 'C:/Program Files/wkhtmltopdf/bin/wkhtmltopdf.exe')
        : env('WKHTMLTOPDF_BINARIES_LINUX', '/usr/bin/wkhtmltopdf'),
];
