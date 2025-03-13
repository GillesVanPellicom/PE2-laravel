<?php

namespace App\Services\Router\Types\Exceptions;

class RouterException extends \Exception {
  public function __construct(string $message) {
    $trace = $this->getTrace();
    $caller = $trace[0] ?? [];
    $class = $caller['class'] ?? 'UnknownClass';
    $function = $caller['function'] ?? 'unknownFunction';

    $shortClass = (new \ReflectionClass($this))->getShortName();

    $fullMessage = "$shortClass in $class::$function() - $message";
    parent::__construct($fullMessage);
  }
}