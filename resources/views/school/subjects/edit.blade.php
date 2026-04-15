@extends('school.layouts.app')
@section('title', 'Edit Subject')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.subjects.index', $school->slug) }}">Subjects</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm" style="max-width:500px">
    <div class="card-header bg-white border-0 fw-bold">
        <i class="bi bi-pencil me-2 text-warning"></i>Edit Subject: {{ $subject->name }}
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.subjects.update', [$school->slug, $subject]) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label small fw-semibold">Class *</label>
                <select name="school_class_id" class="form-select form-select-sm" required>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}"
                            {{ old('school_class_id', $subject->school_class_id) == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-semibold">Subject Name *</label>
                <input type="text" name="name" class="form-control form-control-sm"
                       value="{{ old('name', $subject->name) }}" required>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning btn-sm">
                    <i class="bi bi-check-lg me-1"></i>Update
                </button>
                <a href="{{ route('school.subjects.index', $school->slug) }}"
                   class="btn btn-outline-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
