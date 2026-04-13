@extends('school.layouts.app')
@section('title', 'Paper Preview')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.question-bank.papers', $school->slug) }}">Papers</a></li>
    <li class="breadcrumb-item active">Preview</li>
@endsection

@section('content')
@php
    $isRtl = in_array($paperData['language'], ['urdu', 'arabic']);
    $dir   = $isRtl ? 'rtl' : 'ltr';
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-eye me-2"></i>Paper Preview</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('school.question-bank.generate', $school->slug) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
        <form method="POST" action="{{ route('school.question-bank.save-paper', $school->slug) }}">
            @csrf
            <button type="submit" class="btn btn-success btn-sm">
                <i class="bi bi-save me-1"></i>Save & Go to Print
            </button>
        </form>
    </div>
</div>

{{-- Paper Preview Card --}}
<div class="card border-0 shadow-sm" id="paperPreview">
    <div class="card-body p-4" dir="{{ $dir }}">

        {{-- Header --}}
        <div class="text-center mb-4 pb-3 border-bottom">
            <h4 class="fw-bold">{{ $school->name }}</h4>
            <h5 class="fw-semibold mt-1">{{ $paperData['title'] }}</h5>
            <div class="row mt-3 small">
                <div class="col-4 text-start">
                    <strong>Subject:</strong> {{ $bank->subject?->name }}
                </div>
                <div class="col-4 text-center">
                    <strong>Class:</strong> {{ $bank->schoolClass?->name }}
                </div>
                <div class="col-4 text-end">
                    <strong>Total Marks:</strong> {{ $paperData['total_marks'] }}
                </div>
            </div>
            <div class="row mt-1 small">
                <div class="col-4 text-start">
                    <strong>Date:</strong> {{ $paperData['exam_date'] ? \Carbon\Carbon::parse($paperData['exam_date'])->format('d M Y') : '___________' }}
                </div>
                <div class="col-4 text-center">
                    <strong>Time:</strong> {{ $paperData['duration_minutes'] }} minutes
                </div>
                <div class="col-4 text-end">
                    <strong>Name:</strong> ___________________
                </div>
            </div>
            @if($paperData['instructions'])
            <div class="mt-2 p-2 bg-light rounded small text-start">
                <strong>Instructions:</strong> {{ $paperData['instructions'] }}
            </div>
            @endif
        </div>

        {{-- MCQs --}}
        @if($paperData['mcqs']->count())
        <div class="mb-4">
            <h6 class="fw-bold border-bottom pb-2">
                Section A: Multiple Choice Questions
                <small class="text-muted fw-normal">({{ $paperData['mcqs']->count() }} × {{ $paperData['mcq_marks'] }} = {{ $paperData['mcqs']->count() * $paperData['mcq_marks'] }} marks)</small>
            </h6>
            @foreach($paperData['mcqs'] as $i => $q)
            <div class="mb-3">
                <p class="mb-1"><strong>Q{{ $i+1 }}.</strong> {{ $q->question_text }}</p>
                <div class="row g-1 ms-3 small">
                    <div class="col-6">(a) {{ $q->option_a }}</div>
                    <div class="col-6">(b) {{ $q->option_b }}</div>
                    <div class="col-6">(c) {{ $q->option_c }}</div>
                    <div class="col-6">(d) {{ $q->option_d }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Short Questions --}}
        @if($paperData['shorts']->count())
        <div class="mb-4">
            <h6 class="fw-bold border-bottom pb-2">
                Section B: Short Answer Questions
                <small class="text-muted fw-normal">({{ $paperData['shorts']->count() }} × {{ $paperData['short_marks'] }} = {{ $paperData['shorts']->count() * $paperData['short_marks'] }} marks)</small>
            </h6>
            @foreach($paperData['shorts'] as $i => $q)
            <div class="mb-3">
                <p class="mb-1"><strong>Q{{ $i+1 }}.</strong> {{ $q->question_text }}</p>
                <div class="border-bottom mt-3" style="height:30px"></div>
                <div class="border-bottom mt-2" style="height:30px"></div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Long Questions --}}
        @if(count($paperData['longs'] ?? []) > 0)
        <div class="mb-4">
            <h6 class="fw-bold border-bottom pb-2">
                Section C: Long Answer Questions
                <small class="text-muted fw-normal">({{ $paperData['longs']->count() }} × {{ $paperData['long_marks'] }} = {{ $paperData['longs']->count() * $paperData['long_marks'] }} marks)</small>
            </h6>
            @foreach($paperData['longs'] as $i => $q)
            <div class="mb-3">
                <p class="mb-1"><strong>Q{{ $i+1 }}.</strong> {{ $q->question_text }}</p>
                @for($l = 0; $l < 5; $l++)
                <div class="border-bottom mt-3" style="height:30px"></div>
                @endfor
            </div>
            @endforeach
        </div>
        @endif

    </div>
</div>
@endsection
