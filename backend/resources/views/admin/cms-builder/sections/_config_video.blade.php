<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Heading</label>
        <input type="text" class="form-control form-control-sm" data-key="heading"
               value="{{ $content['heading'] ?? '' }}" placeholder="Watch Our Story">
    </div>
    <div class="col-md-6">
        <label class="form-label">Sub Heading</label>
        <input type="text" class="form-control form-control-sm" data-key="subheading"
               value="{{ $content['subheading'] ?? '' }}">
    </div>
    <div class="col-md-8">
        <label class="form-label">Video URL <small class="text-muted">(YouTube embed or direct MP4)</small></label>
        <input type="text" class="form-control form-control-sm" data-key="video_url"
               value="{{ $content['video_url'] ?? '' }}" placeholder="https://www.youtube.com/embed/...">
    </div>
    <div class="col-md-4">
        <label class="form-label">Thumbnail URL <small class="text-muted">(optional)</small></label>
        <input type="text" class="form-control form-control-sm" data-key="thumbnail"
               value="{{ $content['thumbnail'] ?? '' }}">
    </div>
</div>
