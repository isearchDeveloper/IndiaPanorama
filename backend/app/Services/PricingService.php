<?php

namespace App\Services;

/**
 * PricingService
 *
 * Handles all price calculation logic for group tour bookings.
 *
 * Room type pricing multipliers (relative to base price per person):
 *   - single  : 1.3  (30% premium — solo occupancy)
 *   - double  : 1.0  (standard — two sharing)
 *   - triple  : 0.9  (10% discount — three sharing)
 *
 * GST rate: 5% on tour packages (as per Indian GST rules for tour operators)
 */
class PricingService
{
    public const GST_RATE = 5.0; // percent

    /**
     * Room type multipliers applied to the base price per person.
     */
    private const ROOM_MULTIPLIERS = [
        'single' => 1.30,
        'double' => 1.00,
        'triple' => 0.90,
    ];

    // -------------------------------------------------------
    // Public API
    // -------------------------------------------------------

    /**
     * Get all valid room options for a given number of adults.
     *
     * Returns an array of room option objects, each with:
     *   - key         : unique identifier (e.g. "3_single", "1_double_1_single")
     *   - label       : human-readable label
     *   - rooms       : breakdown of rooms [ ['type'=>'single','count'=>3] ]
     *   - price_per_person : effective price per adult for this option
     *   - total_price : total for all adults (no GST)
     *
     * @param  float $basePrice  Price per person from packages_group_dates.price
     * @param  int   $adults
     * @return array
     */
    public function getRoomOptions(float $basePrice, int $adults): array
    {
        $options = [];

        foreach ($this->buildRoomCombinations($adults) as $combo) {
            $effectivePrice = $this->effectivePricePerPerson($basePrice, $combo['rooms']);
            $options[] = [
                'key'              => $combo['key'],
                'label'            => $combo['label'],
                'rooms'            => $combo['rooms'],
                'price_per_person' => round($effectivePrice, 2),
                'total_price'      => round($effectivePrice * $adults, 2),
            ];
        }

        return $options;
    }

    /**
     * Calculate full booking amount breakdown.
     *
     * @param  float       $basePrice
     * @param  int         $adults
     * @param  int         $child       Children pay half price
     * @param  int         $infant      Infants are free
     * @param  string|null $roomType    e.g. "single", "double", "triple", "1_double_1_single"
     * @param  float       $discount    Coupon discount (already calculated)
     * @return array
     */
    public function calculate(
        float $basePrice,
        int $adults,
        int $child = 0,
        int $infant = 0,
        ?string $roomType = null,
        float $discount = 0.0
    ): array {
        $multiplier = $this->getMultiplierForRoomType($roomType, $adults);

        $pricePerAdult = round($basePrice * $multiplier, 2);
        $pricePerChild = round($basePrice * 0.5, 2);   // 50% of base price

        $adultTotal  = $pricePerAdult * $adults;
        $childTotal  = $pricePerChild * $child;
        $infantTotal = 0.0;                             // infants free

        $subtotal       = round($adultTotal + $childTotal + $infantTotal, 2);
        $discountAmount = min(round($discount, 2), $subtotal);
        $taxableAmount  = $subtotal - $discountAmount;
        $gstAmount      = round($taxableAmount * self::GST_RATE / 100, 2);
        $grandTotal     = round($taxableAmount + $gstAmount, 2);

        return [
            'price_per_adult'  => $pricePerAdult,
            'price_per_child'  => $pricePerChild,
            'price_per_infant' => 0.0,
            'adult_total'      => round($adultTotal, 2),
            'child_total'      => round($childTotal, 2),
            'infant_total'     => 0.0,
            'subtotal'         => $subtotal,
            'discount_amount'  => $discountAmount,
            'taxable_amount'   => $taxableAmount,
            'gst_rate'         => self::GST_RATE,
            'gst_amount'       => $gstAmount,
            'grand_total'      => $grandTotal,
            'room_type'        => $roomType,
            'multiplier'       => $multiplier,
        ];
    }

    // -------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------

    /**
     * Build all valid room combinations for a given adult count.
     *
     * Rules (matching reference site logic):
     *   1 adult  → 1 Single
     *   2 adults → 1 Double  |  2 Singles
     *   3 adults → 1 Triple  |  1 Double + 1 Single  |  3 Singles
     *   4 adults → 2 Doubles |  1 Triple + 1 Single  |  4 Singles
     *   5 adults → 1 Triple + 1 Double  |  2 Doubles + 1 Single  |  5 Singles
     *   6 adults → 2 Triples |  3 Doubles  |  6 Singles
     *   n adults → generalised algorithm
     */
    private function buildRoomCombinations(int $adults): array
    {
        $combos = [];

        // All-single option (always available)
        $combos[] = [
            'key'   => "{$adults}_single",
            'label' => "{$adults} Single Room" . ($adults > 1 ? 's' : ''),
            'rooms' => [['type' => 'single', 'count' => $adults]],
        ];

        if ($adults >= 2) {
            // Max doubles we can fit
            $maxDoubles = intdiv($adults, 2);
            $remainder  = $adults % 2;

            // All-double (+ 1 single if odd)
            $rooms = [['type' => 'double', 'count' => $maxDoubles]];
            if ($remainder) {
                $rooms[] = ['type' => 'single', 'count' => 1];
            }
            $combos[] = [
                'key'   => $this->comboKey($rooms),
                'label' => $this->comboLabel($rooms),
                'rooms' => $rooms,
            ];
        }

        if ($adults >= 3) {
            // Max triples we can fit
            $maxTriples = intdiv($adults, 3);
            $remainder  = $adults % 3;

            $rooms = [['type' => 'triple', 'count' => $maxTriples]];
            if ($remainder === 1) {
                $rooms[] = ['type' => 'single', 'count' => 1];
            } elseif ($remainder === 2) {
                $rooms[] = ['type' => 'double', 'count' => 1];
            }
            $combos[] = [
                'key'   => $this->comboKey($rooms),
                'label' => $this->comboLabel($rooms),
                'rooms' => $rooms,
            ];

            // Mixed: triples + doubles (if adults >= 5)
            if ($adults >= 5) {
                $tripleCount = intdiv($adults, 3) - 1;
                if ($tripleCount > 0) {
                    $remaining = $adults - ($tripleCount * 3);
                    $doubleCount = intdiv($remaining, 2);
                    $singleCount = $remaining % 2;

                    $rooms = [];
                    if ($tripleCount) $rooms[] = ['type' => 'triple', 'count' => $tripleCount];
                    if ($doubleCount) $rooms[] = ['type' => 'double', 'count' => $doubleCount];
                    if ($singleCount) $rooms[] = ['type' => 'single', 'count' => $singleCount];

                    $key = $this->comboKey($rooms);
                    // Avoid duplicates
                    if (!collect($combos)->pluck('key')->contains($key)) {
                        $combos[] = [
                            'key'   => $key,
                            'label' => $this->comboLabel($rooms),
                            'rooms' => $rooms,
                        ];
                    }
                }
            }
        }

        // Remove duplicates by key
        $seen   = [];
        $unique = [];
        foreach ($combos as $c) {
            if (!isset($seen[$c['key']])) {
                $seen[$c['key']] = true;
                $unique[]        = $c;
            }
        }

        return $unique;
    }

    /**
     * Compute the weighted average price-per-person for a room combination.
     *
     * Each room type has a multiplier. We distribute adults across rooms
     * and compute the average multiplier.
     */
    private function effectivePricePerPerson(float $basePrice, array $rooms): float
    {
        $totalPeople = 0;
        $totalCost   = 0.0;

        foreach ($rooms as $room) {
            $type        = $room['type'];
            $count       = $room['count'];
            $capacity    = $this->roomCapacity($type);
            $multiplier  = self::ROOM_MULTIPLIERS[$type] ?? 1.0;
            $people      = $count * $capacity;
            $totalPeople += $people;
            $totalCost   += $basePrice * $multiplier * $people;
        }

        return $totalPeople > 0 ? $totalCost / $totalPeople : $basePrice;
    }

    /**
     * Get the effective multiplier for a given room_type string.
     *
     * room_type can be:
     *   - "single"  → all singles
     *   - "double"  → all doubles
     *   - "triple"  → all triples
     *   - "1_double_1_single" → mixed (parsed)
     *   - null → default (double)
     */
    private function getMultiplierForRoomType(?string $roomType, int $adults): float
    {
        if (!$roomType) {
            return self::ROOM_MULTIPLIERS['double'];
        }

        // Simple single-type keys
        if (isset(self::ROOM_MULTIPLIERS[$roomType])) {
            return self::ROOM_MULTIPLIERS[$roomType];
        }

        // Parse composite key like "2_double_1_single"
        $rooms = $this->parseComboKey($roomType, $adults);
        if (!empty($rooms)) {
            // Compute weighted multiplier
            $totalPeople = 0;
            $weightedSum = 0.0;
            foreach ($rooms as $room) {
                $capacity     = $this->roomCapacity($room['type']);
                $people       = $room['count'] * $capacity;
                $totalPeople += $people;
                $weightedSum += (self::ROOM_MULTIPLIERS[$room['type']] ?? 1.0) * $people;
            }
            return $totalPeople > 0 ? $weightedSum / $totalPeople : 1.0;
        }

        return 1.0; // fallback
    }

    /**
     * Parse a combo key like "2_double_1_single" into rooms array.
     */
    private function parseComboKey(string $key, int $adults): array
    {
        $rooms = [];
        // Pattern: {count}_{type} pairs separated by _
        // e.g. "1_double_1_single" → [1,double,1,single]
        preg_match_all('/(\d+)_(single|double|triple)/', $key, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $rooms[] = ['type' => $match[2], 'count' => (int) $match[1]];
        }
        return $rooms;
    }

    private function roomCapacity(string $type): int
    {
        return match ($type) {
            'single' => 1,
            'double' => 2,
            'triple' => 3,
            default  => 1,
        };
    }

    private function comboKey(array $rooms): string
    {
        return implode('_', array_map(
            fn($r) => "{$r['count']}_{$r['type']}",
            $rooms
        ));
    }

    private function comboLabel(array $rooms): string
    {
        $parts = array_map(function ($r) {
            $type  = ucfirst($r['type']);
            $count = $r['count'];
            return "{$count} {$type} Room" . ($count > 1 ? 's' : '');
        }, $rooms);

        return implode(' + ', $parts);
    }
}
