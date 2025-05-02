<?php

namespace App\Services\Router\Types;

/**
 * Enum TurnType represents the type of turn based on angle.
 *
 * @package App\Services\Router\Types
 */
enum TurnType: string {
    case SLIGHT = 'slight';
    case MODERATE = 'moderate';
    case SHARP = 'sharp';
    case U_TURN = 'u-turn';
    
    /**
     * Get the turn type based on the angle.
     *
     * @param float $angle The angle in degrees
     * @return TurnType The turn type
     */
    public static function fromAngle(float $angle): self {
        if ($angle < 30) {
            return self::SLIGHT;
        } elseif ($angle < 60) {
            return self::MODERATE;
        } elseif ($angle < 120) {
            return self::SHARP;
        } else {
            return self::U_TURN;
        }
    }
    
    /**
     * Get a human-readable name for the turn type.
     *
     * @return string The human-readable name
     */
    public function getDisplayName(): string {
        return match($this) {
            self::SLIGHT => 'Slight',
            self::MODERATE => 'Moderate',
            self::SHARP => 'Sharp',
            self::U_TURN => 'U-turn',
        };
    }
}