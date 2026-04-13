@extends('school.layouts.app')
@section('title', 'Generate Paper')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.question-bank.papers', $school->slug) }}">Papers</a></li>
    <li class="breadcrumb-item active">Generate Paper</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm" style="max-width:750px">
    <div class="card-header bg-white border-0 fw-bold">
        <i class="bi bi-file-earmark-plus me-2 text-success"></i>Generate Exam Paper
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.question-bank.preview', $school->slug) }}">
            @csrf

            {{-- Multiple Bank Selection --}}
            <div class="mb-4">
                <label class="form-label small fw-semibold">
                    Select Question Banks *
                    <span class="text-muted fw-normal">(select one or more - questions will be picked randomly from all)</span>
                </label>

                @if($banks->isEmpty())
                <div class="alert alert-warning border-0 py-2 small">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    No question banks found.
                    <a href="{{ route('school.question-bank.create', $school->slug) }}">Create one first</a>
                </div>
                @else
                <div class="border rounded-3 p-2" style="max-height:280px;overflow-y:auto">
                    @foreach($banks as $bank)
                    <div class="form-check p-2 rounded hover-bg mb-1" style="cursor:pointer"
                         onclick="toggleBank({{ $bank->id }})">
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
                                    <small class="text-muted ms-2">{{ $bank->subject?->name }} / {{ $bank->schoolClass?->name }}</small>
                                </div>
                                <div class="d-flex gap-1">
                                    <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:10px">{{ $bank->mcq_count }} MCQ</span>
                                    <span class="badge bg-success bg-opacity-10 text-success" style="font-size:10px">{{ $bank->short_count }} Short</span>
                                    <span class="badge bg-warning bg-opacity-10 text-warning" style="font-size:10px">{{ $bank->long_count }} Long</span>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size:10px">{{ ucfirst($bank->language) }}</span>
                                </div>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
                <div class="mt-2 small text-muted" id="availableInfo">
                    Select banks to see available questions
                </div>
                @endif
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label small fw-semibold">Paper Title *</label>
                    <input type="text" name="title" class="form-control form-control-sm"
                           placeholder="e.g. Mid Term Exam - Mathematics" required>
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

                <div class="col-12"><hr class="my-1"><p class="small fw-bold text-muted mb-2">Question Count & Marks per Question</p></div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold">MCQ Count</label>
                    <input type="number" name="mcq_count" id="mcqCount" class="form-control form-control-sm" value="10" min="0" oninput="calcTotal()">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Short Q Count</label>
                    <input type="number" name="short_count" id="shortCount" class="form-control form-control-sm" value="5" min="0" oninput="calcTotal()">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Long Q Count</label>
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
                    <label class="form-label small fw-semibold">Paper Instructions</label>
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
                <a href="{{ route('school.question-bank.index', $school->slug) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
.hover-bg:hover { background: #f8f9fa; }
.bank-check:checked + label { color: #0d6efd; }
</style>

@push('scripts')
<script>
function toggleBank(id) {
    const cb = document.getElementById('bank_' + id);
    cb.checked = !cb.checked;
    updateAvailable();
}

function updateAvailable() {
    const checked = document.querySelectorAll('.bank-check:checked');
    let mcq = 0, short = 0, long = 0;
    checked.forEach(cb => {
        mcq   += parseInt(cb.dataset.mcq   || 0);
        short += parseInt(cb.dataset.short || 0);
        long  += parseInt(cb.dataset.long  || 0);
    });
    const info = document.getElementById('availableInfo');
    if (checked.length === 0) {
        info.textContent = 'Select banks to see available questions';
        info.className = 'mt-2 small text-muted';
    } else {
        info.innerHTML = `<i class="bi bi-check-circle text-success me-1"></i>
            <strong>${checked.length}</strong> bank(s) selected —
            Available: <strong>${mcq}</strong> MCQs,
            <strong>${short}</strong> Short,
            <strong>${long}</strong> Long`;
        info.className = 'mt-2 small text-success';
    }
}

function calcTotal() {
    const mcq   = (+document.querySelector('[name=mcq_count]').value   || 0) * (+document.querySelector('[name=mcq_marks]').value   || 1);
    const short = (+document.querySelector('[name=short_count]').value  || 0) * (+document.querySelector('[name=short_marks]').value  || 3);
    const long  = (+document.querySelector('[name=long_count]').value   || 0) * (+document.querySelector('[name=long_marks]').value   || 5);
    document.getElementById('totalMarks').textContent = mcq + short + long;
}

calcTotal();
</script>
@endpush
@endsection
