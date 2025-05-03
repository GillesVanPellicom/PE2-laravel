<?php

namespace App\Services\Router;

use App\Services\Router\Types\NodeType;

/**
 * Class NodeHandlingTimeProvider
 *
 * Provides handling time information for different node types.
 *
 * @package App\Services\Router
 */
class NodeHandlingTimeProvider {
  /**
   * Handling times in hours for different node types.
   * This represents the base-average time spent at a node for package handling.
   *
   * @var array<string, float>
   */
  private array $nodeHandlingTimes = [
    'PICKUP_POINT' => 0.25,       // 15 minutes
    'DISTRIBUTION_CENTER' => 0.5, // 30 minutes
    'AIRPORT' => 1.0,             // 1 hour
    'ADDRESS' => 0.1,             // 6 minutes
    'OFFICE' => 0.2,              // 12 minutes
  ];

  /**
   * Get the handling time for a specific node type.
   *
   * @param  NodeType  $nodeType  The node type
   * @return float The handling time in hours
   */
  public function getHandlingTime(NodeType $nodeType): float {
    return $this->nodeHandlingTimes[$nodeType->value] ?? 0;
  }
}