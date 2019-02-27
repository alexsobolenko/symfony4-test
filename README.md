# SYMFONY 4 test task
1. Install project:
```sh
$ git clone https://github.com/alexsobolenko/symfony4-test.git your_project
$ cd your_project
$ composer install
```
2. Configure `DATABASE_URL` parameter in `.env` file.
3. Create database and run server:
```sh
$ php bin/console doctrine:database:create
$ php bin/console doctrine:migrations:migrate
$ php -S 127.0.0.1:8000 -t public
```

