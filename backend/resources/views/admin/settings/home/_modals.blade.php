{{-- ══════════════════════════════════════════════════════════
     ALL MODALS for Homepage CMS
══════════════════════════════════════════════════════════ --}}

{{-- ── Banner: Add ── --}}
<div class="modal fade" id="addBannerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#0d1526;">
                <h5 class="modal-title text-white">
                    <i class="fas fa-plus me-2" style="color:#2563eb;"></i>Add Slider Banner
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addBannerForm">
                @csrf
                <div class="modal-body">
                    {{-- Image --}}
                    <div class="mb-3">
                        <x-media-picker name="banner_image" picker-id="banner_image_add" label="Banner Image" folder="banner" />
                    </div>
                    {{-- Alt tag --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Banner Alt Tag</label>
                        <input type="text" class="form-control" id="banner_image_alt"
                               name="banner_image_alt" placeholder="Descriptive alt text for the image">
                    </div>
                    {{-- Title --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="banner_title"
                               name="title" required placeholder="e.g. Discover the Magic of India">
                    </div>
                    {{-- Subtitle --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sub Title</label>
                        <input type="text" class="form-control" id="banner_subtitle"
                               name="subtitle" placeholder="Short supporting line">
                    </div>
                    {{-- Button --}}
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Button Text</label>
                            <input type="text" class="form-control" id="banner_button_text"
                                   name="button_text" placeholder="e.g. Explore Tours">
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Button URL</label>
                            <input type="text" class="form-control" id="banner_url"
                                   name="url" placeholder="/packages/kerala-tour">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary banner-submit-btn">
                        <i class="fas fa-save me-1"></i>Save Banner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Banner: Edit ── --}}
<div class="modal fade" id="editBannerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#0d1526;">
                <h5 class="modal-title text-white">
                    <i class="fas fa-edit me-2" style="color:#2563eb;"></i>Edit Banner
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editBannerForm" data-url="">
                @csrf @method('PUT')
                <div class="modal-body">
                    {{-- Image --}}
                    <div class="mb-3">
                        <x-media-picker name="banner_image" picker-id="banner_image_edit" label="Banner Image" folder="banner" />
                    </div>
                    {{-- Alt tag --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Banner Alt Tag</label>
                        <input type="text" class="form-control" id="edit_banner_image_alt"
                               name="banner_image_alt" placeholder="Alt text">
                    </div>
                    {{-- Title --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_banner_title"
                               name="title" required>
                    </div>
                    {{-- Subtitle --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sub Title</label>
                        <input type="text" class="form-control" id="edit_banner_subtitle"
                               name="subtitle" placeholder="Short supporting line">
                    </div>
                    {{-- Button --}}
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Button Text</label>
                            <input type="text" class="form-control" id="edit_banner_button_text"
                                   name="button_text" placeholder="e.g. Explore Tours">
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Button URL</label>
                            <input type="text" class="form-control" id="edit_banner_url"
                                   name="url" placeholder="/packages/kerala-tour">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary edit-banner-submit-btn">
                        <i class="fas fa-save me-1"></i>Update Banner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Blog Item: Add ── --}}
<div class="modal fade" id="addBlogItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#0d1526;">
                <h5 class="modal-title text-white">
                    <i class="fas fa-plus me-2" style="color:#2563eb;"></i>Add Blog
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addBlogItemForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Blog Title</label>
                        <input type="text" class="form-control" id="blog_title" name="title"
                               placeholder="e.g. Top 10 Places to Visit in India">
                    </div>
                    <div class="mb-3">
                        <x-media-picker name="image" picker-id="blog_image_add" label="Blog Image" folder="home-blog-items" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Image Alt Tag</label>
                        <input type="text" class="form-control" id="blog_image_alt" name="image_alt"
                               placeholder="Descriptive alt text">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Blog Link / URL</label>
                        <input type="text" class="form-control" id="blog_link" name="link"
                               placeholder="/blogs/my-blog-post">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary blog-item-submit-btn">
                        <i class="fas fa-save me-1"></i>Add Blog
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Blog Item: Edit ── --}}
<div class="modal fade" id="editBlogItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#0d1526;">
                <h5 class="modal-title text-white">
                    <i class="fas fa-edit me-2" style="color:#2563eb;"></i>Edit Blog
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editBlogItemForm" data-url="">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Blog Title</label>
                        <input type="text" class="form-control" id="edit_blog_title" name="title"
                               placeholder="e.g. Top 10 Places to Visit in India">
                    </div>
                    <div class="mb-3">
                        <x-media-picker name="image" picker-id="blog_image_edit" label="Replace Image" folder="home-blog-items" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Image Alt Tag</label>
                        <input type="text" class="form-control" id="edit_blog_image_alt" name="image_alt"
                               placeholder="Descriptive alt text">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Blog Link / URL</label>
                        <input type="text" class="form-control" id="edit_blog_link" name="link"
                               placeholder="/blogs/my-blog-post">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary edit-blog-item-submit-btn">
                        <i class="fas fa-save me-1"></i>Update Blog
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── About Feature: Add / Edit ── --}}
<div class="modal fade" id="addFeatureModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#0d1526;">
                <h5 class="modal-title text-white">
                    <i class="fas fa-plus me-2" style="color:#22c55e;"></i>
                    <span id="feature-modal-title">Add Feature</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="featureForm">
                <input type="hidden" id="feature_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Icon Class <small class="text-muted fw-normal">(FontAwesome)</small>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text" style="min-width:42px;justify-content:center;">
                                    <i id="feature-icon-preview" class="fas fa-check-circle"></i>
                                </span>
                                <input type="text" class="form-control" id="feature_icon" name="icon_class"
                                       value="fas fa-check-circle" placeholder="fas fa-check-circle">
                            </div>
                            <small class="text-muted">Visit fontawesome.com for class names</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                Feature Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="feature_text" name="text" required
                                   placeholder="e.g. Best Price Guarantee">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input type="number" class="form-control" id="feature_sort" name="sort_order"
                                   min="0" max="99" value="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Feature Description</label>
                            <textarea class="form-control" id="feature_description" name="feature_description"
                                      rows="3"
                                      placeholder="Short description for this feature..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success feature-submit-btn">
                        <i class="fas fa-save me-1"></i>Save Feature
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


