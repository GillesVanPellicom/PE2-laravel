<?php

namespace Tests\Unit\Services\Router;

use App\Services\Router\Types\NodeType;
use PHPUnit\Framework\TestCase;
use App\Services\Router\Types\Node;
use InvalidArgumentException;
use TypeError;
use Ramsey\Uuid\Uuid;

class NodeTest extends TestCase
{
  public function testNodeCreation()
  {
    $node = new Node('testNode', NodeType::AIRPORT);
    $this->assertEquals('testNode', $node->getUUID());
    $this->assertEquals(NodeType::AIRPORT, $node->getType());
  }

  public function testNodeCreationRandom()
  {
    $node = new Node(Uuid::uuid4()->toString(), NodeType::AIRPORT);
    $this->assertNotEmpty($node->getUUID());
  }

  public function testEmptyNodeNameThrowsException()
  {
    $this->expectException(InvalidArgumentException::class);
    new Node('', NodeType::AIRPORT);
  }

  public function testEmptyNodeNameThrowsExceptionRandom()
  {
    $this->expectException(InvalidArgumentException::class);
    new Node('', NodeType::AIRPORT);
  }

  public function testSetAttribute()
  {
    $node = new Node('testNode', NodeType::AIRPORT);
    $node->setAttribute('key', 'value');
    $this->assertEquals('value', $node->getAttribute('key'));
  }

  public function testSetAttributeRandom()
  {
    $node = new Node(Uuid::uuid4()->toString(), NodeType::AIRPORT);
    $node->setAttribute('key', 'value');
    $this->assertEquals('value', $node->getAttribute('key'));
  }

  public function testEmptyAttributeKeyThrowsException()
  {
    $node = new Node('testNode', NodeType::AIRPORT);
    $this->expectException(InvalidArgumentException::class);
    $node->setAttribute('', 'value');
  }

  public function testEmptyAttributeKeyThrowsExceptionRandom()
  {
    $node = new Node(Uuid::uuid4()->toString(), NodeType::AIRPORT);
    $this->expectException(InvalidArgumentException::class);
    $node->setAttribute('', 'value');
  }
}