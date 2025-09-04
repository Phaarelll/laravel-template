@extends('layouts/contentNavbarLayout')

@section('title', 'Search Results')

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <h5 class="card-header">
        Search Results for "{{ $results['query'] }}"
        <span class="badge bg-primary ms-2">{{ $results['users']->count() }} results</span>
      </h5>
      <div class="card-body">
        @if($results['users']->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Avatar</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>User Type</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($results['users'] as $user)
                <tr>
                  <td>
                    <div class="avatar avatar-sm">
                      <img src="{{ $user->profile_photo ? asset('storage/profile_photos/' . $user->profile_photo) : asset('assets/img/avatars/1.png') }}" 
                           alt="Avatar" class="rounded-circle">
                    </div>
                  </td>
                  <td>
                    <strong>{{ $user->name }}</strong>
                  </td>
                  <td>{{ $user->email }}</td>
                  <td>
                    <span class="badge bg-label-primary">{{ ucfirst($user->usertype ?? 'user') }}</span>
                  </td>
                  <td>
                    <div class="dropdown">
                      <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                      </button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('profile.index') }}">
                          <i class="bx bx-user me-1"></i> View Profile
                        </a>
                        <a class="dropdown-item" href="mailto:{{ $user->email }}">
                          <i class="bx bx-envelope me-1"></i> Send Email
                        </a>
                      </div>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center py-5">
            <div class="mb-3">
              <i class="bx bx-search-alt-2 display-4 text-muted"></i>
            </div>
            <h5 class="mb-2">No results found</h5>
            <p class="text-muted mb-4">
              We couldn't find any results for "<strong>{{ $results['query'] }}</strong>". 
              Try adjusting your search terms.
            </p>
            <a href="{{ route('dashboard-analytics') }}" class="btn btn-primary">
              <i class="bx bx-home me-1"></i>
              Back to Dashboard
            </a>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Search Tips Card -->
<div class="row mt-4">
  <div class="col-md-12">
    <div class="card">
      <h5 class="card-header">Search Tips</h5>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <h6 class="text-primary">What you can search for:</h6>
            <ul class="list-unstyled">
              <li><i class="bx bx-check text-success me-2"></i>User names</li>
              <li><i class="bx bx-check text-success me-2"></i>Email addresses</li>
              <li><i class="bx bx-check text-success me-2"></i>Partial matches</li>
            </ul>
          </div>
          <div class="col-md-6">
            <h6 class="text-primary">Search examples:</h6>
            <ul class="list-unstyled">
              <li><code>john</code> - Find users with "john" in their name</li>
              <li><code>@gmail.com</code> - Find Gmail users</li>
              <li><code>admin</code> - Find admin users</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
