@extends('layouts/contentNavbarLayout')

@section('title', 'Contact Us')

@section('content')
<div class="row">
  <div class="col-lg-8 mx-auto">
    <!-- Contact Header -->
    <div class="card mb-4">
      <div class="card-body text-center">
        <h2 class="card-title mb-3">
          <i class="bx bx-envelope me-2 text-primary"></i>
          Contact Us
        </h2>
        <p class="card-text text-muted">
          Have a question or need assistance? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
        </p>
      </div>
    </div>

    <!-- Contact Form -->
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">
          <i class="bx bx-message-dots me-2"></i>
          Send us a Message
        </h5>
      </div>
      <div class="card-body">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <form method="POST" action="{{ route('contact.store') }}">
          @csrf
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bx bx-user"></i>
                </span>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Enter your full name" required>
              </div>
              @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
            
            <div class="col-md-6 mb-3">
              <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bx bx-envelope"></i>
                </span>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email address" required>
              </div>
              @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="mb-3">
            <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="bx bx-tag"></i>
              </span>
              <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" placeholder="What is this about?" required>
            </div>
            @error('subject')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="6" placeholder="Tell us more about your inquiry..." required>{{ old('message') }}</textarea>
            @error('message')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="reset" class="btn btn-outline-secondary me-md-2">
              <i class="bx bx-reset me-1"></i>
              Reset
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="bx bx-send me-1"></i>
              Send Message
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Contact Information -->
    <div class="row mt-4">
      <div class="col-md-4 mb-3">
        <div class="card h-100 text-center">
          <div class="card-body">
            <div class="avatar avatar-lg mx-auto mb-3">
              <div class="avatar-initial bg-label-primary rounded-circle">
                <i class="bx bx-phone bx-md"></i>
              </div>
            </div>
            <h6 class="card-title">Phone</h6>
            <p class="card-text text-muted">+1 (555) 123-4567</p>
          </div>
        </div>
      </div>
      
      <div class="col-md-4 mb-3">
        <div class="card h-100 text-center">
          <div class="card-body">
            <div class="avatar avatar-lg mx-auto mb-3">
              <div class="avatar-initial bg-label-success rounded-circle">
                <i class="bx bx-envelope bx-md"></i>
              </div>
            </div>
            <h6 class="card-title">Email</h6>
            <p class="card-text text-muted">support@example.com</p>
          </div>
        </div>
      </div>
      
      <div class="col-md-4 mb-3">
        <div class="card h-100 text-center">
          <div class="card-body">
            <div class="avatar avatar-lg mx-auto mb-3">
              <div class="avatar-initial bg-label-info rounded-circle">
                <i class="bx bx-map bx-md"></i>
              </div>
            </div>
            <h6 class="card-title">Address</h6>
            <p class="card-text text-muted">123 Business St.<br>City, State 12345</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
