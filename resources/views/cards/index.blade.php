@extends('app')

@section('title', 'ストア一覧')

@section('gmap_js')
  @if(app('env')=='local')
    <script src="{{ asset('/js/dis_rix_index.js') }}"></script>
    <script src="{{ asset('/js/gmap_api.js') }}"></script>
  @endif
  @if(app('env')=='production')
    <script src="{{ secure_asset('/js/dis_rix_index.js') }}"></script>
    <script src="{{ secure_asset('/js/gmap_api.js') }}"></script>
  @endif
@endsection

@section('php')
@php
$gmap_key = config('app.gmap_key');
$link_show = config('app.link_show');

$max = count($stores);

$obj = json_decode(json_encode($geos));
for ($i = 0; $i < $max; $i++) {
  $lat[$i] = $obj->data[$i]->lat;
  $lng[$i] = $obj->data[$i]->lng;
  $value[$i] = "$lat[$i],$lng[$i]";
}

for($i = 0; $i < $max; $i++) {
  if($stores[$i]->smoking == '全席喫煙可') {
  $stores_js[$i] = [
    'id' => $stores[$i]->id,
    'name' => $stores[$i]->name,
    'score' => $stores[$i]->score,
    'type' => $stores[$i]->type,
    'station' => $stores[$i]->station,
    'distance' => '',
    'time' => '',
    'smoking_green' => $stores[$i]->smoking,
    'smoking_yellow' => '',
    'url' => $link_show . $stores[$i]->id,
    'store_pic' => $stores[$i]->store_pic
    ];
  } else {
    $stores_js[$i] = [
    'id' => $stores[$i]->id,
    'name' => $stores[$i]->name,
    'score' => $stores[$i]->score,
    'type' => $stores[$i]->type,
    'station' => $stores[$i]->station,
    'distance' => '',
    'time' => '',
    'smoking_green' => '',
    'smoking_yellow' => $stores[$i]->smoking,
    'url' => $link_show . $stores[$i]->id,
    'store_pic' => $stores[$i]->store_pic
    ];
  }
};

@endphp
@endsection

@section('script_map')
<script>
  let geos = JSON.parse('<?php echo json_encode($value) ?>');
  let stores = JSON.parse('<?php echo json_encode($stores_js) ?>');
  let gmap_key = JSON.parse('<?php echo json_encode($gmap_key) ?>');
  let max = geos.length;
</script>
@endsection

@section('content')
@include('nav')
<div class="container px-0" id="main">
  <!-- jsでinsertBeforeを指定する用の捨て要素 -->
  <div id="sute"></div>
  @for($i = 0; $i < $max; $i++)
  @php
  $store = $stores_js[$i]
  @endphp
<div class="card">
  <div class="row py-2 pl-2 pr-4">

    <div class="col-3 px-0">
      <div>
        <img class="d-block mx-auto rounded-circle store_img" src="{{ $store['store_pic'] }}" id="{{ 'store_pic' . $i }}"></img>
      </div>
    </div>

    <div class="col-6 px-0">
      <h3 class="f-title h6 card-title mb-1" id="{{ 'name' . $i }}">
          {{ $store['name'] }}
      </h3>
      <div class="row">
        <div class="col-8 pr-0">
          <div class="font-weight-lighter f-small pb-1" id="{{ 'score' . $i }}">
            食べログ評価 : {{ $store['score'] }}
          </div>
          <div class="font-weight-lighter f-small pb-1" id="{{ 'type' . $i }}">
            ジャンル : {{ $store['type'] }}
          </div>
          <div class="font-weight-lighter f-small pb-1" id="{{ 'station' . $i }}">
            最寄駅 : {{ $store['station'] }}
          </div>
        </div>
        <div class="col-4 px-0 pt-1 text-center">
        <div class="description green-text f-small" id="{{ 'smoking_green' . $i }}">
          {{ $store['smoking_green'] }}
        </div>
        <div class="description orange-text f-small" id="{{ 'smoking_yellow' . $i }}">
          {{ $store['smoking_yellow'] }}
        </div>
        </div>
      </div>
    </div>

    <div class="col-3 px-0 pt-1">
      <div>
        <div>
          <div class="row">
            <div class="col-7 px-0 f-small text-right">ここから徒歩 : </div>
            <div class="col-1 px-0 f-small text-right orange-text" id="{{ 'time_h' . $i }}"></div>
            <div id="{{ 'hour' . $i }}" class="col-4 pl-0 f-small text-center"></div>
          </div>
          <div class="row">
            <div class="col-8 px-0 f-small text-right orange-text" id="{{ 'time_m' . $i }}"></div>
            <div id="minuts" class="col-4 pl-0 f-small text-center">　分</div>
          </div>
        </div>
        <div class="row">
          <div class="col-8 pr-0 green-text text-right" id="{{ 'distance' . $i }}"></div>
          <div class="col-4 pl-0 text-center">km</div>
        </div>
        <div class="pl-4">
          <div class="green-btn rounded-pill text-center">
            <a class="f-small text-white" type="text/html" href="{{ $store['url'] }}" id="{{ 'url' . $i }}">詳細を見る</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 
  <div class="card mar-t-3 row">
    <div class="col-md-8 card-body d-flex flex-row pb-2">
      <a class="stretched-link" href="{{ $store['url'] }}" id="{{ 'url' . $i }}"></a>
      <img class="rounded-circle store_img" src="{{ $store['store_pic'] }}" id="{{ 'store_pic' . $i }}"></img>
      <div>
        <div>
          <h3 class="font-weight-bold h5 card-title mb-1" id="{{ 'name' . $i }}">
            {{ $store['name'] }}
          </h3>
          <a class="description" id="{{ 'type' . $i }}">
            ジャンル : {{ $store['type'] }}
          </a>
          <div class="font-weight-lighter" id="{{ 'score' . $i }}">
            スイログ評価 ( {{ $store['score'] }} )
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4 card-body pt-0 pb-2">
      <div class="description" id="{{ 'station' . $i }}">
      最寄駅 : {{ $store['station'] }}
      </div>
      <div class="d-flex">
        <div class="description pr-3" id="{{ 'distance' . $i }}"></div>
        <div class="description" id="{{ 'time' . $i }}"></div>
      </div>
      <div class="description text-success" id="{{ 'smoking_green' . $i }}">
      {{ $store['smoking_green'] }}
      </div>
      <div class="description text-warning" id="{{ 'smoking_yellow' . $i }}">
      {{ $store['smoking_yellow'] }}
      </div>
    </div>
  </div> -->
  @endfor
  <!-- ペジネーション -->
  <div class="row d-flex justify-content-center mt-4">
    {{ $stores->links() }}
  </div>
</div>

<!-- マップ -->
<div id="map" class="map-unuse"></div>
@endsection
