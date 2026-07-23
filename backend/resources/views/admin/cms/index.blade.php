@section('title',"Cms Page")
@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">
                    <i class="fas fa-image me-2"></i>Cms Page Settings
                </h1>
            </div>
        </div>
    </div>
    
    <div class="row tab_details">

        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Page List
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pages as $page)
                                <tr>
                                    <td>
                                        <strong>{{$page->title}}</strong>
                                    </td>
                                    <td>
                                        <div class="card mb-3">
                                            <img id="page-banner-image" src="{{ storage_link($page->banner_image) }}" class="card-img-top img-fluid" style="height:50px; object-fit:cover;">
                                        </div>
                                    </td>
                                    <td>

                                        <!-- <button class="btn btn-sm btn-outline-primary" >
                                            <i class="fas fa-eye"></i>
                                        </button> -->

                                        <a href="{{ route('admin.cms-page.edit',$page->id) }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($page->id == 2)
                                        <a href="{{ route('admin.awards.index') }}" class="btn btn-sm btn-outline-success">
                                            <i class="fa-solid fa-trophy"></i>
                                        </a>
                                        @endif
                                        @if($page->id == 12)
                                        <a href="{{ route('admin.teams.index') }}" class="btn btn-sm btn-outline-success">
                                            <i class="fa-solid fa-user"></i>
                                        </a>
                                        @endif
                                        @if($page->id == 1)
                                        <a href="{{ route('admin.cms-page.dmc.city.pages') }}" class="btn btn-sm btn-outline-info">
                                            <i class="fa-solid fa-gear"></i>
                                        </a>
                                        @endif
                                        
                                        <button class="btn btn-sm btn-outline-primary page-meta" data-title="{{$page->title}}" data-id="{{$page->id}}" data-url="{{ route('admin.cms-page-meta.show.meta',$page->id) }}" data-action="{{ route('admin.cms-page.update',$page->id) }}">
                                            <i class="fas fa-globe icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
@section('modal')

<div class="modal fade" id="pageMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="page-title">Edit Meta Info</h5>
                <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
            </div>
            <form id="pageMeta" method="POST" action="" enctype="multipart/form-data">
            @csrf 
            @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="meta_setting">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="page_meta_title" class="form-label">Meta Title</label>
                                <input type="text" class="form-control" id="page_meta_title" name="meta_title">
                                <div class="text-danger d-none" id="page-meta-title-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="page_meta_description" class="form-label">Meta Description</label>
                                <input type="text" class="form-control" id="page_meta_description" name="meta_description">
                                <div class="text-danger d-none" id="page-meta-description-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="page_meta_keywords" class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control" id="page_meta_keywords" name="meta_keywords">
                                <div class="text-danger d-none" id="page-meta-keyords-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="page_h1_heading" class="form-label">H1 Heading</label>
                                <input type="text" class="form-control" id="page_h1_heading" name="h1_heading">
                                <div class="text-danger d-none" id="page-meta-keyords-error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label for="page_meta_details" class="form-label">Extra Meta Tag</label>
                            <textarea class="form-control"  name="meta_details" id="page_meta_details" rows="5" id="meta_details"></textarea>
                            <div class="text-danger d-none" id="page-meta-details-error"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="update-page-meta-btn">
                        <i class="fas fa-save me-2"></i>Update Meta Setting
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let faqIndex = 0;


    $(document).ready(function () {
        @if(session('success'))
            toastr.success("{{ session('success') }} ", 'Success');
        @endif

        $(document).on('click', '.page-meta', function() {
            let btn = $(this);
            let id = $(this).data('id');
            let dataUrl = $(this).data('url').trim();
            let dataAction = $(this).data('action').trim();
            let dataTitle = $(this).data('title').trim();
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            $('#pageMeta').attr('action',dataAction);
            $.ajax({
                type: "GET",
                dataType: "json",
                url: dataUrl,
                success: function(data) {
                    $('#page-title').text('# '+dataTitle+'-Meta Info');
                    if(data.meta != null){
                        $('#page_meta_title').val(data.meta.meta_title);
                        $('#page_meta_description').val(data.meta.meta_description);
                        $('#page_meta_keywords').val(data.meta.meta_keywords);
                        $('#page_h1_heading').val(data.meta.h1_heading);
                        $('#page_meta_details').val(data.meta.meta_details);
                    }
                    $('#pageMetaModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching city details.'
                    });
                },
                complete: function () {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });
    });
</script>
@endsection