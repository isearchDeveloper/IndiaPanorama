@section('title', 'Manage Activity Categories')
@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0 fw-bold"><i class="fas fa-layer-group me-2 text-primary"></i>Manage Activity Categories</h2>
            <small class="text-muted">Powers the "Activities By Category" section on the Activities page — each category links to a page listing its own activities.</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.tourist-activities.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Manage Activity
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTacatModal">
                <i class="fas fa-plus me-2"></i>Add Category
            </button>
        </div>
    </div>

    <div class="mb-3">
        <div class="position-relative" style="max-width:320px;">
            <i class="fas fa-search position-absolute" style="left:12px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:.85rem;"></i>
            <input type="text" id="tacatSearch" class="form-control" style="padding-left:32px;"
                   placeholder="Search category..." value="{{ request('search') }}">
        </div>
    </div>

    <div id="tacatTableWrapper">
        @include('admin.tourist-activities.categories._table')
    </div>

</div>
@endsection

@section('modal')
{{-- ══════════════ ADD CATEGORY MODAL ══════════════ --}}
<div class="modal fade" id="addTacatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Activity Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addTacatForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Adventure" required>
                        <div class="text-danger small mt-1 name-error" style="display:none"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image <span class="text-danger">*</span> <small class="text-muted">(.webp only)</small></label>
                        <input type="file" name="image" class="form-control" accept=".webp,.jpg,.jpeg,.png,image/*" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image Alt</label>
                        <input type="text" name="image_alt" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="add-tacat-submit-btn">
                        <i class="fas fa-save me-1"></i>Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ EDIT CATEGORY MODAL ══════════════ --}}
<div class="modal fade" id="editTacatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Activity Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTacatForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit-tacat-name" class="form-control" required>
                        <div class="text-danger small mt-1 edit-name-error" style="display:none"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image <small class="text-muted">(leave blank to keep current)</small></label>
                        <input type="file" name="image" class="form-control" accept=".webp,.jpg,.jpeg,.png,image/*">
                        <div id="edit-tacat-image-preview" class="mt-2"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image Alt</label>
                        <input type="text" name="image_alt" id="edit-tacat-image-alt" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="edit-tacat-description" class="form-control" rows="3"></textarea>
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
function tacatShowError(xhr, fallback) {
    if (xhr && xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
        toastr.error(Object.values(xhr.responseJSON.errors)[0][0]);
    } else {
        toastr.error(fallback);
    }
}

$(document).ready(function () {

    let tacatSearchTimer;
    $('#tacatSearch').on('input', function () {
        clearTimeout(tacatSearchTimer);
        let q = $(this).val();
        tacatSearchTimer = setTimeout(function () {
            showAjaxLoader($('#tacatTableWrapper'));
            $.get('{{ route("admin.tourist-activity-categories.index") }}', { search: q, ajax: 1 })
                .done(function (res) {
                    $('#tacatTableWrapper').html(res.html);
                    if (typeof window.initSwitchery === 'function') window.initSwitchery();
                })
                .fail(function () { hideAjaxLoader($('#tacatTableWrapper')); toastr.error('Search failed.'); });
        }, 300);
    });

    // ── Add Category ────────────────────────────────────────────────────
    $('#addTacatModal').on('hidden.bs.modal', function () {
        $('#addTacatForm')[0].reset();
        $('.name-error').hide().text('');
    });

    $('#addTacatForm').on('submit', function (e) {
        e.preventDefault();
        let btn = $('#add-tacat-submit-btn');
        btn.prop('disabled', true);
        $('.name-error').hide().text('');
        let fd = new FormData(this);
        $.ajax({ url: '{{ route("admin.tourist-activity-categories.store") }}', type: 'POST', data: fd, processData: false, contentType: false })
            .done(function () {
                toastr.success('Category created!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('addTacatModal')).hide();
                $('#tacatSearch').trigger('input');
            })
            .fail(function (xhr) {
                btn.prop('disabled', false);
                if (xhr.status === 422 && xhr.responseJSON.errors && xhr.responseJSON.errors.name) {
                    $('.name-error').text(xhr.responseJSON.errors.name[0]).show();
                } else {
                    tacatShowError(xhr, 'Failed to create category.');
                }
            });
    });

    // ── Edit Category ───────────────────────────────────────────────────
    $(document).on('click', '.btn-edit-tacat', function () {
        let id = $(this).data('id');
        $.get('{{ url("admin/tourist-activity-categories") }}/' + id).done(function (d) {
            $('#editTacatForm').attr('data-id', d.id);
            $('#edit-tacat-name').val(d.name || '');
            $('#edit-tacat-image-alt').val(d.image_alt || '');
            $('#edit-tacat-description').val(d.description || '');
            $('#edit-tacat-image-preview').html(d.image
                ? '<img src="' + s3BaseUrl + d.image + '" class="img-fluid rounded" style="max-height:100px">' : '');
            $('.edit-name-error').hide().text('');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editTacatModal')).show();
        }).fail(function () { toastr.error('Failed to load category.'); });
    });

    $('#editTacatForm').on('submit', function (e) {
        e.preventDefault();
        let id = $(this).attr('data-id');
        let fd = new FormData(this);
        $.ajax({ url: '{{ url("admin/tourist-activity-categories") }}/' + id, type: 'POST', data: fd, processData: false, contentType: false })
            .done(function (r) {
                toastr.success(r.message || 'Saved!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('editTacatModal')).hide();
                $('#tacatSearch').trigger('input');
            })
            .fail(function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors && xhr.responseJSON.errors.name) {
                    $('.edit-name-error').text(xhr.responseJSON.errors.name[0]).show();
                } else {
                    tacatShowError(xhr, 'Failed to save category.');
                }
            });
    });

    // ── Status toggle ────────────────────────────────────────────────────
    $(document).on('change', '.tacat-status', function () {
        $.ajax({ url: $(this).data('url'), type: 'POST', data: { _token: '{{ csrf_token() }}' } })
            .done(function () { toastr.success('Status updated'); })
            .fail(function () { toastr.error('Failed to update status.'); });
    });

    // ── Delete ───────────────────────────────────────────────────────────
    $(document).on('click', '.delete-tacat', function () {
        let btn = $(this);
        let url = btn.data('url');
        let row = btn.closest('tr');

        Swal.fire({
            title: 'Are you sure?',
            text: 'Activities in this category will become uncategorized, not deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e3342f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it',
        }).then((result) => {
            if (result.isConfirmed) {
                btn.find('.spinner-border').removeClass('d-none');
                btn.find('.icon').addClass('d-none');
                $.ajax({ url: url, type: 'DELETE', data: { _token: '{{ csrf_token() }}' } })
                    .done(function (res) { if (res.status) { row.remove(); toastr.success('Category deleted.'); } })
                    .fail(function () { toastr.error('Delete failed.'); })
                    .always(function () {
                        btn.find('.spinner-border').addClass('d-none');
                        btn.find('.icon').removeClass('d-none');
                    });
            }
        });
    });

});
</script>
@endsection
