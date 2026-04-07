@extends('school.layouts.app')
@section('title', 'Exams')
@section('breadcrumb')
    <li class="breadcrumb-item active">Exams</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Exams</h5>
    @can('create exam')
    <a href="{{ route('school.exams.create', $school->slug) }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Create Exam
    </a>
    @endcan
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Exam Name</th>
                        <th>Type</th>
                        <th>Class</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                    <tr>
                        <td class="fw-semibold">{{ $exam->name }}</td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                {{ str_replace('_', ' ', ucfirst($exam->type)) }}
                            </span>
                        </td>
                        <td>{{ $exam->schoolClass?->name }}</td>
                        <td class="small">{{ \Carbon\Carbon::parse($exam->start_date)->format('d M Y') }}</td>
                        <td class="small">{{ \Carbon\Carbon::parse($exam->end_date)->format('d M Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $exam->status === 'completed' ? 'success' : ($exam->status === 'ongoing' ? 'warning' : ($exam->status === 'cancelled' ? 'danger' : 'info')) }}">
                                {{ ucfirst($exam->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('school.exams.schedules', [$school->slug, $exam]) }}" class="btn btn-xs btn-outline-primary" title="Schedules">
                                    <i class="bi bi-calendar3"></i> Schedule
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No exams created yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($exams->hasPages())
    <div class="card-footer bg-white border-0">{{ $exams->links() }}</div>
    @endif
</div>

<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
