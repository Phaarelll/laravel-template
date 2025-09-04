<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

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

    // Get events from session (temporary storage)
    $allEvents = session('calendar_events', []);
    $monthKey = $currentDate->format('Y-m');
    $events = [];

    // Filter events for current month
    foreach ($allEvents as $date => $eventList) {
      if (strpos($date, $monthKey) === 0) {
        $events[$date] = $eventList;
      }
    }

    // Add sample events if no events exist
    if (empty($events)) {
      $events = [
        $currentDate->format('Y-m-15') => [
          [
            'id' => 'sample1',
            'title' => 'Team Meeting',
            'time' => '10:00',
            'category' => 'work',
            'description' => 'Weekly team sync meeting',
          ],
          [
            'id' => 'sample2',
            'title' => 'Project Review',
            'time' => '14:00',
            'category' => 'work',
            'description' => 'Review project progress',
          ],
        ],
        $currentDate->format('Y-m-20') => [
          [
            'id' => 'sample3',
            'title' => 'Client Presentation',
            'time' => '09:00',
            'category' => 'meeting',
            'description' => 'Present quarterly results',
          ],
        ],
        $currentDate->format('Y-m-25') => [
          [
            'id' => 'sample4',
            'title' => 'Workshop',
            'time' => '13:00',
            'category' => 'training',
            'description' => 'Skills development workshop',
          ],
          [
            'id' => 'sample5',
            'title' => 'Training Session',
            'time' => '15:30',
            'category' => 'training',
            'description' => 'Advanced techniques training',
          ],
        ],
      ];
    }

    return view(
      'content.calendar.calendar',
      compact('currentDate', 'currentMonth', 'currentYear', 'daysInMonth', 'startDayOfWeek', 'events')
    );
  }

  public function getEvents(Request $request)
  {
    // This method can be used for AJAX requests to get events
    $month = $request->get('month', date('m'));
    $year = $request->get('year', date('Y'));

    // Sample events - replace with actual database query
    $events = [
      [
        'id' => 1,
        'title' => 'Team Meeting',
        'start' => $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-15',
        'color' => '#007bff',
      ],
      [
        'id' => 2,
        'title' => 'Project Deadline',
        'start' => $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-25',
        'color' => '#dc3545',
      ],
    ];

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

    $events = session('calendar_events', []);
    $date = $request->date;

    if (!isset($events[$date])) {
      $events[$date] = [];
    }

    $eventId = uniqid();
    $events[$date][] = [
      'id' => $eventId,
      'title' => $request->title,
      'time' => $request->time,
      'description' => $request->description,
      'category' => $request->category ?? 'general',
    ];

    session(['calendar_events' => $events]);

    return response()->json(['success' => true]);
  }

  public function destroy(Request $request)
  {
    $eventId = $request->get('event_id');
    $date = $request->get('date');

    // Get existing events from session
    $allEvents = session('calendar_events', []);

    if (isset($allEvents[$date])) {
      // Remove event with matching ID
      $allEvents[$date] = array_filter($allEvents[$date], function ($event) use ($eventId) {
        return $event['id'] !== $eventId;
      });

      // Remove date if no events left
      if (empty($allEvents[$date])) {
        unset($allEvents[$date]);
      }
    }

    // Save back to session
    session(['calendar_events' => $allEvents]);

    return response()->json([
      'success' => true,
      'message' => 'Event deleted successfully!',
    ]);
  }

  public function update(Request $request)
  {
    $request->validate([
      'event_id' => 'required|string',
      'date' => 'required|date',
      'title' => 'required|string|max:255',
      'time' => 'nullable|string',
      'description' => 'nullable|string',
      'category' => 'nullable|string',
    ]);

    $eventId = $request->event_id;
    $newDate = $request->date;
    $originalDate = $request->input('original_date', $newDate);

    // Get existing events from session
    $allEvents = session('calendar_events', []);

    $eventToUpdate = null;

    // Find and remove the event from original date
    if (isset($allEvents[$originalDate])) {
      foreach ($allEvents[$originalDate] as $index => $event) {
        if ($event['id'] === $eventId) {
          $eventToUpdate = $event;
          unset($allEvents[$originalDate][$index]);
          $allEvents[$originalDate] = array_values($allEvents[$originalDate]);

          // Remove date if no events left
          if (empty($allEvents[$originalDate])) {
            unset($allEvents[$originalDate]);
          }
          break;
        }
      }
    }

    // Add updated event to new date
    if ($eventToUpdate) {
      if (!isset($allEvents[$newDate])) {
        $allEvents[$newDate] = [];
      }

      $allEvents[$newDate][] = [
        'id' => $eventId,
        'title' => $request->title,
        'time' => $request->time,
        'description' => $request->description,
        'category' => $request->category ?? 'general',
      ];
    }

    // Save back to session
    session(['calendar_events' => $allEvents]);

    return response()->json([
      'success' => true,
      'message' => 'Event updated successfully!',
    ]);
  }
}
