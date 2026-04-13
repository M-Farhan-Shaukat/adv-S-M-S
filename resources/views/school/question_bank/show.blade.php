@extends('school.layouts.app')
@section('title', 'Question Bank')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.question-bank.index', $school->slug) }}">Question Banks</a></li>
    <li class="breadcrumb-item active">{{ $questionBank->title }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0 fw-bold">{{ $questionBank->title }}</h5>
        <small class="text-muted">
            {{ $questionBank->subject?->name }} • {{ $questionBank->schoolClass?->name }} •
            <span class="badge bg-{{ $questionBank->difficulty === 'easy' ? 'success' : ($questionBank->difficulty === 'hard' ? 'danger' : 'warning') }}">
                {{ ucfirst($questionBank->difficulty) }}
            </span>
            <span class="badge bg-info bg-opacity-10 text-info ms-1">{{ ucfirst($questionBank->language) }}</span>
        </small>
    </div>
    <a href="{{ route('school.question-bank.generate', $school->slug) }}?bank_id={{ $questionBank->id }}"
       class="btn btn-success btn-sm">
        <i class="bi bi-file-earmark-plus me-1"></i>Generate Paper
    </a>
</div>

@php
    $mcqs   = $questionBank->questions->where('type', 'mcq');
    $shorts = $questionBank->questions->where('type', 'short');
    $longs  = $questionBank->questions->where('type', 'long');
    $isRtl  = in_array($questionBank->language, ['urdu', 'arabic']);
@endphp

<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card border-0 shadow-sm text-center"><div class="card-body py-3">
        <div class="fs-3 fw-bold text-primary">{{ $mcqs->count() }}</div><div class="text-muted small">MCQs</div>
    </div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm text-center"><div class="card-body py-3">
        <div class="fs-3 fw-bold text-success">{{ $shorts->count() }}</div><div class="text-muted small">Short Questions</div>
    </div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm text-center"><div class="card-body py-3">
        <div class="fs-3 fw-bold text-warning">{{ $longs->count() }}</div><div class="text-muted small">Long Questions</div>
    </div></div></div>
</div>

@if($mcqs->count())
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-0 fw-semibold">
        <i class="bi bi-list-check me-2 text-primary"></i>MCQ Questions ({{ $mcqs->count() }})
    </div>
    <div class="card-body p-0">
        @foreach($mcqs as $i => $q)
        <div class="p-3 border-bottom" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
            <div class="d-flex gap-2 mb-2">
                <span class="badge bg-primary flex-shrink-0">Q{{ $i+1 }}</span>
                <span class="fw-semibold">{{ $q->question_text }}</span>
            </div>
            <div class="row g-1 ms-4 small">
                @foreach(['a','b','c','d'] as $opt)
                <div class="col-md-6">
                    <span class="{{ $q->correct_answer === $opt ? 'text-success fw-bold' : 'text-muted' }}">
                        {{ $q->correct_answer === $opt ? '✓' : '○' }}
                        <strong>{{ strtoupper($opt) }}.</strong> {{ $q->{'option_'.$opt} }}
                    </span>
                </div>
                @endforeach
            </div>
            <!-- @if($q->answer_hint)
            <small class="text-muted ms-4 d-block mt-1"><i class="bi bi-lightbulb me-1"></i>{{ $q->answer_hint }}</small>
            @endif -->
        </div>
        @endforeach
    </div>
</div>
@endif

@if($shorts->count())
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 fw-semibold">
        <i class="bi bi-pencil me-2 text-success"></i>Short Questions ({{ $shorts->count() }})
    </div>
    <div class="card-body p-0">
        @foreach($shorts as $i => $q)
        <div class="p-3 border-bottom" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
            <div class="d-flex gap-2 mb-1">
                <span class="badge bg-success flex-shrink-0">Q{{ $i+1 }}</span>
                <span class="fw-semibold">{{ $q->question_text }}</span>
            </div>
            <!-- @if($q->answer_hint)
            <small class="text-muted ms-4 d-block"><i class="bi bi-lightbulb me-1"></i>{{ $q->answer_hint }}</small>
            @endif -->
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
