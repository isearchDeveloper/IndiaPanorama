@props([
    'name',
    'pickerId' => null,
    'label' => 'Gallery',
    'folder' => null,
])

@php
    $pickerId = $pickerId ?: $name;
@endphp

{{--
    Multi-select companion to <x-media-picker>. Renders a grid of currently
    attached gallery images (each with its own alt-text input + remove
    button) and an "Add Images" button that opens a shared multi-select
    Media Library modal — pick any number of existing images and/or upload
    new ones (license required once per new upload, exactly like the single
    picker) in one go.

    Submits as an indexed array: {name}[0][path], {name}[0][alt], {name}[1][path], ...
    so the controller can just loop `$request->input('{name}', [])`, validate
    each `path` via `exists:media,path`, and create the gallery row directly —
    no file handling, no per-image license logic needed anymore.
--}}
<div class="media-gallery-picker" data-field-name="{{ $pickerId }}" data-folder="{{ $folder }}" data-name="{{ $name }}">
    @if($label)
        <label class="form-label form-label-sm mb-1 d-block">{{ $label }}</label>
    @endif

    <div class="mgp-grid row g-2 mb-2"></div>
    <div class="text-muted small mgp-empty">No images yet.</div>

    <button type="button" class="form-control mgp-add text-start">
        <i class="fas fa-images me-1"></i> Add Images
    </button>
</div>

@once
<style>
    .mgp-item { position: relative; }
    .mgp-item .mgp-thumb {
        border: 1px solid var(--border-color, #e2e8f0); border-radius: 8px; overflow: hidden;
        aspect-ratio: 1 / 1; background: #f8fafc;
    }
    .mgp-item .mgp-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .mgp-item .mgp-remove {
        position: absolute; top: 4px; right: 4px; width: 22px; height: 22px; border-radius: 50%;
        background: rgba(220,53,69,0.9); color: #fff; border: none; font-size: 11px;
        display: flex; align-items: center; justify-content: center;
    }
    .mgp-item .mgp-alt {
        margin-top: 4px;
    }
    .mgp-add {
        display: flex; align-items: center; gap: 8px; cursor: pointer;
    }

    #mediaGalleryPickerModal .mgp-modal-thumb {
        position: relative; cursor: pointer; border: 2px solid transparent; border-radius: 8px;
        overflow: hidden; aspect-ratio: 1 / 1; background: #f8fafc;
    }
    #mediaGalleryPickerModal .mgp-modal-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    #mediaGalleryPickerModal .mgp-modal-thumb.selected { border-color: #2563eb; }
    #mediaGalleryPickerModal .mgp-modal-thumb.selected::after {
        content: "\f00c"; font-family: "Font Awesome 6 Free"; font-weight: 900;
        position: absolute; top: 4px; right: 4px; width: 20px; height: 20px; border-radius: 50%;
        background: #2563eb; color: #fff; font-size: 11px; display: flex; align-items: center; justify-content: center;
    }
    #mediaGalleryPickerModal .mgp-dropzone {
        border: 2px dashed var(--border-color, #cbd5e1); border-radius: 10px; padding: 40px 20px;
        text-align: center; color: #64748b; cursor: pointer;
    }
</style>

<div class="modal fade" id="mediaGalleryPickerModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-images me-2"></i>Add Images</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item"><button type="button" class="nav-link active" data-mgp-tab="upload">Upload New</button></li>
                    <li class="nav-item"><button type="button" class="nav-link" data-mgp-tab="library">Media Library</button></li>
                </ul>

                <div data-mgp-pane="upload">
                    <div class="mgp-dropzone" id="mgpDropzone">
                        <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                        <div id="mgpDropzoneText">Click to choose file(s), or drag & drop here</div>
                        <div class="small text-muted mt-1">WEBP only, up to 150KB each</div>
                    </div>
                    <input type="file" id="mgpUploadInput" class="d-none" accept="image/webp" multiple>

                    <div id="mgpLicenseSection" class="mt-3 d-none">
                        <x-image-license-fields name="gallery_license" label="Image License Details" />
                        <button type="button" class="btn btn-primary btn-sm mt-3" id="mgpUploadSubmitBtn">
                            <i class="fas fa-upload me-1"></i> Upload
                        </button>
                    </div>
                    <div class="text-center mt-2 d-none" id="mgpUploadSpinner">
                        <span class="spinner-border spinner-border-sm"></span> Uploading&hellip;
                    </div>
                </div>

                <div data-mgp-pane="library" class="d-none">
                    <div class="row g-2 mb-3">
                        <div class="col-8">
                            <input type="text" class="form-control form-control-sm" id="mgpSearch" placeholder="Search by filename...">
                        </div>
                        <div class="col-4">
                            <select class="form-select form-select-sm" id="mgpFolderFilter">
                                <option value="">All folders</option>
                                @foreach(\App\Models\Media::whereNotNull('folder')->distinct()->orderBy('folder')->pluck('folder') as $mgpFolder)
                                <option value="{{ $mgpFolder }}">{{ humanize_folder_label($mgpFolder) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="mgpGrid" class="row g-2"></div>
                    <div class="text-center text-muted py-4 d-none" id="mgpEmpty">No images found.</div>
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-sm btn-outline-primary d-none" id="mgpLoadMore">Load More</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="mgpUseBtn" disabled>Use Selected (<span id="mgpSelectedCount">0</span>)</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var $target = null;       // the .media-gallery-picker that opened the modal
    var selected = {};        // path -> {path, url}  (object keyed by path so re-clicking toggles cleanly)
    var page = 1, lastPage = 1;
    var pendingFiles = null;

    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('mediaGalleryPickerModal');
        if (el && el.parentElement !== document.body) document.body.appendChild(el);
    });

    $('#mediaGalleryPickerModal').on('show.bs.modal', function () {
        var openCount = $('.modal.show').length;
        var z = 1055 + (openCount * 20);
        $(this).css('z-index', z);
        setTimeout(function () { $('.modal-backdrop').last().css('z-index', z - 5); }, 0);
    });
    $('#mediaGalleryPickerModal').on('hidden.bs.modal', function () {
        if ($('.modal.show').length) $('body').addClass('modal-open');
    });

    // ── Grid item template + reindexing for a target gallery picker block ──
    function itemRowHtml(idx, name, path, url, alt) {
        return '' +
            '<div class="col-6 col-md-4 col-lg-3 mgp-item" data-path="' + path + '">' +
                '<div class="mgp-thumb"><img src="' + url + '" loading="lazy"></div>' +
                '<input type="hidden" name="' + name + '[' + idx + '][path]" value="' + path + '">' +
                '<input type="text" class="form-control form-control-sm mgp-alt" name="' + name + '[' + idx + '][alt]" ' +
                    'value="' + (alt || '').replace(/"/g, '&quot;') + '" placeholder="Alt text">' +
                '<button type="button" class="mgp-remove" title="Remove"><i class="fas fa-times"></i></button>' +
            '</div>';
    }

    function reindex($block) {
        var name = $block.data('name');
        $block.find('.mgp-grid > .mgp-item').each(function (i) {
            $(this).find('input[type=hidden]').attr('name', name + '[' + i + '][path]');
            $(this).find('.mgp-alt').attr('name', name + '[' + i + '][alt]');
        });
        $block.find('.mgp-empty').toggleClass('d-none', $block.find('.mgp-grid > .mgp-item').length > 0);
    }

    function addItemsToTarget($block, items) {
        var name = $block.data('name');
        var $grid = $block.find('.mgp-grid');
        items.forEach(function (item) {
            if ($grid.find('.mgp-item[data-path="' + item.path + '"]').length) return; // no dupes
            var idx = $grid.find('.mgp-item').length;
            $grid.append(itemRowHtml(idx, name, item.path, item.url, item.filename || ''));
        });
        reindex($block);
    }

    $(document).on('click', '.media-gallery-picker .mgp-add', function () {
        openPicker($(this).closest('.media-gallery-picker'));
    });

    $(document).on('click', '.media-gallery-picker .mgp-remove', function () {
        var $block = $(this).closest('.media-gallery-picker');
        $(this).closest('.mgp-item').remove();
        reindex($block);
    });

    function resetGalleryUploadPane() {
        pendingFiles = null;
        $('#mgpUploadInput').val('');
        $('#mgpDropzoneText').text('Click to choose file(s), or drag & drop here');
        $('#mgpLicenseSection').addClass('d-none');
        if (typeof window.resetImageLicenseBlock === 'function') {
            window.resetImageLicenseBlock('gallery_license');
        }
    }

    function resetSelection() {
        selected = {};
        $('#mgpSelectedCount').text('0');
        $('#mgpUseBtn').prop('disabled', true);
        $('#mediaGalleryPickerModal .mgp-modal-thumb.selected').removeClass('selected');
    }

    function openPicker($block) {
        $target = $block;
        resetSelection();
        resetGalleryUploadPane();
        $('#mediaGalleryPickerModal [data-mgp-tab]').removeClass('active');
        $('#mediaGalleryPickerModal [data-mgp-tab="upload"]').addClass('active');
        $('#mediaGalleryPickerModal [data-mgp-pane]').addClass('d-none');
        $('#mediaGalleryPickerModal [data-mgp-pane="upload"]').removeClass('d-none');
        $('#mgpFolderFilter').val('');
        $('#mgpSearch').val('');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('mediaGalleryPickerModal')).show();
    }

    $('#mediaGalleryPickerModal [data-mgp-tab]').on('click', function () {
        var tab = $(this).data('mgp-tab');
        $('#mediaGalleryPickerModal [data-mgp-tab]').removeClass('active');
        $(this).addClass('active');
        $('#mediaGalleryPickerModal [data-mgp-pane]').addClass('d-none');
        $('#mediaGalleryPickerModal [data-mgp-pane="' + tab + '"]').removeClass('d-none');
        if (tab === 'library') loadLibrary(true);
    });

    function thumbHtml(item) {
        return '' +
            '<div class="col-4 col-md-3">' +
                '<div class="mgp-modal-thumb" data-path="' + item.path + '" data-url="' + item.url + '" data-filename="' + item.filename + '">' +
                    '<img src="' + item.url + '" loading="lazy">' +
                '</div>' +
            '</div>';
    }

    function loadLibrary(reset) {
        if (reset) { page = 1; $('#mgpGrid').empty(); }

        var folder = $('#mgpFolderFilter').val();

        $.get('{{ route("admin.media.list") }}', {
            page: page, search: $('#mgpSearch').val(), folder: folder
        }).done(function (res) {
            lastPage = res.last_page;
            res.data.forEach(function (item) {
                $('#mgpGrid').append(thumbHtml(item));
                if (selected[item.path]) $('#mgpGrid .mgp-modal-thumb[data-path="' + item.path + '"]').addClass('selected');
            });
            $('#mgpEmpty').toggleClass('d-none', $('#mgpGrid').children().length > 0);
            $('#mgpLoadMore').toggleClass('d-none', page >= lastPage);
        });
    }

    $('#mgpSearch').on('input', function () {
        clearTimeout(window._mgpSearchTimer);
        window._mgpSearchTimer = setTimeout(function () { loadLibrary(true); }, 350);
    });
    $('#mgpFolderFilter').on('change', function () { loadLibrary(true); });
    $('#mgpLoadMore').on('click', function () { page++; loadLibrary(false); });

    $('#mgpGrid').on('click', '.mgp-modal-thumb', function () {
        var path = $(this).data('path');
        if (selected[path]) {
            delete selected[path];
            $(this).removeClass('selected');
        } else {
            selected[path] = { path: path, url: $(this).data('url'), filename: $(this).data('filename') };
            $(this).addClass('selected');
        }
        var count = Object.keys(selected).length;
        $('#mgpSelectedCount').text(count);
        $('#mgpUseBtn').prop('disabled', count === 0);
    });

    $('#mgpUseBtn').on('click', function () {
        if (!$target) return;
        addItemsToTarget($target, Object.values(selected));
        bootstrap.Modal.getInstance(document.getElementById('mediaGalleryPickerModal')).hide();
    });

    // ── Upload New tab ──
    $('#mgpDropzone').on('click', function () { $('#mgpUploadInput').trigger('click'); });
    $('#mgpDropzone').on('dragover', function (e) { e.preventDefault(); $(this).addClass('dragover'); });
    $('#mgpDropzone').on('dragleave', function () { $(this).removeClass('dragover'); });
    $('#mgpDropzone').on('drop', function (e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        stageFiles(e.originalEvent.dataTransfer.files);
    });
    $('#mgpUploadInput').on('change', function () { stageFiles(this.files); });

    function stageFiles(files) {
        if (!files || !files.length) return;

        for (var i = 0; i < files.length; i++) {
            var f = files[i];
            if (f.type !== 'image/webp') {
                if (window.toastr) toastr.error(f.name + ' is not a WEBP image.'); else alert(f.name + ' is not a WEBP image.');
                return;
            }
            if (f.size > 150 * 1024) {
                if (window.toastr) toastr.error(f.name + ' is larger than 150KB.'); else alert(f.name + ' is larger than 150KB.');
                return;
            }
        }

        pendingFiles = files;
        var names = Array.prototype.map.call(files, function (f) { return f.name; }).join(', ');
        $('#mgpDropzoneText').text(files.length + ' file(s) selected: ' + names);
        $('#mgpLicenseSection').removeClass('d-none');
    }

    $('#mgpUploadSubmitBtn').on('click', function () {
        if (!pendingFiles || !pendingFiles.length) return;
        if (typeof window.validateImageLicenseBlocks === 'function' &&
            !window.validateImageLicenseBlocks('#mgpLicenseSection')) {
            return;
        }

        var fd = new FormData();
        for (var i = 0; i < pendingFiles.length; i++) fd.append('files[]', pendingFiles[i]);
        if ($target && $target.data('folder')) fd.append('folder', $target.data('folder'));

        $('.image-license-block[data-field-name="gallery_license"]').find('input').each(function () {
            if (!this.name) return;
            if (this.type === 'file') {
                if (this.files.length) fd.append(this.name, this.files[0]);
            } else {
                fd.append(this.name, this.value);
            }
        });

        $('#mgpUploadSpinner').removeClass('d-none');
        $('#mgpUploadSubmitBtn').prop('disabled', true);

        $.ajax({
            url: '{{ route("admin.media.store") }}',
            method: 'POST', data: fd, processData: false, contentType: false
        }).done(function (res) {
            if (res.data && res.data.length && $target) {
                addItemsToTarget($target, res.data);
            }
            resetGalleryUploadPane();
            bootstrap.Modal.getInstance(document.getElementById('mediaGalleryPickerModal')).hide();
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors &&
                typeof window.applyLicenseServerErrors === 'function') {
                window.applyLicenseServerErrors(xhr.responseJSON.errors, '#mgpLicenseSection');
            } else {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Upload failed.';
                if (window.toastr) toastr.error(msg); else alert(msg);
            }
        }).always(function () {
            $('#mgpUploadSpinner').addClass('d-none');
            $('#mgpUploadSubmitBtn').prop('disabled', false);
        });
    });

    /**
     * Populate a gallery picker block from already-fetched data (edit-form
     * AJAX populate callbacks) — pass an array of {path, url, alt}.
     *   window.setMediaGalleryItems('pkg_gallery', [{path, url, alt}, ...]);
     */
    window.setMediaGalleryItems = function (pickerId, items) {
        var $block = $('.media-gallery-picker[data-field-name="' + pickerId + '"]');
        if (!$block.length) return;
        $block.find('.mgp-grid').empty();
        addItemsToTarget($block, items || []);
    };

    /** Clear a gallery picker block back to empty. */
    window.resetMediaGalleryPicker = function (pickerId) {
        window.setMediaGalleryItems(pickerId, []);
    };
})();
</script>
@endonce
