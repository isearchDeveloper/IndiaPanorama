@section('title','Departments')
@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">
                    <i class="fas fa-sitemap me-2"></i>Departments
                </h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                    <i class="fas fa-plus me-2"></i>Add Department
                </button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Department Name</th>
                                    <th>Team Members</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departments as $dept)
                                <tr id="dept-row-{{ $dept->id }}">
                                    <td><strong>{{ $dept->name }}</strong></td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $dept->teams_count }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-primary btn-sm edit-dept"
                                            data-url="{{ route('admin.departments.edit', $dept->id) }}"
                                            data-update-url="{{ route('admin.departments.update', $dept->id) }}">
                                            <i class="fa fa-edit icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-dept"
                                            data-id="{{ $dept->id }}"
                                            data-url="{{ route('admin.departments.destroy', $dept->id) }}">
                                            <i class="fas fa-trash icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No departments added yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modal')
{{-- Add Department Modal --}}
<div class="modal fade" id="addDepartmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#2563eb;">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Department</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addDeptForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Department Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="dept-name" name="name" placeholder="e.g. Sales, Operations" required>
                        <div class="text-danger mt-1 dept-name-error" style="display:none;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="add-dept-btn">
                        <i class="fas fa-save me-1"></i>Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Department Modal --}}
<div class="modal fade" id="editDepartmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#7c3aed;">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Department</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editDeptForm" data-url="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Department Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit-dept-name" name="name" required>
                        <div class="text-danger mt-1 edit-dept-name-error" style="display:none;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="update-dept-btn">
                        <i class="fas fa-save me-1"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {

    // ── Add Department ────────────────────────────────────────────
    $('#addDeptForm').on('submit', function (e) {
        e.preventDefault();
        let btn = $('#add-dept-btn');
        btn.prop('disabled', true);
        $('.dept-name-error').hide().text('');

        $.ajax({
            type: 'POST',
            url: '{{ route('admin.departments.store') }}',
            data: $(this).serialize(),
            success: function (res) {
                if (res.success) {
                    toastr.success('Department added successfully!', 'Success');
                    btn.prop('disabled', false);
                    $('#addDepartmentModal').modal('hide');
                    $('#addDeptForm')[0].reset();
                    setTimeout(() => location.reload(), 1200);
                }
            },
            error: function (xhr) {
                btn.prop('disabled', false);
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        $('.dept-name-error').text(errors.name[0]).show();
                    }
                }
            }
        });
    });

    // ── Open Edit Modal ───────────────────────────────────────────
    $(document).on('click', '.edit-dept', function () {
        let btn = $(this);
        btn.find('.spinner-border').removeClass('d-none');
        btn.find('.icon').addClass('d-none');

        $.ajax({
            url: btn.data('url'),
            type: 'GET',
            success: function (dept) {
                $('#edit-dept-name').val(dept.name);
                $('#editDeptForm').attr('data-url', btn.data('update-url'));
                $('.edit-dept-name-error').hide().text('');
                $('#editDepartmentModal').modal('show');
            },
            error: function () {
                toastr.error('Could not load department.', 'Error');
            },
            complete: function () {
                btn.find('.spinner-border').addClass('d-none');
                btn.find('.icon').removeClass('d-none');
            }
        });
    });

    // ── Update Department ─────────────────────────────────────────
    $('#editDeptForm').on('submit', function (e) {
        e.preventDefault();
        let btn = $('#update-dept-btn');
        btn.prop('disabled', true);
        $('.edit-dept-name-error').hide().text('');

        $.ajax({
            type: 'POST',
            url: $(this).attr('data-url'),
            data: $(this).serialize(),
            success: function (res) {
                if (res.success) {
                    toastr.success('Department updated successfully!', 'Success');
                    btn.prop('disabled', false);
                    $('#editDepartmentModal').modal('hide');
                    setTimeout(() => location.reload(), 1200);
                }
            },
            error: function (xhr) {
                btn.prop('disabled', false);
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        $('.edit-dept-name-error').text(errors.name[0]).show();
                    }
                }
            }
        });
    });

    // ── Delete Department ─────────────────────────────────────────
    $(document).on('click', '.delete-dept', function () {
        let btn = $(this);
        let itemUrl = btn.data('url');
        let row = btn.closest('tr');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This department will be deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e3342f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.find('.spinner-border').removeClass('d-none');
                btn.find('.icon').addClass('d-none');

                $.ajax({
                    type: 'DELETE',
                    url: itemUrl,
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (res) {
                        if (res.success) {
                            row.remove();
                            Swal.fire('Deleted!', 'Department deleted successfully.', 'success');
                        }
                    },
                    error: function (xhr) {
                        let msg = xhr.responseJSON?.message || 'Could not delete this department.';
                        Swal.fire('Cannot Delete', msg, 'error');
                    },
                    complete: function () {
                        btn.find('.spinner-border').addClass('d-none');
                        btn.find('.icon').removeClass('d-none');
                    }
                });
            }
        });
    });

});
</script>
@endsection
