@extends("theme.manager")
@section("container")
    <!-- DataTables -->
    <link href="/assets/plugins/fileuploads/css/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/kamadatepicker.min.css" rel="stylesheet"/>
    <script src="/ckeditor/ckeditor.js" type="text/javascript"></script>
    <div class="container">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card-box ">
                    <div class="m-t-0 m-b-30">

                        <h4 class="header-title d-inline-block "> ا ویرایش آگهی</h4>
                        <a href="/manager/advertising/product"
                           class="btn btn-pink btn-trans waves-effect w-md waves-pink m-b-5 float-right">بازگشت</a>
                    </div>


                    <form class="form-horizontal form" method="post" action="" role="form" enctype="multipart/form-data" data-parsley-validate novalidate>
@csrf                     @foreach($errors->all() as $ereor)
                            <div class="alert col-12 mb-3 alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>{{$ereor}}</div>
                        @endforeach
                    <div class="row">


                        <div class="col-12">


                            <div class="form-group row">
                            <label for="inputEmail31" class="col-sm-3 col-form-label">بنر پیش فرض</label>
                            <div class="col-sm-8">
                                <input type="file" name="img" class="dropify" @if($product_advertising["img"]!=null) data-default-file="/advertising_img/{{$product_advertising["img"]}}" @endif>
                            </div>
                        </div>
                            <div class="form-group row">
                            <label for="inputEmail31" class="col-sm-3 col-form-label">نام</label>
                            <div class="col-sm-8">
                                <input parsley-trigger="change" required name="name" value="{{$product_advertising['name']}}" type="text" class="form-control"
                                       id="inputEmail31" placeholder="نام  را وارد کنید">
                            </div>
                        </div>
                            <div class="form-group row">
                            <label for="inputEmail31" class="col-sm-3 col-form-label">قیمت (تومان)</label>
                            <div class="col-sm-8">
                                <input parsley-trigger="change" required name="price" value="{{$product_advertising['price']}}" type="text" class="form-control"
                                       id="inputEmail31" placeholder="قیمت را وارد کنید">
                            </div>
                        </div>

                            <div class="form-group row">
                                <div class="checkbox">
                                    <input id="checkbox0" name="active" @if($product_advertising['active']==1) checked @endif type="checkbox">
                                    <label for="checkbox0">
                                        فعال بودن ایتم برای فروش
                                    </label>
                                </div>
                        </div>

                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <div class="offset-sm-4 text-right col-sm-12">
                                    <button type="submit" class="btn btn-primary btn-trans waves-effect waves-primary w-md m-b-5">
                                       ویرایش
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                    </form>

                </div>
            </div>
        </div>


    </div>


    <script src="/assets/plugins/fileuploads/js/dropify.min.js"></script>
    <script src="/assets/js/kamadatepicker.min.js"></script>
    <script>

        $('.dropify').dropify({
            messages: {
                'default': 'Drag and drop a file here or click',
                'replace': 'Drag and drop or click to replace',
                'remove': 'Remove',
                'error': 'Ooops, something wrong appended.'
            },
            error: {
                'fileSize': 'The file size is too big (1M max).'
            }
        });


    </script>


@endsection
