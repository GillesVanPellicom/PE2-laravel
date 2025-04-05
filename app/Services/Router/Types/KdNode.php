<?php

namespace App\Services\Router\Types;


/**
 * Class KdNode
 *
 * Represents a node in a k-d tree.
 *
 * @package App\Services\Router\Types
 */
class KdNode {
  public Node $node;       // The Node object
  public ?KdNode $left;       // Left child KdNode
  public ?KdNode $right;      // Right child KdNode
  public int $axis;       // 0 for latitude, 1 for longitude


  /**
   * KdNode constructor.
   *
   * @param  Node  $node  The Node object
   * @param  int  $axis  Axis for splitting (0 for latitude, 1 for longitude)
   */
  public function __construct(Node $node, int $axis) {
    $this->node = $node;
    $this->axis = $axis;
    $this->left = null;
    $this->right = null;
  }
}