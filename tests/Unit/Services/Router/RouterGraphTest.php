<?php

namespace Tests\Unit\Services\Router;

use App\Services\Router\Types\NodeType;
use PHPUnit\Framework\TestCase;
use App\Services\Router\Types\RouterGraph;
use App\Services\Router\Types\Node;
use InvalidArgumentException;

class RouterGraphTest extends TestCase {

  public function testAddNodeValid() {
    $graph = new RouterGraph();
    $nodeID = $graph->addNode('1', 'Test Node', 37.422, -122.084, NodeType::DISTRIBUTION_CENTER, 'true', 'false');
    $this->assertEquals('1', $nodeID);
  }

  public function testAddNodeInvalidID() {
    $this->expectException(InvalidArgumentException::class);
    $graph = new RouterGraph();
    $graph->addNode('', 'Test Node', 37.422, -122.084, NodeType::DISTRIBUTION_CENTER, 'true', 'false');
  }

  public function testAddNodeDuplicateID() {
    $this->expectException(InvalidArgumentException::class);
    $graph = new RouterGraph();
    $graph->addNode('1', 'Test Node', 37.422, -122.084, NodeType::DISTRIBUTION_CENTER, 'true', 'false');
    $graph->addNode('1', 'Duplicate Node', 37.422, -122.084, NodeType::DISTRIBUTION_CENTER, 'true', 'false');
  }

  public function testAddNodeInvalidLatitude() {
    $this->expectException(InvalidArgumentException::class);
    $graph = new RouterGraph();
    $graph->addNode('1', 'Test Node', 100.0, -122.084, NodeType::DISTRIBUTION_CENTER, 'true', 'false');
  }

  public function testAddNodeInvalidLongitude() {
    $this->expectException(InvalidArgumentException::class);
    $graph = new RouterGraph();
    $graph->addNode('1', 'Test Node', 37.422, -200.0, NodeType::DISTRIBUTION_CENTER, 'true', 'false');
  }

  public function testAddNodeInvalidEntryNode() {
    $this->expectException(InvalidArgumentException::class);
    $graph = new RouterGraph();
    $graph->addNode('1', 'Test Node', 37.422, -122.084, NodeType::DISTRIBUTION_CENTER, 'invalid', 'false');
  }

  public function testAddNodeInvalidExitNode() {
    $this->expectException(InvalidArgumentException::class);
    $graph = new RouterGraph();
    $graph->addNode('1', 'Test Node', 37.422, -122.084, NodeType::DISTRIBUTION_CENTER, 'true', 'invalid');
  }

  public function testAddEdgeValid() {
    $graph = new RouterGraph();
    $graph->addNode('1', 'Node 1', 37.422, -122.084, NodeType::DISTRIBUTION_CENTER, 'true', 'false');
    $graph->addNode('2', 'Node 2', 37.7749, -122.4194, NodeType::DISTRIBUTION_CENTER, 'true', 'false');
    $graph->addEdge('1', '2');
    $edges = $graph->getEdges();
    $this->assertArrayHasKey('1', $edges);
    $this->assertArrayHasKey('2', $edges['1']);
  }

  public function testAddEdgeNonExistentStartNode() {
    $this->expectException(InvalidArgumentException::class);
    $graph = new RouterGraph();
    $graph->addNode('1', 'Node 1', 37.422, -122.084, NodeType::DISTRIBUTION_CENTER, 'true', 'false');
    $graph->addEdge('nonexistent', '1');
  }

  public function testAddEdgeNonExistentEndNode() {
    $this->expectException(InvalidArgumentException::class);
    $graph = new RouterGraph();
    $graph->addNode('1', 'Node 1', 37.422, -122.084, NodeType::DISTRIBUTION_CENTER, 'true', 'false');
    $graph->addEdge('1', 'nonexistent');
  }

  public function testAddEdgeLoop() {
    $this->expectException(InvalidArgumentException::class);
    $graph = new RouterGraph();
    $graph->addNode('1', 'Node 1', 37.422, -122.084, NodeType::DISTRIBUTION_CENTER, 'true', 'false');
    $graph->addEdge('@1', '@1');
  }

  public function testGetNodes() {
    $graph = new RouterGraph();
    $graph->addNode('1', 'Node 1', 37.422, -122.084, NodeType::DISTRIBUTION_CENTER, 'true', 'false');
    $graph->addNode('2', 'Node 2', 37.7749, -122.4194, NodeType::DISTRIBUTION_CENTER, 'true', 'false');
    $nodes = $graph->getNodes();
    $this->assertCount(2, $nodes);
  }

  public function testGetNodeValid() {
    $graph = new RouterGraph();
    $graph->addNode('1', 'Node 1', 37.422, -122.084, NodeType::DISTRIBUTION_CENTER, 'true', 'false');
    $node = $graph->getNode('1');
    $this->assertInstanceOf(Node::class, $node);
  }

  public function testGetNodeInvalid() {
    $this->expectException(InvalidArgumentException::class);
    $graph = new RouterGraph();
    $graph->getNode('nonexistent');
  }

  public function testGetNeighborsValid() {
    $graph = new RouterGraph();
    $graph->addNode('1', 'Node 1', 37.422, -122.084, NodeType::DISTRIBUTION_CENTER, 'true', 'false');
    $graph->addNode('2', 'Node 2', 37.7749, -122.4194, NodeType::DISTRIBUTION_CENTER, 'true', 'false');
    $graph->addEdge('1', '2');
    $neighbors = $graph->getNeighbors('1');
    $this->assertArrayHasKey('2', $neighbors);
  }

  public function testGetNeighborsInvalid() {
    $this->expectException(InvalidArgumentException::class);
    $graph = new RouterGraph();
    $graph->getNeighbors('nonexistent');
  }

  public function testPrintGraph() {
    $graph = new RouterGraph();
    $graph->addNode('1', 'Node 1', 37.422, -122.084, NodeType::DISTRIBUTION_CENTER, 'true', 'false');
    $graph->addNode('2', 'Node 2', 37.7749, -122.4194, NodeType::DISTRIBUTION_CENTER, 'true', 'false');
    $graph->addEdge('1', '2');
    $this->expectOutputRegex('/=== Graph Structure ===/');
    $graph->printGraph();
  }
}