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

# Include the .devcontainer/setup_gh_copilot_cli.sh script to install the GitHub CLI and GitHub Copilot CLI extension
# It immediately exits and cancels the action if a GitHub CLI token is not found in the host system's ~/.config/gh/config.yml
. $REPO_ROOT/.devcontainer/setup_gh_copilot_cli.sh

# Include the .devcontainer/setup_apache.sh script to install Apache and configure it to serve the site
. $REPO_ROOT/.devcontainer/setup_apache.sh

# Run Composer install
echo -e "${BLUE}Running Composer install${NC}"
composer install

echo -e "${GREEN}Done!"
