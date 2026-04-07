@extends('school.layouts.app')
@section('title', 'Create Exam')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.exams.index', $school->slug) }}">Exams</a></li>
    <li class="breadcrumb-item active">Create Exam</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-header bg-white border-0 fw-bold">
        <i class="bi bi-pencil-square me-2 text-primary"></i>Create New Exam
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.exams.store', $school->slug) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Exam Name *</label>
                    <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Mid Term 2026" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Type *</label>
                    <select name="type" class="form-select form-select-sm" required>
                        <option value="quiz">Quiz</option>
                        <option value="mid_term">Mid Term</option>
                        <option value="final_term">Final Term</option>
                        <option value="annual">Annual</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Class *</label>
                    <select name="school_class_id" class="form-select form-select-sm" required>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Start Date *</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">End Date *</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" required>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Description</label>
                    <textarea name="description" class="form-control form-control-sm" rows="3"></textarea>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i>Create Exam</button>
                <a href="{{ route('school.exams.index', $school->slug) }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
