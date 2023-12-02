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
    # 2. Replace the $day number
    # 3. Replace the defaultName to be DayXX
    sed -i "s/class Day/class Day${DAY}/g" src/Solutions/Day${DAY}.php
    sed -i "s/protected static \$day;/protected static \$day = ${DAY};/g" src/Solutions/Day${DAY}.php
    sed -i "s/protected static \$defaultName;/protected static \$defaultName = 'Day${DAY}';/g" src/Solutions/Day${DAY}.php

    echo -e "Created new file: ${BLUE}src/Solutions/Day${DAY}.php${NC}"
fi


# Add a new use statement to the bin/solve file for the new day if it doesn't already exist
if ! grep -q "use frhel\\\adventofcode2023php\\\Solutions\\\Day${DAY};" bin/solve; then
    sed -i "s/\/\/ Add new includes here/\/\/ Add new includes here\nuse frhel\\\adventofcode2023php\\\Solutions\\\Day${DAY};/g" bin/solve

    echo -e "Added new use statement: ${GREEN}use frhel\\\adventofcode2023php\\\Solutions\\\Day${DAY};${NC} to ${BLUE}bin/solve${NC}"
fi
# Add a new application to the bin/solve file in the format of
# $application->add(new DayXX());
# if it doesn't already exist
if ! grep -q "\$application->add(new Day${DAY}());" bin/solve; then
    sed -i "s/\/\/ Add new applications here/\/\/ Add new applications here\n\$application->add(new Day${DAY}());/g" bin/solve

    echo -e "Added new application: ${GREEN}\$application->add(new Day${DAY}());${NC} to ${BLUE}bin/solve${NC}"
fi

# Create 2 new data files for the day if they don't already exist
if [ ! -f data/day_${DAY} ]; then
    touch data/day_${DAY}
    touch data/day_${DAY}_ex

    echo -e "Created new data files: ${BLUE}data/day_${DAY}${NC} and ${BLUE}data/day_${DAY}_ex${NC}"
fi

echo -e "${GREEN}Done creating new day: ${RED}Day ${DAY}${NC}"


