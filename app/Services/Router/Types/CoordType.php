<?php

namespace App\Services\Router\Types;

/**
 * Enum NodeType represents the type of a node in the RouterGraph.
 *
 * @package App\Services\Router\Types
 */
enum CoordType: string {
  case DEGREE = 'DEGREE';
  case RADIAN = 'RADIAN';
}