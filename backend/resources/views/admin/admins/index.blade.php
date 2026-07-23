@extends('layouts.app')
@section('title', 'Admin Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Admin Management</h1>
        <p class="page-title-sub">Manage administrator accounts and their access</p>
    </div>
    <div class="page-actions">
        @can('add-admin-management')
        <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Admin
        </a>
        @endcan
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4">
    <i class="fas fa-circle-check me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="table-wrapper">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:48px">#</th>
                    <th>Admin</th>
                    <th>Role</th>
                    <th>Permissions</th>
                    <th style="width:100px">Status</th>
                    <th style="width:80px" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $admin)
                <tr>
                    <td class="text-muted" style="font-size:.8rem">{{ $admins->firstItem() + $loop->index }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:34px;height:34px;border-radius:50%;background:var(--brand-light);color:var(--brand);display:flex;align-items:center;justify-content:center;font-size:.78rem;font-weight:700;flex-shrink:0">
                                {{ strtoupper(substr($admin->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="cell-primary">{{ $admin->name }}</div>
                                <div class="cell-secondary">{{ $admin->email }}</div>
                                @if($admin->phone)
                                <div class="cell-secondary">{{ $admin->phone }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        @foreach($admin->roles as $role)
                            <span class="badge badge-brand">{{ $role->name }}</span>
                        @endforeach
                        @if($admin->roles->isEmpty())
                            <span class="badge badge-gray">No role</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-purple">
                            <i class="fas fa-key"></i>
                            {{ $admin->roles->first()?->permissions->count() ?? 0 }} permissions
                        </span>
                    </td>
                    <td>
                        @can('edit-admin-management')
                        <input id="admin_status_{{ $admin->id }}" type="checkbox"
                            data-id="{{ $admin->id }}"
                            data-url="{{ route('admin.admins.toggle-status', $admin) }}"
                            class="js-switch admin-status"
                            {{ $admin->status ? 'checked' : '' }}>
                        @else
                        <span class="badge {{ $admin->status ? 'badge-success' : 'badge-gray' }}">
                            {{ $admin->status ? 'Active' : 'Inactive' }}
                        </span>
                        @endcan
                    </td>
                    <td class="text-center">
                        @canany(['edit-admin-management','delete-admin-management'])
                        <div class="dropdown">
                            <button class="action-menu-btn" data-bs-toggle="dropdown" data-bs-strategy="fixed" aria-label="Actions">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                            <ul class="dropdown-menu action-dropdown-menu dropdown-menu-end">
                                @can('edit-admin-management')
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.admins.edit', $admin) }}">
                                        <i class="fas fa-pen-to-square text-warning"></i> Edit Admin
                                    </a>
                                </li>
                                @endcan
                                @can('delete-admin-management')
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}"
                                          class="delete-admin-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </li>
                                @endcan
                            </ul>
                        </div>
                        @endcanany
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="table-empty">
                            <div class="table-empty-icon"><i class="fas fa-users"></i></div>
                            <div class="table-empty-title">No admins found</div>
                            <div class="table-empty-sub">Add your first admin to get started</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($admins->lastPage() > 1)
    <div class="card-footer d-flex align-items-center justify-content-between">
        <span class="text-muted" style="font-size:.82rem">
            Showing {{ $admins->firstItem() }}–{{ $admins->lastItem() }} of {{ $admins->total() }} admins
        </span>
        @include('admin.common.pagination', ['paginator' => $admins])
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
$(document).on('submit', '.delete-admin-form', function (e) {
    e.preventDefault();
    var form = this;
    Swal.fire({
        title: 'Are you sure?',
        text: 'Permanently delete this admin?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e3342f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it',
    }).then(function (result) {
        if (result.isConfirmed) form.submit();
    });
});

$(document).on('change', '.admin-status', function () {
    const url = $(this).data('url');
    $.ajax({
        type: 'POST',
        url: url,
        data: { '_token': $('meta[name="csrf-token"]').attr('content') },
        dataType: 'json',
        success: function () {
            toastr.success('Status updated successfully.');
        },
        error: function () {
            toastr.error('Failed to update status.');
        }
    });
});
</script>
@endsection
