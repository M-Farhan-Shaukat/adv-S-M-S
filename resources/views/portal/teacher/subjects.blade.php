@extends('portal.teacher.layouts.app')
@section('title', 'My Subjects')

@section('content')
@php $slug = app('school')->slug; @endphp

<div class="row g-3">
    @forelse($assignments as $a)
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:48px;height:48px">
                        <i class="bi bi-book fs-4 text-primary"></i>
                    </div>
                    <div>
                        <div class="fw-bold">{{ $a->subject?->name }}</div>
                        <div class="text-muted small">{{ $a->schoolClass?->name }} - Section {{ $a->section?->name }}</div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-info bg-opacity-10 text-info">{{ $a->session?->name }}</span>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-book fs-1 mb-3 d-block"></i>
                No subjects assigned yet
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection
