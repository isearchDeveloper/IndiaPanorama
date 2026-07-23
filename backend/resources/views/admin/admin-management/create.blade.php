@extends('layouts.app')
@section('title', 'Create Admin')

@push('style')
<style>
.perm-card { border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; margin-bottom:1rem; }
.perm-card-header { background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:.6rem 1rem; display:flex; align-items:center; justify-content:space-between; }
.perm-card-header .group-label { font-size:.8rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em; color:#475569; }
.perm-card-body { padding:.5rem 1rem; }

/* Module rows (granular) */
.perm-module-row { display:flex; align-items:center; gap:.6rem; padding:.35rem 0; border-bottom:1px dashed #f1f5f9; flex-wrap:wrap; }
.perm-module-row:last-child { border-bottom:none; }
.perm-module-label { font-size:.76rem; font-weight:600; color:#475569; white-space:nowrap; min-width:145px; flex-shrink:0; cursor:pointer; }
.perm-module-label:hover { color:#2563eb; }
.perm-module-chips { display:flex; gap:.3rem; flex-wrap:wrap; }

/* Chips */
.perm-chip { display:inline-flex; align-items:center; gap:.3rem; padding:.25rem .6rem; border-radius:20px; border:1.5px solid #cbd5e1; background:#fff; cursor:pointer; font-size:.75rem; color:#64748b; transition:border-color .15s,background .15s,color .15s; user-select:none; }
.perm-chip input[type=checkbox] { display:none; }
.perm-chip .fa-check { font-size:.6rem; }

/* Action-specific checked colors */
.perm-chip--view.checked    { border-color:#3b82f6; background:#eff6ff; color:#1d4ed8; font-weight:600; }
.perm-chip--create.checked  { border-color:#22c55e; background:#f0fdf4; color:#15803d; font-weight:600; }
.perm-chip--edit.checked    { border-color:#f59e0b; background:#fffbeb; color:#b45309; font-weight:600; }
.perm-chip--delete.checked  { border-color:#ef4444; background:#fef2f2; color:#b91c1c; font-weight:600; }

/* Non-granular chip */
.perm-chip--single.checked { border-color:#3b82f6; background:#eff6ff; color:#1d4ed8; font-weight:600; }

.perm-chip:hover { border-color:#93c5fd; background:#f0f9ff; color:#2563eb; }

/* Toggle buttons */
.toggle-group-btn { font-size:.72rem; padding:.2rem .55rem; border-radius:20px; border:1px solid #cbd5e1; background:#fff; color:#64748b; cursor:pointer; transition:all .15s; }
.toggle-group-btn:hover { border-color:#3b82f6; color:#2563eb; }
.toggle-group-btn.all-checked { border-color:#3b82f6; background:#eff6ff; color:#1d4ed8; }

/* Action legend */
.action-legend { display:flex; gap:.4rem; flex-wrap:wrap; margin-bottom:.75rem; padding:.4rem .6rem; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0; }
.action-legend-item { display:flex; align-items:center; gap:.25rem; font-size:.7rem; color:#64748b; }
.action-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0 fw-bold"><i class="fas fa-user-plus me-2 text-primary"></i>Create Admin</h2>
        </div>
        <a href="{{ route('admin.admin-management.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('admin.admin-management.store') }}" method="POST">
        @csrf
        <div class="row g-4">

            {{-- Left: Admin Details --}}
            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-id-card me-2"></i>Admin Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="e.g. Rahul Sharma" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" placeholder="admin@example.com" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min. 8 characters" required minlength="8">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control"
                                   placeholder="Repeat password" required minlength="8">
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <button type="submit" class="btn btn-primary w-100 fw-semibold">
                            <i class="fas fa-user-plus me-2"></i>Create Admin
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right: Permissions --}}
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-shield-alt me-2 text-success"></i>Access Permissions</h6>
                        <div class="d-flex gap-2">
                            <button type="button" class="toggle-group-btn" onclick="toggleAll(true)">
                                <i class="fas fa-check-double me-1"></i>Select All
                            </button>
                            <button type="button" class="toggle-group-btn" onclick="toggleAll(false)">
                                <i class="fas fa-times me-1"></i>Clear All
                            </button>
                        </div>
                    </div>
                    <div class="card-body" style="max-height:70vh; overflow-y:auto;">

                        {{-- Legend --}}
                        <div class="action-legend">
                            <div class="action-legend-item"><div class="action-dot" style="background:#3b82f6"></div> View</div>
                            <div class="action-legend-item"><div class="action-dot" style="background:#22c55e"></div> Add</div>
                            <div class="action-legend-item"><div class="action-dot" style="background:#f59e0b"></div> Edit</div>
                            <div class="action-legend-item"><div class="action-dot" style="background:#ef4444"></div> Delete</div>
                            <div class="action-legend-item ms-2" style="color:#94a3b8; font-size:.68rem;">
                                <i class="fas fa-info-circle me-1"></i>Click module name to toggle all 4 actions
                            </div>
                        </div>

                        @php
                        $groupIcons = [
                            'Packages'        => 'fas fa-box-open text-warning',
                            'Destinations'    => 'fas fa-map-marker-alt text-danger',
                            'Themes & Content'=> 'fas fa-palette text-info',
                            'Cars'            => 'fas fa-car text-secondary',
                            'Page Settings'   => 'fas fa-cog text-primary',
                            'Content'         => 'fas fa-compass text-success',
                            'Enquiries'       => 'fas fa-envelope text-warning',
                            'System'          => 'fas fa-users-cog text-dark',
                        ];
                        $actionColors = ['view'=>'#3b82f6','create'=>'#22c55e','edit'=>'#f59e0b','delete'=>'#ef4444'];
                        @endphp

                        @foreach($permissions as $group => $groupPerms)
                        @php
                            $moduleGroups = $groupPerms->groupBy('module_key');
                            $groupId = 'grp_' . Str::slug($group, '_');
                        @endphp
                        <div class="perm-card">
                            <div class="perm-card-header">
                                <span class="group-label">
                                    <i class="{{ $groupIcons[$group] ?? 'fas fa-circle text-muted' }} me-1"></i>
                                    {{ $group }}
                                    <span class="badge bg-secondary ms-1 fw-normal" style="font-size:.65rem;">
                                        {{ $groupPerms->where('module_key', '!=', null)->pluck('module_key')->unique()->count() ?: $groupPerms->count() }}
                                    </span>
                                </span>
                                <button type="button" class="toggle-group-btn" data-group="{{ $groupId }}">Select All</button>
                            </div>
                            <div class="perm-card-body">
                                @foreach($moduleGroups as $moduleKey => $modulePerms)
                                    @if($moduleKey)
                                        {{-- Granular module row --}}
                                        <div class="perm-module-row">
                                            <span class="perm-module-label" data-module="{{ $moduleKey }}" title="Click to toggle all">
                                                {{ $modulePerms->first()->module_label }}
                                            </span>
                                            <div class="perm-module-chips">
                                                @foreach($modulePerms as $perm)
                                                @php
                                                    $checked = in_array($perm->name, old('permissions', []));
                                                    $chipId  = 'perm_' . str_replace(['.', '-'], '_', $perm->name);
                                                @endphp
                                                <label class="perm-chip perm-chip--{{ $perm->action }} {{ $checked ? 'checked' : '' }}"
                                                       for="{{ $chipId }}"
                                                       data-group="{{ $groupId }}"
                                                       data-module="{{ $moduleKey }}"
                                                       title="{{ ucfirst($perm->action) }} {{ $modulePerms->first()->module_label }}">
                                                    <input type="checkbox" name="permissions[]"
                                                           value="{{ $perm->name }}"
                                                           id="{{ $chipId }}"
                                                           class="perm-check"
                                                           data-group="{{ $groupId }}"
                                                           data-module="{{ $moduleKey }}"
                                                           {{ $checked ? 'checked' : '' }}>
                                                    <i class="fas fa-check" style="font-size:.6rem;color:{{ $actionColors[$perm->action] ?? '#3b82f6' }};{{ $checked ? '' : 'display:none' }}"></i>
                                                    {{ $perm->label }}
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        {{-- Non-granular chips --}}
                                        <div style="display:flex; flex-wrap:wrap; gap:.4rem; padding:.35rem 0;">
                                        @foreach($modulePerms as $perm)
                                        @php
                                            $checked = in_array($perm->name, old('permissions', []));
                                            $chipId  = 'perm_' . str_replace(['.', '-'], '_', $perm->name);
                                        @endphp
                                        <label class="perm-chip perm-chip--single {{ $checked ? 'checked' : '' }}"
                                               for="{{ $chipId }}"
                                               data-group="{{ $groupId }}">
                                            <input type="checkbox" name="permissions[]"
                                                   value="{{ $perm->name }}"
                                                   id="{{ $chipId }}"
                                                   class="perm-check"
                                                   data-group="{{ $groupId }}"
                                                   {{ $checked ? 'checked' : '' }}>
                                            <i class="fas fa-check" style="font-size:.6rem;{{ $checked ? '' : 'display:none' }}"></i>
                                            {{ $perm->label }}
                                        </label>
                                        @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
// Chip click
document.querySelectorAll('.perm-chip').forEach(function(chip) {
    chip.addEventListener('click', function() {
        const cb   = chip.querySelector('input[type=checkbox]');
        const icon = chip.querySelector('.fa-check');
        cb.checked = !cb.checked;
        chip.classList.toggle('checked', cb.checked);
        if (icon) icon.style.display = cb.checked ? '' : 'none';
        updateGroupBtn(chip.dataset.group);
    });
});

// Module label click — toggle all 4 actions in that module
document.querySelectorAll('.perm-module-label').forEach(function(label) {
    label.addEventListener('click', function() {
        const module = label.dataset.module;
        const chips  = document.querySelectorAll('.perm-chip[data-module="' + module + '"]');
        const allChecked = [...chips].every(c => c.classList.contains('checked'));
        chips.forEach(function(chip) {
            const cb   = chip.querySelector('input[type=checkbox]');
            const icon = chip.querySelector('.fa-check');
            cb.checked = !allChecked;
            chip.classList.toggle('checked', !allChecked);
            if (icon) icon.style.display = !allChecked ? '' : 'none';
        });
        if (chips.length) updateGroupBtn(chips[0].dataset.group);
    });
});

// Group Select All button
document.querySelectorAll('.toggle-group-btn[data-group]').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const group  = btn.dataset.group;
        const chips  = document.querySelectorAll('.perm-chip[data-group="' + group + '"]');
        const allChecked = [...chips].every(c => c.classList.contains('checked'));
        chips.forEach(function(chip) {
            const cb   = chip.querySelector('input[type=checkbox]');
            const icon = chip.querySelector('.fa-check');
            cb.checked = !allChecked;
            chip.classList.toggle('checked', !allChecked);
            if (icon) icon.style.display = !allChecked ? '' : 'none';
        });
        btn.textContent = allChecked ? 'Select All' : 'Clear All';
        btn.classList.toggle('all-checked', !allChecked);
    });
});

function updateGroupBtn(group) {
    const chips = document.querySelectorAll('.perm-chip[data-group="' + group + '"]');
    const btn   = document.querySelector('.toggle-group-btn[data-group="' + group + '"]');
    if (!btn || !chips.length) return;
    const allChecked = [...chips].every(c => c.classList.contains('checked'));
    btn.textContent = allChecked ? 'Clear All' : 'Select All';
    btn.classList.toggle('all-checked', allChecked);
}

function toggleAll(state) {
    document.querySelectorAll('.perm-chip').forEach(function(chip) {
        const cb   = chip.querySelector('input[type=checkbox]');
        const icon = chip.querySelector('.fa-check');
        cb.checked = state;
        chip.classList.toggle('checked', state);
        if (icon) icon.style.display = state ? '' : 'none';
    });
    document.querySelectorAll('.toggle-group-btn[data-group]').forEach(function(btn) {
        btn.textContent = state ? 'Clear All' : 'Select All';
        btn.classList.toggle('all-checked', state);
    });
}

// Init group buttons on load
document.querySelectorAll('.toggle-group-btn[data-group]').forEach(function(btn) {
    updateGroupBtn(btn.dataset.group);
});
</script>
@endsection
