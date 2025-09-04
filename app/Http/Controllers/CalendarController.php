<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\CalendarEvent;

class CalendarController extends Controller
{
  public function index(Request $request)
  {
    // Get month and year from request or use current
    $month = $request->get('month', Carbon::now()->month);
    $year = $request->get('year', Carbon::now()->year);

    // Handle month overflow/underflow
    if ($month > 12) {
      $month = 1;
      $year++;
    } elseif ($month < 1) {
      $month = 12;
      $year--;
    }

    // Create date objects
    $currentDate = Carbon::createFromDate($year, $month, 1);
    $currentMonth = $currentDate->month;
    $currentYear = $currentDate->year;

    // Get first day of the month
    $firstDay = Carbon::createFromDate($currentYear, $currentMonth, 1);
    $lastDay = $firstDay->copy()->endOfMonth();

    // Get days in month
    $daysInMonth = $firstDay->daysInMonth;
    $startDayOfWeek = $firstDay->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.

    // Get events from database
    $dbEvents = CalendarEvent::getEventsForMonth($currentYear, $currentMonth);
    $events = [];

    // Group events by date
    foreach ($dbEvents as $event) {
      $dateKey = $event->event_date->format('Y-m-d');
      if (!isset($events[$dateKey])) {
        $events[$dateKey] = [];
      }
      $events[$dateKey][] = [
        'id' => $event->id,
        'title' => $event->title,
        'time' => $event->event_time ? $event->event_time->format('H:i') : null,
        'category' => $event->category,
        'description' => $event->description,
      ];
    }

    // Add sample events if no events exist in database
    if (empty($events)) {
      $sampleEvents = [
        [
          'title' => 'Team Meeting',
          'event_date' => $currentDate->format('Y-m-15'),
          'event_time' => '10:00',
          'category' => 'work',
          'description' => 'Weekly team sync meeting',
        ],
        [
          'title' => 'Project Review',
          'event_date' => $currentDate->format('Y-m-15'),
          'event_time' => '14:00',
          'category' => 'work',
          'description' => 'Review project progress',
        ],
        [
          'title' => 'Client Presentation',
          'event_date' => $currentDate->format('Y-m-20'),
          'event_time' => '09:00',
          'category' => 'meeting',
          'description' => 'Present quarterly results',
        ],
        [
          'title' => 'Workshop',
          'event_date' => $currentDate->format('Y-m-25'),
          'event_time' => '13:00',
          'category' => 'training',
          'description' => 'Skills development workshop',
        ],
        [
          'title' => 'Training Session',
          'event_date' => $currentDate->format('Y-m-25'),
          'event_time' => '15:30',
          'category' => 'training',
          'description' => 'Advanced techniques training',
        ],
      ];

      // Insert sample events into database
      foreach ($sampleEvents as $sampleEvent) {
        CalendarEvent::create($sampleEvent);
      }

      // Reload events from database
      $dbEvents = CalendarEvent::getEventsForMonth($currentYear, $currentMonth);
      $events = [];
      foreach ($dbEvents as $event) {
        $dateKey = $event->event_date->format('Y-m-d');
        if (!isset($events[$dateKey])) {
          $events[$dateKey] = [];
        }
        $events[$dateKey][] = [
          'id' => $event->id,
          'title' => $event->title,
          'time' => $event->event_time ? $event->event_time->format('H:i') : null,
          'category' => $event->category,
          'description' => $event->description,
        ];
      }
    }

    return view(
      'content.calendar.calendar',
      compact('currentDate', 'currentMonth', 'currentYear', 'daysInMonth', 'startDayOfWeek', 'events')
    );
  }

  public function getEvents(Request $request)
  {
    // Get events from database for AJAX requests
    $month = $request->get('month', date('m'));
    $year = $request->get('year', date('Y'));

    $dbEvents = CalendarEvent::getEventsForMonth($year, $month);
    $events = [];

    foreach ($dbEvents as $event) {
      $events[] = [
        'id' => $event->id,
        'title' => $event->title,
        'start' => $event->event_date->format('Y-m-d'),
        'color' => $event->category_color,
        'time' => $event->event_time ? $event->event_time->format('H:i') : null,
        'description' => $event->description,
        'category' => $event->category,
      ];
    }

    return response()->json($events);
  }

  public function store(Request $request)
  {
    $request->validate([
      'title' => 'required|string|max:255',
      'date' => 'required|date',
      'time' => 'nullable|string',
      'description' => 'nullable|string',
      'category' => 'nullable|string',
    ]);

    $event = CalendarEvent::create([
      'title' => $request->title,
      'event_date' => $request->date,
      'event_time' => $request->time,
      'description' => $request->description,
      'category' => $request->category ?? 'general',
    ]);

    return response()->json([
      'success' => true,
      'event' => $event
    ]);
  }

  public function destroy(Request $request)
  {
    $eventId = $request->get('event_id');

    $event = CalendarEvent::find($eventId);
    
    if ($event) {
      $event->delete();
      return response()->json([
        'success' => true,
        'message' => 'Event deleted successfully!',
      ]);
    }

    return response()->json([
      'success' => false,
      'message' => 'Event not found!',
    ], 404);
  }

  public function update(Request $request)
  {
    $request->validate([
      'event_id' => 'required|integer',
      'date' => 'required|date',
      'title' => 'required|string|max:255',
      'time' => 'nullable|string',
      'description' => 'nullable|string',
      'category' => 'nullable|string',
    ]);

    $event = CalendarEvent::find($request->event_id);

    if ($event) {
      $event->update([
        'title' => $request->title,
        'event_date' => $request->date,
        'event_time' => $request->time,
        'description' => $request->description,
        'category' => $request->category ?? 'general',
      ]);

      return response()->json([
        'success' => true,
        'message' => 'Event updated successfully!',
        'event' => $event
      ]);
    }

    return response()->json([
      'success' => false,
      'message' => 'Event not found!',
    ], 404);
  }
}
