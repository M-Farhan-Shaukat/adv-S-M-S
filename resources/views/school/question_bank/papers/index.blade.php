@extends('school.layouts.app')
@section('title', 'Generated Papers')
@section('breadcrumb')
    <li class="breadcrumb-item active">Generated Papers</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-file-earmark-text me-2"></i>Generated Papers</h5>
    <a href="{{ route('school.question-bank.generate', $school->slug) }}" class="btn btn-success btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Generate New Paper
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Title</th><th>Subject</th><th>Class</th><th>Language</th><th>Marks</th><th>MCQ</th><th>Short</th><th>Long</th><th>Date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($papers as $paper)
                    <tr>
                        <td class="fw-semibold">{{ $paper->title }}</td>
                        <td class="small">{{ $paper->subject?->name }}</td>
                        <td class="small">{{ $paper->schoolClass?->name }}</td>
                        <td>
                            <span class="badge bg-info bg-opacity-10 text-info">{{ ucfirst($paper->language) }}</span>
                        </td>
                        <td class="fw-bold">{{ $paper->total_marks }}</td>
                        <td class="small text-primary">{{ $paper->mcq_count }}</td>
                        <td class="small text-success">{{ $paper->short_count }}</td>
                        <td class="small text-warning">{{ $paper->long_count }}</td>
                        <td class="small text-muted">
                            {{ $paper->exam_date ? \Carbon\Carbon::parse($paper->exam_date)->format('d M Y') : '-' }}
                        </td>
                        <td>
                            <a href="{{ route('school.question-bank.paper.print', [$school->slug, $paper]) }}"
                               class="btn btn-xs btn-outline-primary" target="_blank">
                                <i class="bi bi-printer"></i> Print
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-file-earmark-x fs-1 mb-3 d-block opacity-25"></i>
                            No papers generated yet.<br>
                            <a href="{{ route('school.question-bank.generate', $school->slug) }}" class="btn btn-success btn-sm mt-2">
                                Generate First Paper
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($papers->hasPages())
    <div class="card-footer bg-white border-0">{{ $papers->links() }}</div>
    @endif
</div>
<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
