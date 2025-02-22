# Console I/O

For some development-facing functions such as custom migrations or seeders, you might want to interact with the console. 
I've made a set of console helper functions available to make this easier.

These helpers can be imported as such:

```php
use App\Helpers\ConsoleHelper;
```

## Info

Display an info message in the console.

```php
ConsoleHelper::info('This is an info message');
```

## Success

Display a success message in the console.

```php
ConsoleHelper::success('This is a success message');
```

## Warn

Display a warning in the console.

```php
ConsoleHelper::warn('This is a warning message');
```

## Error

Display an error message in the console.

```php
ConsoleHelper::error('This is an error message');
```

## Alert

Display an alert in the console. High-priority.

Results in large, central-aligned text in the console. Vey imposing and hard to miss.

Only use for actual high priority alerts, do not overuse.

```php
ConsoleHelper::alert('This is a high-priority alert message');
```

## Task

Safely runs a taks, times the task and displays completion state (E.g.: DONE, FAIL, etc...).

```php
ConsoleHelper::task('Doing thing A', function () {
  // Write task A here. E.g.: alter the database.
});
```

If the task fails, this task will be marked as failed. The exception can be caught in a try-catch block.

If you have multiple database tasks, you might want the entire batch to roll back if a failure occurs. This is called making the DB operation atomic. It either completes fully, or not at all. This can be accomplished using DB transactions. 

Following is a safe, atomic implementation of multiple tasks:

```php
try {
 DB::transaction(function () {
    // Task A
    ConsoleHelper::task('Doing thing A', function () {
      // Write task A here. E.g.: alter the database.
    });

    // Task B
    ConsoleHelper::task('Doing thing B', function () {
      // Write task B here. E.g.: alter the database.
    });
  });
} catch (Exception $e) {
  ConsoleHelper::printError($e);
}
```

This approach is useful for making development-facing operations safer. It for example aids in the creation of migrations, seeders, etc.

## Two column detail

Prints out two strings, one left-aligned and the other right-aligned, with dots in between as with `ConsoleHelper::task`.

```php
ConsoleHelper::twoColumnDetail('Im on the left', 'Im on the right');
```

## Print error

Prints out an error message extracted from an exception.

```php
try {
  // Some code that might throw an exception
} catch (Exception $e) {
  // Exception doesn't need to originate from a try-catch block, simply done for demonstration purposes.
  ConsoleHelper::printError($e);
}
```

## Confirm

Prints out a confirmation message and waits for user input (via console). Returns boolean. 

```php
if (ConsoleHelper::confirm('Are you sure you want to do this?')) {
  // Do the thing
} else {
  // Do something else
}
```

## Ask

Prints out a question and waits for user input (via console). Returns string.

```php
$answer = ConsoleHelper::ask('What is your name?');
// Do something with the $answer
```

