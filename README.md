## A simple invoicing system
This is a framework-agnostic package to be used as a n invoicing system consisting of the main components:
- Invoice
- Recipient
- Item
- and some other ones, can be found in [`Invoicing\Domain`](https://github.com/HazemNoor/invoicing/tree/main/src/Domain/Models) namespace

Built using [Domain-Driven Design](https://en.wikipedia.org/wiki/Domain-driven_design) principles

## Installation
This package is still under development, but can be used in any framework using composer
- Add github repository source
```shell
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:HazemNoor/invoicing.git",
            "no-api": true
        }
    ]
```
- Add using composer require
```shell
composer require hazemnoor/invoicing dev-main
```

## Development
1. Fetch project
```shell
git clone git@github.com:HazemNoor/invoicing.git
cd invoicing
```
2. Copy file `.env.example` into `.env`
```shell
cp .env.example .env
```
3. Edit file `.env` if needed

4. Make sure to have `docker-compose` installed on your machine, then execute this command to build docker images
```shell
make build
```
5. Run these commands to execute `composer install`
```shell
make up
make install
```

## Other commands
- If you need to log in to docker container, use these commands
```shell
make up
make login
```
- Stop docker containers
```shell
make down
```

## Testing
You can run unit tests using these commands
```shell
make up
make test
```

## Coding Style
The coding style used is [PSR-12](https://www.php-fig.org/psr/psr-12/) and is included with the testing command `make test` using [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)

## Code coverage
Run code coverage, an html report will be generated in `.code-coverage` directory
```shell
make up
make coverage
```
**Current code coverage is 100%**

## Todo
- Implement more repositories to store in different medium, like `MySQL`
