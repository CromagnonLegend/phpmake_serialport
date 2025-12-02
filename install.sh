#!/usr/bin/env bash
set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

OS="$(uname -s)"
echo -e "${GREEN}Detected OS: $OS${NC}"

check_requirements() {
  echo "==> Checking requirements..."

  if ! command -v php &> /dev/null; then
    echo -e "${RED}ERROR: PHP is not installed or not in PATH${NC}"
    exit 1
  fi

  echo "PHP version: $(php -v | head -n 1)"

  if ! command -v phpize &> /dev/null; then
    echo -e "${RED}ERROR: phpize not found${NC}"
    echo "Please install PHP development headers"
    exit 1
  fi

  if ! command -v make &> /dev/null; then
    echo -e "${RED}ERROR: make not found${NC}"
    echo "Please install C build tools"
    exit 1
  fi

  echo -e "${GREEN}✓ All requirements satisfied${NC}"
}

install_linux(){
  echo -e "${GREEN}==> Building for Linux${NC}"
  check_requirements

  phpize
  ./configure --enable-Gorilla
  make

  echo -e "${YELLOW}==> Installing extension (requires sudo)${NC}"
  sudo make install
}

install_mac(){
  echo -e "${GREEN}==> Building for macOS${NC}"
  check_requirements

  if ! command -v autoconf &> /dev/null; then
    echo -e "${YELLOW}WARNING: autoconf not found. Installing via Homebrew...${NC}"
    if command -v brew &> /dev/null; then
      brew install autoconf
    else
      echo -e "${RED}ERROR: Homebrew not found. Please install autoconf manually.${NC}"
      exit 1
    fi
  fi

  phpize
  ./configure --enable-Gorilla
  make

  echo -e "${YELLOW}==> Installing extension (requires sudo)${NC}"
  sudo make install
}

install_windows_msys(){
  echo -e "${GREEN}==> Building for Windows/MSYS2${NC}"
  echo -e "${YELLOW}NOTE: Windows builds require PHP SDK${NC}"

  # assume php development SDK is on PATH
  if command -v phpsdk_setvars.bat &> /dev/null; then
    phpsdk_setvars.bat
  fi

  phpize
  ./configure --enable-Gorilla
  make
  make install
}

case "$OS" in
  Linux*)    install_linux ;;
  Darwin*)   install_mac   ;;
  MINGW*|MSYS*|CYGWIN*) install_windows_msys ;;
  *)
    echo -e "${RED}Unsupported OS: $OS${NC}"
    echo "Please see README.md for manual build instructions."
    exit 1
    ;;
esac

# Auto-enable in php.ini
echo -e "${GREEN}==> Configuring php.ini${NC}"

if [[ "$OS" == "MINGW"* ]] || [[ "$OS" == "MSYS"* ]] || [[ "$OS" == "CYGWIN"* ]]; then
  EXT_FILE="php_Gorilla.dll"
else
  EXT_FILE="Gorilla.so"
fi

INI=$(php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||" | tr -d '\r')

if [ -z "$INI" ] || [ "$INI" == "(none)" ]; then
  echo -e "${YELLOW}WARNING: No php.ini file loaded${NC}"
  echo "Checking for additional .ini scan directory..."

  SCAN_DIR=$(php --ini | grep "Scan for additional" | sed -e "s|.*:\s*||" | tr -d '\r')

  if [ -n "$SCAN_DIR" ] && [ "$SCAN_DIR" != "(none)" ]; then
    INI="${SCAN_DIR}/99-gorilla.ini"
    echo "Will create: $INI"
  else
    echo -e "${RED}ERROR: Cannot determine where to enable extension${NC}"
    echo "Please manually add 'extension=$EXT_FILE' to your php.ini"
    exit 1
  fi
fi

echo "Target INI file: $INI"

if [ -f "$INI" ] && grep -q "extension=$EXT_FILE" "$INI"; then
  echo -e "${GREEN}✓ Extension already enabled in $INI${NC}"
else
  echo "Adding extension=$EXT_FILE to $INI"

  if echo "extension=$EXT_FILE" | sudo tee -a "$INI" >/dev/null; then
    echo -e "${GREEN}✓ Extension enabled with sudo${NC}"
  fi
fi

echo ""
echo -e "${GREEN}==> Verifying installation${NC}"
if php -m | grep -q "Gorilla"; then
  echo -e "${GREEN}✓ SUCCESS: Gorilla extension is loaded!${NC}"
  echo ""
  php -r "if (class_exists('PHPMake\\\\SerialPort')) { echo 'SerialPort class is available\n'; }"
  echo ""
  echo "Extension version: $(php -r 'echo phpversion("Gorilla");')"
else
  echo -e "${RED}✗ WARNING: Extension compiled but not loaded${NC}"
  echo "You may need to restart your web server or PHP-FPM"
  echo "Verify with: php -m | grep Gorilla"
fi

echo ""
echo -e "${GREEN}Installation complete!${NC}"
