<?php

namespace Tests\Unit\Services\Router;

use App\Services\Router\Types\NodeType;
use PHPUnit\Framework\TestCase;
use App\Services\Router\Types\RouterGraph;
use App\Services\Router\Types\Node;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

class RouterGraphTest extends TestCase
{
//  public function testAddNode()
//  {
//    $graph = new RouterGraph();
//    $node = new Node('testNode', NodeType::AIRPORT);
//    $this->assertTrue($graph->addNode($node));
//    $this->assertFalse($graph->addNode($node));
//  }
//
//  public function testAddNodeRandom()
//  {
//    $graph = new RouterGraph();
//    $node = new Node(Uuid::uuid4()->toString());
//    $this->assertTrue($graph->addNode($node));
//    $this->assertFalse($graph->addNode($node));
//  }
//
//  public function testAddEdge()
//  {
//    $graph = new RouterGraph();
//    $node1 = new Node('aaf81e76-781d-47d4-9c47-369655fcc9c6');
//    $node2 = new Node('3aa84cb5-2234-4751-b633-bf655026597c');
//    $graph->addNode($node1);
//    $graph->addNode($node2);
//
//    $graph->addEdge($node1, $node2, 10);
//    $edges = $graph->getEdges();
//    $this->assertEquals(10, $edges['aaf81e76-781d-47d4-9c47-369655fcc9c6']['3aa84cb5-2234-4751-b633-bf655026597c']);
//    $this->assertEquals(10, $edges['3aa84cb5-2234-4751-b633-bf655026597c']['aaf81e76-781d-47d4-9c47-369655fcc9c6']);
//  }
//
//  public function testAddEdgeRandom()
//  {
//    $graph = new RouterGraph();
//    $node1 = new Node(Uuid::uuid4()->toString());
//    $node2 = new Node(Uuid::uuid4()->toString());
//    $graph->addNode($node1);
//    $graph->addNode($node2);
//
//    $weight = mt_rand(1, 100);
//    $graph->addEdge($node1, $node2, $weight);
//    $edges = $graph->getEdges();
//    $this->assertEquals($weight, $edges[$node1->getName()][$node2->getName()]);
//    $this->assertEquals($weight, $edges[$node2->getName()][$node1->getName()]);
//  }
//
//  public function testAddEdgeWithNonExistentNodesThrowsException()
//  {
//    $graph = new RouterGraph();
//    $node1 = new Node('aaf81e76-781d-47d4-9c47-369655fcc9c6');
//    $node2 = new Node('3aa84cb5-2234-4751-b633-bf655026597c');
//
//    $this->expectException(InvalidArgumentException::class);
//    $graph->addEdge($node1, $node2, 10);
//  }
//
//  public function testAddEdgeWithNonExistentNodesThrowsExceptionRandom()
//  {
//    $graph = new RouterGraph();
//    $node1 = new Node(Uuid::uuid4()->toString());
//    $node2 = new Node(Uuid::uuid4()->toString());
//
//    $this->expectException(InvalidArgumentException::class);
//    $graph->addEdge($node1, $node2, mt_rand(1, 100));
//  }
//
//  public function testAddEdgeWithLoopThrowsException()
//  {
//    $graph = new RouterGraph();
//    $node = new Node('node');
//    $graph->addNode($node);
//
//    $this->expectException(InvalidArgumentException::class);
//    $graph->addEdge($node, $node, 10);
//  }
//
//  public function testAddEdgeWithLoopThrowsExceptionRandom()
//  {
//    $graph = new RouterGraph();
//    $node = new Node(Uuid::uuid4()->toString());
//    $graph->addNode($node);
//
//    $this->expectException(InvalidArgumentException::class);
//    $graph->addEdge($node, $node, mt_rand(1, 100));
//  }
//
//  public function testAddEdgeWithNonPositiveWeightThrowsException()
//  {
//    $graph = new RouterGraph();
//    $node1 = new Node('aaf81e76-781d-47d4-9c47-369655fcc9c6');
//    $node2 = new Node('3aa84cb5-2234-4751-b633-bf655026597c');
//    $graph->addNode($node1);
//    $graph->addNode($node2);
//
//    $this->expectException(InvalidArgumentException::class);
//    $graph->addEdge($node1, $node2, 0);
//  }
//
//  public function testAddEdgeWithNonPositiveWeightThrowsExceptionRandom()
//  {
//    $graph = new RouterGraph();
//    $node1 = new Node(Uuid::uuid4()->toString());
//    $node2 = new Node(Uuid::uuid4()->toString());
//    $graph->addNode($node1);
//    $graph->addNode($node2);
//
//    $this->expectException(InvalidArgumentException::class);
//    $graph->addEdge($node1, $node2, mt_rand(-100, 0));
//  }
//
//  public function testGetNodes()
//  {
//    $graph = new RouterGraph();
//    $node1 = new Node('aaf81e76-781d-47d4-9c47-369655fcc9c6');
//    $node2 = new Node('3aa84cb5-2234-4751-b633-bf655026597c');
//    $graph->addNode($node1);
//    $graph->addNode($node2);
//
//    $nodes = $graph->getNodes();
//    $this->assertCount(2, $nodes);
//    $this->assertContains($node1, $nodes);
//    $this->assertContains($node2, $nodes);
//  }
//
//  public function testGetNodesRandom()
//  {
//    $graph = new RouterGraph();
//    $nodeCount = mt_rand(2, 10);
//    $nodes = [];
//
//    for ($i = 0; $i < $nodeCount; $i++) {
//      $node = new Node(Uuid::uuid4()->toString());
//      $graph->addNode($node);
//      $nodes[] = $node;
//    }
//
//    $retrievedNodes = $graph->getNodes();
//    $this->assertCount($nodeCount, $retrievedNodes);
//    foreach ($nodes as $node) {
//      $this->assertContains($node, $retrievedNodes);
//    }
//  }
//
//  public function testGetEdges()
//  {
//    $graph = new RouterGraph();
//    $node1 = new Node('aaf81e76-781d-47d4-9c47-369655fcc9c6');
//    $node2 = new Node('3aa84cb5-2234-4751-b633-bf655026597c');
//    $graph->addNode($node1);
//    $graph->addNode($node2);
//    $graph->addEdge($node1, $node2, 10);
//
//    $edges = $graph->getEdges();
//    $this->assertArrayHasKey('aaf81e76-781d-47d4-9c47-369655fcc9c6', $edges);
//    $this->assertArrayHasKey('3aa84cb5-2234-4751-b633-bf655026597c', $edges['aaf81e76-781d-47d4-9c47-369655fcc9c6']);
//    $this->assertEquals(10, $edges['aaf81e76-781d-47d4-9c47-369655fcc9c6']['3aa84cb5-2234-4751-b633-bf655026597c']);
//  }
//
//  public function testGetEdgesRandom()
//  {
//    $graph = new RouterGraph();
//    $node1 = new Node(Uuid::uuid4()->toString());
//    $node2 = new Node(Uuid::uuid4()->toString());
//    $graph->addNode($node1);
//    $graph->addNode($node2);
//    $weight = mt_rand(1, 100);
//    $graph->addEdge($node1, $node2, $weight);
//
//    $edges = $graph->getEdges();
//    $this->assertArrayHasKey($node1->getName(), $edges);
//    $this->assertArrayHasKey($node2->getName(), $edges[$node1->getName()]);
//    $this->assertEquals($weight, $edges[$node1->getName()][$node2->getName()]);
//  }
//
//  public function testGetNeighbors()
//  {
//    $graph = new RouterGraph();
//    $node1 = new Node('aaf81e76-781d-47d4-9c47-369655fcc9c6');
//    $node2 = new Node('3aa84cb5-2234-4751-b633-bf655026597c');
//    $node3 = new Node('81eac871-1817-4ce6-8017-bcc397115b3e');
//    $graph->addNode($node1);
//    $graph->addNode($node2);
//    $graph->addNode($node3);
//
//    $graph->addEdge($node1, $node2, 10);
//    $graph->addEdge($node1, $node3, 20);
//
//    $neighbors = $graph->getNeighbors('aaf81e76-781d-47d4-9c47-369655fcc9c6');
//    $this->assertCount(2, $neighbors);
//    $this->assertArrayHasKey('3aa84cb5-2234-4751-b633-bf655026597c', $neighbors);
//    $this->assertArrayHasKey('81eac871-1817-4ce6-8017-bcc397115b3e', $neighbors);
//    $this->assertEquals(10, $neighbors['3aa84cb5-2234-4751-b633-bf655026597c']);
//    $this->assertEquals(20, $neighbors['81eac871-1817-4ce6-8017-bcc397115b3e']);
//  }
//
//  public function testGetNeighborsRandom()
//  {
//    $graph = new RouterGraph();
//    $node1 = new Node(Uuid::uuid4()->toString());
//    $node2 = new Node(Uuid::uuid4()->toString());
//    $node3 = new Node(Uuid::uuid4()->toString());
//    $graph->addNode($node1);
//    $graph->addNode($node2);
//    $graph->addNode($node3);
//
//    $weight1 = mt_rand(1, 100);
//    $weight2 = mt_rand(1, 100);
//    $graph->addEdge($node1, $node2, $weight1);
//    $graph->addEdge($node1, $node3, $weight2);
//
//    $neighbors = $graph->getNeighbors($node1->getName());
//    $this->assertCount(2, $neighbors);
//    $this->assertArrayHasKey($node2->getName(), $neighbors);
//    $this->assertArrayHasKey($node3->getName(), $neighbors);
//    $this->assertEquals($weight1, $neighbors[$node2->getName()]);
//    $this->assertEquals($weight2, $neighbors[$node3->getName()]);
//  }
//
//  public function testGetNeighborsForNonExistentNodeThrowsException()
//  {
//    $graph = new RouterGraph();
//    $node1 = new Node('aaf81e76-781d-47d4-9c47-369655fcc9c6');
//    $graph->addNode($node1);
//
//    $this->expectException(InvalidArgumentException::class);
//    $graph->getNeighbors('nonExistentNode');
//  }
//
//  public function testGetNeighborsForNonExistentNodeThrowsExceptionRandom()
//  {
//    $graph = new RouterGraph();
//    $node1 = new Node(Uuid::uuid4()->toString());
//    $graph->addNode($node1);
//
//    $this->expectException(InvalidArgumentException::class);
//    $graph->getNeighbors(Uuid::uuid4()->toString());
//  }
//
//  public function testGetNeighborsForNodeWithNoEdges()
//  {
//    $graph = new RouterGraph();
//    $node1 = new Node('aaf81e76-781d-47d4-9c47-369655fcc9c6');
//    $graph->addNode($node1);
//
//    $neighbors = $graph->getNeighbors('aaf81e76-781d-47d4-9c47-369655fcc9c6');
//    $this->assertCount(0, $neighbors);
//  }
//
//  public function testGetNeighborsForNodeWithNoEdgesRandom()
//  {
//    $graph = new RouterGraph();
//    $node1 = new Node(Uuid::uuid4()->toString());
//    $graph->addNode($node1);
//
//    $neighbors = $graph->getNeighbors($node1->getName());
//    $this->assertCount(0, $neighbors);
//  }
}