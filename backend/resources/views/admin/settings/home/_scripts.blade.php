<script>
/* ════════════════════════════════════════════════════════════
   HOME PAGE CMS — All tab-specific JavaScript
   Loaded after the shared tab-switcher & saveSectionHeading()
   defined in home.blade.php @section('scripts')
════════════════════════════════════════════════════════════ */
$(function () {

/* ─────────────────────────────────────────────────────────
   UTILITY HELPERS
───────────────────────────────────────────────────────── */
function confirmDelete(msg, onConfirmed) {
    Swal.fire({
        title: 'Are you sure?',
        text: msg || 'Are you sure you want to delete this item?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e3342f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it',
    }).then(function (result) {
        if (result.isConfirmed && typeof onConfirmed === 'function') {
            onConfirmed();
        }
    });
}
function btnLoading($btn, isLoading) {
    if (isLoading) {
        $btn.prop('disabled', true)
            .data('html', $btn.html())
            .html('<span class="spinner-border spinner-border-sm me-1"></span>Wait…');
    } else {
        $btn.prop('disabled', false).html($btn.data('html') || $btn.html());
    }
}
/* Bootstrap 5 modal helpers */
if (typeof cmsShowModal === 'undefined') {
    window.cmsShowModal = function (id) {
        var el = document.getElementById(id);
        if (!el) return;
        bootstrap.Modal.getOrCreateInstance(el).show();
    };
    window.cmsHideModal = function (id) {
        var el = document.getElementById(id);
        if (!el) return;
        var m = bootstrap.Modal.getInstance(el);
        if (m) m.hide();
    };
}


/* ═══════════════════════════════════════════════════════════
   ① HERO TAB — Section heading + Banner CRUD
═══════════════════════════════════════════════════════════ */

/* ── Banner Sortable ── */
if (document.getElementById('banner-sortable')) {
    new Sortable(document.getElementById('banner-sortable'), {
        handle: '.banner-drag-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        onEnd: function () {
            var ids = [];
            $('#banner-sortable tr[data-id]').each(function () {
                ids.push($(this).data('id'));
            });
            $.post('{{ route("admin.banners.reorder") }}', {
                _token: CSRF, ids: ids
            }).done(function (r) {
                if (r.success) toastr.success('Slide order updated.');
            });
        }
    });
}

/* ── Add Banner ── */
$(document).on('click', '#add-banner-btn', function () {
    $('#addBannerForm')[0].reset();
    if (typeof window.setMediaPickerValue === 'function') {
        window.setMediaPickerValue('banner_image_add', '', null);
    }
    cmsShowModal('addBannerModal');
});

$('#addBannerForm').on('submit', function (e) {
    e.preventDefault();
    if (!$('#addBannerForm input[name=banner_image]').val()) {
        toastr.warning('Please choose a Banner Image.');
        return;
    }
    var $btn = $(this).find('.banner-submit-btn');
    var fd   = new FormData(this);
    btnLoading($btn, true);
    $.ajax({
        url         : '{{ route("admin.banners.store") }}',
        type        : 'POST',
        data        : fd,
        processData : false,
        contentType : false,
        headers     : { 'X-CSRF-TOKEN': CSRF },
    }).done(function (r) {
        if (r.success) {
            toastr.success('Banner added!');
            cmsHideModal('addBannerModal');
            reloadBannerTable();
        } else {
            toastr.error('Failed to add banner.');
        }
    }).fail(function (xhr) {
        if (xhr.responseJSON && xhr.responseJSON.errors) {
            window.showFormErrors(xhr.responseJSON.errors, { scope: '#addBannerForm', fallback: 'Upload failed.' });
        } else {
            toastr.error((xhr.responseJSON && xhr.responseJSON.message) || 'Upload failed.');
        }
    }).always(function () { btnLoading($btn, false); });
});

/* ── Edit Banner ── */
$(document).on('click', '.edit-banner', function () {
    var $btn = $(this), url = $btn.data('url');
    $btn.find('.spinner-border').removeClass('d-none');
    $btn.find('.icon').addClass('d-none');
    $.get(url, function (b) {
        $('#edit_banner_title').val(b.title);
        $('#edit_banner_subtitle').val(b.subtitle || '');
        $('#edit_banner_button_text').val(b.button_text || '');
        $('#edit_banner_url').val(b.url || '');
        $('#edit_banner_image_alt').val(b.banner_image_alt || '');
        if (typeof window.setMediaPickerValue === 'function') {
            window.setMediaPickerValue('banner_image_edit', b.banner_image_path, b.banner_image);
        }
        $('#editBannerForm').data('url', '{{ url("admin/banners") }}/' + b.id);
        cmsShowModal('editBannerModal');
    }).always(function () {
        $btn.find('.spinner-border').addClass('d-none');
        $btn.find('.icon').removeClass('d-none');
    });
});

$('#editBannerForm').on('submit', function (e) {
    e.preventDefault();
    if (!$('#editBannerForm input[name=banner_image]').val()) {
        toastr.warning('Please choose a Banner Image.');
        return;
    }
    var $btn = $(this).find('.edit-banner-submit-btn');
    var fd   = new FormData(this);
    btnLoading($btn, true);
    $.ajax({
        url         : $(this).data('url'),
        type        : 'POST',
        data        : fd,
        processData : false,
        contentType : false,
        headers     : { 'X-CSRF-TOKEN': CSRF },
    }).done(function (r) {
        if (r.status === 'success' || r.success) {
            toastr.success('Banner updated!');
            cmsHideModal('editBannerModal');
            reloadBannerTable();
        } else {
            toastr.error('Update failed.');
        }
    }).fail(function (xhr) {
        if (xhr.responseJSON && xhr.responseJSON.errors) {
            window.showFormErrors(xhr.responseJSON.errors, { scope: '#editBannerForm', fallback: 'Request failed.' });
        } else {
            toastr.error('Request failed.');
        }
    }).always(function () { btnLoading($btn, false); });
});

function reloadBannerTable() {
    var $tbody = $('#banner-sortable');
    $tbody.html('<tr><td colspan="6" class="text-center py-3"><span class="spinner-border spinner-border-sm text-primary"></span></td></tr>');
    $.get(location.href, function (html) {
        var $rows = $(html).find('#banner-sortable').html();
        if ($rows !== undefined) $tbody.html($rows);
        else location.reload();
    }).fail(function () { location.reload(); });
}

/* ── Delete Banner ── */
$(document).on('click', '.delete-banner', function () {
    var $btn = $(this), url = $btn.data('url');
    confirmDelete('Delete this banner slide?', function () {
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.icon').addClass('d-none');
        $.ajax({
            url    : url,
            type   : 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
        }).done(function (r) {
            if (r.success) {
                $btn.closest('tr').fadeOut(300, function () { $(this).remove(); });
                toastr.success('Banner deleted.');
            } else {
                toastr.error('Delete failed.');
            }
        }).fail(function () { toastr.error('Request failed.'); })
          .always(function () {
              $btn.find('.spinner-border').addClass('d-none');
              $btn.find('.icon').removeClass('d-none');
          });
    });
});

/* ── Banner Status Toggle ── */
$(document).on('change', '.banner-status-toggle', function () {
    var $cb = $(this), url = $cb.data('url'), val = $cb.prop('checked') ? 1 : 0;
    $.ajax({
        url    : url,
        type   : 'PUT',
        data   : { _method: 'PUT', status: val },
        headers: { 'X-CSRF-TOKEN': CSRF },
    }).done(function (r) {
        if (r.status === 'error') {
            toastr.warning(r.message);
            $cb.prop('checked', true);
        } else {
            toastr.success('Status updated.');
        }
    }).fail(function () {
        $cb.prop('checked', !$cb.prop('checked'));
        toastr.error('Request failed.');
    });
});


/* ═══════════════════════════════════════════════════════════
   ② PACKAGES TAB — Heading save
═══════════════════════════════════════════════════════════ */

$('#packages-heading-save').on('click', function () {
    saveSectionHeading('india_tours', $('#packages-heading-form'), $(this));
});


/* ═══════════════════════════════════════════════════════════
   ③ CUSTOMIZED TAB — Heading + Checkbox package selection
═══════════════════════════════════════════════════════════ */

$('#customized-heading-save').on('click', function () {
    saveSectionHeading('customized_tours', $('#customized-heading-form'), $(this));
});

/* Sync hidden IDs input whenever a checkbox changes */
function syncCustomizedIds() {
    var ids = [];
    $('.cust-pkg-check:checked').each(function () {
        ids.push(parseInt($(this).val()));
    });
    $('#customized-selected-ids').val(JSON.stringify(ids));
}

/* Toggle card highlight on click */
$(document).on('change', '.cust-pkg-check', function () {
    var $card = $(this).closest('.cust-pkg-card');
    if ($(this).is(':checked')) {
        $card.addClass('border-primary bg-primary bg-opacity-10');
    } else {
        $card.removeClass('border-primary bg-primary bg-opacity-10');
    }
    syncCustomizedIds();
});

/* Package search filter */
$('#cust-pkg-search').on('input', function () {
    var q = $(this).val().toLowerCase().trim();
    $('.cust-pkg-item').each(function () {
        var title = $(this).data('title') || '';
        $(this).toggle(!q || title.indexOf(q) !== -1);
    });
});

/* Bulk save — send IDs as JSON array in extra_data */
$('#customized-pkg-save').on('click', function () {
    var $btn = $(this);
    syncCustomizedIds();
    var ids = [];
    try { ids = JSON.parse($('#customized-selected-ids').val()) || []; } catch (e) {}
    btnLoading($btn, true);
    $.ajax({
        url    : '{{ route("admin.home-sections.customized-packages") }}',
        type   : 'POST',
        data   : { _token: CSRF, ids: ids },
    }).done(function (r) {
        if (r.success) {
            toastr.success(r.message || 'Customized packages updated.');
        } else {
            toastr.error('Save failed.');
        }
    }).fail(function () { toastr.error('Request failed.'); })
      .always(function () { btnLoading($btn, false); });
});


/* ═══════════════════════════════════════════════════════════
   ④ ABOUT TAB — Left-side content save + Features CRUD
═══════════════════════════════════════════════════════════ */

/* ── Sync all TinyMCE editors in About tab ── */
function syncAboutTinyMCE() {
    if (typeof tinymce === 'undefined') return;
    ['about-description', 'about-right-text'].forEach(function (id) {
        var ed = tinymce.get(id);
        if (ed) ed.save();
    });
}

/* ── Save left-side settings ── */
$('#about-heading-save').on('click', function () {
    var $btn  = $(this);
    var $form = $('#about-heading-form');
    syncAboutTinyMCE();

    var fd = new FormData();
    fd.append('_method',     'PUT');
    fd.append('title',       $form.find('[name=title]').val());
    fd.append('description', $form.find('[name=description]').val());
    fd.append('button_text', $form.find('[name=button_text]').val());
    fd.append('button_url',  $form.find('[name=button_url]').val());
    fd.append('image_alt',   $form.find('[name=image_alt]').val());
    fd.append('image',       $form.find('[name=image]').val());
    btnLoading($btn, true);
    $.ajax({
        url: '{{ url("admin/home-sections") }}/about_intro',
        type: 'POST', data: fd,
        processData: false, contentType: false,
        headers: { 'X-CSRF-TOKEN': CSRF },
    }).done(function (r) {
        if (r.success) {
            toastr.success('Left side settings saved.');
        } else { window.showFormErrors(r.errors, { scope: $form, fallback: 'Save failed.' }); }
    }).fail(function (xhr) {
        window.showFormErrors(xhr.responseJSON && xhr.responseJSON.errors, { scope: $form, fallback: 'Request failed.' });
    }).always(function () { btnLoading($btn, false); });
});

/* ── Save right-side master text ── */
$('#about-right-text-save').on('click', function () {
    var $btn = $(this);
    syncAboutTinyMCE();
    var fd = new FormData();
    fd.append('_method',         'PUT');
    fd.append('right_side_text', $('#about-right-text').val());
    btnLoading($btn, true);
    $.ajax({
        url: '{{ url("admin/home-sections") }}/about_intro',
        type: 'POST', data: fd,
        processData: false, contentType: false,
        headers: { 'X-CSRF-TOKEN': CSRF },
    }).done(function (r) {
        if (r.success) toastr.success('Master text saved.');
        else toastr.error('Save failed.');
    }).fail(function () { toastr.error('Request failed.'); })
      .always(function () { btnLoading($btn, false); });
});


/* ── Features CRUD ── */
var FEAT_BASE = '{{ url("admin/home-about/features") }}';

/* Sortable */
if (document.getElementById('features-table-body')) {
    new Sortable(document.getElementById('features-table-body'), {
        handle: '.feat-drag-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        filter: '#features-empty',
        onEnd: function () {
            var order = [];
            $('#features-table-body tr[data-id]').each(function (idx) {
                order.push({ id: parseInt($(this).data('id')), sort_order: idx + 1 });
            });
            $.ajax({
                url         : FEAT_BASE + '/reorder',
                type        : 'POST',
                data        : JSON.stringify({ order: order }),
                contentType : 'application/json',
                headers     : { 'X-CSRF-TOKEN': CSRF },
            }).done(function (r) {
                if (r.success) toastr.success('Feature order saved.');
            });
        }
    });
}

/* Icon live preview */
$('#feature_icon').on('input', function () {
    $('#feature-icon-preview').removeClass().addClass($(this).val() || 'fas fa-check-circle');
});

/* Open Add modal */
$(document).on('click', '#open-add-feature-btn', function () {
    $('#feature_id').val('');
    $('#feature-modal-title').text('Add Feature');
    $('#featureForm')[0].reset();
    $('#feature_icon').val('fas fa-check-circle');
    $('#feature-icon-preview').removeClass().addClass('fas fa-check-circle');
    cmsShowModal('addFeatureModal');
});

/* Submit (Add / Edit) */
$('#featureForm').on('submit', function (e) {
    e.preventDefault();
    var $btn   = $(this).find('.feature-submit-btn');
    var fid    = $('#feature_id').val();
    var url    = fid ? FEAT_BASE + '/' + fid : FEAT_BASE;
    var method = fid ? 'PUT' : 'POST';
    btnLoading($btn, true);
    $.ajax({
        url    : url,
        type   : method,
        data   : {
            icon_class          : $('#feature_icon').val() || 'fas fa-check-circle',
            text                : $('#feature_text').val(),
            feature_description : $('#feature_description').val(),
            sort_order          : $('#feature_sort').val(),
        },
        headers: { 'X-CSRF-TOKEN': CSRF },
    }).done(function (r) {
        if (r.success) {
            toastr.success(r.message || 'Feature saved.');
            cmsHideModal('addFeatureModal');
            reloadFeaturesList();
        } else { toastr.error('Save failed.'); }
    }).fail(function (xhr) {
        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error.';
        toastr.error(msg);
    }).always(function () { btnLoading($btn, false); });
});

/* Open Edit modal */
$(document).on('click', '.edit-feature', function () {
    var d = $(this).data();
    $('#feature_id').val(d.id);
    $('#feature_text').val(d.text || '');
    $('#feature_icon').val(d.icon || 'fas fa-check-circle').trigger('input');
    $('#feature_description').val(d.description || '');
    $('#feature_sort').val(d.sort || 0);
    $('#feature-modal-title').text('Edit Feature');
    cmsShowModal('addFeatureModal');
});

/* Delete */
$(document).on('click', '.delete-feature', function () {
    var $row = $(this).closest('tr'), id = $(this).data('id');
    confirmDelete('Delete this feature?', function () {
        $.ajax({
            url    : FEAT_BASE + '/' + id,
            type   : 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
        }).done(function (r) {
            if (r.success) {
                $row.fadeOut(200, function () { $(this).remove(); });
                toastr.success('Feature deleted.');
            } else { toastr.error('Delete failed.'); }
        }).fail(function () { toastr.error('Request failed.'); });
    });
});

/* Rebuild table from JSON */
function reloadFeaturesList() {
    $.get(FEAT_BASE, function (r) {
        if (!r.success) return;
        var $tbody = $('#features-table-body');
        $tbody.html('');
        if (!r.data.length) {
            $tbody.html('<tr id="features-empty"><td colspan="6" class="text-center text-muted py-5">' +
                '<i class="fas fa-th-list fa-2x opacity-25 mb-2 d-block"></i>' +
                'No features yet. Click <strong>Add Feature</strong>.</td></tr>');
            return;
        }
        r.data.forEach(function (f) { $tbody.append(buildFeatureRow(f)); });
    });
}

function buildFeatureRow(f) {
    var ic  = f.icon_class || 'fas fa-check-circle';
    var esc = function (s) { return $('<div>').text(s || '').html(); };
    return '<tr data-id="' + f.id + '">' +
        '<td class="feat-drag-handle text-center" style="cursor:grab;"><i class="fas fa-grip-vertical text-muted"></i></td>' +
        '<td class="text-center"><i class="' + ic + '" style="font-size:18px;color:#2563eb;"></i></td>' +
        '<td class="small fw-semibold">' + esc(f.text) + '</td>' +
        '<td class="small text-muted text-truncate" style="max-width:220px;">' + esc(f.feature_description) + '</td>' +
        '<td class="small text-center">' + f.sort_order + '</td>' +
        '<td>' +
        '<button class="btn btn-xs btn-outline-primary edit-feature me-1" data-id="' + f.id + '" ' +
        'data-text="' + esc(f.text) + '" data-icon="' + esc(f.icon_class) + '" ' +
        'data-description="' + esc(f.feature_description) + '" ' +
        'data-sort="' + f.sort_order + '" title="Edit"><i class="fas fa-edit"></i></button>' +
        '<button class="btn btn-xs btn-outline-danger delete-feature" data-id="' + f.id + '" title="Delete">' +
        '<i class="fas fa-trash"></i></button>' +
        '</td></tr>';
}


/* ═══════════════════════════════════════════════════════════
   ⑤ WHY TAB — Title, Subtitle, Image, Alt Tag
═══════════════════════════════════════════════════════════ */

/* Save */
$('#why-heading-save').on('click', function () {
    var $btn  = $(this);
    var $form = $('#why-heading-form');

    var fd = new FormData();
    fd.append('_method',    'PUT');
    fd.append('title',      $form.find('[name=title]').val());
    fd.append('subtitle',   $form.find('[name=subtitle]').val());
    fd.append('image_alt',  $form.find('[name=image_alt]').val());
    fd.append('image',      $form.find('[name=image]').val());
    btnLoading($btn, true);
    $.ajax({
        url         : '{{ url("admin/home-sections") }}/why_choose',
        type        : 'POST',
        data        : fd,
        processData : false,
        contentType : false,
        headers     : { 'X-CSRF-TOKEN': CSRF },
    }).done(function (r) {
        if (r.success) {
            toastr.success('Why section saved.');
        } else {
            window.showFormErrors(r.errors, { scope: $form, fallback: 'Save failed.' });
        }
    }).fail(function (xhr) {
        window.showFormErrors(xhr.responseJSON && xhr.responseJSON.errors, { scope: $form, fallback: 'Request failed.' });
    }).always(function () { btnLoading($btn, false); });
});


/* ═══════════════════════════════════════════════════════════
   ⑥ BLOGS TAB — Heading save + Blog Items CRUD
═══════════════════════════════════════════════════════════ */

var BLOG_BASE = '{{ url("admin/home-blog-items") }}';

$('#blogs-heading-save').on('click', function () {
    saveSectionHeading('latest_blogs', $('#blogs-heading-form'), $(this));
});

/* ── Sortable ── */
if (document.getElementById('blog-items-list')) {
    new Sortable(document.getElementById('blog-items-list'), {
        handle: '.blog-drag-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        filter: '#blog-items-empty',
        onEnd: function () {
            var order = [];
            $('#blog-items-list tr[data-id]').each(function (idx) {
                order.push({ id: parseInt($(this).data('id')), sort_order: idx + 1 });
            });
            $.ajax({
                url         : BLOG_BASE + '/reorder/save',
                type        : 'POST',
                data        : JSON.stringify({ order: order }),
                contentType : 'application/json',
                headers     : { 'X-CSRF-TOKEN': CSRF },
            }).done(function (r) {
                if (r.success) toastr.success('Blog order updated.');
            });
        }
    });
}

/* ── Open Add modal ── */
$(document).on('click', '#add-blog-item-btn', function () {
    $('#addBlogItemForm')[0].reset();
    $('#blog_title').val('');
    if (typeof window.setMediaPickerValue === 'function') {
        window.setMediaPickerValue('blog_image_add', '', null);
    }
    cmsShowModal('addBlogItemModal');
});

/* ── Store ── */
$('#addBlogItemForm').on('submit', function (e) {
    e.preventDefault();
    if (!$('#addBlogItemForm input[name=image]').val()) {
        toastr.warning('Please choose a Blog Image.');
        return;
    }
    var $btn = $(this).find('.blog-item-submit-btn');
    var fd   = new FormData(this);
    btnLoading($btn, true);
    $.ajax({
        url         : BLOG_BASE,
        type        : 'POST',
        data        : fd,
        processData : false,
        contentType : false,
        headers     : { 'X-CSRF-TOKEN': CSRF },
    }).done(function (r) {
        if (r.success) {
            toastr.success('Blog added!');
            cmsHideModal('addBlogItemModal');
            reloadBlogList();
        } else {
            window.showFormErrors(r.errors, { scope: '#addBlogItemForm', fallback: 'Failed to add blog.' });
        }
    }).fail(function (xhr) {
        window.showFormErrors(xhr.responseJSON && xhr.responseJSON.errors, { scope: '#addBlogItemForm', fallback: 'Upload failed.' });
    }).always(function () { btnLoading($btn, false); });
});

/* ── Open Edit modal ── */
$(document).on('click', '.edit-blog-item', function () {
    var url = BLOG_BASE + '/' + $(this).data('id');
    $.get(url, function (r) {
        if (!r.success) { toastr.error('Could not load item.'); return; }
        var d = r.data;
        $('#edit_blog_title').val(d.title || '');
        $('#edit_blog_image_alt').val(d.image_alt || '');
        $('#edit_blog_link').val(d.link || '');
        if (typeof window.setMediaPickerValue === 'function') {
            window.setMediaPickerValue('blog_image_edit', d.image, d.image_url);
        }
        $('#editBlogItemForm').data('url', url);
        cmsShowModal('editBlogItemModal');
    }).fail(function () { toastr.error('Request failed.'); });
});

/* ── Update ── */
$('#editBlogItemForm').on('submit', function (e) {
    e.preventDefault();
    var $btn = $(this).find('.edit-blog-item-submit-btn');
    var fd   = new FormData(this);
    btnLoading($btn, true);
    $.ajax({
        url         : $(this).data('url'),
        type        : 'POST',
        data        : fd,
        processData : false,
        contentType : false,
        headers     : { 'X-CSRF-TOKEN': CSRF },
    }).done(function (r) {
        if (r.success) {
            toastr.success('Blog updated!');
            cmsHideModal('editBlogItemModal');
            reloadBlogList();
        } else {
            window.showFormErrors(r.errors, { scope: '#editBlogItemForm', fallback: 'Update failed.' });
        }
    }).fail(function (xhr) {
        window.showFormErrors(xhr.responseJSON && xhr.responseJSON.errors, { scope: '#editBlogItemForm', fallback: 'Request failed.' });
    }).always(function () { btnLoading($btn, false); });
});

/* ── Delete ── */
$(document).on('click', '.delete-blog-item', function () {
    var $row = $(this).closest('tr'), id = $(this).data('id');
    confirmDelete('Delete this blog item?', function () {
        $.ajax({
            url    : BLOG_BASE + '/' + id,
            type   : 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
        }).done(function (r) {
            if (r.success) {
                $row.fadeOut(200, function () { $(this).remove(); });
                toastr.success('Blog deleted.');
            } else { toastr.error('Delete failed.'); }
        }).fail(function () { toastr.error('Request failed.'); });
    });
});

/* ── Rebuild list from JSON ── */
function reloadBlogList() {
    $.get(BLOG_BASE, function (r) {
        if (!r.success) return;
        var $list = $('#blog-items-list');
        $list.html('');
        if (!r.data.length) {
            $list.html('<tr id="blog-items-empty"><td colspan="6" class="text-center text-muted py-5">' +
                '<i class="fas fa-images fa-2x opacity-25 mb-2 d-block"></i>' +
                'No blog items yet. Click <strong>Add Blog</strong> to get started.</td></tr>');
            return;
        }
        r.data.forEach(function (item) { $list.append(buildBlogRow(item)); });
    });
}

function buildBlogRow(item) {
    var img = item.image_url
        ? '<img src="' + item.image_url + '" alt="' + $('<div>').text(item.image_alt || '').html() + '" style="width:64px;height:46px;object-fit:cover;border-radius:4px;">'
        : '<div style="width:64px;height:46px;background:#f1f5f9;border-radius:4px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-image text-muted opacity-50"></i></div>';
    return '<tr data-id="' + item.id + '">' +
        '<td class="blog-drag-handle text-center" style="cursor:grab;"><i class="fas fa-grip-vertical text-muted"></i></td>' +
        '<td>' + img + '</td>' +
        '<td class="small fw-semibold">' + ($('<div>').text(item.title || '—').html()) + '</td>' +
        '<td class="small">' + ($('<div>').text(item.image_alt || '—').html()) + '</td>' +
        '<td class="small text-truncate" style="max-width:200px;">' + ($('<div>').text(item.link || '—').html()) + '</td>' +
        '<td>' +
        '<button class="btn btn-xs btn-outline-primary edit-blog-item me-1" data-id="' + item.id + '" title="Edit"><i class="fas fa-edit"></i></button>' +
        '<button class="btn btn-xs btn-outline-danger delete-blog-item" data-id="' + item.id + '" title="Delete"><i class="fas fa-trash"></i></button>' +
        '</td></tr>';
}


/* ═══════════════════════════════════════════════════════════
   ⑦ PROMO BANNER TAB — Upload / Edit / Delete / Status toggle
═══════════════════════════════════════════════════════════ */

var PROMO_URL = '{{ url("admin/home-sections") }}/promotional_banner';
@php $promoSecImg = $sections->get('promotional_banner')?->image; @endphp
var PROMO_CURRENT_IMAGE_PATH = {!! json_encode($promoSecImg ?: '') !!};
var PROMO_CURRENT_IMAGE_URL  = {!! json_encode($promoSecImg ? storage_link($promoSecImg) : null) !!};

$('#promo-image-save').on('click', function () {
    var $btn = $(this);
    if (!$('#promo-upload-form input[name=image]').val()) {
        toastr.warning('Please choose a Banner Image.');
        return;
    }
    var fd = new FormData($('#promo-upload-form')[0]);
    fd.append('_method', 'PUT');
    btnLoading($btn, true);
    $.ajax({
        url: PROMO_URL, type: 'POST', data: fd,
        processData: false, contentType: false,
        headers: { 'X-CSRF-TOKEN': CSRF },
    }).done(function (r) {
        if (r.success) {
            toastr.success('Banner uploaded!');
            setTimeout(function () { location.reload(); }, 900);
        } else {
            window.showFormErrors(r.errors, { scope: '#promo-upload-form', fallback: 'Upload failed.' });
        }
    }).fail(function (xhr) {
        window.showFormErrors(xhr.responseJSON && xhr.responseJSON.errors, { scope: '#promo-upload-form', fallback: 'Upload failed.' });
    }).always(function () { btnLoading($btn, false); });
});

/* ── Edit toggle ── */
$('#promo-edit-btn').on('click', function () {
    $('#promo-edit-wrap').slideDown(200);
    $(this).prop('disabled', true);
});

$('#promo-cancel-edit-btn').on('click', function () {
    $('#promo-edit-wrap').slideUp(200);
    $('#promo-edit-btn').prop('disabled', false);
    $('#promo-edit-form')[0].reset();
    if (typeof window.setMediaPickerValue === 'function') {
        window.setMediaPickerValue('promo_image_edit', PROMO_CURRENT_IMAGE_PATH, PROMO_CURRENT_IMAGE_URL);
    }
});

/* ── Save edit ── */
$('#promo-update-btn').on('click', function () {
    var $btn = $(this);
    var fd   = new FormData($('#promo-edit-form')[0]);
    fd.append('_method', 'PUT');
    btnLoading($btn, true);
    $.ajax({
        url: PROMO_URL, type: 'POST', data: fd,
        processData: false, contentType: false,
        headers: { 'X-CSRF-TOKEN': CSRF },
    }).done(function (r) {
        if (r.success) {
            toastr.success('Banner updated!');
            setTimeout(function () { location.reload(); }, 900);
        } else {
            window.showFormErrors(r.errors, { scope: '#promo-edit-form', fallback: 'Update failed.' });
        }
    }).fail(function (xhr) {
        window.showFormErrors(xhr.responseJSON && xhr.responseJSON.errors, { scope: '#promo-edit-form', fallback: 'Request failed.' });
    }).always(function () { btnLoading($btn, false); });
});

/* ── Delete banner ── */
$('#promo-delete-btn').on('click', function () {
    var $btn = $(this);
    confirmDelete('Remove this promo banner image?', function () {
        btnLoading($btn, true);
        $.ajax({
            url    : PROMO_URL + '/image',
            type   : 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
        }).done(function (r) {
            if (r.success) {
                toastr.success('Banner removed.');
                setTimeout(function () { location.reload(); }, 900);
            } else { toastr.error('Delete failed.'); }
        }).fail(function () { toastr.error('Request failed.'); })
          .always(function () { btnLoading($btn, false); });
    });
});

/* ── Status toggle ── */
$('#promo-status-toggle').on('change', function () {
    var $cb  = $(this);
    var isOn = $cb.prop('checked');
    $.ajax({
        url    : PROMO_URL + '/toggle',
        type   : 'PATCH',
        headers: { 'X-CSRF-TOKEN': CSRF },
    }).done(function (r) {
        if (r.success) {
            var label  = r.is_visible ? 'Active' : 'Inactive';
            var badgeCls = r.is_visible ? 'bg-success' : 'bg-secondary';
            $('#promo-status-label').text(label);
            $('#promo-status-badge').removeClass('bg-success bg-secondary').addClass(badgeCls).text(label);
            toastr.success('Banner ' + label.toLowerCase() + '.');
        } else {
            $cb.prop('checked', !isOn);
            toastr.error('Status update failed.');
        }
    }).fail(function () {
        $cb.prop('checked', !isOn);
        toastr.error('Request failed.');
    });
});


/* ═══════════════════════════════════════════════════════════
   GLOBAL 419 handler — expired CSRF session
═══════════════════════════════════════════════════════════ */
$(document).ajaxError(function (event, xhr) {
    if (xhr.status === 419) {
        toastr.error(
            'Your session has expired. <a href="javascript:location.reload()" ' +
            'style="color:#fff;text-decoration:underline;">Click here to refresh</a> and try again.',
            'Session Expired',
            { closeButton: true, timeOut: 0, extendedTimeOut: 0, escapeHtml: false }
        );
    }
});

/* ═══════════════════════════════════════════════════════════
   Clear form state when modals close
═══════════════════════════════════════════════════════════ */
$('#addBannerModal').on('hidden.bs.modal', function () {
    $('#addBannerForm')[0].reset();
    if (typeof window.setMediaPickerValue === 'function') {
        window.setMediaPickerValue('banner_image_add', '', null);
    }
});
$('#editBannerModal').on('hidden.bs.modal', function () {
    $('#editBannerForm')[0].reset();
    if (typeof window.setMediaPickerValue === 'function') {
        window.setMediaPickerValue('banner_image_edit', '', null);
    }
});

$('#addFeatureModal').on('hidden.bs.modal', function () {
    $('#feature_id').val('');
    $('#feature-modal-title').text('Add Feature');
    $('#featureForm')[0].reset();
    $('#feature_icon').val('fas fa-check-circle');
    $('#feature-icon-preview').removeClass().addClass('fas fa-check-circle');
    $('#feature_description').val('');
});

$('#addBlogItemModal').on('hidden.bs.modal', function () {
    $('#addBlogItemForm')[0].reset();
    if (typeof window.setMediaPickerValue === 'function') {
        window.setMediaPickerValue('blog_image_add', '', null);
    }
});
$('#editBlogItemModal').on('hidden.bs.modal', function () {
    $('#editBlogItemForm')[0].reset();
    if (typeof window.setMediaPickerValue === 'function') {
        window.setMediaPickerValue('blog_image_edit', '', null);
    }
});

}); /* end $(function) */
</script>
