#!/bin/bash

# USAGE: ./new.sh <day>

DAY=$1

# Define some SOFT colors
BLUE='\033[0;34m'
GREEN='\033[0;32m'
RED='\033[1;31m'
NC='\033[0m' # No Color

# Fail early
set -e

echo -e "${GREEN}Creating new day: ${RED}Day ${DAY}${NC}"

# Copy the template file to the src/Solutions folder if it doesn't already exist
if [ ! -f src/Solutions/Day${DAY}.php ]; then
    cp templates/Day_Template.php src/Solutions/Day${DAY}.php

    # 1. Replace the class name
    # 4. Update the link to the problem description
    sed -i "s/class Day_Template/class Day${DAY}/g" src/Solutions/Day${DAY}.php
    sed -i "s/Problem description: https:\/\/adventofcode.com\/2023\/day\//Problem description: https:\/\/adventofcode.com\/2023\/day\/${DAY}/g" src/Solutions/Day${DAY}.php

    echo -e "Created new file: ${BLUE}src/Solutions/Day${DAY}.php${NC}"
fi

# Create 2 new data files for the day if they don't already exist
if [ ! -f data/day_${DAY} ]; then
    touch data/day_${DAY}
    touch data/day_${DAY}.ex

    echo -e "Created new data files: ${BLUE}data/day_${DAY}${NC} and ${BLUE}data/day_${DAY}_ex${NC}"
fi

echo -e "${GREEN}Done creating new day: ${RED}Day ${DAY}${NC}"
