#!/bin/bash

# Fail on any error
set -e

# Add aoc.localhost to /etc/hosts
echo -e "${BLUE}Adding aoc.localhost to /etc/hosts${NC}"
echo "
# Added by devcontainer
127.0.0.1 aoc.localhost" >> /etc/hosts

# Create aoc.localhost directory
echo -e "${BLUE}Creating aoc.localhost directory${NC}"
mkdir -p /var/www/aoc.localhost

# Set the owner of the aoc.localhost directory to the current user
echo -e "${BLUE}Setting the owner of the aoc.localhost directory to the current user${NC}"
chown -R $USER:$USER /var/www/aoc.localhost

# Set permissions on the aoc.localhost directory
echo -e "${BLUE}Setting permissions on the aoc.localhost directory${NC}"
chmod -R 755 /var/www/aoc.localhost

# Symlink the learn/ directory to /var/www/aoc.localhost
echo -e "${BLUE}Symlinking /var/www/aoc.localhost to $REPO_ROOT${NC}"
ln -s $REPO_ROOT /var/www/aoc.localhost/

# Prioritize PHP for Apache2
echo -e "${BLUE}Prioritizing PHP for Apache2${NC}"
sed -i 's/index.html/index.php index.html/g' /etc/apache2/mods-enabled/dir.conf

# Copy apache2 config files
echo -e "${BLUE}Copying apache2 config file${NC}"
cp $REPO_ROOT/.devcontainer/config/apache2.conf /etc/apache2/apache2.conf
cp $REPO_ROOT/.devcontainer/config/aoc.localhost.conf /etc/apache2/sites-available/aoc.localhost.conf

# Disable the default site
echo -e "${BLUE}Disabling the default site${NC}"
a2dissite 000-default.conf -q

# Enable the aoc.localhost site
echo -e "${BLUE}Enabling the aoc.localhost site${NC}"
a2ensite aoc.localhost.conf -q

# Restart Apache2
echo -e "${BLUE}Restarting Apache2${NC}"
service apache2 restart -q