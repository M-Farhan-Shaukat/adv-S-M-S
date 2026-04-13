@extends('school.layouts.app')
@section('title', 'Generate Paper')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.question-bank.papers', $school->slug) }}">Papers</a></li>
    <li class="breadcrumb-item active">Generate Paper</li>
@endsection

@section('content')

@php
    $selectedClass   = $classes->firstWhere('id', $selectedClassId);
    $selectedSubject = $subjects->firstWhere('id', $selectedSubjectId ?? null);
@endphp

{{-- Step indicator --}}
<div class="d-flex align-items-center gap-2 mb-4 small">
    <span class="badge {{ $selectedClassId ? 'bg-success' : 'bg-primary' }} rounded-pill px-3 py-2">
        1. Select Class
    </span>
    <i class="bi bi-arrow-right text-muted"></i>
    <span class="badge {{ $selectedSubjectId ? 'bg-success' : ($selectedClassId ? 'bg-primary' : 'bg-secondary') }} rounded-pill px-3 py-2">
        2. Select Subject
    </span>
    <i class="bi bi-arrow-right text-muted"></i>
    <span class="badge {{ $selectedSubjectId ? 'bg-primary' : 'bg-secondary' }} rounded-pill px-3 py-2">
        3. Configure Paper
    </span>
</div>

{{-- STEP 1: Select Class --}}
@if(!$selectedClassId)
<div class="card border-0 shadow-sm" style="max-width:480px">
    <div class="card-header bg-white border-0 fw-bold">
        <i class="bi bi-building me-2 text-primary"></i>Step 1: Select Class
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('school.question-bank.generate', $school->slug) }}">
            <div class="mb-3">
                <label class="form-label small fw-semibold">Which class is this paper for? *</label>
                <select name="class_id" class="form-select" required onchange="this.form.submit()">
                    <option value="">-- Select Class --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

{{-- STEP 2: Select Subject --}}
@elseif(!$selectedSubjectId)
<div class="card border-0 shadow-sm" style="max-width:480px">
    <div class="card-header bg-white border-0 fw-bold">
        <i class="bi bi-book me-2 text-success"></i>Step 2: Select Subject
        <small class="text-muted fw-normal ms-2">Class: {{ $selectedClass?->name }}</small>
    </div>
    <div class="card-body">
        @if($subjects->isEmpty())
        <div class="alert alert-warning border-0 py-2 small">
            <i class="bi bi-exclamation-triangle me-1"></i>
            No subjects found for <strong>{{ $selectedClass?->name }}</strong>.
            <a href="{{ route('school.subjects.index', $school->slug) }}">Add subjects first</a>
        </div>
        @else
        <form method="GET" action="{{ route('school.question-bank.generate', $school->slug) }}">
            <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
            <div class="mb-3">
                <label class="form-label small fw-semibold">Which subject? *</label>
                <select name="subject_id" class="form-select" required onchange="this.form.submit()">
                    <option value="">-- Select Subject --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
        @endif
        <a href="{{ route('school.question-bank.generate', $school->slug) }}"
           class="btn btn-outline-secondary btn-sm mt-2">
            <i class="bi bi-arrow-left me-1"></i>Change Class
        </a>
    </div>
</div>

{{-- STEP 3: Configure Paper --}}
@else
<div class="d-flex align-items-center gap-3 mb-3">
    <div>
        <h5 class="mb-0 fw-bold"><i class="bi bi-file-earmark-plus me-2 text-success"></i>Configure Paper</h5>
        <small class="text-muted">
            Class: <strong>{{ $selectedClass?->name }}</strong> &nbsp;•&nbsp;
            Subject: <strong>{{ $selectedSubject?->name }}</strong>
            <a href="{{ route('school.question-bank.generate', ['school' => $school->slug, 'class_id' => $selectedClassId]) }}"
               class="ms-2 small"><i class="bi bi-pencil"></i> Change subject</a>
        </small>
    </div>
</div>

@if($banks->isEmpty())
<div class="alert alert-warning border-0 shadow-sm">
    <i class="bi bi-exclamation-triangle me-2"></i>
    No question banks found for <strong>{{ $selectedClass?->name }}</strong> →
    <strong>{{ $selectedSubject?->name }}</strong>.
    <a href="{{ route('school.question-bank.create', $school->slug) }}" class="alert-link">
        Create a question bank first
    </a>
</div>
@else

<div class="card border-0 shadow-sm" style="max-width:750px">
    <div class="card-body">
        <form method="POST" action="{{ route('school.question-bank.preview', $school->slug) }}">
            @csrf
            <input type="hidden" name="class_id"   value="{{ $selectedClassId }}">
            <input type="hidden" name="subject_id" value="{{ $selectedSubjectId }}">

            {{-- Bank Selection --}}
            <div class="mb-4">
                <label class="form-label small fw-semibold">
                    Select Question Banks *
                    <span class="text-muted fw-normal">({{ $selectedClass?->name }} → {{ $selectedSubject?->name }})</span>
                </label>
                <div class="border rounded-3 p-2" style="max-height:220px;overflow-y:auto">
                    @foreach($banks as $bank)
                    <div class="form-check p-2 rounded mb-1" style="cursor:pointer"
                         onclick="document.getElementById('bank_{{ $bank->id }}').click()">
                        <input class="form-check-input bank-check" type="checkbox"
                               name="question_bank_ids[]"
                               value="{{ $bank->id }}"
                               id="bank_{{ $bank->id }}"
                               data-mcq="{{ $bank->mcq_count }}"
                               data-short="{{ $bank->short_count }}"
                               data-long="{{ $bank->long_count }}"
                               onchange="updateAvailable()">
                        <label class="form-check-label w-100" for="bank_{{ $bank->id }}" style="cursor:pointer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-semibold">{{ $bank->title }}</span>
                                    @if($bank->chapter)
                                        <small class="text-muted ms-1">— {{ $bank->chapter }}</small>
                                    @endif
                                </div>
                                <div class="d-flex gap-1 flex-shrink-0">
                                    <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:10px">{{ $bank->mcq_count }} MCQ</span>
                                    <span class="badge bg-success bg-opacity-10 text-success" style="font-size:10px">{{ $bank->short_count }} Short</span>
                                    <span class="badge bg-warning bg-opacity-10 text-warning" style="font-size:10px">{{ $bank->long_count }} Long</span>
                                </div>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
                <div id="availableInfo" class="mt-1 small text-muted">Select banks to see available questions</div>
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label small fw-semibold">Paper Title *</label>
                    <input type="text" name="title" class="form-control form-control-sm"
                           placeholder="{{ $selectedClass?->name }} - {{ $selectedSubject?->name }} Exam"
                           value="{{ $selectedClass?->name }} - {{ $selectedSubject?->name }} Exam"
                           required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Language *</label>
                    <select name="language" class="form-select form-select-sm" required>
                        <option value="english">English</option>
                        <option value="urdu">اردو (Urdu)</option>
                        <option value="arabic">العربية (Arabic)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Duration (minutes) *</label>
                    <input type="number" name="duration_minutes" class="form-control form-control-sm" value="60" min="15" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Exam Date</label>
                    <input type="date" name="exam_date" class="form-control form-control-sm">
                </div>

                <div class="col-12"><hr class="my-1"><p class="small fw-bold text-muted mb-2">Questions & Marks</p></div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold">MCQ Count <span id="mcqAvail" class="text-muted fw-normal"></span></label>
                    <input type="number" name="mcq_count" id="mcqCount" class="form-control form-control-sm" value="10" min="0" oninput="calcTotal()">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Short Q Count <span id="shortAvail" class="text-muted fw-normal"></span></label>
                    <input type="number" name="short_count" id="shortCount" class="form-control form-control-sm" value="5" min="0" oninput="calcTotal()">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Long Q Count <span id="longAvail" class="text-muted fw-normal"></span></label>
                    <input type="number" name="long_count" id="longCount" class="form-control form-control-sm" value="2" min="0" oninput="calcTotal()">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Marks per MCQ</label>
                    <input type="number" name="mcq_marks" class="form-control form-control-sm" value="1" min="1" oninput="calcTotal()">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Marks per Short Q</label>
                    <input type="number" name="short_marks" class="form-control form-control-sm" value="3" min="1" oninput="calcTotal()">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Marks per Long Q</label>
                    <input type="number" name="long_marks" class="form-control form-control-sm" value="5" min="1" oninput="calcTotal()">
                </div>

                <div class="col-12">
                    <label class="form-label small fw-semibold">Instructions</label>
                    <textarea name="instructions" class="form-control form-control-sm" rows="2"
                              placeholder="e.g. Attempt all questions. Each MCQ carries 1 mark."></textarea>
                </div>

                <div class="col-12">
                    <div class="alert alert-info border-0 py-2 small mb-0">
                        <i class="bi bi-calculator me-1"></i>
                        Total Marks: <strong id="totalMarks">0</strong>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-eye me-1"></i>Preview Paper
                </button>
                <a href="{{ route('school.question-bank.generate', ['school' => $school->slug, 'class_id' => $selectedClassId]) }}"
                   class="btn btn-outline-secondary">Back</a>
            </div>
        </form>
    </div>
</div>
@endif
@endif

@push('scripts')
<script>
function updateAvailable() {
    const checked = document.querySelectorAll('.bank-check:checked');
    let mcq = 0, short = 0, long = 0;
    checked.forEach(cb => {
        mcq   += parseInt(cb.dataset.mcq   || 0);
        short += parseInt(cb.dataset.short || 0);
        long  += parseInt(cb.dataset.long  || 0);
    });
    document.getElementById('mcqAvail').textContent   = mcq   > 0 ? `(${mcq} avail)`   : '';
    document.getElementById('shortAvail').textContent = short > 0 ? `(${short} avail)` : '';
    document.getElementById('longAvail').textContent  = long  > 0 ? `(${long} avail)`  : '';

    const info = document.getElementById('availableInfo');
    if (!info) return;
    if (checked.length === 0) {
        info.textContent = 'Select banks to see available questions';
        info.className = 'mt-1 small text-muted';
    } else {
        info.innerHTML = `<i class="bi bi-check-circle text-success me-1"></i><strong>${checked.length}</strong> bank(s) — Available: <strong>${mcq}</strong> MCQ, <strong>${short}</strong> Short, <strong>${long}</strong> Long`;
        info.className = 'mt-1 small text-success';
    }
}

function calcTotal() {
    const mcq   = (+document.querySelector('[name=mcq_count]')?.value   || 0) * (+document.querySelector('[name=mcq_marks]')?.value   || 1);
    const short = (+document.querySelector('[name=short_count]')?.value  || 0) * (+document.querySelector('[name=short_marks]')?.value  || 3);
    const long  = (+document.querySelector('[name=long_count]')?.value   || 0) * (+document.querySelector('[name=long_marks]')?.value   || 5);
    const el = document.getElementById('totalMarks');
    if (el) el.textContent = mcq + short + long;
}
if (document.getElementById('totalMarks')) calcTotal();
</script>
@endpush
@endsection
