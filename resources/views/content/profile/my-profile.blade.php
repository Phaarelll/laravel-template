@extends('layouts/contentNavbarLayout')

@section('title', 'My Profile')

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
      <h5 class="card-header">Profile Details</h5>
      <!-- Account -->
      <div class="card-body">
        <div class="d-flex align-items-start align-items-sm-center gap-4">
          <img
            src="{{ $user->profile_photo ? asset('storage/profile_photos/' . $user->profile_photo) : asset('assets/img/avatars/1.png') }}"
            alt="user-avatar"
            class="d-block rounded"
            height="100"
            width="100"
            id="uploadedAvatar"
          />
          <div class="button-wrapper">
            <h5 class="mb-2 text-primary">{{ $user->name }}</h5>
            <p class="text-muted mb-0">{{ $user->email }}</p>
            <p class="text-muted mb-0">Member since {{ $user->created_at->format('M d, Y') }}</p>
            <label for="upload" class="btn btn-primary me-2 mt-2" tabindex="0">
              <span class="d-none d-sm-block">Upload new photo</span>
              <i class="bx bx-upload d-block d-sm-none"></i>
              <input
                type="file"
                id="upload"
                name="profile_photo"
                class="account-file-input"
                hidden
                accept="image/png, image/jpeg"
                form="formAccountSettings"
              />
            </label>
            <button type="button" class="btn btn-outline-secondary account-image-reset mt-2">
              <i class="bx bx-reset d-block d-sm-none"></i>
              <span class="d-none d-sm-block">Reset</span>
            </button>
            <p class="text-muted mb-0 mt-1">Allowed JPG, JPEG or PNG. Max size of 2MB</p>
          </div>
        </div>
      </div>
      <hr class="my-0" />
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

        <form id="formAccountSettings" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="mb-3 col-md-6">
              <label for="name" class="form-label">Name</label>
              <input
                class="form-control"
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $user->name) }}"
                autofocus
                required
              />
            </div>
            <div class="mb-3 col-md-6">
              <label for="email" class="form-label">E-mail</label>
              <input
                class="form-control"
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $user->email) }}"
                required
              />
            </div>
          </div>


          <div class="mt-2">
            <button type="submit" class="btn btn-primary me-2">Save changes</button>
            <button type="reset" class="btn btn-outline-secondary">Cancel</button>
          </div>
        </form>
      </div>
      <!-- /Account -->
    </div>
  </div>
</div>

<!-- Account Information Card -->
<div class="row">
  <div class="col-md-6">
    <div class="card">
      <h5 class="card-header">Account Information</h5>
      <div class="card-body">
        <div class="row">
          <div class="col-sm-3">
            <p class="mb-0">User ID</p>
          </div>
          <div class="col-sm-9">
            <p class="text-muted mb-0">{{ $user->id }}</p>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-sm-3">
            <p class="mb-0">User Type</p>
          </div>
          <div class="col-sm-9">
            <p class="text-muted mb-0">{{ ucfirst($user->usertype ?? 'user') }}</p>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-sm-3">
            <p class="mb-0">Email Verified</p>
          </div>
          <div class="col-sm-9">
            <p class="text-muted mb-0">
              @if($user->email_verified_at)
                <span class="badge bg-success">Verified</span>
              @else
                <span class="badge bg-warning">Not Verified</span>
              @endif
            </p>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-sm-3">
            <p class="mb-0">Last Updated</p>
          </div>
          <div class="col-sm-9">
            <p class="text-muted mb-0">{{ $user->updated_at->format('M d, Y H:i') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card">
      <h5 class="card-header">Quick Actions</h5>
      <div class="card-body">
        <div class="d-grid gap-2">
          <a href="{{ route('auth-login-basic') }}" class="btn btn-outline-primary">
            <i class="bx bx-log-out me-1"></i>
            Logout
          </a>
          <a href="{{ route('dashboard-analytics') }}" class="btn btn-outline-secondary">
            <i class="bx bx-home me-1"></i>
            Go to Dashboard
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
