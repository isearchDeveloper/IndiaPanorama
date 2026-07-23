<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Heading</label>
        <input type="text" class="form-control form-control-sm" data-key="heading"
               value="{{ $content['heading'] ?? '' }}" placeholder="e.g. About Indian Panorama">
    </div>
    <div class="col-md-6">
        <label class="form-label">Sub Heading</label>
        <input type="text" class="form-control form-control-sm" data-key="subheading"
               value="{{ $content['subheading'] ?? '' }}" placeholder="Trusted DMC since 1995">
    </div>
    <div class="col-md-8">
        <label class="form-label">Banner Image</label>
        <x-media-picker name="_cms_hero_banner_image" :value="$content['banner_image'] ?? ''" folder="cms_page" data-key="banner_image" label="" />
    </div>
    <div class="col-md-4">
        <label class="form-label">Banner Image Alt Text <span class="text-danger">*</span></label>
        <input type="text" class="form-control form-control-sm" data-key="banner_image_alt"
               value="{{ $content['banner_image_alt'] ?? '' }}"
               placeholder="e.g. Indian Panorama hero banner">
        <small class="text-muted">Required for SEO &amp; accessibility</small>
    </div>
    <div class="col-md-6">
        <label class="form-label">CTA Button Label</label>
        <input type="text" class="form-control form-control-sm" data-key="cta_label"
               value="{{ $content['cta_label'] ?? '' }}" placeholder="Explore Tours">
    </div>
    <div class="col-md-6">
        <label class="form-label">CTA Button URL</label>
        <input type="text" class="form-control form-control-sm" data-key="cta_url"
               value="{{ $content['cta_url'] ?? '' }}" placeholder="/packages">
    </div>
</div>
