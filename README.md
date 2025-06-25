# Gorilla — PHPMake\SerialPort

I decided to make this package user-friendly by automating the process for installation of the package and making it composer-friendly

A PHP extension for communicating with serial-port devices (e.g. Arduino, AVR, PIC) on POSIX systems (Linux/macOS) and Windows.
This is a standalone PHP extension created using CodeGen_PECL 1.1.3
It[the package] was tested only on Linux so if it doesn't work on your machine or you find any non-breaking bugs reach me out - i'll try fixing 'em

### Note: I recommend, if you're on Windows OS, to stick to the quick install via Composer

### Credits to the original creator of this package: https://github.com/kahagon <Kenichi Ahagon>

---

## Quick Install via Composer

```bash
composer require cromagnonlegend/phpmake_serialport
composer install
```
---

## Building from Source on UNIX-like Systems

If you prefer to build manually, or need to run tests, do:

```bash
phpize
./configure --enable-Gorilla
make
make test
sudo make install
```

---
