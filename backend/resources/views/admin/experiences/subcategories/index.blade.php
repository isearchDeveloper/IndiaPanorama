@section('title', 'Manage Experience Subcategories')
@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0 fw-bold"><i class="fas fa-tags me-2 text-primary"></i>Manage Experience Subcategories</h2>
            <small class="text-muted">Types of experience within a category (e.g. Waterfalls Tours, Beaches, Tiger Safaris).</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.experience-categories.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Categories
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubcategoryModal">
                <i class="fas fa-plus me-2"></i>Add Subcategory
            </button>
        </div>
    </div>

    <div class="mb-3">
        <select id="esFilterCategory" class="form-select" style="max-width:320px;">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ (string)$categoryId === (string)$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>

    <div id="esTableWrapper">
        @include('admin.experiences.subcategories._table')
    </div>

</div>
@endsection

@section('modal')
{{-- ══════════════ ADD SUBCATEGORY MODAL ══════════════ --}}
<div class="modal fade" id="addSubcategoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Subcategory</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSubcategoryForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subcategory Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <x-media-picker name="image" picker-id="as_image" label="Banner Image" folder="experiences/subcategories" />
                        <div class="text-danger small mt-1 add-subcategory-error" style="display:none"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Banner Image Alt</label>
                        <input type="text" name="image_alt" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="as-description" class="form-control tinymce" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Popular Tag <small class="text-muted">(e.g. Jog Falls | Dudhsagar Falls)</small></label>
                        <input type="text" name="popular_tag" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Add Subcategory</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ EDIT SUBCATEGORY MODAL ══════════════ --}}
<div class="modal fade" id="editSubcategoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Subcategory</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSubcategoryForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category_id" id="esub-category" class="form-select" required>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subcategory Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="esub-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <x-media-picker name="image" picker-id="esub_image" label="Banner Image" folder="experiences/subcategories" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Banner Image Alt</label>
                        <input type="text" name="image_alt" id="esub-image-alt" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="esub-description" class="form-control tinymce" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Popular Tag</label>
                        <input type="text" name="popular_tag" id="esub-popular-tag" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {

    function reload(categoryId) {
        showAjaxLoader($('#esTableWrapper'));
        $.get('{{ route("admin.experience-subcategories.index") }}', { category_id: categoryId, ajax: 1 })
            .done(function (res) {
                $('#esTableWrapper').html(res.html);
                if (typeof window.initSwitchery === 'function') window.initSwitchery();
            })
            .fail(function () { hideAjaxLoader($('#esTableWrapper')); toastr.error('Failed to load.'); });
    }

    $('#esFilterCategory').on('change', function () { reload($(this).val()); });

    $('#addSubcategoryForm').on('submit', function (e) {
        e.preventDefault();
        $('.add-subcategory-error').hide().text('');
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        let fd = new FormData(this);
        $.ajax({
            url: '{{ route("admin.experience-subcategories.store") }}', type: 'POST', data: fd,
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Subcategory added.');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('addSubcategoryModal')).hide();
            document.getElementById('addSubcategoryForm').reset();
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('as_image', '', null);
            }
            reload($('#esFilterCategory').val());
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON.errors) {
                $('.add-subcategory-error').text(Object.values(xhr.responseJSON.errors)[0][0]).show();
            } else {
                toastr.error('Failed to add subcategory.');
            }
        });
    });

    $(document).on('click', '.btn-edit-subcategory', function () {
        let id = $(this).data('id');
        $.get('{{ url("admin/experience-subcategories") }}/' + id).done(function (d) {
            $('#editSubcategoryForm').attr('data-id', d.id);
            $('#esub-category').val(d.category_id);
            $('#esub-name').val(d.name || '');
            $('#esub-image-alt').val(d.image_alt || '');
            let description = d.description || '';
            $('#esub-description').val(description);
            $('#esub-popular-tag').val(d.popular_tag || '');
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('esub_image', d.image, d.image ? (s3BaseUrl + d.image) : null);
            }
            $('#editSubcategoryModal').one('shown.bs.modal', function () {
                if (typeof tinymce !== 'undefined' && tinymce.get('esub-description')) {
                    tinymce.get('esub-description').setContent(description);
                }
            });
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editSubcategoryModal')).show();
        }).fail(function () { toastr.error('Failed to load subcategory.'); });
    });

    $('#editSubcategoryForm').on('submit', function (e) {
        e.preventDefault();
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        let id = $(this).attr('data-id');
        let fd = new FormData(this);
        fd.append('_method', 'PUT');
        $.ajax({
            url: '{{ url("admin/experience-subcategories") }}/' + id, type: 'POST', data: fd,
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Subcategory updated.');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editSubcategoryModal')).hide();
            reload($('#esFilterCategory').val());
        }).fail(function () { toastr.error('Failed to update subcategory.'); });
    });

    $(document).on('change', '.subcategory-status', function () {
        $.ajax({
            url: $(this).data('url'), type: 'POST', data: { _token: '{{ csrf_token() }}' },
            success: function () { toastr.success('Status updated'); },
            error: function () { toastr.error('Failed to update status.'); }
        });
    });

    $(document).on('click', '.delete-subcategory', function () {
        let btn = $(this);
        let row = btn.closest('tr');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will permanently delete this subcategory and all its experiences!',
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#e3342f', cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: btn.data('url'), type: 'DELETE', data: { _token: '{{ csrf_token() }}' },
                    success: function (res) { if (res.status) { row.remove(); toastr.success('Subcategory deleted.'); } },
                    error: function () { toastr.error('Failed to delete subcategory.'); }
                });
            }
        });
    });

});
</script>
@endsection
