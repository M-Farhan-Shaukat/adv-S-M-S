@extends('school.layouts.app')
@section('title', 'Edit Role')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.custom-roles.index', $school->slug) }}">Custom Roles</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-header bg-white border-0 fw-bold">
        <i class="bi bi-pencil me-2 text-warning"></i>Edit Role: {{ $customRole->name }}
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.custom-roles.update', [$school->slug, $customRole]) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label small fw-semibold">Role Name *</label>
                <input type="text" name="name" class="form-control form-control-sm"
                       value="{{ old('name', $customRole->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Description</label>
                <input type="text" name="description" class="form-control form-control-sm"
                       value="{{ old('description', $customRole->description) }}">
            </div>
            <div class="mb-4">
                <label class="form-label small fw-semibold">Permissions</label>
                <div class="border rounded p-2" style="max-height:320px;overflow-y:auto">
                    @php
                        $assigned = old('permissions', $customRole->permissionList());
                        $grouped  = $allPermissions->groupBy(fn($p) => explode(' ', $p, 2)[1] ?? 'other');
                    @endphp
                    @foreach($grouped as $group => $perms)
                    <div class="mb-2">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="text-muted fw-semibold" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:1px">
                                {{ $group }}
                            </span>
                            <button type="button" class="btn btn-xs btn-outline-secondary"
                                    onclick="toggleGroup('{{ Str::slug($group) }}')">
                                toggle all
                            </button>
                        </div>
                        <div id="group-{{ Str::slug($group) }}">
                            @foreach($perms as $perm)
                            <div class="form-check form-check-sm">
                                <input class="form-check-input group-{{ Str::slug($group) }}"
                                       type="checkbox" name="permissions[]"
                                       value="{{ $perm }}"
                                       id="ep_{{ Str::slug($perm) }}"
                                       {{ in_array($perm, $assigned) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="ep_{{ Str::slug($perm) }}">
                                    {{ $perm }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning btn-sm">
                    <i class="bi bi-check-lg me-1"></i>Update Role
                </button>
                <a href="{{ route('school.custom-roles.index', $school->slug) }}"
                   class="btn btn-outline-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection

@push('scripts')
<script>
function toggleGroup(group) {
    const boxes = document.querySelectorAll(`.group-${group}`);
    const allChecked = [...boxes].every(b => b.checked);
    boxes.forEach(b => b.checked = !allChecked);
}
</script>
@endpush
