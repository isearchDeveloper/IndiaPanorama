@extends('layouts.app') 
@section('title','Edit CMS Page')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">
                    <i class="fas fa-edit me-2"></i>Create CMS Page
                </h1>
                <a href="{{ route('admin.cms-page.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Page List
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <form id="pageForm" method="POST" action="{{ route('admin.cms-page.create.page') }}" enctype="multipart/form-data">
                @csrf 
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Page Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Title<span class="required-text">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" required>
                                    @error('title')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Slug<span class="required-text">*</span></label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" required>
                                    @error('slug')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Short Description</label>
                                    <textarea class="form-control no-char-limit tinymce @error('sub_title') is-invalid @enderror" id="cms_page_sub_title" name="sub_title" rows="5">
                                       
                                    </textarea>
                                    @error('sub_title')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Banner Image<span class="required-text">*</span> (accept only .webp)</label> 
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <x-media-picker name="banner_image" label="Banner Image" folder="cms_page" />
                                        </div>
                                        <div class="mb-3">
                                            <input type="text"  class="form-control @error('banner_image_alt') is-invalid @enderror"  id="banner_image_alt" name="banner_image_alt" placeholder="Banner Image Alt">
                                            @error('primary_image_alt')
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
                                    <textarea class="form-control no-char-limit tinymce @error('description') is-invalid @enderror" id="cms-page-description" name="description" rows="5">
                                    </textarea>
                                    @error('description')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="invalid-feedback  d-none" id="description-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Submit --}}
                <div class="card mt-3">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-success btn-lg" id="submit-btn">
                            <i class="fas fa-save me-2"></i>Add Page
                        </button>
                        <a href="{{ route('admin.cms-page.index') }}" class="btn btn-secondary btn-lg ms-2">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
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
            }  else {
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

    toolbar:
        'undo redo | styles | bold italic underline strikethrough | ' +
        'alignleft aligncenter alignright alignjustify | ' +
        'bullist numlist outdent indent | link image media | ' +
        'forecolor backcolor | code fullscreen',
    image_title: true,
    automatic_uploads: true,
    file_picker_types: 'image',

    file_picker_callback: function (cb, value, meta) {
        if (meta.filetype === 'image') {
            let input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            input.onchange = function () {
                let file = this.files[0];
                let reader = new FileReader();
                reader.onload = function () {
                    cb(reader.result, { title: file.name });
                };
                reader.readAsDataURL(file);
            };

            input.click();
        }
    },

    /* 🔥 MOST IMPORTANT PART */
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

    setup: function (editor) {
        editor.on('change', function () {
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



    $(document).ready(function () {
        $("#galleryInput").on("change", function(e) {
            selectedFiles = Array.from(e.target.files); // store files
            renderPreview(selectedFiles);
        });

        
    });



</script>
    
@endsection