<?php

namespace App\Helper;

use DateTime;
use DateTimeInterface;

class TimeHelper
{
    /**
     * Génère une durée au format "30 min".
     */
    public static function duration(int $duration): string
    {
        $minutes = round($duration / 60);
        if ($minutes < 60) {
            return $minutes.' min';
        }
        $hours = floor($minutes / 60);
        $minutes = str_pad((string) ($minutes - ($hours * 60)), 2, '0', STR_PAD_LEFT);

        return "{$hours}h{$minutes}";
    }

    /**
     * Génère une durée restante au format "30 min".
     */
    public static function leftTime(DateTimeInterface $expiresAt, bool $unixTimestamp = false): string
    {
        $now = new DateTime();
        if ($unixTimestamp) {
            $now = new DateTime('@' . time());
        }
        $duration = $expiresAt->getTimestamp() - $now->getTimestamp();

        return self::duration($duration);
    }
}
