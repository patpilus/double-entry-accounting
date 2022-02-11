# Double-entry accounting - domain model approach


## Build application

To build application please execute:
```shell script
docker-compose build
```

To launch the application stack: 
```shell script
docker-compose up -d
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
