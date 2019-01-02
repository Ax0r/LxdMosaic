# Install Dependecies
apt-get install apache2 php7.2 php7.2-cli php7.2-json php7.2-mysql unzip zip mysql-server -y

# Install composer

##Download composer
curl -sS https://getcomposer.org/installer -o composer-setup.php
## Install
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

#Mysql commands

mysql < sql/users.sql
mysql < sql/seed.sql

cp examples/lxd_manager.conf /etc/apache2/sites-available/

# Enable required apache mods
a2enmod ssl
a2enmod headers
a2enmod rewrite

# Enable site
a2ensite lxd_manager

systemctl restart apache2
