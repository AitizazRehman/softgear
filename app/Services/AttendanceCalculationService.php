<?php

namespace App\Services;


class AttendanceCalculationService
{
    public function attendanceCalculation($array)
    {
        $time_in_secs = array_map(function ($v) {
            return strtotime($v) - strtotime('00:00:00');
        }, $array);
        $total_time = array_sum($time_in_secs);
        $hours = floor($total_time / 3600);
        $minutes = floor(($total_time % 3600) / 60);
        $seconds = $total_time % 60;
        return ($hours>0?$hours:'00').':'.($minutes>0?$minutes:'00').':'.($seconds>0?$seconds:'00');
    }
}
