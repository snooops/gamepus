#!/bin/bash

# READ THIS!
# READ THIS!
# READ THIS!
# READ THIS!
# READ THIS!
# !!! CAUTION !!!
# NEVER USE THIS SCRIPT FOR PRODUCTION USAGE!
# This script creates a setup that should fit
# development usage.
# For example it creates a database use, which 
# has access from anywhere. You should not use
# this in production usage because for security
# reasons.



# Author: Dennis Meyer
# E-Mail: snooops84@gmail.com
# Homepage: https://www.snooops.net (SSL Powered by "https://letsencrypt.org/")
# You should use https://letsencrypt.org/ if you dont own a certificate on production


#### START Configuration
# OS / System specific environemnt:
# mariadb/mysql root password
DB_ROOT_PASSWORD='caereelo5uveWeeGhoyaash3'

# Application specific environment:

# for simplicity use environment as system user, db name, 
# db user and working directory
APP_ENVIRONMENT='development'
# apache vhost name, not really needed because its the only vhost 
# listen on 80 and if SSL=1 on 443
APACHE_SERVERNAME=$APP_ENVIRONMENT
# name of the php pool (file- and poolname)
FPM_POOL=$APP_ENVIRONMENT
# user the php pool runs at (application user)
FPM_POOL_USER=$APP_ENVIRONMENT
# password of the php pool user, to ssh into it later
FPM_USER_PASS='ieXahngae7hia4iSa1oizain'

# database user for the application
DB_APP_USER=$APP_ENVIRONMENT
# database password for the application user
DB_APP_PASSWORD='ieXahngae7hia4iSa1oizain'
# database to use
DB_APP_DB=$APP_ENVIRONMENT

# Additional PHP Modules you need to install
PHP_MODULES="php5-mcrypt php5-curl php5-gd php5-json php5-imagick php5-cli"

# do you want to setup a ssl vhost? 1=yes 0=no
# this will install a selfsigned certificate
SSL=1

# your ssh public-key to access via sftp the application working directory
SSH_PUB_KEY='ssh-rsa AAAAB3NzaC1yc2EAAAABJQAAAIEAlj1H9P12jHlp608FRwYpqtn9q54lqevxyzVJNNFU+P8fn2G0TvjM6Q19ui7TpKx4nlZkcZLTru3vZprOihjLjZTWnrDPrnwNFnYvjUQa99h0UJNFgUvDyrExE3LvPOfkaDbXDnkKG7c/oPCLLwd0cPJMsT8ZRG62JYK6iY/4XmE= snooops84@gmail.com'
#### END Configuration




#### START Setup
sed -i s/main/'main non-free contrib'/g /etc/apt/sources.list
apt-get update
echo "mariadb-server-10.0 mysql-server/root_password password $DB_ROOT_PASSWORD" | debconf-set-selections
echo "mariadb-server-10.0 mysql-server/root_password_again password $DB_ROOT_PASSWORD" | debconf-set-selections
apt-get install -y vim memcached apache2 libapache2-mod-fastcgi apache2-mpm-prefork php5-fpm php5 php5-cli php5-mysqlnd php5-memcached $PHP_MODULES mariadb-server ca-certificates bash-completion

# Application Environment Setup
VHOSTDIR=/var/www/$APP_ENVIRONMENT

mkdir -p $VHOSTDIR
mkdir $VHOSTDIR/html
mkdir $VHOSTDIR/cgi-bin
mkdir $VHOSTDIR/logs
mkdir $VHOSTDIR/sockets
mkdir $VHOSTDIR/tmp
mkdir $VHOSTDIR/.ssh

echo $SSH_PUB_KEY > $VHOSTDIR/.ssh/authorized_keys

# some fancyness with application user environment setting for most frameworks
echo "PS1='\[\033[0;32m\](\[\e[1;34m\]\u\[\033[0;32m\]@\[\033[0;33m\]\h:\[\033[m\] \w\[\033[0;32m\])\[\033[m\] '
# bash usability featuraz
. /etc/bash_completion
alias ls='ls --color=auto'
alias grep='grep --color=auto'
alias fgrep='fgrep --color=auto'
alias egrep='egrep --color=auto'

# application enviroment settings
# You can change this as you need
# Shopware
ENV=$APP_ENVIRONMENT
ENVIRONMENT=$APP_ENVIRONMENT

# TYPO3
TYPO3_CONTEXT=$APP_ENVIRONMENT

# Symfony
export DATABASE_USER=$DB_APP_USER
export DATABASE_PASSWORD=$DB_APP_PASSWORD
export SYMFONY_ENV=$APP_ENVIRONMENT
export SYMFONY_DEBUG=1
" > $VHOSTDIR/.profile


echo "syntax on
filetype plugin indent on
\" show existing tab with 4 spaces width
set tabstop=4
\" when indenting with '>', use 4 spaces width
set shiftwidth=4
\" On pressing tab, insert 4 spaces
set expandtab
" > $VHOSTDIR/.vimrc

useradd -d $VHOSTDIR -s /bin/bash $FPM_POOL_USER
# set permissions of work directory accessible for application user
chown $FPM_POOL_USER:$FPM_POOL_USER -R $VHOSTDIR
# join www-data to application user group to make php files and generated
# files accessible for apache
usermod -a -G $FPM_POOL_USER www-data

# create database and user and setup privileges
# caution: the user is able to connect from anywhere
# NEVER USE THIS SCRIPT FOR PRODUCTION USAGE!
echo "[client]
user=root
password=$DB_ROOT_PASSWORD" > /root/.my.cnf
mysql -e "create user '$DB_APP_USER'@'%' IDENTIFIED BY '$DB_APP_PASSWORD'"
mysql -e "create database $DB_APP_DB"
mysql -e "grant all privileges on $DB_APP_DB.* TO '$DB_APP_USER'@'%'"




# Create SSL Certificate and VHost if needed
if [ $SSL == 1 ]
then
	mkdir /etc/ssl/localcerts
	
	# Its selfsigned, "hello world"
	openssl req -new -newkey rsa:4096 -days 365 -nodes -x509 \
		-subj "/C=UI/ST=Mars/L=Gale Crater/O=Awesome Project/CN=$APACHE_SERVERNAME" \
		-keyout /etc/ssl/localcerts/$APACHE_SERVERNAME.key  -out /etc/ssl/localcerts/$APACHE_SERVERNAME.crt

	# Vhost with ssl
	echo "
<VirtualHost *:80>
	ServerName $APACHE_SERVERNAME

	## Vhost docroot
	DocumentRoot \"$VHOSTDIR/html\"

	RewriteEngine On
	RewriteCond %{HTTPS} off
	RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]

	## Logging
	ErrorLog \"$VHOSTDIR/logs/apache_error.log\"
	LogLevel warn
	ServerSignature On
	CustomLog \"$VHOSTDIR/logs/apache_access.log\" combined
</VirtualHost>


<VirtualHost *:443>
	ServerName $APACHE_SERVERNAME

	SSLEngine on
	SSLCertificateFile \"/etc/ssl/localcerts/$APACHE_SERVERNAME.crt\"
	SSLCertificateKeyFile \"/etc/ssl/localcerts/$APACHE_SERVERNAME.key\"
	
	## Vhost docroot
	DocumentRoot \"$VHOSTDIR/html\"

	<Directory \"$VHOSTDIR/html\">
		AllowOverride All
		Require all granted
	</Directory>

	<Directory \"/\">
		AllowOverride None
		Require all granted
	</Directory>


	Alias /php5-fcgi $VHOSTDIR/cgi-bin/php5-fcgi
	AddHandler php5-fcgi .php
	Action php5-fcgi /php5-fcgi
	FastCgiExternalServer $VHOSTDIR/cgi-bin/php5-fcgi -idle-timeout 7200 -socket $VHOSTDIR/sockets/phpfpm.socket -pass-header Authorization

	## Logging
	ErrorLog \"$VHOSTDIR/logs/apache_error.log\"
	LogLevel warn
	ServerSignature Off
	CustomLog \"$VHOSTDIR/logs/apache_access.log\" combined
</VirtualHost>

	" > /etc/apache2/sites-available/$APACHE_SERVERNAME.conf
	
# We dont want to use SSL
else
	echo "
<VirtualHost *:80>
	ServerName $APACHE_SERVERNAME
	
	## Vhost docroot
	DocumentRoot \"$VHOSTDIR/html\"

	<Directory \"$VHOSTDIR/html\">
		AllowOverride All
		Require all granted
	</Directory>

	<Directory \"/\">
		AllowOverride None
		Require all granted
	</Directory>


	Alias /php5-fcgi $VHOSTDIR/cgi-bin/php5-fcgi
	AddHandler php5-fcgi .php
	Action php5-fcgi /php5-fcgi
	FastCgiExternalServer $VHOSTDIR/cgi-bin/php5-fcgi -idle-timeout 7200 -socket $VHOSTDIR/sockets/phpfpm.socket -pass-header Authorization

	## Logging
	ErrorLog \"$VHOSTDIR/logs/apache_error.log\"
	LogLevel warn
	ServerSignature Off
	CustomLog \"$VHOSTDIR/logs/apache_access.log\" combined
</VirtualHost>

	" > /etc/apache2/sites-available/$APACHE_SERVERNAME.conf
fi

a2ensite $APACHE_SERVERNAME.conf
a2enmod rewrite
a2enmod alias
a2enmod ssl
a2enmod actions
service apache2 restart



# PHP Setup
echo "[$FPM_POOL]
listen = $VHOSTDIR/sockets/phpfpm.socket
listen.backlog = -1
listen.owner = $FPM_POOL_USER
listen.group = $FPM_POOL_USER
listen.mode = 0660
user = $FPM_POOL_USER
group = $FPM_POOL_USER
pm = dynamic
pm.max_children = 24
pm.start_servers = 4
pm.min_spare_servers = 4
pm.max_spare_servers = 8
pm.max_requests = 20
ping.response = pong
request_terminate_timeout = 0
request_slowlog_timeout = 0
catch_workers_output = no
slowlog = $VHOSTDIR/logs/php-slow.log
php_admin_value[error_log] = $VHOSTDIR/logs/php-error.log
php_admin_value[upload_tmp_dir] = $VHOSTDIR/tmp
php_admin_value[allow_url_fopen] = 1
php_admin_value[apc.enable_clie] = 0
php_admin_value[apc.gc_ttl] = 3600
php_admin_value[apc.shm_size] = 64M
php_admin_value[apc.ttl] = 7200
php_admin_value[date.timezone] = Europe/Berlin
php_admin_value[expose_php] = 0
php_admin_value[file_uploads] = 1
php_admin_value[log_errors] = on
php_admin_value[log_level] = notice
php_admin_value[max_execution_time] = 60
php_admin_value[memory_limit] = 128M

# some frameworks handle this themselfs and
# php dont like to enable opcache twice
#php_admin_value[opcache.enable] = 1
#php_admin_value[opcache.interned_strings_buffer] = 16
#php_admin_value[opcache.max_accelerated_files] = 4000
#php_admin_value[opcache.memory_consumption] = 1024
php_admin_value[post_max_size] = 64M
php_admin_value[upload_max_filesize] = 6M
php_admin_flag[display_errors] = off
" > /etc/php5/fpm/pool.d/$FPM_POOL.conf
service php5-fpm restart

#### END Setup

