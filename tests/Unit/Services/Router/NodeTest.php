<?php

namespace Tests\Unit\Services\Router;

use App\Services\Router\Types\NodeType;
use PHPUnit\Framework\TestCase;
use App\Services\Router\Types\Node;
use InvalidArgumentException;

class NodeTest extends TestCase {
  public function testNodeCreationValid() {
    $node = new Node('1', NodeType::DISTRIBUTION_CENTER, ['latDeg' => 37.422, 'longDeg' => -122.084]);
    $this->assertInstanceOf(Node::class, $node);
    $this->assertEquals('1', $node->getID());
    $this->assertEquals(NodeType::DISTRIBUTION_CENTER, $node->getType());
  }

  public function testNodeCreationInvalid() {
    $this->expectException(InvalidArgumentException::class);
    new Node('', NodeType::DISTRIBUTION_CENTER);
  }

  public function testGetAndSetAttributes() {
    $node = new Node('1', NodeType::DISTRIBUTION_CENTER, ['latDeg' => 37.422, 'longDeg' => -122.084]);
    $this->assertEquals(37.422, $node->getAttribute('latDeg'));
    $this->assertNull($node->getAttribute('nonExistentKey'));

    $node->setAttribute('desc', 'Test Node');
    $this->assertEquals('Test Node', $node->getAttribute('desc'));
  }

  public function testSetAttributeInvalidKey() {
    $node = new Node('1', NodeType::DISTRIBUTION_CENTER);
    $this->expectException(InvalidArgumentException::class);
    $node->setAttribute('', 'value');
  }

  public function testGetDistanceTo() {
    $node1 = new Node('1', NodeType::DISTRIBUTION_CENTER,
      ['latRad' => deg2rad(37.422), 'longRad' => deg2rad(-122.084)]);
    $node2 = new Node('2', NodeType::DISTRIBUTION_CENTER,
      ['latRad' => deg2rad(37.7749), 'longRad' => deg2rad(-122.4194)]);

    $distance = $node1->getDistanceTo($node2);
    $this->assertIsFloat($distance);
    $this->assertGreaterThan(0, $distance);
  }
}