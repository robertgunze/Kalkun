#!/bin/sh

# Configure this (use absolute path)
PHP=/usr/bin/php # php cli path
DAEMON=/var/www/html/Kalkun/scripts/outbox_queue.php # daemon.php path

# Execute
$PHP $DAEMON
