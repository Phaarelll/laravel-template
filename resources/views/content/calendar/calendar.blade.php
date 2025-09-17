  @extends('layouts/contentNavbarLayout')

@section('title', 'Calendar')

@section('vendor-style')
<style>
.calendar-container {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.calendar-header {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    background-color: #e9ecef;
}

.calendar-day-header {
    background: #f8f9fa;
    padding: 0.75rem;
    text-align: center;
    font-weight: 600;
    color: #495057;
}

.calendar-day {
    background: #fff;
    min-height: 100px;
    padding: 0.5rem;
    border: 1px solid #e9ecef;
    position: relative;
    cursor: pointer;
    transition: background-color 0.2s;
}

.calendar-day:hover {
    background-color: #f8f9fa;
}

.calendar-day.other-month {
    background-color: #f8f9fa;
    color: #6c757d;
}

.calendar-day.today {
    background-color: #e3f2fd;
    border-color: #2196f3;
}

.day-number {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.event {
    background: #007bff;
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.75rem;
    margin-bottom: 2px;
    cursor: pointer;
}

.event.event-primary {
    background: #007bff;
}

.event.event-success {
    background: #28a745;
}

.event.event-warning {
    background: #ffc107;
    color: #212529;
}

.event.event-danger {
    background: #dc3545;
}

.event.event-info {
    background: #17a2b8;
}
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
          <i class="bx bx-calendar me-2"></i>
          Launching Product- {{ $currentDate->format('F Y') }}
        </h5>
        <div class="btn-group">
          <button type="button" class="btn btn-outline-primary btn-sm" onclick="previousMonth()">
            <i class="bx bx-chevron-left"></i>
          </button>
          <button type="button" class="btn btn-outline-primary btn-sm" onclick="nextMonth()">
            <i class="bx bx-chevron-right"></i>
          </button>
          <button type="button" class="btn btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#addEventModal">
            <i class="bx bx-plus"></i> Add Event
          </button>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="calendar-container">
          <div class="calendar-grid">
            <!-- Day headers -->
            <div class="calendar-day-header">Sun</div>
            <div class="calendar-day-header">Mon</div>
            <div class="calendar-day-header">Tue</div>
            <div class="calendar-day-header">Wed</div>
            <div class="calendar-day-header">Thu</div>
            <div class="calendar-day-header">Fri</div>
            <div class="calendar-day-header">Sat</div>

            <!-- Empty cells for days before month starts -->
            @for($i = 0; $i < $startDayOfWeek; $i++)
              <div class="calendar-day other-month"></div>
            @endfor

            <!-- Days of the month -->
            @for($day = 1; $day <= $daysInMonth; $day++)
              @php
                $currentDateStr = $currentDate->format('Y-m-') . str_pad($day, 2, '0', STR_PAD_LEFT);
                $isToday = $currentDateStr === date('Y-m-d');
                $dayEvents = $events[$currentDateStr] ?? [];
              @endphp
              <div class="calendar-day {{ $isToday ? 'today' : '' }}" data-date="{{ $currentDateStr }}">
                <div class="day-number">{{ $day }}</div>
                @if(is_array($dayEvents))
                  @foreach($dayEvents as $event)
                    @php
                      $eventClass = 'event';
                      if (isset($event['category'])) {
                        switch($event['category']) {
                          case 'work': $eventClass .= ' event-primary'; break;
                          case 'meeting': $eventClass .= ' event-success'; break;
                          case 'training': $eventClass .= ' event-warning'; break;
                          case 'personal': $eventClass .= ' event-danger'; break;
                          default: $eventClass .= ' event-info';
                        }
                      }
                    @endphp
                    <div class="{{ $eventClass }}"
                         data-event-id="{{ $event['id'] ?? '' }}"
                         data-event-title="{{ is_array($event) ? $event['title'] : $event }}"
                         data-event-time="{{ $event['time'] ?? '' }}"
                         data-event-description="{{ $event['description'] ?? '' }}"
                         data-event-category="{{ $event['category'] ?? 'general' }}"
                         onclick="showEventDetails(this)">
                      {{ is_array($event) ? $event['title'] : $event }}
                      @if(isset($event['time']))
                        <small class="d-block">{{ $event['time'] }}</small>
                      @endif
                    </div>
                  @endforeach
                @endif
              </div>
            @endfor

            <!-- Fill remaining cells -->
            @php
              $totalCells = 42; // 6 rows × 7 days
              $filledCells = $startDayOfWeek + $daysInMonth;
              $remainingCells = $totalCells - $filledCells;
            @endphp
            @for($i = 0; $i < $remainingCells; $i++)
              <div class="calendar-day other-month"></div>
            @endfor
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Quick Stats -->
<div class="row mt-4">
  <div class="col-md-3">
    <div class="card text-center">
      <div class="card-body">
        <i class="bx bx-calendar-event display-4 text-primary"></i>
        <h5 class="mt-2">{{ array_sum(array_map('count', $events)) }}</h5>
        <p class="text-muted mb-0">Total Events</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-center">
      <div class="card-body">
        <i class="bx bx-time display-4 text-success"></i>
        <h5 class="mt-2">{{ $currentDate->format('d') }}</h5>
        <p class="text-muted mb-0">Today</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-center">
      <div class="card-body">
        <i class="bx bx-calendar-check display-4 text-warning"></i>
        <h5 class="mt-2">{{ count($events) }}</h5>
        <p class="text-muted mb-0">Event Days</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-center">
      <div class="card-body">
        <i class="bx bx-calendar display-4 text-info"></i>
        <h5 class="mt-2">{{ $daysInMonth }}</h5>
        <p class="text-muted mb-0">Days in Month</p>
      </div>
    </div>
  </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Event</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="addEventForm">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="eventTitle" class="form-label">Event Title</label>
            <input type="text" class="form-control" id="eventTitle" name="title" required>
          </div>
          <div class="mb-3">
            <label for="eventDate" class="form-label">Date</label>
            <input type="date" class="form-control" id="eventDate" name="date" required>
          </div>
          <div class="mb-3">
            <label for="eventTime" class="form-label">Time (Optional)</label>
            <input type="time" class="form-control" id="eventTime" name="time">
          </div>
          <div class="mb-3">
            <label for="eventCategory" class="form-label">Category</label>
            <select class="form-control" id="eventCategory" name="category">
              <option value="general">General</option>
              <option value="work">Work</option>
              <option value="meeting">Meeting</option>
              <option value="training">Training</option>
              <option value="personal">Personal</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="eventDescription" class="form-label">Description (Optional)</label>
            <textarea class="form-control" id="eventDescription" name="description" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Event</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Event Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="eventDetailsContent">
          <p><strong>Title:</strong> <span id="detailTitle"></span></p>
          <p><strong>Date:</strong> <span id="detailDate"></span></p>
          <p><strong>Time:</strong> <span id="detailTime"></span></p>
          <p><strong>Category:</strong> <span id="detailCategory"></span></p>
          <p><strong>Description:</strong> <span id="detailDescription"></span></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-warning" onclick="editEvent()">Edit</button>
        <button type="button" class="btn btn-danger" onclick="deleteEvent()">Delete</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Event Modal -->
<div class="modal fade" id="editEventModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Event</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editEventForm">
        @csrf
        @method('PUT')
        <input type="hidden" id="editEventId" name="event_id">
        <input type="hidden" id="editEventOriginalDate" name="original_date">
        <div class="modal-body">
          <div class="mb-3">
            <label for="editEventTitle" class="form-label">Event Title</label>
            <input type="text" class="form-control" id="editEventTitle" name="title" required>
          </div>
          <div class="mb-3">
            <label for="editEventDate" class="form-label">Date</label>
            <input type="date" class="form-control" id="editEventDate" name="date" required>
          </div>
          <div class="mb-3">
            <label for="editEventTime" class="form-label">Time (Optional)</label>
            <input type="time" class="form-control" id="editEventTime" name="time">
          </div>
          <div class="mb-3">
            <label for="editEventCategory" class="form-label">Category</label>
            <select class="form-control" id="editEventCategory" name="category">
              <option value="general">General</option>
              <option value="work">Work</option>
              <option value="meeting">Meeting</option>
              <option value="training">Training</option>
              <option value="personal">Personal</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="editEventDescription" class="form-label">Description (Optional)</label>
            <textarea class="form-control" id="editEventDescription" name="description" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Event</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
let currentEventData = {};

function previousMonth() {
    // Navigate to previous month
    window.location.href = '{{ route("calendar") }}?month=' + ({{ $currentMonth }} - 1) + '&year=' + {{ $currentYear }};
}

function nextMonth() {
    // Navigate to next month
    window.location.href = '{{ route("calendar") }}?month=' + ({{ $currentMonth }} + 1) + '&year=' + {{ $currentYear }};
}

function showEventDetails(eventElement) {
    currentEventData = {
        id: eventElement.dataset.eventId,
        title: eventElement.dataset.eventTitle,
        time: eventElement.dataset.eventTime,
        description: eventElement.dataset.eventDescription,
        category: eventElement.dataset.eventCategory,
        date: eventElement.closest('.calendar-day').dataset.date
    };

    document.getElementById('detailTitle').textContent = currentEventData.title;
    document.getElementById('detailDate').textContent = currentEventData.date;
    document.getElementById('detailTime').textContent = currentEventData.time || 'Not specified';
    document.getElementById('detailCategory').textContent = currentEventData.category;
    document.getElementById('detailDescription').textContent = currentEventData.description || 'No description';

    new bootstrap.Modal(document.getElementById('eventDetailsModal')).show();
}

function editEvent() {
    document.getElementById('editEventId').value = currentEventData.id;
    document.getElementById('editEventTitle').value = currentEventData.title;
    document.getElementById('editEventDate').value = currentEventData.date;
    document.getElementById('editEventTime').value = currentEventData.time;
    document.getElementById('editEventCategory').value = currentEventData.category;
    document.getElementById('editEventDescription').value = currentEventData.description;
    document.getElementById('editEventOriginalDate').value = currentEventData.date;

    bootstrap.Modal.getInstance(document.getElementById('eventDetailsModal')).hide();
    new bootstrap.Modal(document.getElementById('editEventModal')).show();
}

function deleteEvent() {
    if (confirm('Are you sure you want to delete this event?')) {
        fetch('{{ route("calendar.destroy") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                event_id: currentEventData.id,
                date: currentEventData.date
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting event');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting event');
        });
    }
}

// Add event form submission
document.getElementById('addEventForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('{{ route("calendar.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('addEventModal')).hide();
            location.reload();
        } else {
            alert('Error adding event');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding event');
    });
});

// Edit event form submission
document.getElementById('editEventForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('{{ route("calendar.update") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editEventModal')).hide();
            location.reload();
        } else {
            alert('Error updating event');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating event');
    });
});

// Calendar day click handler
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.calendar-day').forEach(day => {
        day.addEventListener('click', function(e) {
            if (e.target.classList.contains('day-number')) {
                const date = this.dataset.date;
                document.getElementById('eventDate').value = date;
                new bootstrap.Modal(document.getElementById('addEventModal')).show();
            }
        });
    });
});
</script>
@endsection
