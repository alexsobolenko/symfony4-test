# SYMFONY 4 test task

1. Install project:
```sh
$ git clone https://github.com/alexsobolenko/symfony4-test.git your_project
$ cd your_project
```
2. Configure `DATABASE_URL` parameter in `.env` file.
3. Create database and run server:
    * First run: `make first-run`
    * Another: `make run`
4. For check code run: `make check-syntax`
5. Use docker:
    1. Configure environment in `docker/env.conf`
    2. For first run:
        * `docker-compose up -d --build`
        * `docker exec php-container make docker-prepare`
    3. Another one:
        * `docker-compose up -d`
    4. Stop docker:
        * `docker-compose down`
