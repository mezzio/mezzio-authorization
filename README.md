# Mezzio Authorization middleware

[![Build Status](https://github.com/mezzio/mezzio-authorization/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/mezzio/mezzio-authorization/actions/workflows/continuous-integration.yml)

Laminas-mezzio-authorization provides middleware for [Mezzio](https://github.com/mezzio/mezzio)
and [PSR-7](https://www.php-fig.org/psr/psr-7/) applications for authorizing
specific routes based on [ACL](https://en.wikipedia.org/wiki/Access_control_list)
or [RBAC](https://en.wikipedia.org/wiki/Role-based_access_control) systems.

## Installation

You can install the mezzio-authorization library with
[Composer](https://getcomposer.org):

```bash
$ composer require mezzio/mezzio-authorization
```

## Documentation

Documentation is [in the doc tree](docs/book/), and can be compiled using [mkdocs](https://www.mkdocs.org):

```bash
$ mkdocs build
```

You may also [browse the documentation online](https://docs.mezzio.dev/mezzio-authorization/).
