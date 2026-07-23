@props([
    'name',
    'value' => null,
    'label' => 'Image',
    'folder' => null,
    'errorBag' => null,
    // JS lookup key for window.setMediaPickerValue() — defaults to `name`, but
    // must be set explicitly whenever the same field `name` appears more than
    // once on a page (e.g. an "Add" modal and an "Edit" modal both posting a
    // `banner_image` field), otherwise a by-name lookup would update every
    // matching instance at once instead of just the one being populated.
    'pickerId' => null,
    // For pages that don't submit this field via its `name` (e.g. CMS Builder's
    // generic data-key/data-card-key JSON collector) — when set, the hidden
    // input also carries this attribute so that collector picks it up the same
    // way it does every other field.
    'dataKey' => null,
    'dataCardKey' => null,
])

@php
    $pickerId = $pickerId ?: $name;
    $errBag = $errorBag ?? ($errors ?? null);
    $err = $errBag && method_exists($errBag, 'first') ? $errBag->first($name) : null;
    $previewUrl = $value ? storage_link($value) : null;
@endphp

<div class="media-picker-block" data-field-name="{{ $pickerId }}" data-folder="{{ $folder }}">
    @if($label)
        <label class="form-label form-label-sm mb-1 d-block">{{ $label }}</label>
    @endif

    <input type="hidden" class="media-picker-value @if($err) is-invalid @endif" name="{{ $name }}" value="{{ old($name, $value) }}"
        @if($dataKey) data-key="{{ $dataKey }}" @endif
        @if($dataCardKey) data-card-key="{{ $dataCardKey }}" @endif>

    <div class="media-picker-preview {{ $previewUrl ? '' : 'd-none' }} mb-2">
        <img src="{{ $previewUrl }}" alt="">
        <button type="button" class="media-picker-clear" title="Remove"><i class="fas fa-times"></i></button>
    </div>

    <button type="button" class="form-control media-picker-choose text-start">
        <i class="fas fa-images"></i> Choose Image
    </button>

    @if($err)
        <div class="invalid-feedback d-block">{{ $err }}</div>
    @endif
</div>

@once
<style>
    .media-picker-preview { position: relative; display: block; width: 100%; }
    .media-picker-preview img {
        max-height: 140px; width: auto; max-width: 100%; border-radius: 8px;
        border: 1px solid var(--border-color, #e2e8f0); object-fit: cover; display: block;
    }
    .media-picker-preview .media-picker-clear {
        position: absolute; top: -8px; right: -8px;
        width: 24px; height: 24px; border-radius: 50%;
        background: rgba(220,53,69,0.9); color: #fff; border: none;
        display: flex; align-items: center; justify-content: center; font-size: 11px;
    }
    .media-picker-choose {
        display: flex; align-items: center; gap: 8px; cursor: pointer;
    }
    .media-picker-choose i { color: #6c757d; }

    #mediaPickerModal .mp-thumb {
        position: relative; cursor: pointer; border: 2px solid transparent; border-radius: 8px;
        overflow: hidden; aspect-ratio: 1 / 1; background: #f8fafc;
    }
    #mediaPickerModal .mp-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    #mediaPickerModal .mp-thumb.selected { border-color: #2563eb; }
    #mediaPickerModal .mp-thumb.selected::after {
        content: "\f00c"; font-family: "Font Awesome 6 Free"; font-weight: 900;
        position: absolute; top: 4px; right: 4px; width: 20px; height: 20px; border-radius: 50%;
        background: #2563eb; color: #fff; font-size: 11px; display: flex; align-items: center; justify-content: center;
    }
    #mediaPickerModal .mp-dropzone {
        border: 2px dashed var(--border-color, #cbd5e1); border-radius: 10px; padding: 40px 20px;
        text-align: center; color: #64748b; cursor: pointer;
    }
    #mediaPickerModal .mp-thumb .mp-license-badge {
        position: absolute; bottom: 4px; left: 4px; width: 18px; height: 18px; border-radius: 50%;
        background: rgba(34,197,94,0.9); color: #fff; font-size: 10px;
        display: flex; align-items: center; justify-content: center;
    }
    #mediaPickerModal .mp-dropzone.dragover { border-color: #2563eb; background: #eff6ff; }
</style>

<div class="modal fade" id="mediaPickerModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-images me-2"></i>Media Library</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" data-mp-tab="upload">Upload New</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" data-mp-tab="library">Media Library</button>
                    </li>
                </ul>

                <div data-mp-pane="upload">
                    <div class="mp-dropzone" id="mpDropzone">
                        <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                        <div id="mpDropzoneText">Click to choose file(s), or drag & drop here</div>
                        <div class="small text-muted mt-1">WEBP only, up to 150KB each</div>
                    </div>
                    <input type="file" id="mpUploadInput" class="d-none" accept="image/webp" multiple>

                    <div id="mpLicenseSection" class="mt-3 d-none">
                        <x-image-license-fields name="license" label="Image License Details" />
                        <button type="button" class="btn btn-primary btn-sm mt-3" id="mpUploadSubmitBtn">
                            <i class="fas fa-upload me-1"></i> Upload
                        </button>
                    </div>
                    <div class="text-center mt-2 d-none" id="mpUploadSpinner">
                        <span class="spinner-border spinner-border-sm"></span> Uploading&hellip;
                    </div>
                </div>

                <div data-mp-pane="library" class="d-none">
                    <div class="row g-2 mb-3">
                        <div class="col-8">
                            <input type="text" class="form-control form-control-sm" id="mpSearch" placeholder="Search by filename...">
                        </div>
                        <div class="col-4">
                            <select class="form-select form-select-sm" id="mpFolderFilter">
                                <option value="">All folders</option>
                                @foreach(\App\Models\Media::whereNotNull('folder')->distinct()->orderBy('folder')->pluck('folder') as $mpFolder)
                                <option value="{{ $mpFolder }}">{{ humanize_folder_label($mpFolder) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="mpGrid" class="row g-2"></div>
                    <div class="text-center text-muted py-4 d-none" id="mpEmpty">No images found.</div>
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-sm btn-outline-primary d-none" id="mpLoadMore">Load More</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="mpUseBtn" disabled>Use this image</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var $target = null;      // the .media-picker-block that opened the modal
    var selected = null;     // { path, url }
    var page = 1, lastPage = 1;

    // Shared modal markup must live at body level, not nested inside whichever
    // form/modal the triggering picker instance happened to render inside —
    // otherwise Bootstrap's per-modal Escape-key handler and focus trap on the
    // parent modal fight with this one, and it can end up hidden behind it.
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('mediaPickerModal');
        if (el && el.parentElement !== document.body) document.body.appendChild(el);
    });

    // Stack correctly above whatever modal (if any) is already open, and
    // restore the parent's scroll-lock class that Bootstrap strips on close.
    $('#mediaPickerModal').on('show.bs.modal', function () {
        var openCount = $('.modal.show').length;
        var z = 1055 + (openCount * 20);
        $(this).css('z-index', z);
        setTimeout(function () { $('.modal-backdrop').last().css('z-index', z - 5); }, 0);
    });
    $('#mediaPickerModal').on('hidden.bs.modal', function () {
        if ($('.modal.show').length) $('body').addClass('modal-open');
    });

    function resetSelection() {
        selected = null;
        $('#mpUseBtn').prop('disabled', true);
        $('#mediaPickerModal .mp-thumb.selected').removeClass('selected');
    }

    function thumbHtml(item) {
        var licenseBadge = item.has_license
            ? '<span class="mp-license-badge" title="Licensed"><i class="fas fa-shield-alt"></i></span>'
            : '';
        return '' +
            '<div class="col-4 col-md-3">' +
                '<div class="mp-thumb" data-path="' + item.path + '" data-url="' + item.url + '">' +
                    '<img src="' + item.url + '" loading="lazy">' +
                    licenseBadge +
                '</div>' +
            '</div>';
    }

    function loadLibrary(reset) {
        if (reset) { page = 1; $('#mpGrid').empty(); resetSelection(); }

        var folder = $('#mpFolderFilter').val();

        $.get('{{ route("admin.media.list") }}', {
            page: page,
            search: $('#mpSearch').val(),
            folder: folder
        }).done(function (res) {
            lastPage = res.last_page;
            res.data.forEach(function (item) { $('#mpGrid').append(thumbHtml(item)); });
            $('#mpEmpty').toggleClass('d-none', $('#mpGrid').children().length > 0);
            $('#mpLoadMore').toggleClass('d-none', page >= lastPage);
        });
    }

    function openPicker($block) {
        $target = $block;
        resetSelection();
        resetUploadPane();
        $('#mediaPickerModal [data-mp-tab]').removeClass('active');
        $('#mediaPickerModal [data-mp-tab="upload"]').addClass('active');
        $('#mediaPickerModal [data-mp-pane]').addClass('d-none');
        $('#mediaPickerModal [data-mp-pane="upload"]').removeClass('d-none');
        $('#mpFolderFilter').val('');
        $('#mpSearch').val('');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('mediaPickerModal')).show();
    }

    $(document).on('click', '.media-picker-choose', function () {
        openPicker($(this).closest('.media-picker-block'));
    });

    $(document).on('click', '.media-picker-clear', function () {
        var $block = $(this).closest('.media-picker-block');
        $block.find('.media-picker-value').val('').trigger('change');
        $block.find('.media-picker-preview').addClass('d-none');
    });

    $('#mediaPickerModal [data-mp-tab]').on('click', function () {
        var tab = $(this).data('mp-tab');
        $('#mediaPickerModal [data-mp-tab]').removeClass('active');
        $(this).addClass('active');
        $('#mediaPickerModal [data-mp-pane]').addClass('d-none');
        $('#mediaPickerModal [data-mp-pane="' + tab + '"]').removeClass('d-none');
        if (tab === 'library') loadLibrary(true);
    });

    $('#mpSearch').on('input', function () {
        clearTimeout(window._mpSearchTimer);
        window._mpSearchTimer = setTimeout(function () { loadLibrary(true); }, 350);
    });
    $('#mpFolderFilter').on('change', function () { loadLibrary(true); });
    $('#mpLoadMore').on('click', function () { page++; loadLibrary(false); });

    $('#mpGrid').on('click', '.mp-thumb', function () {
        $('#mediaPickerModal .mp-thumb.selected').removeClass('selected');
        $(this).addClass('selected');
        selected = { path: $(this).data('path'), url: $(this).data('url') };
        $('#mpUseBtn').prop('disabled', false);
    });

    $('#mpUseBtn').on('click', function () {
        if (!selected || !$target) return;
        $target.find('.media-picker-value').val(selected.path).trigger('change');
        $target.find('.media-picker-preview img').attr('src', selected.url);
        $target.find('.media-picker-preview').removeClass('d-none');
        bootstrap.Modal.getInstance(document.getElementById('mediaPickerModal')).hide();
    });

    // ── Upload New tab ──
    // A brand-new image always needs its License Details filled in before it's
    // actually uploaded — this is the ONE place a file enters the shared library,
    // so it's the only place we can guarantee proof-of-rights gets captured.
    // Picking a file just stages it + reveals the license form; the real upload
    // only fires from the "Upload" button, after client-side license validation.
    var pendingFiles = null;

    function resetUploadPane() {
        pendingFiles = null;
        $('#mpUploadInput').val('');
        $('#mpDropzoneText').text('Click to choose file(s), or drag & drop here');
        $('#mpLicenseSection').addClass('d-none');
        if (typeof window.resetImageLicenseBlock === 'function') {
            window.resetImageLicenseBlock('license');
        }
    }

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
        $('#mpDropzoneText').text(files.length + ' file(s) selected: ' + names);
        $('#mpLicenseSection').removeClass('d-none');
    }

    $('#mpDropzone').on('click', function () { $('#mpUploadInput').trigger('click'); });
    $('#mpDropzone').on('dragover', function (e) { e.preventDefault(); $(this).addClass('dragover'); });
    $('#mpDropzone').on('dragleave', function () { $(this).removeClass('dragover'); });
    $('#mpDropzone').on('drop', function (e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        stageFiles(e.originalEvent.dataTransfer.files);
    });
    $('#mpUploadInput').on('change', function () { stageFiles(this.files); });

    $('#mpUploadSubmitBtn').on('click', function () {
        if (!pendingFiles || !pendingFiles.length) return;
        if (typeof window.validateImageLicenseBlocks === 'function' &&
            !window.validateImageLicenseBlocks('#mpLicenseSection')) {
            return;
        }
        uploadFiles(pendingFiles);
    });

    function uploadFiles(files) {
        var fd = new FormData();
        for (var i = 0; i < files.length; i++) fd.append('files[]', files[i]);
        if ($target && $target.data('folder')) fd.append('folder', $target.data('folder'));

        $('.image-license-block[data-field-name="license"]').find('input').each(function () {
            if (!this.name) return;
            if (this.type === 'file') {
                if (this.files.length) fd.append(this.name, this.files[0]);
            } else {
                fd.append(this.name, this.value);
            }
        });

        $('#mpUploadSpinner').removeClass('d-none');
        $('#mpUploadSubmitBtn').prop('disabled', true);

        $.ajax({
            url: '{{ route("admin.media.store") }}',
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false
        }).done(function (res) {
            resetUploadPane();
            $('#mediaPickerModal [data-mp-tab="library"]').trigger('click');
            loadLibrary(true);
            if (res.data && res.data.length) {
                selected = { path: res.data[0].path, url: res.data[0].url };
                $('#mpUseBtn').prop('disabled', false);
            }
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors &&
                typeof window.applyLicenseServerErrors === 'function') {
                window.applyLicenseServerErrors(xhr.responseJSON.errors, '#mpLicenseSection');
            } else {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Upload failed.';
                if (window.toastr) toastr.error(msg); else alert(msg);
            }
        }).always(function () {
            $('#mpUploadSpinner').addClass('d-none');
            $('#mpUploadSubmitBtn').prop('disabled', false);
        });
    }

    /**
     * For edit-form AJAX populate callbacks: fills a picker field's hidden
     * value + preview from data already fetched (path = bare relative path
     * for the hidden input, url = ready-made display URL from storage_link()).
     *   window.setMediaPickerValue('banner_image', data.banner_image_path, data.banner_image);
     */
    window.setMediaPickerValue = function (name, path, url) {
        var $block = $('.media-picker-block[data-field-name="' + name + '"]');
        if (!$block.length) return;
        $block.find('.media-picker-value').val(path || '');
        if (path && url) {
            $block.find('.media-picker-preview img').attr('src', url);
            $block.find('.media-picker-preview').removeClass('d-none');
        } else {
            $block.find('.media-picker-preview').addClass('d-none');
        }
    };

    /**
     * Build the same field markup as a string, for JS-driven dynamic rows
     * (e.g. a template function that injects a form section via innerHTML).
     * `pickerId` must be unique on the page, same rules as the picker-id prop.
     */
    window.mediaPickerFieldHtml = function (name, pickerId, label, folder, dataKey, dataCardKey) {
        label = label || 'Image';
        return '' +
            '<div class="media-picker-block" data-field-name="' + pickerId + '" data-folder="' + (folder || '') + '">' +
                (label ? '<label class="form-label form-label-sm mb-1 d-block">' + label + '</label>' : '') +
                '<input type="hidden" class="media-picker-value" name="' + name + '" value=""' +
                    (dataKey ? ' data-key="' + dataKey + '"' : '') +
                    (dataCardKey ? ' data-card-key="' + dataCardKey + '"' : '') +
                '>' +
                '<div class="media-picker-preview d-none mb-2">' +
                    '<img src="" alt="">' +
                    '<button type="button" class="media-picker-clear" title="Remove"><i class="fas fa-times"></i></button>' +
                '</div>' +
                '<button type="button" class="form-control media-picker-choose text-start">' +
                    '<i class="fas fa-images"></i> Choose Image' +
                '</button>' +
            '</div>';
    };
})();
</script>
@endonce
