<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Heading</label>
        <input type="text" class="form-control form-control-sm" data-key="heading"
               value="{{ $content['heading'] ?? '' }}" placeholder="Gallery">
    </div>
    <div class="col-md-3">
        <label class="form-label">Layout</label>
        <select class="form-select form-select-sm" data-key="layout">
            <option value="grid"    {{ ($content['layout'] ?? 'grid')    === 'grid'    ? 'selected' : '' }}>Grid</option>
            <option value="masonry" {{ ($content['layout'] ?? '')        === 'masonry' ? 'selected' : '' }}>Masonry</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Columns</label>
        <select class="form-select form-select-sm" data-key="columns">
            @foreach([2,3,4] as $c)
            <option value="{{ $c }}" {{ ($content['columns'] ?? 3) == $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label mb-0 fw-medium">Images</label>
            <button type="button" class="btn btn-sm btn-outline-success btn-add-gallery">
                <i class="fas fa-plus me-1"></i> Add Image
            </button>
        </div>
        <div class="gallery-tbody">
            @if(!empty($content['images']))
                @foreach($content['images'] as $img)
                <div class="gallery-row">
                    <div class="row g-2">
                        <div class="col-7">
                            <input type="text" class="form-control form-control-sm" data-key="src"
                                   placeholder="Image URL or S3 path" value="{{ $img['src'] ?? '' }}">
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control form-control-sm" data-key="alt"
                                   placeholder="Alt text" value="{{ $img['alt'] ?? '' }}">
                        </div>
                        <div class="col-1 text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-rm-gallery">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
            <div class="gallery-row">
                <div class="row g-2">
                    <div class="col-7">
                        <input type="text" class="form-control form-control-sm" data-key="src" placeholder="Image URL or S3 path">
                    </div>
                    <div class="col-4">
                        <input type="text" class="form-control form-control-sm" data-key="alt" placeholder="Alt text">
                    </div>
                    <div class="col-1 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-rm-gallery">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
