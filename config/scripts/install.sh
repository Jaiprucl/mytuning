#!/bin/bash

# -e break script on errors
# -xv show debug information
# set -exv

echo "-------> Run installation script"

# Update list
echo "---------> Update apt list"
apt-get update -y

# Install Composer
composer install