# Authorization adapters

You can configure the authorization adapter to use via your service container
configuration. Specifically, you can either map the service name
`Mezzio\Authorization\AuthorizationInterface` to a factory, or alias it
to the appropriate service.

For instance, using [Mezzio container configuration](https://docs.mezzio.dev/mezzio/v3/features/container/config/),
you could select the mezzio-authorization-acl adapter in either of the
following ways:

- Using an alias:
  
  ```php
  use Mezzio\Authorization\AuthorizationInterface;
  use Mezzio\Authorization\Acl\LaminasAcl;
  
  return [
      'dependencies' => [
          // Using an alias:
          'aliases' => [
              AuthorizationInterface::class => LaminasAcl::class,
          ],
      ],
  ];
  ```

- Mapping to a factory:
  
  ```php
  use Mezzio\Authorization\AuthorizationInterface;
  use Mezzio\Authorization\Acl\LaminasAclFactory;
  
  return [
      'dependencies' => [
          // Using a factory:
          'factories' => [
              AuthorizationInterface::class => LaminasAclFactory::class,
          ],
      ],
  ];
  ```

We provide two different adapters.

- The RBAC adapter is provided by [mezzio-authorization-rbac](https://github.com/mezzio/mezzio-authorization-rbac).
- The ACL adapter is provided by [mezzio-authorization-acl](https://github.com/mezzio/mezzio-authorization-acl/).

Each adapter is installable via [Composer](https://getcomposer.org):

```bash
$ composer require mezzio/mezzio-authorization-rbac
# or
$ composer require mezzio/mezzio-authorization-acl
```

In each adapter, we use the **route name** as the resource. This means you
can specify if a role is authorized to access a specific HTTP _route_.
However, this is just one approach to implementing an authorization system; you
can create your own system by implementing the
[AuthorizationInterface](https://github.com/mezzio/mezzio-authorization/blob/master/src/AuthorizationInterface.php).

For more information on the adapters, please read the
[RBAC documentation](https://docs.mezzio.dev/mezzio-authorization-rbac/)
and the [ACL documentation](https://docs.mezzio.dev/mezzio-authorization-acl/).
