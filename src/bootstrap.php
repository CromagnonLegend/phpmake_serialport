<?php
/** Checks if Gorilla extension is loaded and provides helper functions */

namespace PHPMake\SerialPort;

if (!extension_loaded('Gorilla')) {
    $errorMessage = <<<'EOT'

╔════════════════════════════════════════════════════════════════════════════╗
║                                                                            ║
║    ERROR: Gorilla extension is not loaded                                  ║
║                                                                            ║
║  The PHPMake\SerialPort package requires the Gorilla C extension to be     ║
║  compiled and loaded in your PHP installation.                             ║
║                                                                            ║
║  Quick Installation:                                                       ║
║  ──────────────────────────────────────────────────────────────────────    ║
║                                                                            ║
║  1. Navigate to vendor/cromagnonlegend/phpmake_serialport/                 ║
║  2. Run: composer run install-extension                                    ║
║     OR                                                                     ║
║     Run: bash install.sh                                                   ║
║                                                                            ║
║  Manual Installation:                                                      ║
║  ──────────────────────────────────────────────────────────────────────    ║
║                                                                            ║
║  On Linux/macOS:                                                           ║
║    $ phpize                                                                ║
║    $ ./configure --enable-Gorilla                                          ║
║    $ make                                                                  ║
║    $ sudo make install                                                     ║
║    $ echo "extension=Gorilla.so" | sudo tee -a $(php --ini | grep          ║
║      "Loaded Configuration" | sed -e "s|.*:\s*||")                         ║
║                                                                            ║
║  On Windows:                                                               ║
║    See INSTALL.WINDOWS.md for detailed instructions                        ║
║                                                                            ║
║  Requirements:                                                             ║
║  ──────────────────────────────────────────────────────────────────────    ║
║  - PHP development headers (php-dev or php-devel package)                  ║
║  - C compiler (gcc, clang, or Visual Studio)                               ║
║  - autoconf and automake tools                                             ║
║                                                                            ║
║  After installation, verify with:                                          ║
║    $ php -m | grep Gorilla                                                 ║
║    $ composer run check-extension                                          ║
║                                                                            ║
║  For more information, see:                                                ║
║    https://github.com/cromagnonlegend/phpmake_serialport                   ║
║                                                                            ║
╚════════════════════════════════════════════════════════════════════════════╝

EOT;

    if (PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg') {
        fwrite(STDERR, $errorMessage);
    }

    throw new \RuntimeException(
        "Gorilla extension is not loaded. This package requires the compiled C extension. " .
        "Run 'composer run install-extension' or see the documentation for installation instructions."
    );
}

if (!function_exists('PHPMake\SerialPort\check_extension')) {
    function check_extension(): array
    {
        return [
            'loaded' => extension_loaded('Gorilla'),
            'version' => phpversion('Gorilla') ?: 'unknown',
            'SerialPort_class_exists' => class_exists('PHPMake\\SerialPort', false),
        ];
    }
}

if (!class_exists('PHPMake\\SerialPort', false)) {
    throw new \RuntimeException(
        "Gorilla extension is loaded but the SerialPort class is not available. " .
        "The extension may be incompatible with your PHP version or improperly compiled."
    );
}
