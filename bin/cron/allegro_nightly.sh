#!/bin/sh
. ~/.cronenv; /usr/bin/php71 /var/www/home/legacy/app/bin/run.php Jbank/Buscustomer/Model/Nightly/Load/Banklaan
