@extends('layouts.app')
@section('title','Edit News Page')
@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">
                    <i class="fas fa-edit me-2"></i>Edit News
                </h1>
                <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Page List
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form id="pageForm" method="POST" action="{{ route('admin.news.update',$news->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">News Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Type<span class="required-text">*</span></label>
                                    <select class="form-control" name="type" required>
                                        <option value="news" @if($news->type =="news") selected @endif)>News</option>
                                        <option value="newsletter" @if($news->type =="newsletter") selected @endif)>Newsletter</option>
                                    </select>
                                    @error('type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Title<span class="required-text">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ $news->title}}" required>
                                    @error('title')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Slug <small class="text-muted">(fixed after creation)</small></label>
                                    <input type="text" class="form-control" id="slug" name="slug" value="{{ $news->slug}}" readonly>
                                    @error('slug')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12 d-none" id="news-date-wrapper">
                                <div class="mb-3">
                                    <label class="form-label"> Date <span class="required-text">*</span></label>
                                    <input type="text"
                                        class="form-control @error('news_date') is-invalid @enderror"
                                        name="news_date"
                                        id="news_date"
                                        placeholder="Select news date"
                                        value="{{ old('news_date', $news->created_at) }}">

                                    @error('news_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label">Banner Image<span class="required-text">*</span> (accept only .webp)</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <x-media-picker name="news_thumbnail" label="Banner Image" folder="news" :value="$news->primary_img" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <input type="text" class="form-control @error('news_thumbnail_alt') is-invalid @enderror" id="news_thumbnail_alt" name="news_thumbnail_alt" value="{{ $news->primary_img_alt }}" placeholder="Banner Image Alt">
                                            @error('news_thumbnail_alt')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="cms-page-description" class="form-label">Page Details<span class="required-text">*</span></label>
                                    <textarea class="form-control no-char-limit tinymce @error('description') is-invalid @enderror" id="cms-page-description" name="description" rows="5">{{ $news->description}}
                                    </textarea>
                                    @error('description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="invalid-feedback  d-none" id="description-error"></div>
                                </div>
                            </div>
                            <div class="col-md-12 text-right">
                                <a href="{{ route('admin.news.index') }}" class="btn btn-secondary btn-lg ms-2">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-success btn-lg" id="submit-btn">
                                    <i class="fas fa-save me-2"></i>Update News
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>
    let selectedFiles = [];

    function renderPreview(files) {
        let dt = new DataTransfer();
        $(".new-uplod").remove();
        files.forEach((file, index) => {
            if (file.type === 'image/webp') {
                dt.items.add(file); // keep valid file

                let reader = new FileReader();
                reader.onload = function(e) {
                    let col = `
                        <div class="col-md-3 text-center mb-3 new-uplod" data-filename="${file.name}">
                            <div class="card">
                                <img src="${e.target.result}" class="card-img-top img-fluid mb-2" 
                                    style="height:150px; object-fit:cover;">
                                <div class="card-body p-2">
                                    <input type="text" name="images_alt[]"  class="form-control" placeholder="Image Alt Text">
                                    <button type="button" class="btn btn-sm btn-danger remove-btn">Delete</button> 
                                </div>
                            </div>
                        </div>`;
                    $("#galleryPreview").append(col);
                };
                reader.readAsDataURL(file);
            } else {
                toastr.error("Only .webp images are allowed!", 'Error');
            }
        });

        document.getElementById("galleryInput").files = dt.files;
        selectedFiles = Array.from(dt.files);
    }

    // tinymce.init({
    //     selector: '.tinymce',
    //     plugins: 'wordcount',
    //     setup: function (editor) {
    //         editor.on('keydown', function (e) {
    //             // NO LIMIT
    //         });
    //     }
    // });

    tinymce.init({
        selector: '.tinymce',
        height: 500,
        menubar: true,
        branding: false,
        statusbar: true,

        plugins: [
            'advlist autolink lists link image charmap preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table help wordcount'
        ],

        toolbar: 'undo redo | styles | bold italic underline strikethrough | ' +
            'alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | link image media | ' +
            'forecolor backcolor | code fullscreen',
        image_title: true,
        automatic_uploads: true,
        file_picker_types: 'image',

        file_picker_callback: function(cb, value, meta) {
            if (meta.filetype === 'image') {
                let input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');

                input.onchange = function() {
                    let file = this.files[0];
                    let reader = new FileReader();
                    reader.onload = function() {
                        cb(reader.result, {
                            title: file.name
                        });
                    };
                    reader.readAsDataURL(file);
                };

                input.click();
            }
        },

        /* ðŸ”¥ MOST IMPORTANT PART */
        valid_elements: '*[*]',
        extended_valid_elements: '*[*]',
        valid_children: '+body[style]',
        verify_html: false,
        cleanup: false,
        forced_root_block: false,
        invalid_elements: '',
        entity_encoding: 'raw',

        /* Allow <style> tag */
        custom_elements: 'style',
        protect: [
            /<style[\s\S]*?>[\s\S]*?<\/style>/gi
        ],

        /* Allow inline CSS */
        content_style: `
        body { font-family: Arial, sans-serif; }
    `,

        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });

    // document.addEventListener('DOMContentLoaded', function() {
    //     tinymce.init({
    //         selector: '#cms-page-description, #cms_page_sub_title',
    //         height: 700,
    //         menubar: true,
    //         statusbar: true,
    //         branding: false,

    //         // Allow all tags and all attributes (React/Next friendly)
    //         valid_elements: '*[*]',
    //         extended_valid_elements: '*[*]',
    //         valid_children: '+body[*]',
    //         verify_html: false,
    //         cleanup: false,
    //         entity_encoding: 'raw',
    //         forced_root_block: false,
    //         invalid_elements: '', // allow everything
    //         protect: [
    //             /\<\/?(?:Link|Image)[^>]*\>/g // protect React-like tags from being cleaned
    //         ],

    //         plugins: [
    //             'advlist autolink lists charmap print preview hr pagebreak',
    //             'searchreplace wordcount visualblocks visualchars fullscreen',
    //             'insertdatetime media nonbreaking save table directionality',
    //             'emoticons template paste textcolor colorpicker textpattern code',
    //             'image media link'
    //         ],

    //         toolbar:
    //             'undo redo | styles | bold italic underline | bullist numlist | ' +
    //             'alignleft aligncenter alignright alignjustify | ' +
    //             'image media link | code fullscreen',

    //         images_upload_url: "{{ route('admin.upload-image') }}",
    //         automatic_uploads: true,
    //         headers: {
    //             'X-CSRF-TOKEN': "{{ csrf_token() }}"
    //         },
    //         file_picker_types: 'image',
    //         file_picker_callback: function(cb, value, meta) {
    //             if (meta.filetype === 'image') {
    //                 const input = document.createElement('input');
    //                 input.setAttribute('type', 'file');
    //                 input.setAttribute('accept', 'image/webp');

    //                 input.onchange = function() {
    //                     const file = this.files[0];
    //                     const reader = new FileReader();
    //                     reader.onload = function() {
    //                         const id = 'blobid' + (new Date()).getTime();
    //                         const blobCache = tinymce.activeEditor.editorUpload.blobCache;
    //                         const base64 = reader.result.split(',')[1];
    //                         const blobInfo = blobCache.create(id, file, base64);
    //                         blobCache.add(blobInfo);
    //                         cb(blobInfo.blobUri(), { title: file.name });
    //                     };
    //                     reader.readAsDataURL(file);
    //                 };

    //                 input.click();
    //             }
    //         },

    //         setup: function(editor) {
    //             editor.on('change', () => editor.save());

    //             const enforceWordLimit = () => {
    //                 const text = editor.getContent({ format: 'text' }).trim();
    //                 const words = text.split(/\s+/).filter(w => w.length > 0);
    //                 if (words.length > 10000) {
    //                     showWarning(editor.getElement(), `Word limit exceeded! Max 10000 words.`);
    //                     const trimmed = words.slice(0, 10000).join(' ');
    //                     editor.setContent(trimmed);
    //                     editor.selection.select(editor.getBody(), true);
    //                     editor.selection.collapse(false);
    //                 }
    //             };

    //             editor.on('input', enforceWordLimit);
    //             editor.on('paste', () => setTimeout(enforceWordLimit, 10));
    //         }
    //     });


    // });



    $(document).ready(function() {
        $("#galleryInput").on("change", function(e) {
            selectedFiles = Array.from(e.target.files); // store files
            renderPreview(selectedFiles);
        });


    });


</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    function toggleNewsDate() {
        let type = document.querySelector('select[name="type"]').value;
        let dateWrapper = document.getElementById('news-date-wrapper');
        let dateInput = document.getElementById('news_date');

        if (type === 'news') {
            dateWrapper.classList.add('d-none');
            dateInput.removeAttribute('required');
            dateInput.value = '';
        } else {
            dateWrapper.classList.remove('d-none');
            dateInput.setAttribute('required', 'required');
        }
    }
    let newsDatePicker;

    document.addEventListener('DOMContentLoaded', function() {

        const newsDateInput = document.getElementById('news_date');
        const typeSelect = document.querySelector('select[name="type"]');

        if (newsDateInput) {
            newsDatePicker = flatpickr(newsDateInput, {
                dateFormat: "Y-m-d",
                maxDate: "today",
                allowInput: false,
                disableMobile: true,
                animate: true
            });
        }

        toggleNewsDate();

        if (typeSelect) {
            typeSelect.addEventListener('change', toggleNewsDate);
        }
    });

    function toggleNewsDate() {
        let type = document.querySelector('select[name="type"]').value;
        let wrapper = document.getElementById('news-date-wrapper');
        let dateInput = document.getElementById('news_date');

        if (type === 'news') {
            wrapper.classList.add('d-none');
            dateInput.removeAttribute('required');
            dateInput.value = '';
        } else {
            wrapper.classList.remove('d-none');
            dateInput.setAttribute('required', 'required');

        }
    }

    $(document).on('change', 'input[type="file"]', function() {

        const file = this.files[0];
        if (!file) return;

        const maxSize = 150 * 1024; // 150 KB

        // reset flag
        hasInvalidFile = false;

        if (file.type !== 'image/webp') {

            hasInvalidFile = true;

            Swal.fire({
                icon: 'error',
                title: 'Invalid file type',
                text: 'Only .webp images are allowed.',
                confirmButtonColor: '#e3342f'
            });

            $(this).val('');
            return;
        }

        if (file.size > maxSize) {

            hasInvalidFile = true;

            Swal.fire({
                icon: 'error',
                title: 'Image too large',
                text: 'Please upload image under 150 KB only.',
                confirmButtonColor: '#e3342f'
            });

            $(this).val('');
        }
    });
</script>
@endsection