@extends('school.layouts.app')
@section('title', 'Exam Schedules')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.exams.index', app('school')->slug) }}">Exams</a></li>
    <li class="breadcrumb-item active">{{ $exam->name }} - Schedules</li>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-plus-circle me-2 text-primary"></i>Add Schedule
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('school.exams.schedules.store', [app('school')->slug, $exam]) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Subject *</label>
                        <select name="subject_id" class="form-select form-select-sm" required>
                            @foreach($subjects as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Section *</label>
                        <select name="section_id" class="form-select form-select-sm" required>
                            @foreach($sections as $sec)
                                <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Date *</label>
                        <input type="date" name="exam_date" class="form-control form-control-sm" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Start Time *</label>
                            <input type="time" name="start_time" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">End Time *</label>
                            <input type="time" name="end_time" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Total Marks *</label>
                            <input type="number" name="total_marks" class="form-control form-control-sm" value="100" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Passing Marks *</label>
                            <input type="number" name="passing_marks" class="form-control form-control-sm" value="40" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Room</label>
                        <input type="text" name="room" class="form-control form-control-sm" placeholder="e.g. Room 101">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Add Schedule</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">
                Schedules for {{ $exam->name }}
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Subject</th><th>Section</th><th>Date</th><th>Time</th><th>Marks</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @forelse($schedules as $s)
                            <tr>
                                <td class="fw-semibold">{{ $s->subject?->name }}</td>
                                <td>{{ $s->section?->name }}</td>
                                <td class="small">{{ \Carbon\Carbon::parse($s->exam_date)->format('d M Y') }}</td>
                                <td class="small">{{ $s->start_time }} - {{ $s->end_time }}</td>
                                <td class="small">{{ $s->total_marks }} / {{ $s->passing_marks }}</td>
                                <td>
                                    <a href="{{ route('school.exams.marks', [app('school')->slug, $s]) }}" class="btn btn-xs btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Marks
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">No schedules added</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
