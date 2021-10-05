phpunit:
	php7.4 bin/console cache:clear --env=test
	php7.4 bin/console doctrine:database:drop --env=test --if-exists --force
	php7.4 bin/console doctrine:database:create --env=test --if-not-exists
	php7.4 bin/console doctrine:schema:update --env=test --force
	php7.4 vendor/bin/phpunit tests/*
	php7.4 bin/console doctrine:database:drop --env=test --force
