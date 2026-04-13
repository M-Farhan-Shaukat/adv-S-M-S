<!DOCTYPE html>
<html lang="{{ in_array($paper->language, ['urdu','arabic']) ? 'ur' : 'en' }}"
      dir="{{ in_array($paper->language, ['urdu','arabic']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $paper->title }} - {{ $school->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: {{ in_array($paper->language, ['urdu','arabic']) ? "'Noto Nastaliq Urdu', 'Jameel Noori Nastaleeq', serif" : "'Times New Roman', serif" }};
            font-size: 12pt;
            color: #000;
            background: #fff;
            padding: 20px;
        }

        /* Urdu/Arabic font loading */
        @if(in_array($paper->language, ['urdu','arabic']))
        @import url('https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;700&display=swap');
        body { font-family: 'Noto Nastaliq Urdu', serif; font-size: 14pt; line-height: 2; }
        @endif

        .paper-container { max-width: 800px; margin: 0 auto; }

        /* Header */
        .paper-header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 12px; margin-bottom: 16px; }
        .school-name { font-size: 18pt; font-weight: bold; }
        .paper-title { font-size: 14pt; font-weight: bold; margin-top: 4px; }
        .paper-meta { display: flex; justify-content: space-between; margin-top: 10px; font-size: 11pt; }
        .paper-meta-center { text-align: center; }
        .instructions { background: #f5f5f5; border: 1px solid #ccc; padding: 8px 12px; margin: 10px 0; font-size: 10pt; border-radius: 4px; }

        /* Sections */
        .section-title {
            font-size: 12pt; font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 4px; margin: 16px 0 10px;
        }
        .section-marks { font-weight: normal; font-size: 10pt; color: #444; }

        /* Questions */
        .question { margin-bottom: 14px; }
        .question-text { font-weight: bold; margin-bottom: 4px; }
        .options { display: grid; grid-template-columns: 1fr 1fr; gap: 2px 20px; margin-left: 20px; font-size: 11pt; }
        .answer-lines { margin-top: 6px; margin-left: 20px; }
        .answer-line { border-bottom: 1px solid #999; height: 24px; margin-bottom: 4px; }

        /* Answer Key (hidden on print by default) */
        .answer-key { display: none; }
        .answer-key.show-key { display: block; }
        .answer-key-section { margin-top: 30px; border-top: 2px dashed #000; padding-top: 16px; }
        .answer-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px; }
        .answer-item { text-align: center; font-size: 10pt; border: 1px solid #ccc; padding: 3px; border-radius: 3px; }
        .answer-item .ans { font-weight: bold; color: #006400; text-transform: uppercase; }

        /* Print styles */
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            .answer-key { display: none !important; }
            @page { margin: 1.5cm; size: A4; }
        }
    </style>
</head>
<body>
<div class="paper-container">

    {{-- No-print toolbar --}}
    <div class="no-print" style="background:#1e3a5f;color:white;padding:10px 16px;margin:-20px -20px 20px;display:flex;justify-content:space-between;align-items:center;">
        <span style="font-family:sans-serif;font-size:14px;font-weight:bold">
            📄 {{ $paper->title }}
        </span>
        <div style="display:flex;gap:8px">
            <button onclick="toggleAnswerKey()" style="background:#28a745;color:white;border:none;padding:6px 14px;border-radius:4px;cursor:pointer;font-family:sans-serif">
                👁 Toggle Answer Key
            </button>
            <button onclick="window.print()" style="background:#0d6efd;color:white;border:none;padding:6px 14px;border-radius:4px;cursor:pointer;font-family:sans-serif">
                🖨 Print / Download PDF
            </button>
            <a href="{{ route('school.question-bank.papers', $school->slug) }}"
               style="background:#6c757d;color:white;border:none;padding:6px 14px;border-radius:4px;cursor:pointer;text-decoration:none;font-family:sans-serif">
                ← Back
            </a>
        </div>
    </div>

    {{-- Paper Header --}}
    <div class="paper-header">
        <div class="school-name">{{ $school->name }}</div>
        <div class="paper-title">{{ $paper->title }}</div>
        <div class="paper-meta">
            <div>
                <div><strong>Subject:</strong> {{ $paper->subject?->name }}</div>
                <div><strong>Class:</strong> {{ $paper->schoolClass?->name }}</div>
            </div>
            <div class="paper-meta-center">
                <div><strong>Total Marks:</strong> {{ $paper->total_marks }}</div>
                <div><strong>Time:</strong> {{ $paper->duration_minutes }} minutes</div>
            </div>
            <div style="text-align:right">
                <div><strong>Date:</strong> {{ $paper->exam_date ? \Carbon\Carbon::parse($paper->exam_date)->format('d M Y') : '___________' }}</div>
                <div><strong>Name:</strong> ___________________</div>
            </div>
        </div>
        @if($paper->instructions)
        <div class="instructions">
            <strong>Instructions:</strong> {{ $paper->instructions }}
        </div>
        @endif
    </div>

    {{-- Section A: MCQs --}}
    @if($mcqs->count())
    <div class="section-title">
        Section A: Multiple Choice Questions
        <span class="section-marks">
            ({{ $mcqs->count() }} × {{ $paper->mcq_marks }} = {{ $mcqs->count() * $paper->mcq_marks }} marks)
        </span>
    </div>
    @foreach($mcqs as $i => $pq)
    @php $q = $pq->question; @endphp
    @if($q)
    <div class="question">
        <div class="question-text">Q{{ $i+1 }}. {{ $q->question_text }}</div>
        <div class="options">
            <div>(a) {{ $q->option_a }}</div>
            <div>(b) {{ $q->option_b }}</div>
            <div>(c) {{ $q->option_c }}</div>
            <div>(d) {{ $q->option_d }}</div>
        </div>
    </div>
    @endif
    @endforeach
    @endif

    {{-- Section B: Short Questions --}}
    @if($shorts->count())
    <div class="section-title">
        Section B: Short Answer Questions
        <span class="section-marks">
            ({{ $shorts->count() }} × {{ $paper->short_marks }} = {{ $shorts->count() * $paper->short_marks }} marks)
        </span>
    </div>
    @foreach($shorts as $i => $pq)
    @php $q = $pq->question; @endphp
    @if($q)
    <div class="question">
        <div class="question-text">Q{{ $i+1 }}. {{ $q->question_text }}</div>
        <div class="answer-lines">
            <div class="answer-line"></div>
            <div class="answer-line"></div>
            <div class="answer-line"></div>
        </div>
    </div>
    @endif
    @endforeach
    @endif

    {{-- Section C: Long Questions --}}
    @if($longs->count())
    <div class="section-title">
        Section C: Long Answer Questions
        <span class="section-marks">
            ({{ $longs->count() }} × {{ $paper->long_marks }} = {{ $longs->count() * $paper->long_marks }} marks)
        </span>
    </div>
    @foreach($longs as $i => $pq)
    @php $q = $pq->question; @endphp
    @if($q)
    <div class="question">
        <div class="question-text">Q{{ $i+1 }}. {{ $q->question_text }}</div>
        <div class="answer-lines">
            @for($l = 0; $l < 8; $l++)
            <div class="answer-line"></div>
            @endfor
        </div>
    </div>
    @endif
    @endforeach
    @endif

    {{-- Answer Key (toggle-able, hidden on print) --}}
    <div class="answer-key" id="answerKey">
        <div class="answer-key-section">
            <div class="section-title">Answer Key (MCQs)</div>
            <div class="answer-grid">
                @foreach($mcqs as $i => $pq)
                @php $q = $pq->question; @endphp
                @if($q)
                <div class="answer-item">
                    <div style="font-size:9pt;color:#666">Q{{ $i+1 }}</div>
                    <div class="ans">{{ $q->correct_answer }}</div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>

</div>

<script>
function toggleAnswerKey() {
    const key = document.getElementById('answerKey');
    key.classList.toggle('show-key');
}
</script>
</body>
</html>
