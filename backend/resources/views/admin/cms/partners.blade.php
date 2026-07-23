@section('title','Partners')
@extends('layouts.app')

@push('style')
<style>
/* upload progress bar */
#uploadProgress { display: none; }
#uploadBar { transition: width .3s; }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">
                    <i class="fas fa-handshake me-2"></i>Partners
                </h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPartnerModal">
                    <i class="fas fa-plus me-2"></i>Add Partner
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
                                    <th style="width:120px">Logo</th>
                                    <th>Alt Tag</th>
                                    <th style="width:110px">Status</th>
                                    <th style="width:120px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($partners as $p)
                                <tr id="partner-row-{{ $p->id }}">
                                    <td>
                                        <img src="{{ storage_link($p->image) }}" alt="{{ $p->alt }}"
                                             style="height:40px; max-width:100px; object-fit:contain;">
                                    </td>
                                    <td>{{ $p->alt ?: '—' }}</td>
                                    <td>
                                        <input type="checkbox"
                                               class="js-switch partner-status"
                                               data-id="{{ $p->id }}"
                                               data-url="{{ route('admin.partners.update', $p->id) }}"
                                               {{ $p->is_active ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-primary btn-sm edit-partner"
                                                data-id="{{ $p->id }}"
                                                data-url="{{ route('admin.partners.show', $p->id) }}">
                                            <i class="fa fa-edit icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-partner"
                                                data-id="{{ $p->id }}"
                                                data-url="{{ route('admin.partners.destroy', $p->id) }}">
                                            <i class="fas fa-trash icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none"></span>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No partners added yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @include('admin.common.pagination', ['paginator' => $partners])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modal')

{{-- ── Add Partner Modal (multi-upload) ───────────────────── --}}
<div class="modal fade" id="addPartnerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Partner Logos</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <x-media-gallery-picker name="gallery_images" picker-id="partner_add" label="Logos" folder="partners" />

                {{-- Upload progress --}}
                <div id="uploadProgress" class="mt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted" id="uploadStatus">Uploading…</small>
                        <small class="text-muted" id="uploadCount"></small>
                    </div>
                    <div class="progress" style="height:6px;">
                        <div id="uploadBar" class="progress-bar bg-primary" style="width:0%"></div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="addPartnerSaveBtn" disabled>
                    <i class="fas fa-save me-1"></i>
                    <span id="addPartnerSaveLabel">Save</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Edit Partner Modal ───────────────────────────────────── --}}
<div class="modal fade" id="editPartnerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Partner</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPartnerForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-partner-id">
                <div class="modal-body">
                    <div class="mb-3">
                        <x-media-picker name="image" picker-id="partner_image_edit" label="Logo" folder="partners" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alt Tag</label>
                        <input type="text" class="form-control" name="alt" id="edit-alt"
                               placeholder="e.g. USTOA Partner Logo">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="edit-submit-btn">
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

    // ════════════════════════════════════════
    //  MULTI-UPLOAD — state (backed by the shared media-gallery-picker component)
    // ════════════════════════════════════════
    const $pickerBlock = $('.media-gallery-picker[data-field-name="partner_add"]');
    const $grid       = $pickerBlock.find('.mgp-grid');
    const $saveBtn    = $('#addPartnerSaveBtn');
    const $saveLabel  = $('#addPartnerSaveLabel');
    const $progress   = $('#uploadProgress');
    const $bar        = $('#uploadBar');
    const $status     = $('#uploadStatus');
    const $countLabel = $('#uploadCount');

    function updateSaveBtn() {
        let count = $grid.find('.mgp-item').length;
        if (count === 0) {
            $saveBtn.prop('disabled', true);
            $saveLabel.text('Save');
        } else {
            $saveBtn.prop('disabled', false);
            $saveLabel.text('Save ' + count + ' image' + (count > 1 ? 's' : ''));
        }
    }

    // Keep the Save button's enabled state / label in sync with the picker's
    // staging grid (items are added/removed by the shared component's own JS).
    if ($grid.length) {
        new MutationObserver(updateSaveBtn).observe($grid[0], { childList: true });
    }
    updateSaveBtn();

    // ── Save: create one Partner row per staged image, sequentially ──────
    $saveBtn.on('click', function () {
        let items = [];
        $grid.find('.mgp-item').each(function () {
            items.push({
                path: $(this).data('path'),
                alt: $(this).find('.mgp-alt').val() || ''
            });
        });
        if (!items.length) return;

        $saveBtn.prop('disabled', true);
        $progress.show();
        let total = items.length;
        let done  = 0;
        let failed = 0;

        function uploadNext(i) {
            if (i >= items.length) {
                $bar.css('width', '100%');
                let msg = done + ' added';
                if (failed) msg += ', ' + failed + ' failed';
                $status.text(msg);
                toastr.success(done + ' partner logo' + (done > 1 ? 's' : '') + ' added!');
                window.resetMediaGalleryPicker('partner_add');
                setTimeout(function () {
                    $('#addPartnerModal').modal('hide');
                    location.reload();
                }, 1000);
                return;
            }

            let entry = items[i];
            $status.text('Saving ' + (i + 1) + ' of ' + total + '…');
            $countLabel.text(Math.round(((i) / total) * 100) + '%');
            $bar.css('width', Math.round(((i) / total) * 100) + '%');

            $.ajax({
                url: '{{ route("admin.partners.store") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    image: entry.path,
                    alt: entry.alt
                },
                success: function () { done++; },
                error: function (xhr) {
                    failed++;
                    let msg = entry.path;
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        let errs = xhr.responseJSON.errors;
                        msg += ': ' + Object.values(errs).flat().join(', ');
                    }
                    toastr.error('Failed: ' + msg);
                },
                complete: function () {
                    $bar.css('width', Math.round(((i + 1) / total) * 100) + '%');
                    $countLabel.text(Math.round(((i + 1) / total) * 100) + '%');
                    uploadNext(i + 1);
                }
            });
        }

        uploadNext(0);
    });

    // ── Reset modal on close ──────────────────────────────────
    $('#addPartnerModal').on('hidden.bs.modal', function () {
        window.resetMediaGalleryPicker('partner_add');
        $progress.hide();
        $bar.css('width', '0%');
        $status.text('Uploading…');
        $countLabel.text('');
        updateSaveBtn();
    });

    // ════════════════════════════════════════
    //  EDIT PARTNER
    // ════════════════════════════════════════
    $(document).on('click', '.edit-partner', function () {
        let btn = $(this);
        btn.find('.spinner-border').removeClass('d-none');
        btn.find('.icon').addClass('d-none');
        $.ajax({
            url: btn.data('url'),
            type: 'GET',
            success: function (data) {
                $('#edit-partner-id').val(data.id);
                $('#edit-alt').val(data.alt);
                if (typeof window.setMediaPickerValue === 'function') {
                    window.setMediaPickerValue('partner_image_edit', data.image, s3BaseUrl + data.image);
                }
                $('#editPartnerForm').attr('data-url', '{{ url("admin/partners") }}/' + data.id);
                $('#editPartnerModal').modal('show');
            },
            error: function () { toastr.error('Failed to load partner data.'); },
            complete: function () {
                btn.find('.spinner-border').addClass('d-none');
                btn.find('.icon').removeClass('d-none');
            }
        });
    });

    $('#editPartnerForm').on('submit', function (e) {
        e.preventDefault();
        let btn = $('#edit-submit-btn');
        btn.prop('disabled', true);
        let formData = new FormData(this);
        formData.append('_method', 'PUT');
        $.ajax({
            url: $(this).attr('data-url'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function () {
                toastr.success('Partner updated!');
                $('#editPartnerModal').modal('hide');
                setTimeout(() => location.reload(), 1200);
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    if (errors.image) toastr.error(errors.image[0]);
                    if (errors.alt)   toastr.error(errors.alt[0]);
                    if (!errors.image && !errors.alt) {
                        toastr.error(window.firstErrorMessage(errors, 'Update failed.'));
                    }
                } else {
                    toastr.error('Update failed. Please try again.');
                }
            },
            complete: function () { btn.prop('disabled', false); }
        });
    });

    // ════════════════════════════════════════
    //  STATUS TOGGLE
    // ════════════════════════════════════════
    $(document).on('change', '.partner-status', function () {
        let status = $(this).prop('checked') ? 1 : 0;
        $.ajax({
            url:  $(this).data('url'),
            type: 'PUT',
            data: { status: status, _token: '{{ csrf_token() }}' },
            success: function () { toastr.success('Status updated'); },
            error:   function () { toastr.error('Failed to update status.'); }
        });
    });

    // ════════════════════════════════════════
    //  DELETE
    // ════════════════════════════════════════
    $(document).on('click', '.delete-partner', function () {
        let btn = $(this);
        let row = btn.closest('tr');
        Swal.fire({
            title: 'Delete this partner?',
            text: 'This logo will be permanently removed.',
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
                    url:  btn.data('url'),
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (res) {
                        if (res.success) { row.remove(); toastr.success('Partner deleted.'); }
                    },
                    error:    function () { toastr.error('Delete failed.'); },
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
