<?php

namespace App\Helpers;

use Exception;
use Illuminate\Console\OutputStyle;
use Illuminate\Console\View\Components\Factory;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;


class ConsoleHelper {

  // Output methods

  /**
   * Executes a task and logs the result to console.
   * Task is executed safely, meaning that any exceptions thrown by the task are caught and logged.
   *
   * @param  string  $msg  Description of the task
   * @param  callable  $task  Task to execute
   * @return void
   */
  public static function task(string $msg, callable $task): void {
    self::getFactory()->task($msg, $task);
  }

  public static function info(string $msg): void {
    self::getFactory()->info($msg);
  }

  public static function alert(string $msg): void {
    self::getFactory()->alert($msg);
  }

  public static function error(string $msg): void {
    self::getFactory()->error($msg);
  }

  public static function warn(string $msg): void {
    self::getFactory()->warn($msg);
  }

  public static function success(string $msg): void {
    self::getFactory()->success($msg);
  }

  public static function twoColumnDetail(string $l, $r): void {
    self::getFactory()->twoColumnDetail($l, $r);
  }

  public static function printError(Exception $e): void {
    $errMsg = sprintf(
      "Permission system initialization failed with the following exception: %s in %s.",
      $e->getMessage(),
      $e->getFile(),
    );

    ConsoleHelper::error($errMsg);
  }

  // Input methods

  public static function secret(string $msg): string {
    return self::getFactory()->secret($msg);
  }

  public static function confirm(string $msg): bool {
    return self::getFactory()->confirm($msg);
  }

  public static function ask(string $msg): string {
    return self::getFactory()->ask($msg);
  }


  /**
   * Initializes and returns a Factory instance.
   *
   * @return Factory
   */
  private static function getFactory(): Factory {
    $outputStyle = new OutputStyle(new ArgvInput(), new ConsoleOutput());
    return new Factory($outputStyle);
  }
}