@extends($layout ?? 'admin.layouts.app')
@section('title', $school->name . ' — Users')

@section('content')

<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="mb-0 fw-bold"><i class="bi bi-people me-2 text-primary"></i>{{ $school->name }}</h5>
        <small class="text-muted">Users &nbsp;•&nbsp; <code>{{ $school->slug }}</code></small>
    </div>
    <span class="ms-auto badge bg-{{ $school->is_active ? 'success' : 'danger' }} fs-6">
        {{ $school->is_active ? 'Active' : 'Inactive' }}
    </span>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body p-2 p-md-3">
        <form method="GET" class="d-flex flex-wrap gap-2 align-items-center">
            <select name="role" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                <option value="">All Roles</option>
                @foreach($roles as $r)
                    <option value="{{ $r->name }}" {{ request('role') == $r->name ? 'selected' : '' }}>
                        {{ ucfirst($r->name) }}
                    </option>
                @endforeach
            </select>
            <div class="input-group input-group-sm" style="width:220px">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control border-start-0" placeholder="Search...">
            </div>
            @if(request('search') || request('role'))
                <a href="{{ route('admin.schools.users', $school) }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            @endif
        </form>
    </div>
</div>

{{-- Users Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center
                                            justify-content-center fw-bold text-primary flex-shrink-0"
                                     style="width:32px;height:32px;font-size:0.8rem">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="fw-semibold small">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="small text-muted">{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-info bg-opacity-10 text-info">
                                {{ $user->getRoleNames()->first() ?? '—' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.users.status', $user) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-xs btn-outline-{{ $user->is_active ? 'danger' : 'success' }}"
                                        title="{{ $user->is_active ? 'Disable' : 'Enable' }}">
                                    <i class="bi bi-{{ $user->is_active ? 'x-circle' : 'check-circle' }}"></i>
                                    {{ $user->is_active ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No users found for this school</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white border-0">{{ $users->links() }}</div>
    @endif
</div>

<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
