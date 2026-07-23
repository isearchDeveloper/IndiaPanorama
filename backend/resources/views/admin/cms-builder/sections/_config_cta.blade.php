<div class="row g-3">
    <div class="col-12">
        <label class="form-label">Heading</label>
        <input type="text" class="form-control form-control-sm" data-key="heading"
               value="{{ $content['heading'] ?? '' }}" placeholder="Plan Your Dream India Journey">
    </div>
    <div class="col-12">
        <label class="form-label">Sub Heading</label>
        <input type="text" class="form-control form-control-sm" data-key="subheading"
               value="{{ $content['subheading'] ?? '' }}" placeholder="Customized tours crafted just for you">
    </div>
    <div class="col-md-6">
        <label class="form-label">Button Label</label>
        <input type="text" class="form-control form-control-sm" data-key="button_label"
               value="{{ $content['button_label'] ?? '' }}" placeholder="Get a Free Quote">
    </div>
    <div class="col-md-6">
        <label class="form-label">Button URL</label>
        <input type="text" class="form-control form-control-sm" data-key="button_url"
               value="{{ $content['button_url'] ?? '' }}" placeholder="/contact">
    </div>
    <div class="col-md-6">
        <label class="form-label">Background Image</label>
        <x-media-picker name="_cms_cta_image" :value="$content['image'] ?? ''" folder="cms_page" data-key="image" label="" />
    </div>
    <div class="col-md-6">
        <label class="form-label">Background Image Alt Text <span class="text-danger">*</span></label>
        <input type="text" class="form-control form-control-sm" data-key="image_alt"
               value="{{ $content['image_alt'] ?? '' }}"
               placeholder="e.g. Plan your India journey background">
        <small class="text-muted">Required when background image is set</small>
    </div>
</div>
