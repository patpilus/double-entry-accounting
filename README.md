# Double-entry accounting example


## Build application

To build application please execute:
```shell script
docker-compose build
```

To start application bash: 
```shell script
docker-compose exec app bash
```

To install required dependencies:
```shell script
composer install
```

## Launching tests

Inside docker `app` container:
```shell script
composer test
```
or
```shell script
vendor/bin/phpunit
```
