@extends('school.layouts.app')
@section('title', 'Question Banks')
@section('breadcrumb')
    <li class="breadcrumb-item active">Question Banks</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-robot me-2 text-primary"></i>AI Question Banks</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('school.question-bank.papers', $school->slug) }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-text me-1"></i>Generated Papers
        </a>
        <a href="{{ route('school.question-bank.create', $school->slug) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Create from Image
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Title</th><th>Subject</th><th>Class</th><th>Difficulty</th><th>Language</th><th>Questions</th><th>Created</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($banks as $bank)
                    <tr>
                        <td class="fw-semibold">{{ $bank->title }}</td>
                        <td class="small">{{ $bank->subject?->name }}</td>
                        <td class="small">{{ $bank->schoolClass?->name }}</td>
                        <td>
                            <span class="badge bg-{{ $bank->difficulty === 'easy' ? 'success' : ($bank->difficulty === 'hard' ? 'danger' : 'warning') }}">
                                {{ ucfirst($bank->difficulty) }}
                            </span>
                        </td>
                        <td><span class="badge bg-info bg-opacity-10 text-info">{{ ucfirst($bank->language) }}</span></td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ $bank->questions_count }}</span></td>
                        <td class="small text-muted">{{ $bank->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('school.question-bank.show', [$school->slug, $bank]) }}" class="btn btn-xs btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('school.question-bank.generate', $school->slug) }}?bank_id={{ $bank->id }}" class="btn btn-xs btn-outline-success">
                                    <i class="bi bi-file-earmark-plus"></i> Paper
                                </a>
                                <form method="POST" action="{{ route('school.question-bank.destroy', [$school->slug, $bank]) }}" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-robot fs-1 mb-3 d-block opacity-25"></i>
                            No question banks yet.<br>
                            <a href="{{ route('school.question-bank.create', $school->slug) }}" class="btn btn-primary btn-sm mt-2">
                                <i class="bi bi-plus-lg me-1"></i>Create from Book Image
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($banks->hasPages())
    <div class="card-footer bg-white border-0">{{ $banks->links() }}</div>
    @endif
</div>
<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
