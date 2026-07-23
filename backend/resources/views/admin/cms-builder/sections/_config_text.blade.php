<div class="row g-3">
    <div class="col-12">
        <label class="form-label">Heading</label>
        <input type="text" class="form-control form-control-sm" data-key="heading"
               value="{{ $content['heading'] ?? '' }}" placeholder="Section heading">
    </div>
    <div class="col-12">
        <label class="form-label">Content</label>
        <textarea class="form-control tinymce" data-key="body" rows="6">{{ $content['body'] ?? '' }}</textarea>
    </div>
</div>
