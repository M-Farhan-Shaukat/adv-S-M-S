@extends('school.layouts.app')
@section('title', 'Create Question Bank')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.question-bank.index', $school->slug) }}">Question Banks</a></li>
    <li class="breadcrumb-item active">Create from Images</li>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-bold">
                <i class="bi bi-robot me-2 text-primary"></i>AI Question Generator
                <small class="text-muted fw-normal ms-2">Upload book pages → AI generates questions</small>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('school.question-bank.process-image', $school->slug) }}"
                      enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Subject *</label>
                            <select name="subject_id" class="form-select form-select-sm" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Class *</label>
                            <select name="school_class_id" class="form-select form-select-sm" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">Title *</label>
                            <input type="text" name="title" class="form-control form-control-sm"
                                   placeholder="e.g. Chapter 3 - Photosynthesis" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Chapter</label>
                            <input type="text" name="chapter" class="form-control form-control-sm" placeholder="Chapter 3">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Difficulty *</label>
                            <select name="difficulty" class="form-select form-select-sm" required>
                                <option value="easy">Easy</option>
                                <option value="medium" selected>Medium</option>
                                <option value="hard">Hard</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Language *</label>
                            <select name="language" class="form-select form-select-sm" required>
                                <option value="english">English</option>
                                <option value="urdu">اردو (Urdu)</option>
                                <option value="arabic">العربية (Arabic)</option>
                            </select>
                        </div>
                        <div class="col-md-4"></div>

                        {{-- Question counts --}}
                        <div class="col-12">
                            <p class="small fw-bold text-muted mb-2 text-uppercase" style="letter-spacing:1px">Questions to Generate</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">MCQs</label>
                            <input type="number" name="mcq_count" class="form-control form-control-sm" value="10" min="0" max="30">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Short Questions</label>
                            <input type="number" name="short_count" class="form-control form-control-sm" value="5" min="0" max="20">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Long Questions</label>
                            <input type="number" name="long_count" class="form-control form-control-sm" value="2" min="0" max="10">
                        </div>

                        {{-- Multiple Image Upload --}}
                        <div class="col-12">
                            <label class="form-label small fw-semibold">
                                Book Page Images *
                                <span class="text-muted fw-normal">(select multiple - JPG, PNG, WEBP)</span>
                            </label>

                            {{-- Hidden file input --}}
                            <input type="file"
                                   name="source_images[]"
                                   id="imageInput"
                                   accept=".jpg,.jpeg,.png,.webp,.gif,.bmp"
                                   class="d-none"
                                   multiple="multiple"
                                   onchange="addImages(this)">

                            {{-- Upload buttons --}}
                            <div class="d-flex gap-2 mb-2">
                                <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="document.getElementById('imageInput').click()">
                                    <i class="bi bi-folder2-open me-1"></i>Browse Images
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="clearImages()" id="clearBtn" style="display:none">
                                    <i class="bi bi-x-circle me-1"></i>Clear All
                                </button>
                                <span id="imageCountBadge" class="badge bg-primary align-self-center"
                                      style="display:none"></span>
                            </div>

                            {{-- Preview area --}}
                            <div id="previewArea" class="border rounded-3 p-3 bg-light"
                                 style="min-height:100px">
                                <div id="emptyMsg" class="text-center text-muted py-2">
                                    <i class="bi bi-images fs-3 d-block mb-1 opacity-50"></i>
                                    <small>No images selected yet. Click "Browse Images" above.</small>
                                </div>
                                <div id="imageGrid" class="row g-2" style="display:none"></div>
                            </div>

                            @error('source_images')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            @error('source_images.*')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-robot me-2"></i>Generate Questions with AI
                        </button>
                        <small class="text-muted ms-3">
                            <i class="bi bi-clock me-1"></i>May take 30-90 seconds per image
                        </small>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-primary bg-opacity-10 mb-3">
            <div class="card-body">
                <h6 class="fw-bold text-primary"><i class="bi bi-info-circle me-2"></i>How it works</h6>
                <ol class="small text-muted ps-3 mb-0">
                    <li class="mb-1">Upload 1-10 book page photos</li>
                    <li class="mb-1">AI reads text from each image</li>
                    <li class="mb-1">AI generates MCQs, Short & Long questions</li>
                    <li class="mb-1">Review, edit & set correct answers</li>
                    <li class="mb-1">Save to question bank</li>
                    <li>Generate printable papers anytime</li>
                </ol>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold"><i class="bi bi-cpu me-2 text-success"></i>AI Status</h6>
                @php $provider = env('AI_PROVIDER', 'ollama'); @endphp
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-success fs-6">{{ strtoupper($provider) }}</span>
                    <small class="text-muted">{{ $provider === 'ollama' ? 'Local • Free • Private' : 'Cloud • Free tier' }}</small>
                </div>
                @if($provider === 'ollama')
                <div id="ollamaStatus" class="small text-muted">
                    <i class="bi bi-circle-fill text-warning me-1" style="font-size:8px"></i>Checking...
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Global file list (DataTransfer to accumulate files)
let allFiles = new DataTransfer();

function addImages(input) {
    // Add new files to accumulated list
    Array.from(input.files).forEach(file => {
        // Avoid duplicates by name
        const exists = Array.from(allFiles.files).some(f => f.name === file.name && f.size === file.size);
        if (!exists && allFiles.files.length < 10) {
            allFiles.items.add(file);
        }
    });

    // Sync back to input
    input.files = allFiles.files;

    renderPreviews();

    // Reset input so same file can be re-added if needed
    input.value = '';
}

function renderPreviews() {
    const grid     = document.getElementById('imageGrid');
    const emptyMsg = document.getElementById('emptyMsg');
    const badge    = document.getElementById('imageCountBadge');
    const clearBtn = document.getElementById('clearBtn');
    const count    = allFiles.files.length;

    grid.innerHTML = '';

    if (count === 0) {
        grid.style.display = 'none';
        emptyMsg.style.display = 'block';
        badge.style.display = 'none';
        clearBtn.style.display = 'none';
        return;
    }

    grid.style.display = 'flex';
    grid.style.flexWrap = 'wrap';
    emptyMsg.style.display = 'none';
    badge.style.display = 'inline-block';
    badge.textContent = count + ' image' + (count > 1 ? 's' : '') + ' selected';
    clearBtn.style.display = 'inline-block';

    Array.from(allFiles.files).forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.style.cssText = 'position:relative;width:100px;margin:4px';
            div.innerHTML = `
                <img src="${e.target.result}"
                     style="width:100px;height:80px;object-fit:cover;border-radius:6px;border:2px solid #dee2e6">
                <span style="position:absolute;top:2px;left:4px;background:#0d6efd;color:white;
                             font-size:10px;padding:1px 5px;border-radius:3px">Pg ${i+1}</span>
                <button type="button" onclick="removeImage(${i})"
                        style="position:absolute;top:2px;right:2px;background:#dc3545;color:white;
                               border:none;border-radius:50%;width:18px;height:18px;font-size:10px;
                               cursor:pointer;line-height:1;padding:0">×</button>
                <div style="font-size:9px;color:#666;text-align:center;overflow:hidden;
                            white-space:nowrap;text-overflow:ellipsis;max-width:100px">
                    ${file.name}
                </div>`;
            grid.appendChild(div);
        };
        reader.readAsDataURL(file);
    });

    // Sync to actual input
    document.getElementById('imageInput').files = allFiles.files;
}

function removeImage(index) {
    const newDt = new DataTransfer();
    Array.from(allFiles.files).forEach((f, i) => {
        if (i !== index) newDt.items.add(f);
    });
    allFiles = newDt;
    renderPreviews();
}

function clearImages() {
    allFiles = new DataTransfer();
    renderPreviews();
}

// Loading state on submit
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    if (allFiles.files.length === 0) {
        e.preventDefault();
        alert('Please select at least one image.');
        return;
    }
    // Sync files to input before submit
    document.getElementById('imageInput').files = allFiles.files;

    const btn = document.getElementById('submitBtn');
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Processing ${allFiles.files.length} image(s)... please wait`;
    btn.disabled = true;
});
</script>
@endpush
@endsection
