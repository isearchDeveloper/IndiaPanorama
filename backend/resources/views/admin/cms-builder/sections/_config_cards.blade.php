<div class="row g-3">
    <div class="col-12">
        <label class="form-label">Heading</label>
        <input type="text" class="form-control form-control-sm" data-key="heading"
               value="{{ $content['heading'] ?? '' }}" placeholder="e.g. Plan Your Dream India Journey">
    </div>
    <div class="col-12">
        <label class="form-label">Sub Heading</label>
        <input type="text" class="form-control form-control-sm" data-key="subheading"
               value="{{ $content['subheading'] ?? '' }}" placeholder="Short description below the heading">
    </div>
    <div class="col-md-8">
        <label class="form-label">Background Image <span class="text-muted small">(optional)</span></label>
        <x-media-picker name="_cms_cards_bg_image" :value="$content['bg_image'] ?? ''" folder="cms_page" data-key="bg_image" label="" />
    </div>
    <div class="col-md-4">
        <label class="form-label">Background Image Alt Text <span class="text-danger">*</span></label>
        <input type="text" class="form-control form-control-sm" data-key="bg_image_alt"
               value="{{ $content['bg_image_alt'] ?? '' }}"
               placeholder="e.g. Feature cards section background">
        <small class="text-muted">Required when background image is set</small>
    </div>
    <div class="col-md-6">
        <label class="form-label">CTA Button Label <span class="text-muted small">(optional)</span></label>
        <input type="text" class="form-control form-control-sm" data-key="cta_label"
               value="{{ $content['cta_label'] ?? '' }}" placeholder="e.g. Customize Your Tour">
    </div>
    <div class="col-md-6">
        <label class="form-label">CTA Button URL</label>
        <input type="text" class="form-control form-control-sm" data-key="cta_url"
               value="{{ $content['cta_url'] ?? '' }}" placeholder="/contact">
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">Feature Cards</label>
        <div class="cards-tbody">
            @foreach($content['cards'] ?? [] as $card)
            <div class="card-item-row">
                <div class="row g-2 align-items-start">
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Image</label>
                        <x-media-picker name="_cms_card_image_{{ $loop->index }}" :value="$card['image'] ?? ''" folder="cms_page" data-card-key="image" label="" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Image Alt <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" data-card-key="image_alt"
                               placeholder="Describe image" value="{{ $card['image_alt'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Title</label>
                        <input type="text" class="form-control form-control-sm" data-card-key="title"
                               placeholder="Card title" value="{{ $card['title'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Description</label>
                        <textarea class="form-control form-control-sm" data-card-key="description"
                                  rows="3" placeholder="Card description">{{ $card['description'] ?? '' }}</textarea>
                    </div>
                    <div class="col-md-1 text-end">
                        <label class="form-label small d-block">&nbsp;</label>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-rm-card-item">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary mt-2 btn-add-card-item">
            <i class="fas fa-plus me-1"></i>Add Card
        </button>
    </div>
</div>
