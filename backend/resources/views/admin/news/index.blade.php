@section('title',"Cms Page")
@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">
                    <i class="fas fa-image me-2"></i>News Page
                </h1>
                <a href="{{route('admin.news.create')}}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create News
                </a>
            </div>
        </div>
    </div>

    <div class="row tab_details">

        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>News List ({{$news->total()}})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Image</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($news as $new)
                                <tr>
                                    <td>
                                        <strong title="{{ $new->title }}">
                                            {{ \Illuminate\Support\Str::limit($new->title, 20, '...') }}
                                        </strong>
                                    </td>
                                    <td>
                                        {!! \Illuminate\Support\Str::limit(strip_tags($new->description), 40, '...') !!}
                                    </td>
                                    <td>
                                        <div class="card mb-3">
                                            <img id="page-banner-image" src="{{ storage_link($new->primary_img) }}" class="card-img-top img-fluid" style="height:50px; object-fit:cover;">
                                        </div>
                                    </td>
                                    <td>{{ $new->type}}</td>
                                    <td>
                                        <input id="status_{{$new->id }}" type="checkbox" data-id="{{$new->id }}" data-url="{{ route('admin.news.update',$new->id) }}" class="js-switch news_status" <?php echo $new->is_active == 1 ? 'checked' : '' ?>>
                                    </td>

                                    <td>

                                        <!-- <button class="btn btn-sm btn-outline-primary" >
                                            <i class="fas fa-eye"></i>
                                        </button> -->

                                        <a href="{{ route('admin.news.edit',$new->id) }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-primary news-faqs" data-id="{{$new->id}}" data-title="{{$new->title}}" data-url="{{ route('admin.news.faqUpdate',$new->id) }}">
                                            <i class="fa fa-question-circle icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>

                                        <button class="btn btn-sm btn-outline-primary news-meta"
                                            data-id="{{ $new->id }}"
                                            data-url="{{ route('admin.news-meta.show.meta', $new->id) }}"
                                            data-upurl="{{ route('admin.news.update', $new->id) }}">
                                            <i class="fa fa-globe icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none"></span>
                                        </button>

                                        {{-- <button class="btn btn-sm btn-outline-primary news-meta" data-title="{{$new->title}}" data-id="{{$new->id}}" data-upurl="{{ route('admin.news.update',$new->id) }}" data-action="{{ route('admin.news.update',$new->id) }}" data-url="{{ route('admin.news-meta.show.meta',$news->id) }}">
                                        <i class="fas fa-globe icon"></i>
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button> --}}

                                        <button class="btn btn-sm btn-outline-danger delete-news" data-id="{{ $new->id }}" data-url="{{ route('admin.news.destroy',$new->id) }}">
                                            <i class="fas fa-trash icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>

                                        <a href="{{ config('app.frontend_url') }}/news/{{$new->slug}}" title="preview" class="btn btn-sm btn-outline-success" target="_blank">
                                            <i class="fa fa-tv" aria-hidden="true"></i>
                                        </a>

                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No News Found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @include('admin.common.pagination', ['paginator' => $news])
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="NewsMetaModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="news-title">Edit Meta Info</h5>
                    <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
                </div>
                <form id="newsMeta" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="meta_setting">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="news_meta_title" class="form-label">Meta Title</label>
                                    <input type="text" class="form-control" id="news_meta_title" name="meta_title">
                                    <div class="text-danger d-none" id="news-meta-title-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="news_meta_description" class="form-label">Meta Description</label>
                                    <input type="text" class="form-control" id="news_meta_description" name="meta_description">
                                    <div class="text-danger d-none" id="news-meta-description-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="news_meta_keywords" class="form-label">Meta Keywords</label>
                                    <input type="text" class="form-control" id="news_meta_keywords" name="meta_keywords">
                                    <div class="text-danger d-none" id="news-meta-keyords-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="news_h1_heading" class="form-label">H1 Heading</label>
                                    <input type="text" class="form-control" id="news_h1_heading" name="h1_heading">
                                    <div class="text-danger d-none" id="news-meta-keyords-error"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label for="news_meta_details" class="form-label">Extra Meta Tag</label>
                                <textarea class="form-control" name="meta_details" id="news_meta_details" rows="5" id="meta_details"></textarea>
                                <div class="text-danger d-none" id="news-meta-details-error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="update-news-meta-btn">
                            <i class="fas fa-save me-2"></i>Update Meta Setting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    {{-- faq --}}
    <div class="modal fade" id="faqModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="city-title">Faqs</h5>
                    <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
                </div>
                <form id="faqForm" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="update-page-setting-btn" data-url="">
                            <i class="fas fa-save me-2"></i>Save Faqs
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function escHtml(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    $(document).ready(function() {
        let faqIndex = 0;
        @if(session('success'))
        toastr.success("{{ session('success') }} ", 'Success');
        @endif

        $(document).on('change', '.news_status', function() {
            let news_status = $(this).prop('checked') === true ? 1 : 0;
            $.ajax({
                type: "PUT",
                dataType: "json",
                url: $(this).data('url'),
                data: {
                    'status': news_status
                },
                success: function(data) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing your request.'
                    });
                }

            });
        });
        //faqs
        $(document).on('click', '.news-faqs', function() {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            let id = btn.data('id');
            let title = btn.data('title');
            let dataUrl = btn.data('url');

            $('#faqForm').attr('action', dataUrl);
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ route('admin.news.index') }}", // better to point this to a dedicated faq route
                data: {
                    id: id,
                    faqs: 'list'
                },
                success: function(data) {
                    $('#faqModal .modal-title').text(title + ' FAQ');
                    let faqs = data.news.faqs || [];
                    let body = `
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="faq_title" class="form-label">Faq Title<span class="required-text">*</span></label>
                            <input value="${escHtml(data.news.faq_title)}" class="form-control" name="faq_title" id="faq_title" placeholder="" required>
                        </div>
                    </div>
                    <table class="table" id="faqTable">
                        <thead>
                            <tr>
                                <th>Question</th>
                                <th>Answer</th>
                                <th><button type="button" class="btn btn-sm btn-outline-success" id="addFaqRow"><i class="fas fa-plus"></button></th>
                            </tr>
                        </thead>
                        <tbody>
                    `;

                    if (faqs.length > 0) {
                        $.each(faqs, function(index, faq) {
                            body += `
                                <tr class="b-none">
                                    <td><input type="text" name="faqs[${faqIndex}][question]" value="${escHtml(faq.question)}" class="form-control" required /></td>
                                    <td><textarea name="faqs[${faqIndex}][answer]" class="form-control">${escHtml(faq.answer)}</textarea></td>
                                    <td><button type="button" class="btn btn-sm btn-outline-danger removeFaqRow"><i class="fas fa-trash"></button></td>
                                </tr>
                            `;
                            faqIndex++;
                        });
                    } else {
                        body += `
                            <tr class="b-none">
                                <td><input type="text" name="faqs[${faqIndex}][question]" class="form-control" required/></td>
                                <td><textarea name="faqs[${faqIndex}][answer]" class="form-control"></textarea></td>
                                <td></td>
                            </tr>
                        `;
                        faqIndex++;
                    }

                    body += `</tbody></table>`;

                    $('#faqModal .modal-body').html(body);

                    // open modal
                    $('#faqModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching news FAQs.'
                    });
                },
                complete: function() {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $(document).on('click', '#addFaqRow', function() {
            let row = `
                <tr class="b-none">
                    <td><input type="text" name="faqs[${faqIndex}][question]" class="form-control" required /></td>
                    <td><textarea name="faqs[${faqIndex}][answer]" class="form-control"></textarea></td>
                    <td><button type="button" class="btn btn-sm btn-outline-danger removeFaqRow"><i class="fas fa-trash"></button></td>
                </tr>`;
            faqIndex++;
            $('#faqTable tbody').append(row);

        });

        $(document).on('click', '.removeFaqRow', function() {
            $(this).closest('tr').remove();
        });
    });

    $(document).on('click', '.news-meta', function() {

        let btn = $(this);
        let dataUrl = $(this).data('url');
        let dataupUrl = $(this).data('upurl');

        btn.find('.spinner-border').removeClass('d-none');
        btn.find('.icon').addClass('d-none');

        $.ajax({
            type: "GET",
            dataType: "json",
            url: dataUrl,
            success: function(data) {
                $('#news-title').text('# ' + data.title + ' - Meta Info');

                if (data.meta) {
                    $('#news_meta_title').val(data.meta.meta_title);
                    $('#news_meta_description').val(data.meta.meta_description);
                    $('#news_meta_keywords').val(data.meta.meta_keywords);
                    $('#news_h1_heading').val(data.meta.h1_heading);
                    $('#news_meta_details').val(data.meta.meta_details);
                } else {
                    $('#newsMeta')[0].reset();
                }

                $('#newsMeta').attr('action', dataupUrl);
                $('#NewsMetaModal').modal('show');
            },
            error: function() {
                Swal.fire('Error', 'Failed to load meta', 'error');
            },
            complete: function() {
                btn.find('.spinner-border').addClass('d-none');
                btn.find('.icon').removeClass('d-none');
            }
        });
    });


    $(document).on("click", ".delete-news", function(e) {
        e.preventDefault();
        let itemId = $(this).data("id");
        let itemUrl = $(this).data("url");
        let row = $(this).closest("tr"); // parent <tr>
        Swal.fire({
            title: "Are you sure?",
            text: "This item will be deleted!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#e3342f",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it",
            cancelButtonText: "Cancel",
            customClass: {
                popup: 'rounded-2xl shadow-lg', // Rounded + shadow
                confirmButton: 'px-4 py-2 text-white',
                cancelButton: 'px-4 py-2 text-white'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let btn = $(this);
                btn.find('.spinner-border').removeClass('d-none');
                btn.find('.icon').addClass('d-none');
                $.ajax({
                    type: "DELETE",
                    url: itemUrl,
                    success: function(res) {
                        if (res.success) {
                            row.remove();
                            Swal.fire(
                                "Deleted!",
                                "The item has been deleted successfully.",
                                "success"
                            );
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Category not deleted!', 'Failed');
                    },
                    complete: function() {
                        // Hide loader, show button text back
                        btn.find('.spinner-border').addClass('d-none');
                        btn.find('.icon').removeClass('d-none');
                    }
                });

            }
        });
    });
</script>

@endsection