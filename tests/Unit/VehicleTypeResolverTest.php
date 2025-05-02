<?php

namespace Tests\Unit;

use App\Services\Router\Types\Node;
use App\Services\Router\Types\NodeType;
use App\Services\Router\Types\VehicleType;
use App\Services\Router\VehicleTypeResolver;
use PHPUnit\Framework\TestCase;

class VehicleTypeResolverTest extends TestCase
{
    private VehicleTypeResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new VehicleTypeResolver();
    }

    public function testFirstSegmentUsesVan()
    {
        // Create test nodes
        $node1 = $this->createMock(Node::class);
        $node1->method('getID')->willReturn('123');
        
        $node2 = $this->createMock(Node::class);
        $node2->method('getID')->willReturn('456');
        
        // First segment (index 0) should use a van
        $vehicleType = $this->resolver->resolveVehicleType($node1, $node2, 0, 5);
        
        $this->assertEquals(VehicleType::VAN, $vehicleType);
    }

    public function testLastSegmentUsesVan()
    {
        // Create test nodes
        $node1 = $this->createMock(Node::class);
        $node1->method('getID')->willReturn('123');
        
        $node2 = $this->createMock(Node::class);
        $node2->method('getID')->willReturn('456');
        
        // Last segment (index 4 of 5) should use a van
        $vehicleType = $this->resolver->resolveVehicleType($node1, $node2, 4, 5);
        
        $this->assertEquals(VehicleType::VAN, $vehicleType);
    }

    public function testAirportToAirportUsesAirplane()
    {
        // Create test nodes
        $node1 = $this->createMock(Node::class);
        $node1->method('getID')->willReturn('@AIR_EBBR');
        
        $node2 = $this->createMock(Node::class);
        $node2->method('getID')->willReturn('@AIR_EDDM');
        
        // Middle segment (index 2 of 5) between airports should use an airplane
        $vehicleType = $this->resolver->resolveVehicleType($node1, $node2, 2, 5);
        
        $this->assertEquals(VehicleType::AIRPLANE, $vehicleType);
    }

    public function testDefaultVehicleIsTruck()
    {
        // Create test nodes
        $node1 = $this->createMock(Node::class);
        $node1->method('getID')->willReturn('123');
        
        $node2 = $this->createMock(Node::class);
        $node2->method('getID')->willReturn('456');
        
        // Middle segment (index 2 of 5) between regular nodes should use a truck
        $vehicleType = $this->resolver->resolveVehicleType($node1, $node2, 2, 5);
        
        $this->assertEquals(VehicleType::TRUCK, $vehicleType);
    }

    public function testNodeIdBasedResolution()
    {
        // Test airport to airport
        $vehicleType = $this->resolver->resolveVehicleTypeFromNodeIDs('@AIR_EBBR', '@AIR_EDDM');
        $this->assertEquals(VehicleType::AIRPLANE, $vehicleType);
        
        // Test regular nodes
        $vehicleType = $this->resolver->resolveVehicleTypeFromNodeIDs('123', '456');
        $this->assertEquals(VehicleType::TRUCK, $vehicleType);
    }
}