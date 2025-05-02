<?php

namespace App\Services\Router;

use App\Services\Router\Types\Node;
use App\Services\Router\Types\VehicleType;

/**
 * Class VehicleTypeResolver
 * 
 * Determines the appropriate vehicle type for a given segment of a route.
 *
 * @package App\Services\Router
 */
class VehicleTypeResolver {
    /**
     * Determine the vehicle type for a segment based on the position in the path.
     *
     * @param Node $currentNode The current node
     * @param Node $nextNode The next node
     * @param int $segmentIndex The index of the segment in the path
     * @param int $totalSegments The total number of segments in the path
     * @return VehicleType The determined vehicle type
     */
    public function resolveVehicleType(Node $currentNode, Node $nextNode, int $segmentIndex, int $totalSegments): VehicleType {
        // First hop from origin to next movement and the penultimate and destination movement always use vans
        if ($segmentIndex === 0 || $segmentIndex >= $totalSegments - 1) {
            return VehicleType::VAN;
        }
        
        // For air travel between airports, use airplane
        if (str_starts_with($currentNode->getID(), '@AIR_') && str_starts_with($nextNode->getID(), '@AIR_')) {
            return VehicleType::AIRPLANE;
        }
        
        // Default to truck for all other cases
        return VehicleType::TRUCK;
    }
    
    /**
     * Determine the vehicle type for a segment based on node IDs.
     * This is used for backward compatibility with existing code.
     *
     * @param string $currentNodeID The current node ID
     * @param string $nextNodeID The next node ID
     * @return VehicleType The determined vehicle type
     */
    public function resolveVehicleTypeFromNodeIDs(string $currentNodeID, string $nextNodeID): VehicleType {
        // For air travel between airports, use airplane
        if (str_starts_with($currentNodeID, '@AIR_') && str_starts_with($nextNodeID, '@AIR_')) {
            return VehicleType::AIRPLANE;
        }
        
        // Default to truck for all other cases
        return VehicleType::TRUCK;
    }
}