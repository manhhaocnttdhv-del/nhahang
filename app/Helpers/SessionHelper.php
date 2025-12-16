<?php

namespace App\Helpers;

class SessionHelper
{
    /**
     * Map session to time range
     * 
     * @param string $session morning, lunch, afternoon, dinner
     * @return array ['start' => 'HH:MM', 'end' => 'HH:MM']
     */
    public static function getSessionTimeRange($session)
    {
        $ranges = [
            'morning' => ['start' => '08:00', 'end' => '11:00'],
            'lunch' => ['start' => '11:00', 'end' => '14:00'],
            'afternoon' => ['start' => '14:00', 'end' => '17:00'],
            'dinner' => ['start' => '17:00', 'end' => '22:00'],
        ];

        return $ranges[$session] ?? $ranges['lunch'];
    }

    /**
     * Get session name in Vietnamese
     */
    public static function getSessionName($session)
    {
        $names = [
            'morning' => 'Sáng',
            'lunch' => 'Trưa',
            'afternoon' => 'Chiều',
            'dinner' => 'Tối',
        ];

        return $names[$session] ?? $session;
    }

    /**
     * Get all sessions
     */
    public static function getAllSessions()
    {
        return [
            'morning' => 'Sáng (8:00 - 11:00)',
            'lunch' => 'Trưa (11:00 - 14:00)',
            'afternoon' => 'Chiều (14:00 - 17:00)',
            'dinner' => 'Tối (17:00 - 22:00)',
        ];
    }
}

