@extends("theme.default")
@section("container")
    <!-- DataTables -->
    <link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
    <!-- Responsive datatable examples -->
    <link href="/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
    <!-- Multi Item Selection examples -->
    <link href="/assets/plugins/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css"/>

    <script src="/assets/js/modernizr.min.js"></script>
<style>
    #chat2 .form-control {
        border-color: transparent;
    }

    #chat2 .form-control:focus {
        border-color: transparent;
        box-shadow: inset 0px 0px 0px 1px transparent;
    }

    .divider:after,
    .divider:before {
        content: "";
        flex: 1;
        height: 1px;
        background: #eee;
    }
    .rounded-3 {
        border-radius: 5px !important;
        font-size: 12px;
        padding: 9px 16px !important;
        line-height: 24px;
    }
   .btn-download-container {
        border-radius: 50%;
        background-color: white;
        color: #3498db;
        width: 50px;
        height: 50px;
        position: relative;
        float: right;
       text-align: center;
       line-height: 55px;
       font-size: 22px;
    }
    .widget-user .wid-u-info {
        margin-right: 58px;
    }
</style>
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6 ">

            <div class="card" id="chat2">
                <div class="card-header d-flex justify-content-between align-items-center p-3">
                    <h5 class="mb-0">تیکت </h5>
                    <a href="/admin/ticket/{{$grop["id"]}}" class="btn btn-primary btn-trans waves-effect w-md waves-primary m-b-5 float-right">برگشت</a>
                </div>
                <div class="card-body" id="card-body22" data-mdb-perfect-scrollbar="true" style="position: relative; height: 400px;overflow: auto;">
                    @foreach($messages as $message)
                        @if($message["type"]==1)
                            @if($message["file"]!=null)
                                <div style="box-shadow: none;background-color: #f5f6f7;display: inline-block;min-height: auto; margin-bottom: 3px;" class="card-box  widget-user position-relative">
                                    <div>
                                        <div class="btn-download-container">
                                            <a rel="nofollow" class="no-link-inherit" href="/file_ticket/{{$message["file"]}}" download>
                                                <div class="btn-download">
                                                    <i class="fa fa-download"></i>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="wid-u-info">
                                            <h5 class="mt-0 m-b-5"> {{$message["text"]}}</h5>
                                            <small class="text-custom"><a rel="nofollow" class="no-link-inherit tc-white" href="/file_ticket/{{$message["file"]}}" download>دانلود</a></small>
                                        </div>
                                    </div>
                                    <a class="btn btn-danger btn-delete" id="{{$message["id"]}}" style="position: absolute;left: 0;bottom: 0;" data-toggle="tooltip" data-placement="top" data-original-title="حذف"><i class="fa fa-times"></i></a>
                                </div>
                                <p class="small ms-3 mb-3 text-muted">{{$message["am"]}}</p>
                                @else
                                <div class="d-flex flex-row justify-content-start">
                                  <div>
                                    <p class="small p-2 ms-3 mb-1 rounded-3" style="background-color: #f5f6f7;">{{$message["text"]}}</p>
                                    <p class="small ms-3 mb-3 text-muted">{{$message["am"]}}</p>
                                  </div>
                                </div>
                                @endif
                            @else
                            @if($message["file"]!=null)
                                <div class="d-flex flex-row justify-content-end ">
                                    <div>
                                <div style="box-shadow: none;background-color: #188ae2;display: inline-block;min-height: auto; margin-bottom: 3px;" class="card-box  widget-user position-relative">
                                    <div>
                                        <div class="btn-download-container">
                                            <a rel="nofollow" class="no-link-inherit" href="/file_ticket/{{$message["file"]}}" download>
                                                <div class="btn-download">
                                                    <i class="fa fa-download"></i>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="wid-u-info">
                                            <h5 class="mt-0 m-b-5 text-white"> {{$message["text"]}}</h5>
                                            <small class="text-custom"><a rel="nofollow" class="no-link-inherit tc-white text-white" href="/file_ticket/{{$message["file"]}}" download>دانلود</a></small>
                                        </div>
                                    </div>
                                    <a class="btn btn-danger btn-delete"  id="{{$message["id"]}}" style="position: absolute;left: 0;bottom: 0;" data-toggle="tooltip" data-placement="top" data-original-title="حذف"><i class="fa fa-times"></i></a>

                                </div>
                                <p class="small text-right ms-3 mb-3 text-muted">{{$message["am"]}}</p>
                                    </div>
                                </div>
                            @else
                            <div class="d-flex flex-row justify-content-end ">
                              <div>
                                @php $name_extra_class = ($message['id_user'] > 0) ? 'mb-0' : 'mb-3' @endphp
                                <p class="small p-2 me-3 mb-1 text-white rounded-3 bg-primary">{{$message["text"]}}</p>
                                <p class="small me-3 {{$name_extra_class}} text-muted d-flex justify-content-end">{{$message["am"]}} ({{$message["id_user"]}})</p>
                              </div>
                            </div>
                                @endif
                            @endif

                        @endforeach





                </div>
                <form class="form-horizontal form" method="post" action="" role="form" enctype="multipart/form-data"
                            data-parsley-validate novalidate>
                    @csrf

                    <div class="card-footer p-3">
                      <select name="status" class="w-100 p-2 border-info">
                        <option>تغییر وضعیت به ...</option>
                        <option value="0">بسته</option>
                        <option value="4">در انتظار اقدام کاربر</option>
                        <option value="5">بایگانی</option>
                      </select>
                    </div>

                    @php
                      if(
                        $ticket["active"] != 0 //وضعیت بسته
                        &&
                        $ticket["active"] != 5 //وضعیت بایگانی
                      ):
                        $ticket_open = true;
                        $extra_class_card_footer = "justify-content-start";
                      else:
                        $extra_class_card_footer = "justify-content-end";
                      endif
                    @endphp

                            <div class="card-footer text-muted d-flex {{$extra_class_card_footer}} align-items-center p-3" style="flex-wrap: wrap;">
                              @if(isset($ticket_open))
                                <input name="file" id="file_input_hidden" style="display: none;" type="file">
                                <textarea name="text" class="form-control form-control-lg"
                                          id="exampleFormControlInput1"
                                          placeholder="متن پیام ..."
                                          rows="8"
                                          maxlength="3000"
                                          style="resize: vertical; min-height: 160px;"></textarea>
                                <div id="selected_file_name" style="display:none;flex:0 0 100%;font-size:0.9em;color:#28a745;margin:8px 0 0;text-align:right;">
                                    <i class="fa fa-paperclip"></i> فایل انتخاب شده: <span id="file_name_text"></span>
                                </div>
                                <button style="width: 139px;min-width: 98px;" id="input_file" type="button"
                                        class="btn btn-icon waves-effect waves-light btn-info m-b-5 m-l-15 m-r-10">انتخاب فایل</button>
                              @endif

                              <button type="submit" style="width: 71px;min-width: 62px;" class="btn btn-icon waves-effect waves-light btn-success m-b-5">ارسال</button>
                            </div>
                </form>
            </div>

        </div>

    </div>



    <script src="/assets/js/printThis.js"></script>
    <script>


        var body =  $("#card-body22");

        body.stop().animate({scrollTop:body.height()}, 500, 'swing');


        $("#input_file").click(function () {
            $('#file_input_hidden').trigger('click');
        })

        $('#file_input_hidden').on('change', function () {
            var fileName = this.files.length > 0 ? this.files[0].name : '';
            if (fileName) {
                $('#file_name_text').text(fileName);
                $('#selected_file_name').show();
            } else {
                $('#selected_file_name').hide();
            }
        })


        $(".btn-delete").click(function(){
            var id=$(this).attr("id");



            Swal.fire({html:'<div class="mt-3 font-sans"><img src="/img/clipart2619611.png" style="width: 150px;"><div class="mt-4 pt-2 fs-15 mx-5"><h4 class="font-sans">مطمئن هستید ؟  </h4><p style="direction: rtl;" class="text-muted mx-4 mb-0">ایا از حذف فایل مطمئن هستید ؟</p>           <form class="d-inline-block" action="/admin/ticket/message/delete/'+id+'" method="post">@csrf<input name="_method" type="hidden" value="delete"><button type="submit" class="btn btn-danger waves-effect w-md waves-light m-b-5 mt-3" aria-label="">بله , حذف </button> <button type="button" class="btn btn-secondary waves-effect w-md m-b-5 mt-3" id="close-delete">لغو</button></form></div></div>',showCancelButton:!0,confirmButtonClass:"d-none",confirmButtonText:"Yes, Delete It!",cancelButtonClass:"d-none",buttonsStyling:!1,showCloseButton:!0})

            $("#close-delete").click(function(e){
                $(".swal2-close").trigger('click');

            });
        });

    </script>
@endsection
