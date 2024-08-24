@extends('layouts.layout')
@section('title', 'Email Template Edit')
@section('extracss')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/validation/form-validation.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/vendors.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css')}}">
@endsection
@section('content')

<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-12 mb-2 mt-1">
        <div class="breadcrumbs-top">
          <h5 class="content-header-title float-left pr-1 mb-0">Global Settings</h5>
          <div class="d-flex justify-content-between">
            <div class="breadcrumb-wrapper d-none d-sm-block ">
              <ol class="breadcrumb p-0 mb-0 pl-1">
                <li class="breadcrumb-item"><a href=""><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item"><a href="@if(whoCanCheck(config('app.arrWhoCanCheck'), 'email_template_listing') === true) {{route('email-template-list')}} @endif">Email Template</a>
                </li>
                <li class="breadcrumb-item active">Edit
                </li>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="content-body">
      <section id="basic-input">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Edit Email Template</h4>
              </div>
              <div class="card-body">
                <form action="{{ route('admin.updateEmailTemplate',encrypt($data->id)) }}" method="post" id="email-template-form">
                  @csrf
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <div class="controls">
                          <label>Title</label>
                          <input type="text" onload="convertToSlug(this.value)" onkeyup="convertToSlug(this.value)" class="form-control @error('title') is-invalid @enderror" placeholder="Title" value="{{$data->title}}" name="title">
                          @error('title')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <div class="controls">
                          <label>Slug</label>
                          <input type="text" class="form-control @error('slug') is-invalid @enderror" placeholder="Slug" value="{{$data->slug}}" name="slug" id="slug">
                          @error('slug')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <div class="controls">
                          <label>Subject</label>
                          <input type="text" class="form-control @error('subject') is-invalid @enderror" placeholder="Subject" value="{{$data->subject}}" name="subject">
                          @error('subject')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                        </div>
                      </div>
                    </div>
                    
                    
                    <div class="col-md-6">
                      <div class="form-group">
                        <div class="controls">
                          <label class="d-block">Status</label>
                          <ul class="list-unstyled mb-0">
                            <li class="d-inline-block mr-2 mb-1">
                              <fieldset>
                                <div class="radio radio-shadow">
                                  <input type="radio" class="@error('is_active') is-invalid @enderror" value="1" id="active" name="is_active" @if($data->is_active==1) checked @endif>
                                  <label for="active">Active</label>

                                </div>
                              </fieldset>
                            </li>
                            <li class="d-inline-block mr-2 mb-1">
                              <fieldset>
                                <div class="radio radio-shadow">
                                  <input type="radio" class="@error('is_active') is-invalid @enderror" value="0" id="inactive" @if($data->is_active==0) checked @endif name="is_active">
                                  <label for="inactive">Inactive</label>
                                  @error('is_active')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                                </div>
                              </fieldset>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>

                  

                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <div class="controls">
                          <label>Body</label>
                          <textarea class="form-control @error('body') is-invalid @enderror" name="body"  id="editor1">{{$data->body}}</textarea>
                          @error('body')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                        </div>
                      </div>
                    </div>

                    <div class="col-md-6 inc">
                      <label>CC Email</label>  
                      
                      @for($count = 0; $count < count($data->email_cc); $count++)
                      <div class="form-group ">
                        <div class="controls">
                          
                          <div class="row">
                            <div class="col-md-10">
                          <input type="email" class="form-control " name="cc[]" value="{{$data->email_cc[$count]->email_cc}}" placeholder="abc@xyz.com">
                              
                        </div>
                            <div class="col-md-1">
                              @if($count==0)
                              <span class="btn-success" style="width:20px;height:20px;text-align:center" type="submit" id="append" name="append">+</span>
                              @else
                              
                              <span class="remove_this btn-danger" style="width:20px;height:20px;text-align:center" type="submit" id="append" name="append">-</span>
                              @endif
                          
                          
                        </div>
                          </div>
                        </div>

                      </div>

                    
                    
                    @endfor
                     

                    </div>
                    


                  <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary mr-1">Submit</button>
                    <a href="{{ route('email-template-list') }}" type="reset" class="btn btn-light-secondary">Cancel</a>
                  </div>
              </div>
              </form>
            </div>
          </div>
        </div>
    </div>
    </section>
  </div>
</div>
</div>


@endsection
@section('extrajs')

<!-- BEGIN: Page Vendor JS-->
<script src="{{asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/datatables/datatable.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/forms/validation/email-template/email-template.js')}}"></script>
<script src="https://cdn.ckeditor.com/4.19.1/standard/ckeditor.js"></script>
<!-- END: Page Vendor JS-->
<script>
  $('.select2').select2();

  CKEDITOR.replace( 'editor1' );


  jQuery(document).ready( function () {
        $("#append").click( function(e) {
          e.preventDefault();
        $(".inc").append('<div class="row" style="margin-top:5px;">\
          <div class="col-md-10">\
                <input class="form-control" type="email" name="cc[]" placeholder="abc@xyz.com">\
                </div><div class="col-md-2">\
                  <span class="remove_this btn-danger" style="width:20px;height:20px;text-align:center" type="submit" id="append" name="append">-</span>\
                </div>\
                <br>\
                <br>\
            </div>');
        return false;
        });

    jQuery(document).on('click', '.remove_this', function() {
        jQuery(this).parent().parent().remove();
        return false;
        });
    $("input[type=submit]").click(function(e) {
      e.preventDefault();
      $(this).next("[name=textbox]")
      .val(
        $.map($(".inc :text"), function(el) {
          return el.value
        }).join(",\n")
      )
    })
  });


  function convertToSlug( str ) {
    
    //replace all special characters | symbols with a space
    str = str.replace(/[`~!@#$%^&*()_\-+=\[\]{};:'"\\|\/,.<>?\s]/g, ' ')
             .toLowerCase();
      
    // trim spaces at start and end of string
    str = str.replace(/^\s+|\s+$/gm,'');
      
    // replace space with dash/hyphen
    str = str.replace(/\s+/g, '-');   
    document.getElementById("slug").value = str;
    //return str;
  }
</script>
@endsection