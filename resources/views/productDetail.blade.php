@extends('layout.main')

@php
	use App\Models\sepatu;
	use App\Models\kategori;
	use App\Models\DetailSepatu;
	use App\Models\supplier;
	$userLoggedIn = Session::get('userLoggedIn');
	$listSepatu = sepatu::All();
	$listDetail = DetailSepatu::All();
    $listCategory = kategori::all();
    $listBrand = supplier::all();

    $kategori = '' ;
    $brand = '' ;

	$listSize = DetailSepatu::select('detail_sepatu_ukuran')
	->where('fk_sepatu','=',$sepatu['id'])
	->distinct()
	->get();

	$listWarna = DetailSepatu::select('detail_sepatu_warna')
	->where('fk_sepatu','=',$sepatu['id'])
	->distinct()
	->get();

	$gambar = '';
	$harga = 0;

	foreach($listDetail as $key) {
        if ($key->fk_sepatu == $sepatu['id']) {
            $gambar = $key->detail_sepatu_gambar;
			$harga = $key->detail_sepatu_harga;
        }
    }

    foreach($listCategory as $key) {
        if ($key->kategori_id == $sepatu['kategori']) {
            $kategori = $key->kategori_nama;
        }
    }

    foreach($listBrand as $key) {
        if ($key->supplier_id == $sepatu['supplier']) {
            $brand = $key->supplier_name;
        }
    }

@endphp

@section('content')

	<!-- Start Banner Area -->
	<section class="banner-area organic-breadcrumb">
		<div class="container">
			<div class="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
				<div class="col-first">
					<h1>Detail Product</h1>
					<nav class="d-flex align-items-center">
						<a href="/home">Home<span class="lnr lnr-arrow-right"></span></a>
						<a href="/products">Shop<span class="lnr lnr-arrow-right"></span></a>
						<a href="/products/{{$sepatu['id']}}">Detail</a>
					</nav>
				</div>
			</div>
		</div>
	</section>
	<!-- End Banner Area -->

	<!--================Single Product Area =================-->
	<form action="{{ route('to-cart-or-checkout') }}" method="post">
		@csrf
		<div class="product_image_area">
			<div class="container">
				<div class="row s_product_inner">
					<div class="col-lg-6">
						<img src="{{ Storage::url("photo/$gambar") }}" class="img-fluid"  alt="">
					</div>
					<div class="col-lg-5 offset-lg-1">
						<div class="s_product_text">
							<input type="hidden" name="sepatu_id" value="{{ $sepatu['id'] }}">
							<h3>{{ $sepatu['name'] }}</h3>
							<h2>{{ formatCurrencyIDR($harga) }}</h2>
							<ul class="list">
								<li><a class="active" href="category/{{$sepatu['kategori']}}"><span>Category</span> : {{ $kategori }}</a></li>
								<li><a class="active" href="brand/{{$sepatu['supplier']}}"><span>Brand</span> : {{ $brand }}</a></li>
							</ul>
							<p>
								{{ $sepatu['name'] }} <br>
								Size : <br>
									<select name="size" style="height:10vh;width:15vw">
										@foreach ($listSize as $key => $size)
											<option id="{{ $size->detail_sepatu_ukuran }}"  value="{{ $size->detail_sepatu_ukuran }}">{{ $size->detail_sepatu_ukuran }}</option>
										@endforeach
									</select>	
								<br> <br>
								Color : <br>
								<select name="color" id="">
									@foreach ($listWarna as $key => $warna)
										<option id="{{ $warna->detail_sepatu_warna }}"  value="{{ $warna->detail_sepatu_warna }}"> {{ $warna->detail_sepatu_warna }}</option>
									@endforeach
								</select>
								<div class="product_count">
									<label for="qty">Quantity:</label>
									<input type="text" name="qty" id="sst" maxlength="12" value="1" title="Quantity:" class="input-text qty">
									<button onclick="var result = document.getElementById('sst'); var sst = result.value; if( !isNaN( sst )) result.value++;return false;"
									class="increase items-count" type="button"><i class="lnr lnr-chevron-up"></i></button>
									<button onclick="var result = document.getElementById('sst'); var sst = result.value; if( !isNaN( sst ) &amp;&amp; sst > 0 ) result.value--;return false;"
										class="reduced items-count" type="button"><i class="lnr lnr-chevron-down"></i></button>
								</div>
								<br> <br>
								Stock : {{ $sepatu['stock'] }}
							</p>
							<div class="card_area d-flex align-items-center">
								<button type="submit" name="cart" class="icon_btn border-0"><i class="ti-bag"></i></button>
								<button type="submit" name="checkout" class="primary-btn border-0"><i class="ti-money"></i>Checkout</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
	<!--================End Single Product Area =================-->

	<!--================Product Description Area =================-->
	<section class="product_description_area">
		<div class="container">
			<ul class="nav nav-tabs" id="myTab" role="tablist" style="height:3vh">

			</ul>

		</div>
	</section>
	<!--================End Product Description Area =================-->

	<!-- Start related-product Area -->

	@include('layout.deals-of-the-week')
	
	<!-- End related-product Area -->


@endsection
