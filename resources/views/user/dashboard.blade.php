@extends("theme.user")
@section("container")
@php
$user_grop = App\user_grop::findOrfail(session('grop_user'));

$id_grop=$user_grop['grop_id'];
$form = App\form::where(function ($query) use ($id_grop) {
    $query->where(function ($query) use ($id_grop) {
        // Check if id_grop is an array in the database
        $query->whereRaw("JSON_OVERLAPS(id_grop, json_array(?))", [$id_grop]);
    })->orWhere(function ($query) use ($id_grop) {
        // Check if id_grop is not an array (single value)
        $query->where('id_grop', $id_grop);
    });
	})->where("show_onuserpanel",1)->get();
	$formIds = $form->pluck('id')->toArray();
	
	$under = App\under::whereIn('id_form', $formIds)
		->whereNotNull('id_menu')
		->whereIn('id_menu', App\menu::pluck('id'))
		->get();

@endphp
    <div class="row justify-content-center" style="
    gap: 5px;
">
                @foreach($under as $item)
    @if($item->id_menu) <!-- Check if 'menu' exists -->
        <a class="btn btn-info" href="{{ url('user/menu/' . $item->id_menu . '/' . $item->id) }}">
              {{ $item->name }}
        </a><br>
    @endif
@endforeach
        <div class="col-12 mb-3">
            <div  class="card-box table-responsive ">
                <div class="text-center">

                    <img style="max-width: 100%;" src="/grop_img/{{$grop["img2"]}}">
                </div>
            </div>

        </div>
        

    </div>

@endsection
