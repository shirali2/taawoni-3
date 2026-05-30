@extends("theme.user")
@section("container")
    <div class="container">

        <div class="row mt-3">
              <div class="col-12">
                  <div class="alert alert-danger">
                      {{$message}}
                  </div>
              </div>

        </div>
    </div>


@endsection
