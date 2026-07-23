@section('title','Media Library')
@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">
                    <i class="fas fa-images me-2"></i>Media Library
                </h1>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" id="mlSyncBtn">
                        <i class="fas fa-sync-alt me-2"></i>Sync Library
                        <span class="spinner-border spinner-border-sm d-none ms-1" role="status" aria-hidden="true"></span>
                    </button>
                    <button type="button" class="btn btn-primary" id="mlUploadBtn" data-bs-toggle="modal" data-bs-target="#mlUploadModal">
                        <i class="fas fa-upload me-2"></i>Upload
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="mlSearch" placeholder="Search by filename...">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="mlFolder">
                                <option value="">All Folders</option>
                                @foreach($folders as $folder)
                                <option value="{{ $folder }}">{{ humanize_folder_label($folder) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="mlGrid" class="row g-3"></div>
                    <div class="text-center text-muted py-4 d-none" id="mlEmpty">No images found.</div>

                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-outline-primary d-none" id="mlLoadMore">
                            Load More
                            <span class="spinner-border spinner-border-sm d-none ms-1" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modal')
<div class="modal fade" id="mlUploadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-upload me-2"></i>Upload Images</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">File(s)</label>
                    <input type="file" class="form-control" id="mlUploadInput" accept="image/*" multiple>
                </div>
                <div class="mb-3">
                    <label class="form-label">Folder <small class="text-muted">(optional — defaults to "media")</small></label>
                    <input type="text" class="form-control" id="mlUploadFolder" placeholder="e.g. banner">
                </div>
                <x-image-license-fields name="license" label="Image License Details" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="mlUploadSubmitBtn">
                    <i class="fas fa-upload me-1"></i> Upload
                    <span class="spinner-border spinner-border-sm d-none ms-1"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mlDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attachment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body ml-details-modal-body">
                <div class="text-center py-5 d-none" id="mlDetailsLoading">
                    <span class="spinner-border"></span>
                </div>
                <div id="mlDetailsBody">
                    <div class="row g-4">
                        <div class="col-md-5 ml-details-sticky-col">
                            <div class="ml-details-preview">
                                <img id="mlDetailsImg" src="" alt="">
                            </div>
                            <div class="input-group input-group-sm mt-2">
                                <input type="text" class="form-control" id="mlDetailsUrl" readonly>
                                <button class="btn btn-outline-secondary" type="button" id="mlCopyUrlBtn" title="Copy URL">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="ml-detail-row"><span class="ml-detail-label">Uploaded on</span><span id="mlDetailsCreatedAt"></span></div>
                            <div class="ml-detail-row"><span class="ml-detail-label">Uploaded by</span><span id="mlDetailsUploader"></span></div>
                            <div class="ml-detail-row"><span class="ml-detail-label">File name</span><span id="mlDetailsFilename" class="text-break"></span></div>
                            <div class="ml-detail-row"><span class="ml-detail-label">Original name</span><span id="mlDetailsOriginalName" class="text-break"></span></div>
                            <div class="ml-detail-row"><span class="ml-detail-label">File type</span><span id="mlDetailsMime"></span></div>
                            <div class="ml-detail-row"><span class="ml-detail-label">File size</span><span id="mlDetailsSize"></span></div>
                            <div class="ml-detail-row"><span class="ml-detail-label">Dimensions</span><span id="mlDetailsDimensions"></span></div>
                            <div class="ml-detail-row"><span class="ml-detail-label">Folder</span><span id="mlDetailsFolder"></span></div>
                            <div class="ml-detail-row"><span class="ml-detail-label">Source</span><span id="mlDetailsSource"></span></div>
                        </div>
                    </div>
                    <div class="row g-4 mt-1">
                        <div class="col-12">
                            <div class="ml-license-card">
                                <h6 class="d-flex align-items-center gap-2 mb-3">
                                    <i class="fas fa-shield-alt text-success"></i>License Details
                                </h6>
                                <div id="mlDetailsLicenseYes" class="d-none row">
                                    <div class="col-md-6 ml-detail-row"><span class="ml-detail-label">Source of Image</span><span id="mlDetailsLicSource"></span></div>
                                    <div class="col-md-6 ml-detail-row"><span class="ml-detail-label">Download Date</span><span id="mlDetailsLicDate"></span></div>
                                    <div class="col-md-6 ml-detail-row"><span class="ml-detail-label">Account ID</span><span id="mlDetailsLicAccount"></span></div>
                                    <div class="col-md-6 ml-detail-row"><span class="ml-detail-label">License Key</span><span id="mlDetailsLicKey"></span></div>
                                    <div class="col-12 ml-detail-row"><span class="ml-detail-label">Document</span><span id="mlDetailsLicDoc"></span></div>
                                </div>
                                <div id="mlDetailsLicenseNo" class="text-muted small d-none">No license details on file for this image.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .ml-item { cursor: default; }
    .ml-thumb {
        position: relative;
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 8px;
        overflow: hidden;
        aspect-ratio: 1 / 1;
        background: #f8fafc;
    }
    .ml-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; cursor: pointer; }
    .ml-thumb .ml-delete {
        position: absolute; top: 6px; right: 6px;
        width: 26px; height: 26px; border-radius: 50%;
        background: rgba(220,53,69,0.9); color: #fff; border: none;
        display: flex; align-items: center; justify-content: center; font-size: 12px;
    }
    .ml-thumb .ml-license-badge {
        position: absolute; bottom: 6px; left: 6px;
        width: 22px; height: 22px; border-radius: 50%;
        background: rgba(34,197,94,0.9); color: #fff;
        display: flex; align-items: center; justify-content: center; font-size: 11px;
    }
    .ml-caption { font-size: 11px; color: #64748b; margin-top: 4px; word-break: break-all; }

    .ml-details-modal-body { background: #fff; }
    .ml-details-sticky-col { position: sticky; top: 0; align-self: flex-start; }
    .ml-details-preview {
        background: #f0f0f1;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 240px;
        overflow: hidden;
    }
    .ml-details-preview img { max-width: 100%; max-height: 360px; object-fit: contain; }
    .ml-detail-row {
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f1;
        font-size: 13px;
        line-height: 1.5;
        word-break: break-word;
    }
    .ml-detail-row:last-child { border-bottom: none; }
    .ml-detail-label {
        display: block;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: #8c8f94;
        font-weight: 600;
        margin-bottom: 2px;
    }
    .ml-license-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
    }
    .ml-license-card .ml-detail-row { border-bottom-color: #e2e8f0; }
</style>
@endpush

@section('scripts')
<script>
$(function () {
    var CSRF = $('meta[name="csrf-token"]').attr('content');
    var page = 1;
    var lastPage = 1;

    function itemHtml(item) {
        var licenseBadge = item.has_license
            ? '<span class="ml-license-badge" title="Licensed"><i class="fas fa-shield-alt"></i></span>'
            : '';
        return '' +
            '<div class="col-6 col-md-3 col-lg-2 ml-item" data-id="' + item.id + '">' +
                '<div class="ml-thumb">' +
                    '<img src="' + item.url + '" loading="lazy">' +
                    licenseBadge +
                    '<button type="button" class="ml-delete" data-id="' + item.id + '" title="Delete">' +
                        '<i class="fas fa-times"></i>' +
                    '</button>' +
                '</div>' +
                '<div class="ml-caption">' + item.filename + '</div>' +
            '</div>';
    }

    function load(reset) {
        if (reset) { page = 1; $('#mlGrid').empty(); }

        $.get('{{ route("admin.media.list") }}', {
            page: page,
            search: $('#mlSearch').val(),
            folder: $('#mlFolder').val()
        }).done(function (res) {
            lastPage = res.last_page;
            res.data.forEach(function (item) { $('#mlGrid').append(itemHtml(item)); });
            $('#mlEmpty').toggleClass('d-none', $('#mlGrid').children().length > 0);
            $('#mlLoadMore').toggleClass('d-none', page >= lastPage);
        });
    }

    load(true);

    var searchTimer;
    $('#mlSearch').on('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () { load(true); }, 350);
    });
    $('#mlFolder').on('change', function () { load(true); });

    $('#mlLoadMore').on('click', function () {
        var $btn = $(this);
        $btn.find('.spinner-border').removeClass('d-none');
        page++;
        $.get('{{ route("admin.media.list") }}', {
            page: page,
            search: $('#mlSearch').val(),
            folder: $('#mlFolder').val()
        }).done(function (res) {
            lastPage = res.last_page;
            res.data.forEach(function (item) { $('#mlGrid').append(itemHtml(item)); });
            $btn.toggleClass('d-none', page >= lastPage);
        }).always(function () { $btn.find('.spinner-border').addClass('d-none'); });
    });

    $('#mlUploadModal').on('hidden.bs.modal', function () {
        $('#mlUploadInput').val('');
        $('#mlUploadFolder').val('');
        if (typeof window.resetImageLicenseBlock === 'function') {
            window.resetImageLicenseBlock('license');
        }
    });

    $('#mlUploadSubmitBtn').on('click', function () {
        var files = document.getElementById('mlUploadInput').files;
        if (!files.length) {
            toastr.warning('Please choose at least one file.');
            return;
        }
        if (typeof window.validateImageLicenseBlocks === 'function' &&
            !window.validateImageLicenseBlocks('#mlUploadModal')) {
            return;
        }

        var fd = new FormData();
        for (var i = 0; i < files.length; i++) fd.append('files[]', files[i]);
        if ($('#mlUploadFolder').val()) fd.append('folder', $('#mlUploadFolder').val());

        $('.image-license-block[data-field-name="license"]').find('input').each(function () {
            if (!this.name) return;
            if (this.type === 'file') {
                if (this.files.length) fd.append(this.name, this.files[0]);
            } else {
                fd.append(this.name, this.value);
            }
        });

        var $btn = $(this).prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');

        $.ajax({
            url: '{{ route("admin.media.store") }}',
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false
        }).done(function () {
            toastr.success('Uploaded!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('mlUploadModal')).hide();
            load(true);
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors &&
                typeof window.applyLicenseServerErrors === 'function') {
                window.applyLicenseServerErrors(xhr.responseJSON.errors, '#mlUploadModal');
            } else {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Upload failed.';
                if (window.toastr) toastr.error(msg); else alert(msg);
            }
        }).always(function () {
            $btn.prop('disabled', false).find('.spinner-border').addClass('d-none');
        });
    });

    $('#mlGrid').on('click', '.ml-delete', function () {
        var id = $(this).data('id');
        var $item = $(this).closest('.ml-item');
        if (!confirm('Delete this image? It may still be used elsewhere on the site — deleting it here will break those references.')) return;

        $.ajax({
            url: '/admin/media/' + id,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF }
        }).done(function () {
            $item.remove();
        });
    });

    // ── Details modal (click a thumbnail) ───────────────────────────────────
    $('#mlGrid').on('click', '.ml-thumb img', function () {
        var id = $(this).closest('.ml-item').data('id');
        var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('mlDetailsModal'));
        var url = "{{ route('admin.media.show', ':id') }}";
            url = url.replace(':id', id);

        $('#mlDetailsBody').addClass('d-none');
        $('#mlDetailsLoading').removeClass('d-none');
        modal.show();
        
        

        $.get(url).done(function (d) {
            $('#mlDetailsImg').attr('src', d.url);
            $('#mlDetailsUrl').val(d.url);
            $('#mlDetailsFilename').text(d.filename || '—');
            $('#mlDetailsOriginalName').text(d.original_name || '—');
            $('#mlDetailsFolder').text(d.folder ? humanizeFolderLabel(d.folder) : '—');
            $('#mlDetailsDimensions').text((d.width && d.height) ? (d.width + ' × ' + d.height + ' pixels') : '—');
            $('#mlDetailsSize').text(d.size_human || '—');
            $('#mlDetailsMime').text(d.mime_type || '—');
            $('#mlDetailsSource').text(d.source === 'upload' ? 'Uploaded' : (d.source === 'synced' ? 'Synced from disk' : (d.source || '—')));
            var uploaderText = d.uploaded_by_email
                ? (d.uploaded_by ? d.uploaded_by + ' (' + d.uploaded_by_email + ')' : d.uploaded_by_email)
                : (d.uploaded_by || '—');
            $('#mlDetailsUploader').text(uploaderText);
            $('#mlDetailsCreatedAt').text(d.created_at || '—');

            if (d.license) {
                $('#mlDetailsLicSource').text(d.license.source_of_image || '—');
                $('#mlDetailsLicDate').text(d.license.download_date || '—');
                $('#mlDetailsLicAccount').text(d.license.account_id || '—');
                $('#mlDetailsLicKey').text(d.license.license_key || '—');
                $('#mlDetailsLicDoc').html(d.license.license_key_file_url
                    ? '<a href="' + d.license.license_key_file_url + '" target="_blank"><i class="fas fa-file-pdf me-1"></i>View document</a>'
                    : '—');
                $('#mlDetailsLicenseYes').removeClass('d-none');
                $('#mlDetailsLicenseNo').addClass('d-none');
            } else {
                $('#mlDetailsLicenseYes').addClass('d-none');
                $('#mlDetailsLicenseNo').removeClass('d-none');
            }
        }).fail(function () {
            toastr.error('Failed to load image details.');
        }).always(function () {
            $('#mlDetailsLoading').addClass('d-none');
            $('#mlDetailsBody').removeClass('d-none');
        });
    });

    function humanizeFolderLabel(folder) {
        return folder.replace(/[-_]/g, ' ').replace(/\b\w/g, function (c) { return c.toUpperCase(); });
    }

    $('#mlCopyUrlBtn').on('click', function () {
        var $input = $('#mlDetailsUrl');
        $input[0].select();
        navigator.clipboard.writeText($input.val()).then(function () {
            toastr.success('URL copied to clipboard.');
        }).catch(function () {
            document.execCommand('copy');
            toastr.success('URL copied to clipboard.');
        });
    });

    $('#mlSyncBtn').on('click', function () {
        var $btn = $(this);
        $btn.prop('disabled', true).find('.spinner-border').removeClass('d-none');
        $.post('{{ route("admin.media.sync") }}').done(function () {
            load(true);
        }).always(function () {
            $btn.prop('disabled', false).find('.spinner-border').addClass('d-none');
        });
    });
});
</script>
@endsection
