@extends('school.layouts.app')
@section('title', 'Custom Roles')
@section('breadcrumb')
    <li class="breadcrumb-item active">Custom Roles</li>
@endsection

@section('content')
<div class="row g-3">

    {{-- Create Form --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-plus-circle me-2 text-primary"></i>Create Custom Role
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('school.custom-roles.store', $school->slug) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Role Name *</label>
                        <input type="text" name="name" class="form-control form-control-sm"
                               placeholder="e.g. Vice Principal" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Description</label>
                        <input type="text" name="description" class="form-control form-control-sm"
                               placeholder="Optional description" value="{{ old('description') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Permissions</label>
                        <div class="border rounded p-2" style="max-height:280px;overflow-y:auto">
                            @php
                                $grouped = $allPermissions->groupBy(fn($p) => explode(' ', $p, 2)[1] ?? 'other');
                            @endphp
                            @foreach($grouped as $group => $perms)
                            <div class="mb-2">
                                <div class="text-muted fw-semibold" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:1px">
                                    {{ $group }}
                                </div>
                                @foreach($perms as $perm)
                                <div class="form-check form-check-sm">
                                    <input class="form-check-input" type="checkbox"
                                           name="permissions[]" value="{{ $perm }}"
                                           id="perm_{{ Str::slug($perm) }}"
                                           {{ in_array($perm, old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="perm_{{ Str::slug($perm) }}">
                                        {{ $perm }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-plus-lg me-1"></i>Create Role
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Roles List --}}
    <div class="col-lg-8">
        @forelse($roles as $role)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-semibold">{{ $role->name }}</span>
                    @if($role->description)
                        <small class="text-muted ms-2">— {{ $role->description }}</small>
                    @endif
                </div>
                <div class="d-flex gap-1 align-items-center">
                    <span class="badge bg-primary bg-opacity-10 text-primary">
                        {{ $role->users_count }} users
                    </span>
                    <a href="{{ route('school.custom-roles.edit', [$school->slug, $role]) }}"
                       class="btn btn-xs btn-outline-warning">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form method="POST"
                          action="{{ route('school.custom-roles.destroy', [$school->slug, $role]) }}"
                          onsubmit="return confirm('Delete this role?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
            <div class="card-body py-2">
                {{-- Permissions --}}
                <div class="mb-2">
                    @forelse($role->permissions as $p)
                        <span class="badge bg-secondary bg-opacity-10 text-secondary me-1 mb-1">{{ $p->permission }}</span>
                    @empty
                        <span class="text-muted small">No permissions assigned</span>
                    @endforelse
                </div>

                {{-- Assign User --}}
                <!-- <form method="POST"
                      action="{{ route('school.custom-roles.assign-user', [$school->slug, $role]) }}"
                      class="d-flex gap-2 mt-2">
                    @csrf
                    <select name="user_id" class="form-select form-select-sm" style="max-width:220px" required>
                        <option value="">Assign user...</option>
                        @foreach(\App\Models\User::where('school_id', $school->id)->get() as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-person-plus"></i> Assign
                    </button>
                </form> -->

                {{-- Assigned Users --}}
                @if($role->users->count())
                <div class="mt-2 d-flex flex-wrap gap-1">
                    @foreach($role->users as $u)
                    <span class="badge bg-info bg-opacity-10 text-info d-flex align-items-center gap-1">
                        {{ $u->name }}
                        <form method="POST"
                              action="{{ route('school.custom-roles.remove-user', [$school->slug, $role, $u]) }}"
                              class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn p-0 border-0 text-danger" style="font-size:0.7rem"
                                    title="Remove">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                        </form>
                    </span>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-shield-plus fs-1 mb-3 d-block opacity-25"></i>
                No custom roles yet. Create one using the form.
            </div>
        </div>
        @endforelse
    </div>
</div>
<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
