<?php

namespace App\Services\Router\Types;

/**
 * Enum NodeType represents the type of a node in the RouterGraph.
 *
 * @package App\Services\Router\Types
 */
enum MoveOperationType: string {
  case IN = "IN";
  case OUT = 'OUT';
  case DELIVER = 'DELIVER';
}