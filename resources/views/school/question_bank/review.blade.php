@extends('school.layouts.app')
@section('title', 'Review AI Questions')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.question-bank.index', $school->slug) }}">Question Banks</a></li>
    <li class="breadcrumb-item active">Review Questions</li>
@endsection

@section('content')
@php
    $isRtl   = in_array($form['language'] ?? 'english', ['urdu', 'arabic']);
    $dir     = $isRtl ? 'rtl' : 'ltr';
    $mcqs    = collect($questions)->where('type', 'mcq');
    $shorts  = collect($questions)->where('type', 'short');
    $longs   = collect($questions)->where('type', 'long');
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0 fw-bold"><i class="bi bi-check2-square me-2 text-success"></i>Review AI Generated Questions</h5>
        <small class="text-muted">Edit questions, set correct answers, then save</small>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-primary">{{ $mcqs->count() }} MCQs</span>
        <span class="badge bg-success">{{ $shorts->count() }} Short</span>
        <span class="badge bg-warning text-dark">{{ $longs->count() }} Long</span>
    </div>
</div>

<form method="POST" action="{{ route('school.question-bank.save-questions', $school->slug) }}">
    @csrf

    @foreach($questions as $i => $q)
    @php
        $type  = $q['type'] ?? 'mcq';
        $isMcq = $type === 'mcq';
        $isLong = $type === 'long';
        $color = $isMcq ? 'primary' : ($isLong ? 'warning' : 'success');
        $label = $isMcq ? 'MCQ' : ($isLong ? 'Long' : 'Short');
    @endphp

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-{{ $color }}">{{ $label }} #{{ $i + 1 }}</span>
                <input type="hidden" name="questions[{{ $i }}][type]" value="{{ $type }}">
            </div>
            <div class="d-flex align-items-center gap-2">
                <label class="form-label small mb-0 fw-semibold">Marks:</label>
                <input type="number" name="questions[{{ $i }}][marks]"
                       class="form-control form-control-sm" style="width:70px"
                       value="{{ $q['marks'] ?? ($isLong ? 5 : ($isMcq ? 1 : 3)) }}" min="1" max="20">
            </div>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label small fw-semibold">Question *</label>
                <textarea name="questions[{{ $i }}][question_text]"
                          class="form-control form-control-sm" rows="{{ $isLong ? 3 : 2 }}" required
                          dir="{{ $dir }}">{{ $q['question_text'] ?? '' }}</textarea>
            </div>

            @if($isMcq)
            <div class="row g-2 mb-3">
                @foreach(['a','b','c','d'] as $opt)
                <div class="col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text fw-bold text-uppercase">{{ $opt }}</span>
                        <input type="text" name="questions[{{ $i }}][option_{{ $opt }}]"
                               class="form-control"
                               value="{{ $q['option_' . $opt] ?? '' }}"
                               placeholder="Option {{ strtoupper($opt) }}"
                               dir="{{ $dir }}">
                    </div>
                </div>
                @endforeach
            </div>
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-success">Correct Answer *</label>
                    <select name="questions[{{ $i }}][correct_answer]" class="form-select form-select-sm" required>
                        @foreach(['a','b','c','d'] as $opt)
                        <option value="{{ $opt }}" {{ ($q['correct_answer'] ?? '') === $opt ? 'selected' : '' }}>
                            Option {{ strtoupper($opt) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label small fw-semibold">Explanation</label>
                    <input type="text" name="questions[{{ $i }}][answer_hint]"
                           class="form-control form-control-sm"
                           value="{{ $q['answer_hint'] ?? '' }}"
                           placeholder="Brief explanation (optional)" dir="{{ $dir }}">
                </div>
            </div>
            @else
            {{-- Short / Long --}}
            <div>
                <label class="form-label small fw-semibold">
                    {{ $isLong ? 'Answer Guide / Key Points' : 'Expected Answer / Hint' }}
                </label>
                <textarea name="questions[{{ $i }}][answer_hint]"
                          class="form-control form-control-sm" rows="{{ $isLong ? 4 : 2 }}"
                          placeholder="{{ $isLong ? 'Detailed answer guide, key points to cover...' : 'Expected answer or key points' }}"
                          dir="{{ $dir }}">{{ $q['answer_hint'] ?? '' }}</textarea>
                <input type="hidden" name="questions[{{ $i }}][option_a]" value="">
                <input type="hidden" name="questions[{{ $i }}][option_b]" value="">
                <input type="hidden" name="questions[{{ $i }}][option_c]" value="">
                <input type="hidden" name="questions[{{ $i }}][option_d]" value="">
                <input type="hidden" name="questions[{{ $i }}][correct_answer]" value="">
            </div>
            @endif
        </div>
    </div>
    @endforeach

    @if(empty($questions))
    <div class="alert alert-warning border-0">
        <i class="bi bi-exclamation-triangle me-2"></i>No questions generated. Please go back and try again.
    </div>
    @endif

    <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-success">
            <i class="bi bi-check-lg me-1"></i>Save {{ count($questions) }} Questions to Bank
        </button>
        <a href="{{ route('school.question-bank.create', $school->slug) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Start Over
        </a>
    </div>
</form>
@endsection
