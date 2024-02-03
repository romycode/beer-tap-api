<img align="left"  width="150" height="150" src=".github/sns.logo.png" />

## Sonosuite PHP recruitment challenge

This repository is a PHP skeleton with Symfony & PostgreSQL designed for quickly getting started developing an API.
Check the [Getting Started](#getting-started) for full details.

<br>

## Technologies

* [PHP 8.1](https://www.php.net/releases/8.1/en.php)
* [Symfony 6](https://symfony.com/releases/6.0)
* [PostgreSQL 14](https://www.postgresql.org/about/news/postgresql-14-released-2318/)
* [PHPUnit](https://phpunit.readthedocs.io/en/9.5/)
* [Docker](https://www.docker.com/)
* [Make](https://www.gnu.org/software/make/manual/make.html)

## Getting Started

Within the [Makefile](Makefile) you can handle the entire flow to get everything up & running:

1. Install `make` on your computer, if you do not already have it.
2. Build the Docker image: `make build`
3. Start the application: `make up`

As you could see on the [Makefile](Makefile) script, you could just avoid those steps and just execute `make up`, as
**build** is dependant of it.

Go to `http://localhost:8080/api/health` to see that everything is up & running!

## Overview

This is a very simple API with a `/health` endpoint that connects to the database to check that everything is going
well.

### Structure

```scala
$ tree -L 4 src

src
|-- Health // Bounded Context: Features related to the health logc
|    `-- Application
|       |-- Model // The DTOs representing the Response of the Query Handler 
|       |   |-- GetHealthResponse.php
|       |-- Query
|       |   |-- GetHealthQuery.php
|       |-- Service // Handlers for the given Application queries
|       |   |-- GetHealthHandler.php
|    `-- Domain
|       |-- Model // Entities related to the bounded context 
|       |   |-- Health.php
|       |-- Repository
|       |   |-- HealthRepositoryInterface.php 
|    `-- Infrastructure
|       |-- Api // Concrete implementation of the API endpoints
|       |   |-- HealthController.php
|       |   |-- routes.yaml // Specific definition of the routes regarding this Bounded Context
|       `-- Repository // Concrete repository implementations
|           |-- HealthRepository.php
```

This skeleton is based on
a [Clean Architecture](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html) approach, so you
could find the first basic layers:

> You could find here two amazing articles ([here](https://www.educative.io/blog/clean-architecture-tutorial)
> and [here](https://www.freecodecamp.org/news/modern-clean-architecture/)) explaining the Clean Architecture with Java!
> (credits to [@bertilMuth](https://twitter.com/BertilMuth) and [@ryanthelin](https://dev.to/ryanthelin)).

### Application

The main orchestrator. Interacts with the events received through the Message Handler and the services are the ones in
charge of interacting with the Domain layer.

### Infrastructure

Here you will find the different files to interact with the outside, which implements the contract defined in the Domain
layer. In this folder you will find

* `Api`: Here you will have the classes that handle the REST endpoints and the Request/Response
* `Repository`: Here it is the persistence layer, which interact with the PostgreSQL database, decoupling the rest of
  the application and being easy to change the database engine.

### Domain

Any of your domain Entities, or Services, that models your business logic. These classes should be completely isolated
of any external dependency or framework, but interact with them. This layer should follow the Dependency Inversion
principle.

## License

This project is licensed with the [MIT license](LICENSE).

---

<p align="center">
  Made with ❤️ by <a href="https://sonosuite.com">Sonosuite</a> Tech team
</p>
