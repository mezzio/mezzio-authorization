# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.1.0 - TBD

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.1 - 2019-06-18

### Added

- [zendframework/zend-expressive-authorization#25](https://github.com/zendframework/zend-expressive-authorization/pull/25) adds support for PHP 7.3.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.0 - 2018-09-11

### Added

- Usage of [mezzio-authentication](https://github.com/zendframework/zend-expressive-authentication)
  ^1.0.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.4.0 - 2018-03-15

### Added

- [zendframework/zend-expressive-authorization#16](https://github.com/zendframework/zend-expressive-authorization/pull/16) adds
  support for PSR-15.

### Changed

- [zendframework/zend-expressive-authorization#18](https://github.com/zendframework/zend-expressive-authorization/pull/18)
  changes the constructor of the `Mezzio\Authorization\AuthorizationMiddleware`
  class to accept a callable `$responseFactory` instead of a
  `Psr\Http\Message\ResponseInterface` response prototype. The
  `$responseFactory` should produce a `ResponseInterface` implementation when
  invoked.

- [zendframework/zend-expressive-authorization#18](https://github.com/zendframework/zend-expressive-authorization/pull/18)
  updates the `AuthorizationMiddlewareFactory` to no longer use
  `Mezzio\Authentication\ResponsePrototypeTrait`, and instead always
  depend on the `Psr\Http\Message\ResponseInterface` service to correctly return
  a PHP callable capable of producing a `ResponseInterface` instance.

### Deprecated

- Nothing.

### Removed

- [zendframework/zend-expressive-authorization#19](https://github.com/zendframework/zend-expressive-authorization/pull/19)
  removes the file `config/authorization.php` and merges its contents into the
  `Mezzio\Authorization\ConfigProvider` class.

- [zendframework/zend-expressive-authorization#16](https://github.com/zendframework/zend-expressive-authorization/pull/16) and
  [zendframework/zend-expressive-authorization#11](https://github.com/zendframework/zend-expressive-authorization/pull/11)
  remove support for http-interop/http-middleware and
  http-interop/http-server-middleware.

### Fixed

- Nothing.

## 0.3.0 - 2017-11-28

### Added

- [zendframework/zend-expressive-authorization#13](https://github.com/zendframework/zend-expressive-authorization/pull/13) adds
  a requirement on the mezzio-authentication package, v 0.2.0 and up.

### Changed

- [zendframework/zend-expressive-authorization#13](https://github.com/zendframework/zend-expressive-authorization/pull/13)
  modifies the `AuthorizationMiddleware` workflow. It now looks for a
  `Mezzio\Authentication\UserInterface` request parameter that
  implements that interface; with none available, it returns a 401 status.
  Additionally, it now uses `UserInterface::getUserRoles()`, which returns an
  array of roles; as such, it loops through each, delegating request processing
  for the first role granted permission.

- [zendframework/zend-expressive-authorization#13](https://github.com/zendframework/zend-expressive-authorization/pull/13)
  pins to http-interop/http-middleware 0.4.1, as that is the most recent version
  supported by mezzio-authentication.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.2.0 - 2017-10-09

### Added

- [zendframework/zend-expressive-authorization#8](https://github.com/zendframework/zend-expressive-authorization/pull/8) adds
  support for http-interop/http-middleware 0.5.0 via a polyfill provided by the
  package webimpress/http-middleware-compatibility. Essentially, this means you
  can drop this package into an application targeting either the 0.4.1 or 0.5.0
  versions of http-middleware, and it will "just work".

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.1.0 - 2017-09-28

Initial release.

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
