@extends('portal.teacher.layouts.app')
@section('title', 'Exam Schedule')

@section('content')
@php $slug = app('school')->slug; @endphp

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 fw-semibold">
        <i class="bi bi-calendar3 me-2 text-primary"></i>Upcoming Exams
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Exam</th><th>Subject</th><th>Section</th><th>Date</th><th>Time</th><th>Marks</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @forelse($schedules as $s)
                    <tr>
                        <td class="fw-semibold small">{{ $s->exam?->name }}</td>
                        <td class="small">{{ $s->subject?->name }}</td>
                        <td class="small">{{ $s->section?->name }}</td>
                        <td class="small {{ \Carbon\Carbon::parse($s->exam_date)->isToday() ? 'text-danger fw-bold' : '' }}">
                            {{ \Carbon\Carbon::parse($s->exam_date)->format('d M Y') }}
                            @if(\Carbon\Carbon::parse($s->exam_date)->isToday())
                                <span class="badge bg-danger ms-1">Today</span>
                            @endif
                        </td>
                        <td class="small">{{ $s->start_time }} - {{ $s->end_time }}</td>
                        <td class="small">{{ $s->total_marks }} / {{ $s->passing_marks }}</td>
                        <td>
                            <a href="{{ route('teacher.marks.enter', [$slug, $s]) }}" class="btn btn-xs btn-outline-primary">
                                <i class="bi bi-pencil"></i> Enter Marks
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No upcoming exams</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
