@php
	use App\Models\sepatu;
	use App\Models\wishlist;
	use App\Models\DetailSepatu;
	$userLoggedIn = Session::get('userLoggedIn');

	$listDetail = DetailSepatu::all();
    $listWishlist = wishlist::all();

	$price = 0;
	$gambar = '';
@endphp


<section class="lattest-product-area pb-40 category-list">
    <div class="row">
        @forelse ($listSepatu as $key=>$sepatu)
        <!-- single product -->  
                @php
                    $price = $sepatu->details[0]->detail_sepatu_harga ;
                    $gambar =  $sepatu->details[0]->detail_sepatu_pict ; 
                @endphp

                <a href="{{ route('product-detail', $sepatu->sepatu_id) }}">
                <div class="col-lg-4 col-md-6">
                    <div class="single-product" style="width: max-content">
                    <img class="img-fluid object-fit-cover" style="width: 15vw; height: 30vh;" src="{{ Storage::url("photo/$gambar") }}" alt="" >
                        <div class="product-details">
                            <h6>{{ $sepatu->sepatu_name }}</h6>
                            <div class="price">
                                <h6>{{ formatCurrencyIDR($price) }}</h6>
                                <h6 class="l-through">{{ formatCurrencyIDR($price + 50000) }}</h6>
                            </div>
                            
                            <div class="prd-bottom" style="padding: 0px; margin: 0;">
                                {{-- <a href="{{ route('add-to-whislist', $sepatu->sepatu_id) }}" class="social-info"> --}}
                                {{-- <span class="ti-star"></span> --}}
                                
                                    @if (isset($userLoggedIn)) 
                                        @php
                                            $wishlist = wishlist::where('fk_sepatu', '=', $sepatu->sepatu_id)
                                                ->where('fk_customer', '=', $userLoggedIn['id'])
                                                ->count();
                                        @endphp
                                    @else 
                                        @php
                                            $wishlist = 0
                                        @endphp
                                    @endif

                                @if ($wishlist == 0)
                                    <form action="{{ route('add-to-wishlist') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="url" value="{{  URL::current()  }}">
                                        <input type="hidden" name="sepatu_id" value="{{  $sepatu->sepatu_id  }}">
                                        <button type="submit" class="like" style="height: fit-content; width: fit-content; background-color: #ffffff00; border: 0px">
                                        {{-- <button class="like"> --}}
                                            <i class="fa-regular fa-heart fa-2x" style="color: #ffa600;  cursor: pointer"></i>
                                        {{-- </button>     --}}
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('remove-from-wishlist') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="url" value="{{  URL::current()  }}">
                                        <input type="hidden" name="sepatu_id" value="{{  $sepatu->sepatu_id  }}">
                                        <button type="submit" class="like" style="height: fit-content; width: fit-content; background-color: #ffffff00; border: 0px">
                                        {{-- <button class="like"> --}}
                                            <i class="fa-solid fa-heart fa-2x" style="color: #ffa600;  cursor: pointer"></i>
                                        {{-- </button>     --}}
                                        </button>
                                    </form>
                                    
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
                </a>
            
        @empty
            <div style="height: 15vh;"></div>
            <h1 style="margin:auto">No Products Yet</h1>
        @endforelse
    </div>


</section>