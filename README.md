# DDD Article CMS sample

Example of using the DDD approach with the Symphony 5 infrastructure

## Installation

Up Docker containers

```shell
$ docker-compose up -d
```

Open applications shell

```shell
$ docker-compose exec app sh
```

Apply migrations

```shell
$ ./bin/console doctrine:migrations:migrate
```

Load fixtures

```shell
$ ./bin/console doctrine:fixtures:load
```

## Automated tests

```shell
$ php /var/www/vendor/phpunit/phpunit/phpunit --no-configuration /var/www/tests
```

## Documentation

Swagger UI: [http://localhost/docs](http://localhost/docs)

For this sample, you can use the api key `special-key` to test the authorization filters.

OAS3 API documentation:

```text
public/specification.yaml
```

ER Diagram

```text
article.uml
```
