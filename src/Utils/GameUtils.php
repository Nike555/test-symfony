<?php

namespace App\Utils;

class GameUtils
{
    /**
     * Check if user can play in current time interval
     * @return bool
     */
    public static function checkPlayTimeInterval(): bool
    {
        $currentTime = time();
        $startTime1 = strtotime('00:00:00');
        $endTime1 = strtotime('09:00:00');
        $startTime2 = strtotime('21:00:00');
        $endTime2 = strtotime('23:59:59');

        return !(($currentTime >= $startTime1 && $currentTime <= $endTime1) || ($currentTime >= $startTime2 && $currentTime <= $endTime2));
    }
}