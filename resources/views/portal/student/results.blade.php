@extends('portal.layouts.app')
@section('title', 'My Results')
@section('portal-name', 'Student Portal')

@section('sidebar-links')
@php $slug = app('school')->slug; @endphp
<a href="{{ route('student.dashboard', $slug) }}" class="portal-nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
<a href="{{ route('student.results', $slug) }}" class="portal-nav-link active"><i class="bi bi-award"></i> My Results</a>
<a href="{{ route('student.exam-schedule', $slug) }}" class="portal-nav-link"><i class="bi bi-calendar3"></i> Exam Schedule</a>
<a href="{{ route('student.attendance', $slug) }}" class="portal-nav-link"><i class="bi bi-calendar-check"></i> Attendance</a>
<a href="{{ route('student.fees', $slug) }}" class="portal-nav-link"><i class="bi bi-receipt"></i> Fee Vouchers</a>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 fw-semibold">
        <i class="bi bi-award me-2 text-warning"></i>My Exam Results
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Exam</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Marks</th>
                        <th>Grade</th>
                        <th>Result</th>
                        <th>Recheck</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($marks as $mark)
                    <tr>
                        <td class="fw-semibold">{{ $mark->examSchedule?->exam?->name }}</td>
                        <td>{{ $mark->examSchedule?->subject?->name }}</td>
                        <td class="small">{{ \Carbon\Carbon::parse($mark->examSchedule?->exam_date)->format('d M Y') }}</td>
                        <td>
                            @if($mark->is_absent)
                                <span class="text-muted">Absent</span>
                            @else
                                <strong>{{ $mark->obtained_marks }}</strong> / {{ $mark->examSchedule?->total_marks }}
                            @endif
                        </td>
                        <td>
                            @if(!$mark->is_absent)
                            <span class="badge bg-{{ in_array($mark->grade, ['A+','A','B']) ? 'success' : ($mark->grade === 'F' ? 'danger' : 'warning') }}">
                                {{ $mark->grade }}
                            </span>
                            @endif
                        </td>
                        <td>
                            @if(!$mark->is_absent)
                            @php $pct = ($mark->obtained_marks / $mark->examSchedule->total_marks) * 100; @endphp
                            <span class="badge bg-{{ $pct >= 40 ? 'success' : 'danger' }}">
                                {{ $pct >= 40 ? 'Pass' : 'Fail' }}
                            </span>
                            @endif
                        </td>
                        <td>
                            @if(!$mark->is_absent && !$mark->recheckRequest)
                            <button class="btn btn-xs btn-outline-warning" onclick="openRecheck({{ $mark->id }})">
                                <i class="bi bi-arrow-repeat"></i> Request
                            </button>
                            @elseif($mark->recheckRequest)
                            <span class="badge bg-{{ $mark->recheckRequest->status === 'completed' ? 'success' : 'secondary' }}">
                                {{ ucfirst($mark->recheckRequest->status) }}
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No results published yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recheck Modal -->
<div class="modal fade" id="recheckModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-arrow-repeat me-2"></i>Request Recheck</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('student.recheck', app('school')->slug) }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="student_marks_id" id="recheckMarkId">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Reason for Recheck *</label>
                        <textarea name="reason" class="form-control form-control-sm" rows="4"
                            placeholder="Please explain why you want a recheck (minimum 20 characters)..." required minlength="20"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-warning">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>

@push('scripts')
<script>
function openRecheck(markId) {
    document.getElementById('recheckMarkId').value = markId;
    new bootstrap.Modal(document.getElementById('recheckModal')).show();
}
</script>
@endpush
@endsection
