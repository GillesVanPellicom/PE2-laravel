<?php

namespace Tests\Unit\Services\Router;

use PHPUnit\Framework\TestCase;
use App\Services\Router\Types\Node;
use InvalidArgumentException;
use TypeError;
use Ramsey\Uuid\Uuid;

class NodeTest extends TestCase
{
  public function testNodeCreation()
  {
    $node = new Node('testNode', ['key' => 'value']);
    $this->assertEquals('testNode', $node->getName());
    $this->assertEquals(['key' => 'value'], $node->getAttributes());
  }

  public function testNodeCreationRandom()
  {
    $node = new Node(Uuid::uuid4()->toString(), ['key' => 'value']);
    $this->assertNotEmpty($node->getName());
    $this->assertEquals(['key' => 'value'], $node->getAttributes());
  }

  public function testEmptyNodeNameThrowsException()
  {
    $this->expectException(InvalidArgumentException::class);
    new Node('');
  }

  public function testEmptyNodeNameThrowsExceptionRandom()
  {
    $this->expectException(InvalidArgumentException::class);
    new Node('');
  }

  public function testGetAttribute()
  {
    $node = new Node('testNode', ['key' => 'value']);
    $this->assertEquals('value', $node->getAttribute('key'));
    $this->assertNull($node->getAttribute('nonExistentKey'));
  }

  public function testGetAttributeRandom()
  {
    $node = new Node(Uuid::uuid4()->toString(), ['key' => 'value']);
    $this->assertEquals('value', $node->getAttribute('key'));
    $this->assertNull($node->getAttribute('nonExistentKey'));
  }

  public function testSetAttribute()
  {
    $node = new Node('testNode');
    $node->setAttribute('key', 'value');
    $this->assertEquals('value', $node->getAttribute('key'));
  }

  public function testSetAttributeRandom()
  {
    $node = new Node(Uuid::uuid4()->toString());
    $node->setAttribute('key', 'value');
    $this->assertEquals('value', $node->getAttribute('key'));
  }

  public function testEmptyAttributeKeyThrowsException()
  {
    $node = new Node('testNode');
    $this->expectException(InvalidArgumentException::class);
    $node->setAttribute('', 'value');
  }

  public function testEmptyAttributeKeyThrowsExceptionRandom()
  {
    $node = new Node(Uuid::uuid4()->toString());
    $this->expectException(InvalidArgumentException::class);
    $node->setAttribute('', 'value');
  }
}