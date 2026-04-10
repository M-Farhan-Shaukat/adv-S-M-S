@extends('admin.layouts.app')
@section('title', 'No School Assigned')

@section('content')
<div class="text-center py-5">
    <i class="bi bi-building-x fs-1 text-warning mb-3 d-block"></i>
    <h4 class="fw-bold">No School Assigned</h4>
    <p class="text-muted">Your account has not been assigned to a school yet.<br>Please contact the system administrator.</p>
    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button class="btn btn-outline-secondary btn-sm mt-2">
            <i class="bi bi-box-arrow-right me-1"></i>Logout
        </button>
    </form>
</div>
@endsection
