# Permission System

This project uses a [Role-Based Access Control (RBAC)](https://en.wikipedia.org/wiki/Role-based_access_control) permission system. Below is a guide on how to use and extend the permission system.

### ⚠️ NOTICE

> Team members, before using the permission system yourself, read this entire document. There is some information here which, when not kept in mind, could make development infinitely more painful because of the global nature of the permission system.

## Roles and Permissions Seeder

All roles and permissions are defined in [RolesAndPermissionsSeeder.php](/database/seeders/RolesAndPermissionsSeeder.php). There you can add your own roles and permissions as needed by your department.

## Updating / initializing permission system

To utilise the permission system, the database has to be seeded with the required information.

You don't have to run this command just yet, but I'm placing it here because you *will* need to run it to test any of your changes. For example, after you have added your own permissions and want to check if the operation was successful.

To seed the permission system specifically, you can run the following command:

```bash
./artisan db:seed --class=RolesAndPermissionsSeeder
```

Which will result in an output similar to the following, albeit a little more colorful:

```txt
  INFO  Initializing roles and permissions.
  INFO  Starting transaction.  

  Removing potential existing roles and permissions .................................................................................... 7.22ms DONE
  Resetting cached roles and permissions ............................................................................................... 3.54ms DONE
  Defining permission nodes ........................................................................................................... 22.42ms DONE
  Updating cache with new permission nodes ............................................................................................. 0.38ms DONE
  Defining roles and assigning permissions ............................................................................................ 17.90ms DONE
  Applying role inheritance ............................................................................................................ 0.00ms DONE

  SUCCESS  Roles and permissions initialized.  
```

This seeder also gets run automatically when a normal seed command is called.

For safety, I've made this command is atomic, meaning it will complete fully and without incident or not at all. If an error is encountered, for example when you enter an invalid permission name, all changes already made that call will be rolled back.

The system will also clearly tell you where something went wrong. This looks like the following:

```txt
  INFO  Initializing roles and permissions.
  INFO  Starting transaction.  
  
  Removing potential existing roles and permissions .................................................................................... 6.61ms DONE
  Resetting cached roles and permissions ............................................................................................... 3.01ms DONE
  Defining permission nodes ........................................................................................................... 19.96ms DONE
  Updating cache with new permission nodes ............................................................................................. 0.37ms DONE
  Defining roles and assigning permissions ............................................................................................ 14.15ms DONE
  Applying role inheritance ............................................................................................................ 0.99ms FAIL

  ERROR  Initialization failed with the following exception: There is no role named `useree` for guard `web`. in vendor/spatie/laravel-permission/src/Exceptions/RoleDoesNotExist.php.  

  SUCCESS  Rollback complete. 
  ```

## Adding your own permissions

To add your own permissions, you can define them in the `$permissions` array at the top of [RolesAndPermissionsSeeder.php](/database/seeders/RolesAndPermissionsSeeder.php), under `Definitions`. Add them to the already existing array, don't create your own array. They will get automatically initialized when the seed command is run.

Following is an example of the pre-existing permissions array filled with an example permission block from the distribution center department:

```php
  private array $permissions = [
    // BEGIN: distribution center permissions
    // Post permissions
    'posts.edit.all',
    'posts.edit.own',

    // Console permissions
    'console.view',
    'console.command.reboot',
    // END: distribution center permissions
  ];
```

As you can see, permissions are kept by type and then by department. This convention is in place since this file will most likely be edited multiple times by every department. By keeping your own additions seperated and clearly organised, merges will be made easier.

Always add a `,` to the end of the last permission in a department. This is to prevent merge conflicts when adding new permissions.

### Permission naming conventions

To maintain consistency, clarity, and scalability in permission naming, follow these guidelines when creating new permissions names.

Permissions follow a dot-separated hierarchy:

```txt
<feature_group>.<feature_name>.<action/scope (optional)>.(...)
```

E.g.:

```txt
console.view
console.command.reboot
```

- **feature_group**: Broad category (e.g., posts, console).
- **feature_name**: Specific functionality (e.g., edit, command).
- **action/scope (optional)**: Defines operation or scope (e.g., own, global, reboot).
- **(...)**: Additional levels as needed.

Following is a list of do's and don'ts when naming permissions. This to not only make my job managing permissions easier, but also to facilitate team-wide understanding and consistency.

### ✅ Do’s

Use clear, descriptive names

- E.g.: users.profile.update.email instead of users.editemail.

Use consistent terminology

- E.g.: Use own and global instead of mixing own, all, and any.

Follow a logical hierarchy

- E.g.: console.command.reboot (instead of console.reboot.cmd).

Use full words instead of abbreviations

- E.g.: console.command.execute (instead of console.cmd.exec).

### ❌ Don'ts

Avoid redundancy

- E.g.: posts.manage.edit should just be posts.edit.

Don’t use mixed naming conventions

- E.g.: Naming your permission feature_x.edit.global whilst the convention set by others already is feature_y.edit.all.

Avoid overly specific permissions unless necessary

- E.g.: console.command.reboot.server1 is too specific; use console.command.reboot and apply further checks in logic.

Avoid special characters, keep to alphanumeric

- E.g.: air&traffic.edit§12 is not allowed. Use air_traffic.edit.12 instead.

## Adding your own roles

Roles are a predefined set of permissions that can be assigned to users. One role may be assigned to multiple user, but a user can only have one role at a time.

To add your own roles, you can define them in the `$roles` array at the top of [RolesAndPermissionsSeeder.php](/database/seeders/RolesAndPermissionsSeeder.php), under `Definitions`. Add them to the already existing array, don't create your own array. They will get automatically initialized when the seed command is run.

```php
  private array $role = [
    // BEGIN: distribution center roles
    'sysadmin' => ['*'],
    'admin'    => ['posts.*', 'console.command.ping', 'console.comand.reboot'],
    'group_B3' => ['scanner.actiongroup.18', 'scanner.actiongroup.39'],
    'employee' => ['posts.edit.all', 'console.view', 'console.cmd.reboot'],
    'user'     => ['posts.edit.own'],
    // END: distribution center roles
  ];
```

As you can see, roles are kept by department. This convention is in place since this file will most likely be edited multiple times by every department. By keeping your own additions seperated and clearly organised, merges will be made easier.

A role is defined by a key-value pair. The key is the role name, and the value is an array of permissions this role has access to.

Always add a `,` to the end of the last role in a department. This is to prevent merge conflicts when adding new permissions.

A special permission wildcard (`*`) exists to give a role access to all permissions, as illustrated by the `sysadmin` role in the above example. Can be used for testing purposes, but should be mostly avoided in production. Not to be confused with `posts.*` under `admin`. This is simply a custom permission name, not a pre-defined mechanism. It will not automatically give you access to all permissions of a specific node.

## Adding role inheritance

Role inheritance is a way to easily give a child role access to all permissions from a parent role. This is useful when you have multiple roles that share a lot of permissions, but also require some unique permissions.

To add your own inheritance, you can define them in the `$roleInheritance` array at the top of [RolesAndPermissionsSeeder.php](/database/seeders/RolesAndPermissionsSeeder.php), under `Definitions`. Add them to the already existing array, don't create your own array. They will get automatically initialized when the seed command is run.

```php
  // BEGIN: distribution center inheritance
  private array $roleInheritance = [
    'employee' => 'group_B3',
  ];
  // END: distribution center inheritance
```

Where:

```php
'<parent_role>' => '<child_role>',
```

or in other words:

```php
'<inheritee>' => '<inheritor>',
```

In this example, the `group_B3` role inherits all permissions from the `employee` role. This inheritance means that if new permission nodes are added to the `employee` role, these will automatically be available to the `group_B3` role upon re-initialisation of the permission system.

This makes it so that `group_B3` is an `employee` in all ways but name. Consequently, you can’t look up all `employee` role members and expect to see the `group_B3` role members in the results. To work around this, you could add a permission that serves solely to tag the parent role, with no other function.

E.g.: you could add `group_tag.employee` to the `employee` role. This way, you can look up all roles which inherit from `employee` by looking for the `group_tag.employee` permission, and treating `employee` as an abstract role, only to be inherited, never assigned.

In this example, `group_B3` is set up to function as a regular `employee`, but with additional scanner-app-related permissions specific to that group.

## Manually checking permissions, roles and assignment

In development, you might want to check if a user has a specific permission, role, or if a role has a specific permission. You may do so using the following command:

```bash
./artisan permission:show
```

This will result in an output similar to the following:

```txt
+--------------------+-------+----------+------+
|                    | admin | sysadmin | user |
+--------------------+-------+----------+------+
| console.cmd.reboot |  ✔    |  ✔       |  ·   |
| console.view       |  ✔    |  ✔       |  ·   |
| posts.edit.all     |  ✔    |  ✔       |  ·   |
| posts.edit.own     |  ·    |  ✔       |  ✔   |
+--------------------+-------+----------+------+
```

## Utilizing permissions in code

The permission system let's you use Laravel's native `@can` blade directive to check if a user has a certain permission. E.g.:

```php
<body>
@can('articles.edit.own')
  <h1>Edit article</h1>
@endcan
</body>
```

Or, with manual variable fetching:

```php
// HTML code...
@if(auth()->user()->can('articles.edit.own'))
//
@endif
```

You can use `@can`, `@cannot`, `@canany`, and `@guest` to test for permission-related access. You can also mix other conditions but only in the `@if` directive. E.g.:

```php
// HTML code...
@cannot('posts.view.any')
//
@endcan
```

```php
// HTML code...
@if(auth()->user()->can('posts.view.any') || (auth()->user()->can('posts.view.own') && other_condition))
//  
@endif
```

You can also use `@haspermission('permission-name')`, with a corresponding `@endhaspermission`.

There is no `@hasanypermission` directive: use `@canany` instead.

### Considerations

Although it is possible to check for a specific group instead of a permission node, it is not recommended to do so and I have therefore not included it in this guide. If you base feature access on roles, only people with that exact role will be able to access your feature. Even administrators will fail your role check. Work with permissions for access checks exclusively as to be more modular and flexible. This way, when we need to present, we can simply use some kind of superadmin role to view every feature.