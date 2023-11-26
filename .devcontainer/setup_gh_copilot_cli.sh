#!/bin/bash

# github.com/frhel

# DESCRIPTION:
# Drop-in script to install the GitHub Copilot CLI extension for GitHub CLI from another shell script on Dev Container
# Assumes GREEN, BLUE and NC(no color) are defined as colors for output. See below for example. Uncomment the colors
# if you want to use them.
# GREEN='\033[0;92m'
# BLUE='\033[0;94m'
# NC='\033[0m' # No Color

# REQUIREMENTS:
# - GitHub CLI must be installed on the host machine
# - GitHub CLI must be installed on the container (this is done in the devcontainer.json file or Dockerfile or similar)
# - GitHub CLI must be configured with a token in ~/.config/gh/config.yml on the host machine

# THOUGHTS:
# I really want to use the GitHub Copilot CLI extension, but I can't configure it to load locally so I need to
# install it on the container directly.
# Am hoping this will become part of the devcontainer.json file or a VSCode plugin in the future
# so I don't have to do it this way. But for now, this is what we have.

# Set this to false if you don't want to add the aliases
ADD_ALIASES=true

# Default aliases to add, change these to your liking
# The -t flag specifies the type of response you want from Copilot, e.g. shell, git, or gh
ALIASES=(
    'copilot="gh copilot"'
    'csh="gh copilot suggest -t shell"'
    'cgit="gh copilot suggest -t git"'
    'cgh="gh copilot suggest -t gh"'
    '??="gh copilot explain"'
)

# Fail on any error
set -e

# Check if we have a .config/gh/config.yml file and if it contains a token. Exit if we don't
if ! [ -f ~/.config/gh/config.yml ] || ! [ -f ~/.config/gh/hosts.yml ] || ! grep -q oauth_token ~/.config/gh/hosts.yml || ! [ -x "$(command -v gh)" ]; then
    if ! [ -f ~/.config/gh/config.yml ]; then
        echo -e "${YELLOW}No GitHub CLI config file found in ~/.config/gh/config.yml${NC}"
    fi
    if ! [ -f ~/.config/gh/hosts.yml ]; then
        echo -e "${YELLOW}No GitHub CLI hosts file found in ~/.config/gh/hosts.yml${NC}"
    fi
    if ! grep -q token ~/.config/gh/config.yml; then
        echo -e "${YELLOW}No GitHub CLI token found in ~/.config/gh/config.yml${NC}"
    fi
    if ! [ -x "$(command -v gh)" ]; then
        echo -e "${YELLOW}GitHub CLI is not installed${NC}"
    fi
    echo -e "${YELLOW}Skipping installation of GitHub Copilot extension for CLI${NC}"
    echo -e "${YELLOW}If you want to use GitHub Copilot, please run 'gh auth login' to login to GitHub CLI${NC}"
    echo -e "${YELLOW}Then run 'gh extension install github/gh-copilot' to install the GitHub Copilot extension for CLI${NC}"
    exit 0
fi

# Install GitHub Copilot extension for CLI
echo "Found GitHub CLI token in ~/.config/gh/config.yml"
echo "Installing GitHub Copilot extension for CLI"
gh extension install github/gh-copilot

if $ADD_ALIASES; then
    # Count the number of aliases we have
    NUM_ALIASES=${#ALIASES[@]}
    CURR_ALIAS_NUM=0

    # Add the aliases to the bashrc file
    echo -e "${GREEN}Adding aliases to /etc/bash.bashrc${NC}"
    while [ $CURR_ALIAS_NUM -lt $NUM_ALIASES ]; do
        echo -e "Setting alias ${GREEN}${ALIASES[$CURR_ALIAS_NUM]}${NC}"
        alias "${ALIASES[$CURR_ALIAS_NUM]}"; echo "alias ${ALIASES[$CURR_ALIAS_NUM]}" >> /etc/bash.bashrc
        CURR_ALIAS_NUM=$((CURR_ALIAS_NUM+1))
    done

    echo -e "${YELLOW}If the aliases are not working in your terminal, restart your session with 'exec bash'${NC}"
fi
