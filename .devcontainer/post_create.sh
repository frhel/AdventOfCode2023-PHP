#!/bin/bash

# Fail on any error
set -e

# Define some color variables
RED='\033[0;91m'
GREEN='\033[0;92m'
YELLOW='\033[0;93m'
BLUE='\033[0;94m'
PURPLE='\033[0;95m'
CYAN='\033[0;96m'
WHITE='\033[0;97m'
GREY='\033[0;90m'
NC='\033[0m' # No Color

# Set current user as owner of the repo
chown -R $USER:$USER .

# Grab the PHP version without trailin .0
PHP_VERSION=$(php -v | grep -oP "PHP \K[0-9]+")
echo -e "${BLUE}PHP version: $PHP_VERSION${NC}"

# Figure out where the php.ini file is located
PHP_INI=$(php -i | grep "Loaded Configuration File" | sed -e "s|.*=>\s*||")
# Get just the directory path
PHP_INI_DIR=$(dirname $PHP_INI)
XDEBUG_INI="$PHP_INI_DIR/conf.d/xdebug.ini"

# Copy php.ini and xdebug.ini files from the repo to the php directory
echo -e "${BLUE}Copying php.ini and xdebug.ini files from the repo to the php directory${NC}"
cp $REPO_ROOT/.devcontainer/config/php.ini $PHP_INI
cp $REPO_ROOT/.devcontainer/config/xdebug.ini $XDEBUG_INI

# Add the PHP repository
echo -e "${BLUE}Adding the PHP repository${NC}"
echo "deb http://ppa.launchpad.net/ondrej/php/ubuntu focal main" | tee /etc/apt/sources.list.d/php.list
echo "deb-src http://ppa.launchpad.net/ondrej/php/ubuntu focal main" | tee -a /etc/apt/sources.list.d/php.list
apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 4F4EA0AAE5267A6C

# Install php-process
echo -e "${BLUE}Installing php-process${NC}"
apt-get update
apt-get install php$PHP_VERSION-process

# Include the .devcontainer/setup_gh_copilot_cli.sh script to install the GitHub CLI and GitHub Copilot CLI extension
# It immediately exits and cancels the action if a GitHub CLI token is not found in the host system's ~/.config/gh/config.yml
. $REPO_ROOT/.devcontainer/setup_gh_copilot_cli.sh

# add the php 8.0 repository
# echo -e "${BLUE}Adding the php 8.0 repository${NC}"
# add-apt-repository ppa:ondrej/php
# apt-get update
# apt-get install php-process

# Run Composer install
echo -e "${BLUE}Running Composer install${NC}"
composer install

echo -e "${GREEN}Done!"
