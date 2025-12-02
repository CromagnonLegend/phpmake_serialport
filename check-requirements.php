#!/usr/bin/env php
<?php

function color($text, $color) {
    $colors = [
        'red' => "\033[0;31m",
        'green' => "\033[0;32m",
        'yellow' => "\033[1;33m",
        'blue' => "\033[0;34m",
        'reset' => "\033[0m",
    ];
    return $colors[$color] . $text . $colors['reset'];
}

function success($msg) {
    echo color("✓ ", 'green') . $msg . PHP_EOL;
}

function error($msg) {
    echo color("✗ ", 'red') . $msg . PHP_EOL;
}

function warning($msg) {
    echo color("⚠ ", 'yellow') . $msg . PHP_EOL;
}

function info($msg) {
    echo color("ℹ ", 'blue') . $msg . PHP_EOL;
}

echo PHP_EOL;
echo color("═══════════════════════════════════════════════════════════", 'blue') . PHP_EOL;
echo color("  PHPMake\\SerialPort Requirements Checker", 'blue') . PHP_EOL;
echo color("═══════════════════════════════════════════════════════════", 'blue') . PHP_EOL;
echo PHP_EOL;

$allRequirementsMet = true;

echo color("PHP Environment:", 'yellow') . PHP_EOL;
echo "────────────────────────────────────────────────────────────" . PHP_EOL;

$phpVersion = PHP_VERSION;
$phpVersionId = PHP_VERSION_ID;

if ($phpVersionId >= 70400) {
    success("PHP Version: $phpVersion (>= 7.4.0 required)");
} else {
    error("PHP Version: $phpVersion (>= 7.4.0 required)");
    $allRequirementsMet = false;
}

echo "  PHP Binary: " . PHP_BINARY . PHP_EOL;
echo "  SAPI: " . PHP_SAPI . PHP_EOL;

if (extension_loaded('Gorilla')) {
    success("Gorilla extension is already loaded!");
    $version = phpversion('Gorilla');
    echo "  Extension version: " . ($version ?: 'unknown') . PHP_EOL;
    if (class_exists('PHPMake\\SerialPort', false)) {
        success("SerialPort class is available");
    } else {
        warning("Gorilla loaded but SerialPort class not found");
    }
    echo PHP_EOL;
    info("Extension is already installed. You may want to rebuild if updating.");
    echo PHP_EOL;
    exit(0);
} else {
    info("Gorilla extension is not loaded (will be installed)");
}

echo PHP_EOL;

echo color("Build Tools:", 'yellow') . PHP_EOL;
echo "────────────────────────────────────────────────────────────" . PHP_EOL;

$requiredCommands = ['phpize', 'php-config', 'gcc', 'make', 'autoconf'];
$os = strtoupper(substr(PHP_OS, 0, 3));

foreach ($requiredCommands as $cmd) {
    $output = [];
    $returnVar = 0;

    if ($os === 'WIN') {
        exec("where $cmd 2>NUL", $output, $returnVar);
    } else {
        exec("command -v $cmd 2>/dev/null", $output, $returnVar);
    }

    if ($returnVar === 0 && !empty($output)) {
        success("$cmd: " . trim($output[0]));
    } else {
        error("$cmd: Not found");
        $allRequirementsMet = false;

        if ($cmd === 'phpize' || $cmd === 'php-config') {
            echo "    Install: ";
            if (file_exists('/etc/debian_version')) {
                echo "sudo apt-get install php-dev" . PHP_EOL;
            } elseif (file_exists('/etc/redhat-release')) {
                echo "sudo yum install php-devel" . PHP_EOL;
            } elseif ($os === 'DAR') {
                echo "brew install php" . PHP_EOL;
            } else {
                echo "Install PHP development headers" . PHP_EOL;
            }
        } elseif ($cmd === 'gcc' || $cmd === 'make') {
            echo "    Install: ";
            if (file_exists('/etc/debian_version')) {
                echo "sudo apt-get install build-essential" . PHP_EOL;
            } elseif (file_exists('/etc/redhat-release')) {
                echo "sudo yum groupinstall 'Development Tools'" . PHP_EOL;
            } elseif ($os === 'DAR') {
                echo "xcode-select --install" . PHP_EOL;
            } else {
                echo "Install C compiler and build tools" . PHP_EOL;
            }
        } elseif ($cmd === 'autoconf') {
            echo "    Install: ";
            if (file_exists('/etc/debian_version')) {
                echo "sudo apt-get install autoconf" . PHP_EOL;
            } elseif (file_exists('/etc/redhat-release')) {
                echo "sudo yum install autoconf" . PHP_EOL;
            } elseif ($os === 'DAR') {
                echo "brew install autoconf" . PHP_EOL;
            } else {
                echo "Install autoconf package" . PHP_EOL;
            }
        }
    }
}

echo PHP_EOL;

echo color("PHP Configuration:", 'yellow') . PHP_EOL;
echo "────────────────────────────────────────────────────────────" . PHP_EOL;

$extensionDir = ini_get('extension_dir');
if ($extensionDir && is_dir($extensionDir)) {
    success("Extension directory: $extensionDir");

    if (is_writable($extensionDir)) {
        success("Extension directory is writable");
    } else {
        warning("Extension directory is not writable (will need sudo)");
    }
} else {
    error("Extension directory not found: $extensionDir");
    $allRequirementsMet = false;
}

$iniFile = php_ini_loaded_file();
if ($iniFile) {
    success("Loaded php.ini: $iniFile");

    if (is_writable($iniFile)) {
        success("php.ini is writable");
    } else {
        warning("php.ini is not writable (will need sudo)");
    }
} else {
    warning("No php.ini file loaded");

    $scanDir = php_ini_scanned_files();
    if ($scanDir) {
        info("Additional .ini files are scanned");
        $scanDirPath = dirname(explode(',', $scanDir)[0]);
        if ($scanDirPath && is_dir($scanDirPath)) {
            info("Scan directory: $scanDirPath");
            if (is_writable($scanDirPath)) {
                success("Scan directory is writable");
            } else {
                warning("Scan directory is not writable (will need sudo)");
            }
        }
    }
}

echo PHP_EOL;

echo color("System Permissions:", 'yellow') . PHP_EOL;
echo "────────────────────────────────────────────────────────────" . PHP_EOL;

$currentUser = get_current_user();
success("Current user: $currentUser");

if ($os !== 'WIN') {
    $serialDevices = ['/dev/ttyUSB0', '/dev/ttyACM0', '/dev/ttyS0'];
    $foundDevice = false;

    foreach ($serialDevices as $device) {
        if (file_exists($device)) {
            $foundDevice = true;
            if (is_readable($device) && is_writable($device)) {
                success("Serial device $device is accessible");
            } else {
                warning("Serial device $device exists but not accessible");
                echo "    Fix: sudo usermod -a -G dialout $currentUser" . PHP_EOL;
                echo "         (then log out and log back in)" . PHP_EOL;
            }
        }
    }

    if (!$foundDevice) {
        info("No serial devices detected (connect hardware to test)");
    }
}

echo PHP_EOL;
echo color("═══════════════════════════════════════════════════════════", 'blue') . PHP_EOL;

if ($allRequirementsMet) {
    echo PHP_EOL;
    success("All requirements are satisfied!");
    echo PHP_EOL;
    echo color("Next steps:", 'green') . PHP_EOL;
    echo "  1. Run: composer run install-extension" . PHP_EOL;
    echo "     OR" . PHP_EOL;
    echo "     Run: bash install.sh" . PHP_EOL;
    echo "  2. Verify: composer run check-extension" . PHP_EOL;
    echo PHP_EOL;
    exit(0);
} else {
    echo PHP_EOL;
    error("Some requirements are missing!");
    echo PHP_EOL;
    echo color("Please install the missing requirements listed above.", 'yellow') . PHP_EOL;
    echo "Then run this script again to verify." . PHP_EOL;
    echo PHP_EOL;
    echo "For detailed installation instructions, see:" . PHP_EOL;
    echo "  https://github.com/cromagnonlegend/phpmake_serialport/blob/main/INSTALL.md" . PHP_EOL;
    echo PHP_EOL;
    exit(1);
}
