# PHPMake\SerialPort (Gorilla Extension)

A high-performance PHP extension for communicating with serial port devices (e.g., Arduino, AVR, PIC microcontrollers) via COM ports on Windows or TTY devices on Linux/macOS.

This package provides a Composer-friendly wrapper around the **Gorilla** C extension, originally created by [Kenichi Ahagon](https://github.com/kahagon).

## Features

- Fast native C implementation for optimal performance
- Cross-platform support: Linux, macOS, and Windows
- Full serial port control: baud rate, parity, stop bits, flow control
- Canonical and non-canonical modes for text and binary communication
- Hardware flow control: RTS/CTS, DTR/DSR support
- Composer integration for easy dependency management
- IDE-friendly with PHPDoc stubs for autocompletion

## Installation

### Requirements

- PHP 7.4 or higher
- C compiler (gcc, clang, or MSVC)
- PHP development headers
- autoconf and make

### Quick Install

```bash
composer require cromagnonlegend/phpmake_serialport

cd vendor/cromagnonlegend/phpmake_serialport

php check-requirements.php

composer run install-extension

composer run check-extension
```

### Platform-Specific Setup

<details>
<summary><b>Linux (Ubuntu/Debian)</b></summary>

```bash
sudo apt-get update
sudo apt-get install -y php-dev build-essential autoconf

composer require cromagnonlegend/phpmake_serialport
cd vendor/cromagnonlegend/phpmake_serialport

bash install.sh

sudo usermod -a -G dialout $USER
```
</details>

<details>
<summary><b>macOS</b></summary>

```bash
xcode-select --install

brew install php autoconf

composer require cromagnonlegend/phpmake_serialport
cd vendor/cromagnonlegend/phpmake_serialport

bash install.sh
```
</details>

<details>
<summary><b>Windows</b></summary>

Windows installation requires Visual Studio and the PHP SDK. See [INSTALL.WINDOWS.md](INSTALL.WINDOWS.md) for detailed instructions.

Alternatively, pre-compiled DLLs may be available in the releases section.
</details>

---

## Quick Start

```php
<?php
require 'vendor/autoload.php';

use PHPMake\SerialPort;

$port = new SerialPort();

try {
    $port->open('/dev/ttyUSB0');

    $port->setBaudRate(SerialPort::BAUD_RATE_9600);
    $port->setFlowControl(SerialPort::FLOW_CONTROL_NONE);
    $port->setCanonical(false)
         ->setVTime(1)
         ->setVMin(0);

    $data = $port->read(256);
    echo "Received: " . bin2hex($data) . "\n";

    $bytesWritten = $port->write("Hello Arduino!\n");
    echo "Sent $bytesWritten bytes\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    if ($port->isOpen()) {
        $port->close();
    }
}
```

---

## Documentation

### Opening and Closing Ports

```php
$port = new SerialPort();

$port->open('/dev/ttyUSB0');

$port = new SerialPort('/dev/ttyUSB0');

if ($port->isOpen()) {
    echo "Port is open\n";
}

$port->close();
```

### Configuring Serial Parameters

```php
$port->setBaudRate(SerialPort::BAUD_RATE_115200);
$currentBaud = $port->getBaudRate();

$port->setCharSize(SerialPort::CHAR_SIZE_8);

$port->setParity(SerialPort::PARITY_NONE);

$port->setNumOfStopBits(SerialPort::STOP_BITS_1_0);

$port->setFlowControl(SerialPort::FLOW_CONTROL_NONE);
```

### Reading and Writing Data

```php
$data = $port->read(256);

$bytesWritten = $port->write("Hello\n");

$bytesWritten = $port->write("\x01\x02\x03\x04");

$port->flush();
```

### Canonical vs Non-Canonical Mode

```php
$port->setCanonical(false)
     ->setVMin(1)
     ->setVTime(10);

$port->setCanonical(true);
```

For more details on VMIN and VTIME behavior, see: [termios VMIN/VTIME](http://www.unixwiz.net/techtips/termios-vmin-vtime.html)

### Hardware Flow Control

```php
$port->setRTS(true);
$rts = $port->getRTS();

$port->setDTR(true);
$dtr = $port->getDTR();

$cts = $port->getCTS();

$dsr = $port->getDSR();

$dcd = $port->getDCD();

$rng = $port->getRNG();
```

### Available Constants

```php
SerialPort::BAUD_RATE_9600
SerialPort::BAUD_RATE_19200
SerialPort::BAUD_RATE_38400
SerialPort::BAUD_RATE_57600
SerialPort::BAUD_RATE_115200
SerialPort::BAUD_RATE_230400

SerialPort::CHAR_SIZE_5
SerialPort::CHAR_SIZE_6
SerialPort::CHAR_SIZE_7
SerialPort::CHAR_SIZE_8

SerialPort::PARITY_NONE
SerialPort::PARITY_ODD
SerialPort::PARITY_EVEN
SerialPort::PARITY_MARK
SerialPort::PARITY_SPACE

SerialPort::FLOW_CONTROL_NONE
SerialPort::FLOW_CONTROL_HARD
SerialPort::FLOW_CONTROL_SOFT
```

---

## Examples

Check the [examples/](examples/) directory for complete working examples:

- **[basic-usage.php](examples/basic-usage.php)** - Simple echo server example
- **[write-read.php](examples/write-read.php)** - Write and read data
- **[dump-status.php](examples/dump-status.php)** - Display port configuration
- **[arduino/](examples/arduino/)** - Arduino communication examples
- **[echo/](examples/echo/)** - Echo server implementations
- **[file-transfer/](examples/file-transfer/)** - File transfer examples

---

## Troubleshooting

### Extension Not Loading

```bash
php -m | grep Gorilla

php -d display_errors=1 -r "var_dump(extension_loaded('Gorilla'));"

php --ini
```

### Permission Denied on Serial Port (Linux)

```bash
sudo usermod -a -G dialout $USER

sudo chmod 666 /dev/ttyUSB0
```

### Compilation Errors

```bash
cd vendor/cromagnonlegend/phpmake_serialport
make clean
phpize --clean

composer run install-extension
```

---

## Testing

```bash
cd vendor/cromagnonlegend/phpmake_serialport
make test

php examples/basic-usage.php /dev/ttyUSB0
```

---

## Credits

- **Original Author**: [Kenichi Ahagon](https://github.com/kahagon) - Creator of the Gorilla extension
- **Composer Integration**: CromagnonLegend - Modernized packaging and documentation

---

## Important Notes

- This package requires a **compiled C extension** to function
- The extension must be installed separately after `composer install`
- Tested primarily on Linux; Windows and macOS support may vary
- Serial port access requires appropriate system permissions

---
