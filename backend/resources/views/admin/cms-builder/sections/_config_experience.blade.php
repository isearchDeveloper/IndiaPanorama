<div class="row g-3">
    <div class="col-12">
        <label class="form-label">Heading</label>
        <input type="text" class="form-control form-control-sm" data-key="heading"
               value="{{ $content['heading'] ?? '' }}" placeholder="India is a Best Experienced by Road">
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea class="form-control form-control-sm" data-key="description" rows="2"
                  placeholder="Travel comfortably in our modern, well-maintained vehicles…">{{ $content['description'] ?? '' }}</textarea>
    </div>
    <div class="col-md-8">
        <label class="form-label">Background Image <span class="text-muted small">(optional)</span></label>
        <x-media-picker name="_cms_experience_bg_image" :value="$content['bg_image'] ?? ''" folder="cms_page" data-key="bg_image" label="" />
    </div>
    <div class="col-md-4">
        <label class="form-label">Background Image Alt Text <span class="text-danger">*</span></label>
        <input type="text" class="form-control form-control-sm" data-key="bg_image_alt"
               value="{{ $content['bg_image_alt'] ?? '' }}"
               placeholder="e.g. Experience India travel background">
        <small class="text-muted">Required when background image is set</small>
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">Feature Items</label>
        <div class="exp-items-tbody">
            @foreach($content['items'] ?? [] as $item)
            <div class="experience-item-row">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" data-exp-key="icon"
                               placeholder="fas fa-car" value="{{ $item['icon'] ?? '' }}">
                    </div>
                    <div class="col-md-7">
                        <input type="text" class="form-control form-control-sm" data-exp-key="title"
                               placeholder="Item title" value="{{ $item['title'] ?? '' }}">
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-rm-experience-item">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary mt-2 btn-add-experience-item">
            <i class="fas fa-plus me-1"></i>Add Item
        </button>
        <small class="text-muted d-block mt-1">Icon: use Font Awesome class, e.g. <code>fas fa-car</code></small>
    </div>
</div>
