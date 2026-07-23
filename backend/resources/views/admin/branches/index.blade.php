@section('title','Branches Address')
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">
                    <i class="fas fa-building me-2"></i>Branches Address
                </h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBranchModal">
                    <i class="fas fa-plus me-2"></i>Add Branch
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Phone(s)</th>
                                    <th style="width:110px">Status</th>
                                    <th style="width:120px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($branches as $b)
                                <tr id="branch-row-{{ $b->id }}">
                                    <td class="fw-medium">{{ $b->name }}</td>
                                    <td class="small">{{ $b->address }}</td>
                                    <td class="small">{{ implode(', ', $b->phones ?? []) }}</td>
                                    <td>
                                        <input type="checkbox"
                                               class="js-switch branch-status"
                                               data-id="{{ $b->id }}"
                                               data-url="{{ route('admin.branches.update', $b->id) }}"
                                               {{ $b->is_active ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-primary btn-sm edit-branch"
                                                data-id="{{ $b->id }}"
                                                data-url="{{ route('admin.branches.show', $b->id) }}">
                                            <i class="fa fa-edit icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-branch"
                                                data-id="{{ $b->id }}"
                                                data-url="{{ route('admin.branches.destroy', $b->id) }}">
                                            <i class="fas fa-trash icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none"></span>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No branches added yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @include('admin.common.pagination', ['paginator' => $branches])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modal')

{{-- ── Add Branch Modal ───────────────────────────────────── --}}
<div class="modal fade" id="addBranchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Branch</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addBranchForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Branch Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Hyderabad" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Full office address"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number(s)</label>
                        <div id="add-phones-rows"></div>
                        <button type="button" class="btn btn-sm btn-outline-success mt-1" id="add-phone-row-add">
                            <i class="fas fa-plus me-1"></i>Add Phone
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Add Branch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Edit Branch Modal ───────────────────────────────────── --}}
<div class="modal fade" id="editBranchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Branch</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editBranchForm">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" id="edit-branch-id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Branch Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit-branch-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" id="edit-branch-address" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number(s)</label>
                        <div id="edit-phones-rows"></div>
                        <button type="button" class="btn btn-sm btn-outline-success mt-1" id="edit-phone-row-add">
                            <i class="fas fa-plus me-1"></i>Add Phone
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function phoneRowHtml(value) {
    value = (value || '').replace(/"/g, '&quot;');
    return '<div class="input-group mb-2 phone-row">' +
        '<input type="text" name="phones[]" class="form-control" value="' + value + '" placeholder="e.g. +91 8925845194">' +
        '<button type="button" class="btn btn-outline-danger rm-phone-row"><i class="fas fa-trash"></i></button>' +
        '</div>';
}

$(document).ready(function () {

    // ── Add Branch ──────────────────────────────────────────
    $('#add-phone-row-add').on('click', function () {
        $('#add-phones-rows').append(phoneRowHtml(''));
    });
    $('#addBranchModal').on('shown.bs.modal', function () {
        if (!$('#add-phones-rows .phone-row').length) $('#add-phones-rows').append(phoneRowHtml(''));
    });
    $('#addBranchModal').on('hidden.bs.modal', function () {
        $('#addBranchForm')[0].reset();
        $('#add-phones-rows').empty();
    });
    $(document).on('click', '#add-phones-rows .rm-phone-row', function () {
        $(this).closest('.phone-row').remove();
    });

    $('#addBranchForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("admin.branches.store") }}', type: 'POST', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Branch added.');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('addBranchModal')).hide();
            setTimeout(() => location.reload(), 800);
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                toastr.error(Object.values(xhr.responseJSON.errors)[0][0]);
            } else {
                toastr.error('Failed to add branch.');
            }
        });
    });

    // ── Edit Branch ──────────────────────────────────────────
    $('#edit-phone-row-add').on('click', function () {
        $('#edit-phones-rows').append(phoneRowHtml(''));
    });
    $(document).on('click', '#edit-phones-rows .rm-phone-row', function () {
        $(this).closest('.phone-row').remove();
    });

    $(document).on('click', '.edit-branch', function () {
        let btn = $(this);
        btn.find('.spinner-border').removeClass('d-none');
        btn.find('.icon').addClass('d-none');
        $.ajax({
            url: btn.data('url'), type: 'GET',
        }).done(function (d) {
            $('#edit-branch-id').val(d.id);
            $('#edit-branch-name').val(d.name || '');
            $('#edit-branch-address').val(d.address || '');
            $('#edit-phones-rows').empty();
            let phones = d.phones || [];
            if (phones.length) {
                phones.forEach(p => $('#edit-phones-rows').append(phoneRowHtml(p)));
            } else {
                $('#edit-phones-rows').append(phoneRowHtml(''));
            }
            $('#editBranchForm').attr('data-url', '{{ url("admin/branches") }}/' + d.id);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editBranchModal')).show();
        }).fail(function () {
            toastr.error('Failed to load branch.');
        }).always(function () {
            btn.find('.spinner-border').addClass('d-none');
            btn.find('.icon').removeClass('d-none');
        });
    });

    $('#editBranchForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('data-url'), type: 'POST', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Branch updated.');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editBranchModal')).hide();
            setTimeout(() => location.reload(), 800);
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                toastr.error(Object.values(xhr.responseJSON.errors)[0][0]);
            } else {
                toastr.error('Failed to update branch.');
            }
        });
    });

    // ── Status toggle ──────────────────────────────────────────
    $(document).on('change', '.branch-status', function () {
        $.ajax({
            url: $(this).data('url'), type: 'PUT',
            data: { status: $(this).prop('checked') ? 1 : 0, _token: '{{ csrf_token() }}' },
            success: function () { toastr.success('Status updated'); },
            error: function () { toastr.error('Failed to update status.'); }
        });
    });

    // ── Delete ───────────────────────────────────────────────
    $(document).on('click', '.delete-branch', function () {
        let btn = $(this);
        let row = btn.closest('tr');
        Swal.fire({
            title: 'Delete this branch?',
            text: 'This branch will be permanently removed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e3342f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete',
        }).then((result) => {
            if (result.isConfirmed) {
                btn.find('.spinner-border').removeClass('d-none');
                btn.find('.icon').addClass('d-none');
                $.ajax({
                    url: btn.data('url'), type: 'DELETE', data: { _token: '{{ csrf_token() }}' },
                }).done(function (res) {
                    if (res.success) { row.remove(); toastr.success('Branch deleted.'); }
                }).fail(function () {
                    toastr.error('Delete failed.');
                }).always(function () {
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                });
            }
        });
    });

});
</script>
@endsection
