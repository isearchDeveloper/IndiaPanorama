@extends('layouts.app')
@section('title', 'Admin Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0"><i class="fas fa-users-cog me-2"></i>Admin Management</h2>
            <a href="{{ route('admin.admin-management.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>Add New Admin
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
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
                        @forelse($admins as $admin)
                        <tr>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>
                                @if($admin->is_super_admin)
                                    <span class="badge bg-danger">Super Admin</span>
                                @else
                                    <span class="badge bg-secondary">Admin</span>
                                @endif
                            </td>
                            <td>
                                @if($admin->is_super_admin)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge {{ $admin->is_active ? 'bg-success' : 'bg-warning text-dark' }}"
                                          id="status-badge-{{ $admin->id }}">
                                        {{ $admin->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if(!$admin->is_super_admin)
                                <a href="{{ route('admin.admin-management.edit', $admin->id) }}"
                                   class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm {{ $admin->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} me-1"
                                        onclick="toggleStatus({{ $admin->id }}, this)"
                                        title="{{ $admin->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="fas {{ $admin->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                </button>
                                <form action="{{ route('admin.admin-management.destroy', $admin->id) }}"
                                      method="POST" class="d-inline" onsubmit="return confirmDelete(event)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No admins found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleStatus(id, btn) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Change this admin\'s status?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, change it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('{{ url('admin/admin-management') }}/' + id + '/toggle-status', {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                if (res.success) {
                    const badge = document.getElementById('status-badge-' + id);
                    if (res.is_active) {
                        badge.className = 'badge bg-success';
                        badge.textContent = 'Active';
                        btn.className = 'btn btn-sm btn-outline-warning me-1';
                        btn.title = 'Deactivate';
                        btn.querySelector('i').className = 'fas fa-ban';
                    } else {
                        badge.className = 'badge bg-warning text-dark';
                        badge.textContent = 'Inactive';
                        btn.className = 'btn btn-sm btn-outline-success me-1';
                        btn.title = 'Activate';
                        btn.querySelector('i').className = 'fas fa-check';
                    }
                    toastr.success('Status updated.');
                }
            }).fail(function(xhr) {
                toastr.error(xhr.responseJSON?.error || 'Something went wrong.');
            });
        }
    });
}

function confirmDelete(e) {
    e.preventDefault();
    const form = e.target;
    Swal.fire({
        title: 'Delete Admin?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete!'
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}
</script>
@endsection
