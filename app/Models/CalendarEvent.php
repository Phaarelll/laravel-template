<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'event_date',
        'event_time',
        'description',
        'category',
        'color'
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime:H:i',
    ];

    // Get events for a specific month
    public static function getEventsForMonth($year, $month)
    {
        return self::whereYear('event_date', $year)
                   ->whereMonth('event_date', $month)
                   ->orderBy('event_date')
                   ->orderBy('event_time')
                   ->get();
    }

    // Get color based on category
    public function getCategoryColorAttribute()
    {
        $colors = [
            'work' => '#007bff',
            'meeting' => '#28a745',
            'training' => '#ffc107',
            'personal' => '#dc3545',
            'general' => '#17a2b8'
        ];

        return $colors[$this->category] ?? $colors['general'];
    }
}
