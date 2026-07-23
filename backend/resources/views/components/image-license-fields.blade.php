@props([
    'name' => 'license',
    'license' => null,
    'label' => 'Image License Details',
    'errorBag' => null,
])

@php
    $existing = $license; // ImageLicense model or null
    $errBag = $errorBag ?? ($errors ?? null);
    $uid = 'lic_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $name) . '_' . substr(md5($name . microtime()), 0, 6);
    $err = fn ($field) => $errBag && method_exists($errBag, 'first') ? $errBag->first("{$name}.{$field}") : null;
    $hasAnyError = $err('source_of_image') || $err('download_date') || $err('license_file');
    $hasData = $existing && ($existing->source_of_image || $existing->download_date || $existing->account_id || $existing->license_key || $existing->license_key_file);
    $expanded = $hasData || $hasAnyError;
@endphp

<div class="image-license-block" data-field-name="{{ $name }}">
    <button type="button" class="btn license-toggle-btn d-inline-flex align-items-center gap-2"
            data-bs-toggle="collapse" data-bs-target="#{{ $uid }}"
            aria-expanded="{{ $expanded ? 'true' : 'false' }}" aria-controls="{{ $uid }}">
        <i class="fas fa-shield-alt"></i>
        <span>{{ $label }}</span>
        <span class="badge license-status-badge {{ $hasData ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
            {{ $hasData ? 'Added' : 'Not set' }}
        </span>
        <i class="fas fa-chevron-down license-toggle-chevron"></i>
    </button>

    <div class="collapse {{ $expanded ? 'show' : '' }}" id="{{ $uid }}">
        <div class="border rounded p-3 mt-2 bg-light">
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label form-label-sm mb-1">Source of Image <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm license-source @if($err('source_of_image')) is-invalid @endif"
                           name="{{ $name }}[source_of_image]"
                           value="{{ old("{$name}.source_of_image", $existing->source_of_image ?? '') }}"
                           placeholder="e.g. Shutterstock, Unsplash">
                    @if($err('source_of_image'))
                        <div class="invalid-feedback d-block">{{ $err('source_of_image') }}</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label form-label-sm mb-1">Download Date <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm license-download-date @if($err('download_date')) is-invalid @endif"
                           name="{{ $name }}[download_date]"
                           value="{{ old("{$name}.download_date", $existing?->download_date?->format('Y-m-d') ?? '') }}"
                           placeholder="YYYY-MM-DD" autocomplete="off" readonly>
                    @if($err('download_date'))
                        <div class="invalid-feedback d-block">{{ $err('download_date') }}</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label form-label-sm mb-1">Account ID</label>
                    <input type="text" class="form-control form-control-sm license-account"
                           name="{{ $name }}[account_id]"
                           value="{{ old("{$name}.account_id", $existing->account_id ?? '') }}"
                           placeholder="Account / Contributor ID">
                </div>
                <div class="col-md-6">
                    <label class="form-label form-label-sm mb-1">License Key</label>
                    <input type="text" class="form-control form-control-sm license-key-input"
                           name="{{ $name }}[license_key]"
                           value="{{ old("{$name}.license_key", $existing->license_key ?? '') }}"
                           placeholder="License / Serial Key">
                </div>
                <div class="col-md-12">
                    <label class="form-label form-label-sm mb-1">License Document <small class="text-muted">(PDF/JPG/PNG, max 2 MB)</small></label>
                    <div class="mb-1 license-current-doc {{ $existing?->license_key_file ? '' : 'd-none' }}">
                        <a href="{{ $existing?->license_key_file ? storage_link($existing->license_key_file) : '#' }}"
                           target="_blank" class="small license-current-doc-link">
                            <i class="fas fa-file-pdf me-1"></i>View current document
                        </a>
                    </div>
                    <input type="file" class="form-control form-control-sm license-file @if($err('license_file')) is-invalid @endif"
                           name="{{ $name }}[license_file]"
                           data-has-existing="{{ $existing?->license_key_file ? '1' : '0' }}"
                           accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png">
                    @if($err('license_file'))
                        <div class="invalid-feedback d-block">{{ $err('license_file') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@once
<style>
    .license-toggle-btn {
        border: none;
        background: transparent;
        padding: 4px 2px;
        color: #64748b;
        font-weight: 600;
        font-size: .82rem;
    }
    .license-toggle-btn:hover { color: #2563eb; }
    .license-toggle-btn i.fa-shield-alt { font-size: .8rem; }
    .license-status-badge { font-size: .68rem; font-weight: 600; vertical-align: middle; }
    .license-toggle-chevron { font-size: .7rem; transition: transform .2s ease; }
    .license-toggle-btn[aria-expanded="true"] .license-toggle-chevron { transform: rotate(180deg); }
</style>
<script>
(function () {
    // ── Eagerly attach a datepicker to any .license-download-date field, however/whenever it lands in the DOM ──
    // Native `focus` doesn't bubble, so a delegated $(document).on('focus', selector, ...)
    // handler (the previous approach here) never actually fires — only `click` did, and
    // only for a plain mouse click, not Tab-into-field or programmatic focus. Instead we
    // initialize jQuery UI's datepicker directly on the element itself as soon as it's
    // in the DOM (on ready, on any Bootstrap modal being shown, and via MutationObserver
    // for anything added later, mirroring the TinyMCE init pattern below). Once
    // `.datepicker()` has run, jQuery UI binds its own real (non-delegated) focus handler
    // straight onto the element, which fires correctly for every future open.
    //
    // Being inside a Bootstrap modal (itself `position: fixed`), jQuery UI's own
    // internal offsetParent-walking logic detects the fixed ancestor and switches
    // the calendar itself to `position: fixed` — so beforeShow must re-position it
    // in VIEWPORT-relative coordinates (getBoundingClientRect()), not document-
    // relative ones (jQuery's .offset(), which bakes in the page's scroll offset).
    // Using .offset() here previously meant the calendar rendered `window.scrollY`
    // pixels too low — invisible (off-screen) on any page scrolled down at all,
    // which is exactly why it looked broken specifically on tabs/rows further down
    // a long page (e.g. City list) while working fine near the top.
    function positionDatepicker($dp, $input) {
        var rect = $input[0].getBoundingClientRect();
        $dp.css({ top: (rect.bottom) + 'px', left: rect.left + 'px' });
    }

    function initDatepickerOn($el) {
        if (!$el || !$el.length || $el.data('dp-init') || !$.fn.datepicker) return;
        $el.data('dp-init', true);
        $el.datepicker({
            dateFormat: 'yy-mm-dd', maxDate: 0, changeMonth: true, changeYear: true, yearRange: '2000:+0',
            beforeShow: function (input, inst) {
                setTimeout(function () { positionDatepicker(inst.dpDiv, $(input)); }, 0);
            }
        });
    }

    function initAllDatepickers(scope) {
        var $scope = scope ? $(scope) : $(document);
        if ($scope.is('.license-download-date')) initDatepickerOn($scope);
        $scope.find('.license-download-date').each(function () { initDatepickerOn($(this)); });
    }

    $(document).ready(function () { initAllDatepickers(); });

    $(document).on('shown.bs.modal', function (ev) { initAllDatepickers(ev.target); });

    var licenseDpObserver = new MutationObserver(function (mutations) {
        mutations.forEach(function (m) {
            m.addedNodes.forEach(function (n) {
                if (n.nodeType !== 1) return;
                initAllDatepickers(n);
            });
        });
    });
    licenseDpObserver.observe(document.body, { childList: true, subtree: true });

    // The "Image License Details" section itself is a Bootstrap collapse. If the
    // user clicks straight into the Download Date field while that collapse is
    // still mid-expand (very natural — click the toggle, then immediately click
    // the field), beforeShow's offset snapshot lands on the field's in-transit
    // position, not its resting one, so the calendar renders detached/overlapping
    // the field instead of anchored under it. Once the collapse genuinely finishes
    // expanding, re-snap the calendar to the field's real, final position.
    $(document).on('shown.bs.collapse', '.image-license-block .collapse', function () {
        var $input = $(this).find('.license-download-date');
        var $dp = $('#ui-datepicker-div');
        if (!$input.length || !$dp.length || !$dp.is(':visible')) return;
        if (!$.datepicker._curInst || !$.datepicker._curInst.input || $.datepicker._curInst.input[0] !== $input[0]) return;
        positionDatepicker($dp, $input);
    });

    function setLicenseBadge($block, hasData) {
        $block.find('.license-status-badge')
            .toggleClass('bg-success-subtle text-success', hasData)
            .toggleClass('bg-secondary-subtle text-secondary', !hasData)
            .text(hasData ? 'Added' : 'Not set');
    }

    function setCollapse($block, expand) {
        var el = $block.find('.collapse')[0];
        if (!el || typeof bootstrap === 'undefined') return;
        bootstrap.Collapse.getOrCreateInstance(el, { toggle: false })[expand ? 'show' : 'hide']();
    }

    /**
     * For shared/AJAX-driven modals (one static block reused for whichever row you clicked —
     * e.g. Region/State/City "Page Settings"): fill a block's fields from a fetched license
     * object. Call this after your $.get(...) resolves, once per block on the page:
     *
     *   window.populateImageLicenseBlock('banner_license', data.banner_license);
     *
     * `lic` may be null/undefined (nothing saved yet) — fields are just cleared and the
     * section collapses back down; if there IS saved data, the section auto-expands.
     */
    window.populateImageLicenseBlock = function (name, lic) {
        var $block = $('.image-license-block[data-field-name="' + name + '"]');
        if (!$block.length) return;
        lic = lic || {};
        $block.find('.invalid-feedback').remove();
        $block.find('.license-source').val(lic.source_of_image || '').removeClass('is-invalid');
        $block.find('.license-download-date').val(lic.download_date || '').removeClass('is-invalid');
        $block.find('.license-account').val(lic.account_id || '');
        $block.find('.license-key-input').val(lic.license_key || '');
        var $file = $block.find('.license-file').val('').removeClass('is-invalid');
        $file.attr('data-has-existing', lic.license_key_file_url ? '1' : '0');
        $block.find('.license-current-doc')
            .toggleClass('d-none', !lic.license_key_file_url)
            .find('.license-current-doc-link').attr('href', lic.license_key_file_url || '#');

        var hasData = !!(lic.source_of_image || lic.download_date || lic.account_id || lic.license_key || lic.license_key_file_url);
        setLicenseBadge($block, hasData);
        setCollapse($block, hasData);
    };

    /** Clear a block back to its empty, collapsed state — e.g. before opening an "Add new" form. */
    window.resetImageLicenseBlock = function (name) {
        window.populateImageLicenseBlock(name, null);
    };

    /**
     * Validate every .image-license-block currently on the page (or pass a jQuery/DOM
     * subtree to scope it). Returns true if all pass; expands the section if it was
     * collapsed, shows one SweetAlert, and scrolls to the first problem otherwise.
     * Safe to call from any form's "publish" submit handler:
     *
     *   if (!window.validateImageLicenseBlocks()) return;
     */
    window.validateImageLicenseBlocks = function (scope) {
        var ok = true;
        var $blocks = scope ? $(scope).find('.image-license-block') : $('.image-license-block');
        $blocks.each(function () {
            if (!ok) return; // stop at the first problem so we don't spam alerts
            var $block = $(this);
            var label = $block.data('field-name') || 'this image';
            var $source = $block.find('.license-source');
            var $date = $block.find('.license-download-date');
            var $account = $block.find('.license-account');
            var $key = $block.find('.license-key-input');
            var $file = $block.find('.license-file');

            var fail = function ($field, message) {
                ok = false;
                $field.addClass('is-invalid');
                setCollapse($block, true);
                if (window.Swal) Swal.fire('Missing Information', message, 'warning');
                setTimeout(function () {
                    $('html,body').animate({ scrollTop: $field.offset().top - 100 }, 400);
                }, 150);
            };

            if (!$source.val() || !$source.val().trim()) {
                fail($source, 'Please enter Source of Image (' + label + ').');
                return;
            }
            if (!$date.val() || !$date.val().trim()) {
                fail($date, 'Please enter Download Date (' + label + ').');
                return;
            }
            var hasExistingFile = $file.data('has-existing') == '1' || $file.attr('data-has-existing') === '1';
            var hasNewFile = $file.length && $file[0].files && $file[0].files.length > 0;
            if (!$account.val() && !$key.val() && !hasNewFile && !hasExistingFile) {
                fail($file, 'Provide an Account ID, License Key, or upload the License Document (' + label + ').');
            }
        });
        return ok;
    };

    /**
     * Build the same field markup as a string, for JS-driven dynamic rows
     * (e.g. an "Add gallery image" button). `name` should already include
     * the final index, e.g. "gallery_source[3]".
     */
    window.imageLicenseFieldsHtml = function (name, label) {
        label = label || 'Image License Details';
        var uid = 'lic_dyn_' + Math.random().toString(36).slice(2, 9);
        return '' +
            '<div class="image-license-block" data-field-name="' + name + '">' +
                '<button type="button" class="btn license-toggle-btn d-inline-flex align-items-center gap-2" data-bs-toggle="collapse" data-bs-target="#' + uid + '" aria-expanded="false" aria-controls="' + uid + '">' +
                    '<i class="fas fa-shield-alt"></i>' +
                    '<span>' + label + '</span>' +
                    '<span class="badge license-status-badge bg-secondary-subtle text-secondary">Not set</span>' +
                    '<i class="fas fa-chevron-down license-toggle-chevron"></i>' +
                '</button>' +
                '<div class="collapse" id="' + uid + '">' +
                    '<div class="border rounded p-3 mt-2 bg-light">' +
                        '<div class="row g-2">' +
                            '<div class="col-md-6">' +
                                '<label class="form-label form-label-sm mb-1">Source of Image <span class="text-danger">*</span></label>' +
                                '<input type="text" class="form-control form-control-sm license-source" name="' + name + '[source_of_image]" placeholder="e.g. Shutterstock, Unsplash">' +
                            '</div>' +
                            '<div class="col-md-6">' +
                                '<label class="form-label form-label-sm mb-1">Download Date <span class="text-danger">*</span></label>' +
                                '<input type="text" class="form-control form-control-sm license-download-date" name="' + name + '[download_date]" placeholder="YYYY-MM-DD" autocomplete="off" readonly>' +
                            '</div>' +
                            '<div class="col-md-6">' +
                                '<label class="form-label form-label-sm mb-1">Account ID</label>' +
                                '<input type="text" class="form-control form-control-sm license-account" name="' + name + '[account_id]" placeholder="Account / Contributor ID">' +
                            '</div>' +
                            '<div class="col-md-6">' +
                                '<label class="form-label form-label-sm mb-1">License Key</label>' +
                                '<input type="text" class="form-control form-control-sm license-key-input" name="' + name + '[license_key]" placeholder="License / Serial Key">' +
                            '</div>' +
                            '<div class="col-md-12">' +
                                '<label class="form-label form-label-sm mb-1">License Document <small class="text-muted">(PDF/JPG/PNG, max 2 MB)</small></label>' +
                                '<input type="file" class="form-control form-control-sm license-file" data-has-existing="0" name="' + name + '[license_file]" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png">' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';
    };

    // Keep the badge live as the admin types/uploads, so they don't have to submit to see it register.
    $(document).on('input change', '.image-license-block input', function () {
        var $block = $(this).closest('.image-license-block');
        var $file = $block.find('.license-file');
        var hasData = !!(
            $block.find('.license-source').val() ||
            $block.find('.license-download-date').val() ||
            $block.find('.license-account').val() ||
            $block.find('.license-key-input').val() ||
            ($file.length && $file[0].files && $file[0].files.length > 0) ||
            $file.attr('data-has-existing') === '1'
        );
        setLicenseBadge($block, hasData);
    });
})();
</script>
@endonce
