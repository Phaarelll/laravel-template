@extends('layouts/contentNavbarLayout')

@section('title', 'Settings')

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/css/dark-mode.css')}}">
@endsection

@section('page-script')
<script src="{{asset('assets/js/dark-mode.js')}}"></script>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
      <h5 class="card-header">Application Settings</h5>
      <div class="card-body">
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @if (session('success'))
          <div class="alert alert-success">
            {{ session('success') }}
          </div>
        @endif

        @if (session('error'))
          <div class="alert alert-danger">
            {{ session('error') }}
          </div>
        @endif

        <form method="POST" action="{{ route('settings.update') }}">
          @csrf
          @method('PUT')
          
          <!-- Notification Settings -->
          <div class="row mb-4">
            <div class="col-md-12">
              <h6 class="text-muted">Notification Settings</h6>
              <hr>
            </div>
            <div class="col-md-6">
              <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="notifications" name="notifications" checked>
                <label class="form-check-label" for="notifications">
                  Enable Push Notifications
                </label>
              </div>
              <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" checked>
                <label class="form-check-label" for="email_notifications">
                  Enable Email Notifications
                </label>
              </div>
            </div>
          </div>

          <!-- Appearance Settings -->
          <div class="row mb-4">
            <div class="col-md-12">
              <h6 class="text-muted">Appearance</h6>
              <hr>
            </div>
            <div class="col-md-6">
              <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="dark_mode" name="dark_mode">
                <label class="form-check-label" for="dark_mode">
                  Dark Mode
                </label>
              </div>
            </div>
          </div>

          <!-- Language & Region -->
          <div class="row mb-4">
            <div class="col-md-12">
              <h6 class="text-muted">Language & Region</h6>
              <hr>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="language" class="form-label">Language</label>
                <select class="form-select" id="language" name="language">
                  <option value="en" selected>English</option>
                  <option value="id">Bahasa Indonesia</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="timezone" class="form-label">Timezone</label>
                <select class="form-select" id="timezone" name="timezone">
                  <option value="Asia/Jakarta" selected>Asia/Jakarta (WIB)</option>
                  <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                  <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                  <option value="UTC">UTC</option>
                </select>
              </div>
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary me-2">Save Settings</button>
            <button type="reset" class="btn btn-outline-secondary">Reset</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Privacy & Security Settings -->
<div class="row">
  <div class="col-md-6">
    <div class="card">
      <h5 class="card-header">Privacy & Security</h5>
      <div class="card-body">
        <div class="d-grid gap-2">
          <button class="btn btn-outline-primary" type="button">
            <i class="bx bx-shield me-1"></i>
            Two-Factor Authentication
          </button>
          <button class="btn btn-outline-secondary" type="button">
            <i class="bx bx-lock me-1"></i>
            Change Password
          </button>
          <button class="btn btn-outline-info" type="button">
            <i class="bx bx-download me-1"></i>
            Download My Data
          </button>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="card">
      <h5 class="card-header">Account Actions</h5>
      <div class="card-body">
        <div class="d-grid gap-2">
          <a href="{{ route('profile.index') }}" class="btn btn-outline-primary">
            <i class="bx bx-user me-1"></i>
            Edit Profile
          </a>
          <button class="btn btn-outline-warning" type="button">
            <i class="bx bx-refresh me-1"></i>
            Clear Cache
          </button>
          <button class="btn btn-outline-danger" type="button">
            <i class="bx bx-trash me-1"></i>
            Delete Account
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- System Information -->
<div class="row mt-4">
  <div class="col-md-12">
    <div class="card">
      <h5 class="card-header">System Information</h5>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <p><strong>Application Version:</strong> 1.0.0</p>
            <p><strong>Laravel Version:</strong> {{ app()->version() }}</p>
            <p><strong>PHP Version:</strong> {{ phpversion() }}</p>
          </div>
          <div class="col-md-6">
            <p><strong>Current User:</strong> {{ $user->name }}</p>
            <p><strong>User Type:</strong> {{ ucfirst($user->usertype ?? 'user') }}</p>
            <p><strong>Last Login:</strong> {{ $user->updated_at->format('M d, Y H:i') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
