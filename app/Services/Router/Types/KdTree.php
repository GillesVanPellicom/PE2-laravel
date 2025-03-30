<?php

namespace App\Services\Router\Types;

/**
 * Class KdTree
 *
 * Represents a k-d tree for organizing nodes for nearest neighbor search.
 *
 * @package App\Services\Router\Types
 */
class KdTree {

  private ?KdNode $root; // Root node


  /**
   * KdTree constructor.
   *
   * @param  array  $nodes  Array of Node objects to build the tree from
   */
  public function __construct(array $nodes) {
    $this->root = $this->buildTree($nodes, 0);
  }


  /**
   * Builds the k-d tree recursively.
   *
   * @param  array  $nodes  Array of Node objects
   * @param  int  $depth  Current depth in the tree
   * @return KdNode|null The root node of the k-d tree
   */
  private function buildTree(array $nodes, int $depth): ?KdNode {
    if (empty($nodes)) {
      return null;
    }

    $k = 2; // 2D: latitude and longitude
    $axis = $depth % $k; // Determine axis based on depth

    // Sort nodes by the current axis
    usort($nodes, function ($a, $b) use ($axis) {
      if ($axis == 0) {
        return $a->getLat(CoordType::DEGREE) <=> $b->getLat(CoordType::DEGREE);
      }
      return $a->getLong(CoordType::DEGREE) <=> $b->getLong(CoordType::DEGREE);
    });

    $median = floor(count($nodes) / 2); // Find median index
    $node = $nodes[$median]; // Select median node
    $leftNodes = array_slice($nodes, 0, $median); // Nodes to the left
    $rightNodes = array_slice($nodes, $median + 1); // Nodes to the right

    // Create new KdNode and recursively build left and right subtrees
    $kdNode = new KdNode($node, $axis);
    $kdNode->left = $this->buildTree($leftNodes, $depth + 1);
    $kdNode->right = $this->buildTree($rightNodes, $depth + 1);

    return $kdNode;
  }


  /**
   * Finds the nearest node to the given latitude and longitude.
   *
   * @param  float  $lat  Latitude in degrees
   * @param  float  $long  Longitude in degrees
   * @return Node|null The nearest Node object or null if the tree is empty
   */
  public function findNearest(float $lat, float $long): ?Node {
    if ($this->root === null) {
      return null;
    }
    return $this->nearest($this->root, $lat, $long, $this->root->node, PHP_FLOAT_MAX)[0];
  }


  /**
   * Recursively searches for the nearest node.
   *
   * @param  KdNode|null  $kdNode  Current node in the k-d tree
   * @param  float  $lat  Latitude in degrees
   * @param  float  $long  Longitude in degrees
   * @param  Node  $best  The current best node
   * @param  float  $bestDist  The current best distance
   * @return array An array containing the best node and the best distance
   */
  private function nearest(?KdNode $kdNode, float $lat, float $long, Node $best, float $bestDist): array {
    if ($kdNode === null) {
      return [$best, $bestDist];
    }

    $currentNode = $kdNode->node;
    $currentLat = $currentNode->getLat(CoordType::DEGREE);
    $currentLong = $currentNode->getLong(CoordType::DEGREE);
    $dx = $currentLat - $lat;
    $dy = $currentLong - $long;
    $dist = $dx * $dx + $dy * $dy; // Calculate squared distance

    // Update best node and distance if current node is closer
    if ($dist < $bestDist) {
      $best = $currentNode;
      $bestDist = $dist;
    }

    $axis = $kdNode->axis;
    $diff = ($axis == 0) ? $lat - $currentLat : $long - $currentLong;
    $near = ($diff <= 0) ? $kdNode->left : $kdNode->right; // Determine near subtree
    $far = ($diff <= 0) ? $kdNode->right : $kdNode->left; // Determine far subtree

    // Recursively search the near subtree
    [$best, $bestDist] = $this->nearest($near, $lat, $long, $best, $bestDist);

    // Check if we need to search the far subtree
    $planeDist = ($axis == 0) ? $lat - $currentLat : $long - $currentLong;
    if ($planeDist * $planeDist < $bestDist) {
      [$best, $bestDist] = $this->nearest($far, $lat, $long, $best, $bestDist);
    }

    return [$best, $bestDist];
  }


  public function visualize(): void {

    $this->printTree($this->root, 0, "", true);
    echo "\n";
  }

/**
 * Prints the KD-tree structure with color formatting for debugging.
 *
 * @param ?KdNode $node The current node in the KD-tree
 * @param int $depth The current depth in the tree
 * @param string $prefix The prefix string for tree visualization
 * @param bool $isTail Whether this is the last child of its parent
 * @param bool $isLeft Whether this node is a left child
 * @return void
 */
private function printTree(?KdNode $node, int $depth, string $prefix, bool $isTail, bool $isLeft = true): void {
    if ($node === null) {
        return;
    }

    $lat = $node->node->getLat(CoordType::DEGREE);
    $long = $node->node->getLong(CoordType::DEGREE);
    $axis = $node->axis; // 0 for latitude, 1 for longitude
    $axisLabel = $axis == 0 ? "Lateral axis" : "Longitudinal axis";
    $splitValue = $axis == 0 ? $lat : $long;
    $nodeInfo = sprintf("\033[1;33m%s\033[0m (%s: \033[1;32m%.4f\033[0m) [\033[1;35m%.4f\033[0m,\033[1;35m %.4f\033[0m]",
        $node->node->getID(), $axisLabel, $splitValue, $lat, $long);

    // Print the current node
    if ($depth == 0) {
        echo "\033[1;34m[Root]\033[0m " . $nodeInfo . "\n";
    } else {
        echo "\033[1;37m" . $prefix . ($isTail ? "└──" : "├──") . "\033[0m"
             . ($isLeft ? "\033[1;36mL\033[0m" : "\033[1;31mR\033[0m")
             . ": " . $nodeInfo . "\n";
    }

    // Determine the new prefix for children
    $newPrefix = $prefix . ($isTail ? "    " : "\033[1;37m│   \033[0m");

    // Print left and right children
    if ($node->left !== null || $node->right !== null) {
        if ($node->left !== null) {
            $this->printTree($node->left, $depth + 1, $newPrefix, $node->right === null, true);
        }
        if ($node->right !== null) {
            $this->printTree($node->right, $depth + 1, $newPrefix, true, false);
        }
    }

    // Add a newline to separate different levels and maintain the pipe symbol
    if ($depth > 0 && $isTail) {
        echo "\033[1;37m" . $prefix . "\033[0m\n";
    }
}
}