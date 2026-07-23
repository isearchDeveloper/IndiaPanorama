@section('title', 'CMS Builder')
@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 fw-bold mb-0"><i class="fas fa-layer-group me-2 text-primary"></i>CMS Builder Pages</h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.cms-builder.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> New Page
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Sections</th>
                            <th>Status</th>
                            <th>Sitemap</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pages as $page)
                        <tr>
                            <td class="fw-medium">{{ $page->title }}</td>
                            <td><code>/{{ $page->slug }}</code></td>
                            <td>
                                <span class="badge bg-secondary">{{ $page->sections_count }}</span>
                            </td>
                            <td>
                                @if($page->is_published)
                                <span class="badge bg-success">Published</span>
                                @else
                                <span class="badge bg-warning text-dark">Draft</span>
                                @endif
                            </td>
                            <td>
                                @if($page->in_sitemap)
                                <i class="fas fa-check text-success"></i>
                                @else
                                <i class="fas fa-times text-muted"></i>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $page->updated_at->diffForHumans() }}</td>
                            <td>
                                <a href="{{ route('admin.cms-builder.edit', $page) }}"
                                    class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.cms-builder.toggle-publish', $page) }}"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $page->is_published ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                        title="{{ $page->is_published ? 'Unpublish' : 'Publish' }}">
                                        <i class="fas fa-{{ $page->is_published ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-info btn-seo-page"
                                    data-id="{{ $page->id }}"
                                    data-title="{{ $page->title }}"
                                    data-url="{{ route('admin.cms-builder.seo.update', $page) }}"
                                    data-fetch="{{ route('admin.cms-page-meta.show.meta', $page) }}"
                                    title="SEO Settings">
                                    <i class="fa fa-globe icon"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-page"
                                    data-url="{{ route('admin.cms-builder.destroy', $page) }}"
                                    data-title="{{ $page->title }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-layer-group fa-2x mb-2 d-block opacity-25"></i>
                                No builder pages yet.
                                <a href="{{ route('admin.cms-builder.create') }}">Create the first one</a>.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($pages->lastPage() > 1)
        <div class="card-footer">
            @include('admin.common.pagination', ['paginator' => $pages])
        </div>
        @endif
    </div>

</div>

{{-- SEO Modal --}}
<div class="modal fade" id="seoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#e87c1e;">
                <h5 class="modal-title text-white fw-bold">
                    <i class="fas fa-search me-2"></i><span id="seoModalTitle"></span> — SEO Settings
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="seoForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Meta Title</label>
                            <input type="text" name="meta_title" id="seoMetaTitle" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Meta Description</label>
                            <input type="text" name="meta_description" id="seoMetaDesc" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="seoMetaKeywords" class="form-control"
                                placeholder="keyword1, keyword2, ...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">H1 Heading</label>
                            <input type="text" name="h1_heading" id="seoH1" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Extra Meta Tag</label>
                            <textarea name="meta_details" id="seoMetaDetails" class="form-control" rows="4"
                                placeholder="<meta name=&quot;robots&quot; content=&quot;index,follow&quot;>"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning text-white fw-semibold" id="btnSaveSeo">
                    <i class="fas fa-save me-1"></i> Update SEO Setting
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // ── SEO Modal ─────────────────────────────────────────────────────────────────
    let seoUpdateUrl = '';

    $(document).on('click', '.btn-seo-page', function() {
        const btn = $(this);
        seoUpdateUrl = btn.data('url');
        const fetchUrl = btn.data('fetch');
        const title = btn.data('title');

        $('#seoModalTitle').text(title);
        $('#seoMetaTitle, #seoMetaDesc, #seoMetaKeywords, #seoH1, #seoMetaDetails').val('');

        $.get(fetchUrl, function(res) {
            const m = res.meta || {};
            $('#seoMetaTitle').val(m.meta_title || '');
            $('#seoMetaDesc').val(m.meta_description || '');
            $('#seoMetaKeywords').val(m.meta_keywords || '');
            $('#seoH1').val(m.h1_heading || '');
            $('#seoMetaDetails').val(m.meta_details || '');
        });

        $('#seoModal').modal('show');
    });

    $('#btnSaveSeo').on('click', function() {
        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving…');
        $.ajax({
            url: seoUpdateUrl,
            type: 'PUT',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                meta_title: $('#seoMetaTitle').val(),
                meta_description: $('#seoMetaDesc').val(),
                meta_keywords: $('#seoMetaKeywords').val(),
                h1_heading: $('#seoH1').val(),
                meta_details: $('#seoMetaDetails').val(),
            },
        }).done(function() {
            toastr.success('SEO settings saved.');
            $('#seoModal').modal('hide');
        }).fail(function(xhr) {
            const errors = xhr.responseJSON?.errors;
            if (errors) {
                toastr.error(Object.values(errors).flat().join(' '));
            } else {
                toastr.error('Failed to save SEO settings.');
            }
        }).always(function() {
            $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Update SEO Setting');
        });
    });

    $(document).on('click', '.btn-delete-page', function() {
        const url = $(this).data('url');
        const title = $(this).data('title');
        Swal.fire({
            title: 'Delete Page?',
            text: '"' + title + '" and all its sections will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Yes, delete',
        }).then(function(r) {
            if (!r.isConfirmed) return;
            $.ajax({
                url: url,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            }).done(function() {
                toastr.success('Page deleted.');
                setTimeout(() => location.reload(), 800);
            }).fail(function() {
                toastr.error('Failed to delete page.');
            });
        });
    });
</script>
@endsection