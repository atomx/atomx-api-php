
generate:
	php sami.phar update config.php

deploy:
	rsync -avr --delete build/* /var/www/www.atomx.com/public/atomx-api-php/
	cp index.php /var/www/www.atomx.com/public/atomx-api-php/

.PHONY: build install

