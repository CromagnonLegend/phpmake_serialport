#!/usr/bin/env bash
set -e

OS="$(uname -s)"
echo "Detected OS: $OS"

install_linux(){
  echo "==> Building for Linux"
  phpize
  ./configure --enable-Gorilla
  make
  sudo make install
}

install_mac(){
  echo "==> Building for macOS"
  # macOS Big Sur+, homebrew PHP may need:
  # brew install autoconf
  phpize
  ./configure --enable-Gorilla
  make
  sudo make install
}

install_windows_msys(){
  echo "==> Building for Windows/MSYS2"
  # assume php development SDK is on PATH
  phpsdk_setvars.bat
  phpize
  ./configure --enable-Gorilla
  make
  make install
}

case "$OS" in
  Linux*)    install_linux ;;
  Darwin*)   install_mac   ;;
  MINGW*|MSYS*) install_windows_msys ;;
  *) 
    echo "Unsupported OS: $OS"
    echo "Please see README for manual build instructions."
    exit 1
    ;;
esac

# Auto-enable in php.ini
INI=$(php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||")
if ! grep -q "extension=Gorilla.so" "$INI"; then
  echo "extension=Gorilla.so" | sudo tee -a "$INI" >/dev/null
  echo "Appended extension=Gorilla.so to $INI"
else
  echo "Gorilla.so already enabled in $INI"
fi

echo "Gorilla extension installed and enabled."
