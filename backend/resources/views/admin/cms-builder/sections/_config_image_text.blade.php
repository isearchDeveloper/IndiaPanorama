<div class="row g-3">
    <div class="col-12">
        <label class="form-label">Heading</label>
        <input type="text" class="form-control form-control-sm" data-key="heading"
               value="{{ $content['heading'] ?? '' }}" placeholder="Section heading">
    </div>
    <div class="col-md-6">
        <label class="form-label">Image</label>
        <x-media-picker name="_cms_image_text_image" :value="$content['image'] ?? ''" folder="cms_page" data-key="image" label="" />
    </div>
    <div class="col-md-6">
        <label class="form-label">Image Alt Text</label>
        <input type="text" class="form-control form-control-sm" data-key="image_alt"
               value="{{ $content['image_alt'] ?? '' }}">
    </div>
    <div class="col-12">
        <label class="form-label">Content</label>
        <textarea class="form-control tinymce" data-key="body" rows="5">{{ $content['body'] ?? '' }}</textarea>
    </div>
</div>
