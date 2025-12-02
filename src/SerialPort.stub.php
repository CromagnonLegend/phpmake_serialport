<?php

namespace PHPMake;

/** Stub file providing IDE autocompletion for the Gorilla C extension SerialPort class */
class SerialPort
{
    public const BAUD_RATE_50 = 50;
    public const BAUD_RATE_75 = 75;
    public const BAUD_RATE_110 = 110;
    public const BAUD_RATE_134 = 134;
    public const BAUD_RATE_150 = 150;
    public const BAUD_RATE_200 = 200;
    public const BAUD_RATE_300 = 300;
    public const BAUD_RATE_600 = 600;
    public const BAUD_RATE_1200 = 1200;
    public const BAUD_RATE_1800 = 1800;
    public const BAUD_RATE_2400 = 2400;
    public const BAUD_RATE_4800 = 4800;
    public const BAUD_RATE_9600 = 9600;
    public const BAUD_RATE_19200 = 19200;
    public const BAUD_RATE_38400 = 38400;
    public const BAUD_RATE_57600 = 57600;
    public const BAUD_RATE_115200 = 115200;
    public const BAUD_RATE_230400 = 230400;

    public const CHAR_SIZE_5 = 5;
    public const CHAR_SIZE_6 = 6;
    public const CHAR_SIZE_7 = 7;
    public const CHAR_SIZE_8 = 8;
    public const CHAR_SIZE_DEFAULT = self::CHAR_SIZE_8;

    public const STOP_BITS_1_0 = 10;
    public const STOP_BITS_1_5 = 15;
    public const STOP_BITS_2_0 = 20;

    public const FLOW_CONTROL_NONE = 0;
    public const FLOW_CONTROL_HARD = 1;
    public const FLOW_CONTROL_SOFT = 2;
    public const FLOW_CONTROL_DEFAULT = self::FLOW_CONTROL_NONE;

    public const PARITY_EVEN = 20;
    public const PARITY_ODD = 21;
    public const PARITY_NONE = 22;
    public const PARITY_MARK = 24;
    public const PARITY_SPACE = 25;
    public const PARITY_DEFAULT = self::PARITY_NONE;

    public function __construct(string $device = '') {}
    public function __destruct() {}
    public function open(string $device): void {}
    public function close(): bool {}
    public function isOpen(): bool {}
    public function flush(): int {}
    public function read(int $length = 1): string {}
    public function write(string $data): int {}
    public function getBaudRate(): int {}
    public function setBaudRate(int $baudRate): self {}
    public function getCharSize(): int {}
    public function setCharSize(int $charSize): self {}
    public function getFlowControl(): int {}
    public function setFlowControl(int $flowControl): self {}
    public function getNumOfStopBits(): int {}
    public function setNumOfStopBits(int $numOfStopBits): self {}
    public function getParity(): int {}
    public function setParity(int $parity): self {}
    public function isCanonical(): bool {}
    public function setCanonical(bool $canonical): self {}

    /** VMIN: Minimum characters for non-canonical read */
    public function getVMin(): int {}
    public function setVMin(int $vMin): self {}

    /** VTIME: Timeout in deciseconds for non-canonical read */
    public function getVTime(): int {}
    public function setVTime(int $vTime): self {}

    /** CTS: Clear To Send line status */
    public function getCTS(): bool {}

    /** RTS: Request To Send line status */
    public function getRTS(): bool {}
    public function setRTS(bool $rts): self {}

    /** DSR: Data Set Ready line status */
    public function getDSR(): bool {}

    /** DTR: Data Terminal Ready line status */
    public function getDTR(): bool {}
    public function setDTR(bool $dtr): self {}

    /** DCD: Data Carrier Detect line status */
    public function getDCD(): bool {}

    /** RNG: Ring Indicator line status */
    public function getRNG(): bool {}

    public function getWin32NewLine(): string {}
    public function setWin32NewLine(string $nl): self {}
    public function getWin32ReadIntervalTimeout(): int {}
    public function setWin32ReadIntervalTimeout(int $time): self {}
    public function getWin32ReadTotalTimeoutMultiplier(): int {}
    public function setWin32ReadTotalTimeoutMultiplier(int $time): self {}
    public function getWin32ReadTotalTimeoutConstant(): int {}
    public function setWin32ReadTotalTimeoutConstant(int $time): self {}
    public function getWin32WriteTotalTimeoutMultiplier(): int {}
    public function setWin32WriteTotalTimeoutMultiplier(int $time): self {}
    public function getWin32WriteTotalTimeoutConstant(): int {}
    public function setWin32WriteTotalTimeoutConstant(int $time): self {}
}
