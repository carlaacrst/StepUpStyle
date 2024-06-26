<?php

namespace App\Http\Controllers;

use App\Charts\chartPenjualan;
use App\Models\DetailSepatu;
use App\Models\dtrans;
use App\Models\htrans;
use App\Models\kategori;
use App\Models\notifikasi;
use App\Models\retur;
use App\Models\sepatu;
use App\Models\SubKategori;
use App\Models\supplier;
use App\Models\wishlist;
use App\Models\user;
use App\Services\RajaOngkir;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

// Mengatur perpindahan halaman
class PageController extends Controller
{
    // public function viewTest() {
    //     $rajaOngkir = new RajaOngkir(config('rajaongkir.API'));
    //     $cost = $rajaOngkir->postCost(384);
    //     return view('payment.test', compact('cost'));
    // }

    // USER

    public function viewHome(){
        //pengecekan Auth User
        return view('home');
    }

    public function viewLogin(){
        //pengecekan Auth User
        return view('login');
    }

    public function viewRegister(){
        //pengecekan Auth User
        return view('register');
    }

    public function viewContact(){
        //pengecekan Auth User
        return view('contact');
    }

    public function viewAllProducts(){
        //select DB
        $page = "All Products";

        $listSepatu = sepatu::All();

        return view('products', compact('listSepatu'));
    }

    public function viewDetailRetur(Request $request, $id){
        //select DB
        $retur = retur::find($id);
        // dd($retur);
        $stock = retur::where('fk_sepatu','=',$retur->fk_sepatu)
                        ->where('retur_status','=',1)->count();
        $sepatu = [
            "id" => $retur->fk_sepatu,
            "supplier" => $retur->sepatu->sepatu_supplier_id,
            "kategori" => $retur->sepatu->sepatu_kategori_id,
            "ukuran" => $retur->sepatu->sepatu_ukuran_id,
            "picture" => $retur->retur_pict,
            "name" => $retur->sepatu->sepatu_name,
            "stock" => $stock,
            "price" => $retur->sepatu->sepatu_price,
            "color" => $retur->sepatu->sepatu_color,
        ];


        return view('product-retur-detail', ["sepatu" => $sepatu, "retur" => $retur]);
    }

    public function viewDetailProduct(Request $request){
        //select DB
        $page = "Detail Products";
        $listSepatu = sepatu::all();
        $id = $request->id;
        $sepatu = [];
        foreach ($listSepatu as $key => $f) {
            if ($f->sepatu_id == $id) {
                $sepatu = [
                    "id" => $f->sepatu_id,
                    "supplier" => $f->sepatu_supplier_id,
                    "kategori" => $f->sepatu_kategori_id,
                    "ukuran" => $f->sepatu_ukuran_id,
                    "picture" => $f->sepatu_pict,
                    "name" => $f->sepatu_name,
                    "stock" => $f->sepatu_stock,
                    "price" => $f->sepatu_price,
                    "color" => $f->sepatu_color,
                ];
            }
        }

        return view('productDetail', ["sepatu" => $sepatu]);
    }

    function getSizeDetail($id, $size)
    {
        $temp = DetailSepatu::select('*')
        ->where('fk_sepatu','=',$id)
        ->where('detail_sepatu_ukuran','=',$size)
        ->orderBy('detail_sepatu_ukuran')
        ->get();

        $listcolor = array();
        $price = 0;
        $stock = 0;
        $picture = "";
        foreach ($temp as $key => $item) {
            if ($key == 0) {
                $price = $item->detail_sepatu_harga;
                $stock = $item->detail_sepatu_stok;
                $picture = $item->detail_sepatu_pict;
            }
            $listcolor[] = [
                'nama' => $item->detail_sepatu_warna
            ];
        }

        return response()->json([
            'listcolor' => $listcolor ?? [],
            'price' => $price,
            'stock' => $stock,
            'picture' => $picture
        ]);
    }

    function getColorDetail($id, $size, $color)
    {
        $temp = DetailSepatu::select('*')
        ->where('fk_sepatu','=',$id)
        ->where('detail_sepatu_ukuran','=',$size)
        ->where('detail_sepatu_warna','=',$color)
        ->get();
        $price = $temp[0]['detail_sepatu_harga'];
        $stock = $temp[0]['detail_sepatu_stok'];
        $picture = $temp[0]->detail_sepatu_pict;
        return response()->json([
            'price' => $price,
            'stock' => $stock,
            'picture' => $picture
        ]);
    }

    public function viewNewArrival(){
        //select DB
        $page = "New Arrival";

        $listSepatu = sepatu::orderBy('created_at', 'DESC')->get();

        return view('products-new-arrival', compact('listSepatu'));
    }

    public function viewCategoryProducts(Request $request){
        //select DB
        $page = "Category";
        $id = $request->id;
        $listSepatu = sepatu::where('sepatu_kategori_id','=',$id)->get();
        return view('products-category', compact('listSepatu'));
    }

    public function viewSubCategoryProducts(Request $request){
        //select DB
        $page = "Category";
        $id = $request->id;
        $listSepatu = sepatu::where('sepatu_subkategori_id','=',$id)->get();
        return view('products-category', compact('listSepatu'));
    }

    public function viewBestSeller(){
        //select DB
        $page = "Best Seller";

        // $listSepatu = DB::table('sepatu')
        // ->join('dtrans_penjualan', 'sepatu.sepatu_id', '=', 'dtrans_penjualan.fk_detail_sepatu')
        // ->select('sepatu.sepatu_id','sepatu.sepatu_name', DB::raw('count(*) as total_bought'))
        // ->groupBy('sepatu.sepatu_id','sepatu.sepatu_name')
        // ->orderByDesc('total_bought')
        // ->get();

        // $listSepatu = DB::table('sepatu')
        // ->join('detail_sepatu', 'sepatu.sepatu_id', '=', 'detail_sepatu.fk_sepatu')
        // ->join('dtrans_penjualan', 'detail_sepatu.detail_sepatu_id', '=', 'dtrans_penjualan.fk_detail_sepatu')
        // ->select('sepatu.sepatu_id', 'sepatu.sepatu_name', DB::raw('SUM(dtrans_penjualan.dtrans_penjualan_qty) as total_bought'))
        // ->groupBy('sepatu.sepatu_id', 'sepatu.sepatu_name')
        // ->orderByDesc('total_bought')
        // ->get();

        $listSepatu = sepatu::join('detail_sepatu', 'sepatu.sepatu_id', '=', 'detail_sepatu.fk_sepatu')
        ->join('dtrans_penjualan', 'detail_sepatu.detail_sepatu_id', '=', 'dtrans_penjualan.fk_detail_sepatu')
        ->select('sepatu.sepatu_id', 'sepatu.sepatu_name', DB::raw('SUM(dtrans_penjualan.dtrans_penjualan_qty) as total_bought'))
        ->groupBy('sepatu.sepatu_id', 'sepatu.sepatu_name')
        ->orderByDesc('total_bought')
        ->get();

        return view('products-best-seller', compact('listSepatu'));
    }

    public function viewBrandProducts(Request $request){
        //select DB
        $page = "Brand";
        $id = $request->id;
        $listSepatu = sepatu::where('sepatu_supplier_id','=',$id)->get();

        return view('products-brand', compact('listSepatu'));
    }

    public function viewSearchProducts(Request $request){
        //select DB
        $page = "Search";
        $search = $request->search;
        $listSepatu = sepatu::where('sepatu_name',$request->search)
        ->orWhere('sepatu_name','like',"%{$request->search}%")
        ->get();;

        return view('products-search', compact('listSepatu'));
    }

    public function viewFilteredProducts(Request $request){
        //select DB
        $page = "Filter";

        $query = sepatu::query();

        if ($request->has('brand')) {
            $query->whereIn('sepatu_supplier_id', $request->brand);
        }

        if ($request->has('size')) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->whereIn('detail_sepatu_ukuran', $request->size);
            });
        }

        if ($request->has('color')) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->whereIn('detail_sepatu_warna', $request->color);
            });
        }

        if ($request->has('min_price') && $request->min_price !== null) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->where('detail_sepatu_harga', '>=', $request->min_price);
            });
        }

        if ($request->has('max_price') && $request->max_price !== null) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->where('detail_sepatu_harga', '<=', $request->max_price);
            });
        }

        $listSepatu = $query->get();

        return view('products-filter', compact('listSepatu'));
    }

    public function viewFlashSale(){
        $userLoggedIn = Session::get('userLoggedIn');
	    $listRetur = retur::where('retur_status','=',1)->get();
        // dd($listRetur);

        return view('products-flashsale', compact('listRetur'));
    }

    public function viewCart(){
        //pengecekan Auth User
        $cartSepatu = json_decode(Cookie::get('cartSepatu'), true) ?? [];
        $rajaOngkir = new RajaOngkir(config('rajaongkir.API'));
        $cities = $rajaOngkir->getCities();
        $provinces = $rajaOngkir->getProvinces();
        return view('cart', compact('cartSepatu', 'cities', 'provinces'));
    }

    public function backPage(Request $request){
        return redirect($request->input('url'));
    }

    public function toCartOrCheckout(Request $req){
        if ($req->has('cart')) {
            return redirect()->route('add-to-cart', [
                "id" => $req->input('sepatu_id'),
                "size" =>  $req->input('size'),
                "color" =>  $req->input('color'),
                "qty" =>  $req->input('qty')
            ]);
            // return redirect('cart/add/{}');
        }
        else if($req->has('checkout')){
            $tempCart = json_decode(Cookie::get('cartSepatu'), true);
            Cookie::queue('tempCart', json_encode($tempCart));
            $cart = [];
            Cookie::queue('cartSepatu', json_encode($cart), 1209600);

            return redirect()->route('add-to-cart', [
                "id" => $req->input('sepatu_id'),
                "size" =>  $req->input('size'),
                "color" =>  $req->input('color'),
                "qty" =>  $req->input('qty')
            ]);
            // return redirect()->route('add-to-cart', [
            //     "id" => $req->input('sepatu_id'),
            //     "size" =>  $req->input('size'),
            //     "color" =>  $req->input('color'),
            //     "qty" =>  $req->input('qty')
            // ]);
        }
    }

    public function viewWishlist(Request $request){
        //select DB
        $page = "Wishlist";

        $userLoggedIn = Session::get('userLoggedIn');

        // $listWishlist = wishlist::where('fk_customer', '=', $userLoggedIn['id'])
        //                     ->with('shoe')
        //                     ->get();

        $listWishlist = wishlist::where('fk_customer', $userLoggedIn['id'])
                        ->with(['shoe.details' => function ($query) {
                        $query->select('fk_sepatu', 'detail_sepatu_pict');
                        }])
                        ->get();

        return view('wishlist', compact('listWishlist'));
    }


    // public function viewCheckout(){
    //     //pengecekan Auth User
    //     $userLoggedIn = Session::get('userLoggedIn');
    //     $cartSepatu = json_decode(Cookie::get('cartSepatu'), true) ?? [];
    //     return view('checkout', compact('cartSepatu', 'userLoggedIn'));
    // }

    public function viewFormRetur($dtrans_id){
        //pengecekan Auth User
        $userLoggedIn = Session::get('userLoggedIn');
        $dtrans = dtrans::find($dtrans_id);
        // Cookie::queue('tempRetur', json_encode($dtrans ), 1209600);
        // $tempRetur = json_decode(Cookie::get('tempRetur'), true);
        // dd($dtrans );

        if($dtrans) {
            $detailSepatu = $dtrans->detail;
            $sepatu = $detailSepatu->sepatu;
            // dd($sepatu);
            Cookie::queue('tempRetur', json_encode($dtrans), 1209600);
            return view('retur-form', compact('userLoggedIn', 'dtrans', 'detailSepatu','sepatu'));
        } else {
            return redirect()->back()->with('error', 'Data not found.');
        }
    }

    public function viewOrders(){
        //pengecekan Auth User
        $userLoggedIn = Session::get('userLoggedIn');
        $orders = user::find($userLoggedIn['id'])->orders()->orderBy('created_at', 'DESC')->get();
        return view('history', compact('orders'));
    }

    public function viewProfile(){
        //pengecekan Auth User
        return view('profile');
    }

    public function viewCategory(){
        //pengecekan Auth User
        return view('category');
    }

    // ADMIN

    public function viewAdminNotif()
    {
        $list = notifikasi::all()->sortByDesc("created_at");
        $listnotif_order = array();
        $listnotif_retur = array();
        foreach ($list as $notif) {
            if ($notif->notifikasi_status == 0) {
                if ($notif->notifikasi_type == 1) {
                    $listnotif_order[] = [
                        'id' => $notif->notifikasi_id,
                        'content' => $notif->notifikasi_content,
                        'diff' => Carbon::parse($notif->created_at)->diffForHumans()
                    ];
                } else {
                    $listnotif_retur[] = [
                        'id' => $notif->notifikasi_id,
                        'content' => $notif->notifikasi_content,
                        'diff' => Carbon::parse($notif->created_at)->diffForHumans()
                    ];
                }
            }
        }

        $unread_order = count($listnotif_order);
        $unread_retur = count($listnotif_retur);

        return view('admin.notifikasi.adminnotif', compact('listnotif_order', 'listnotif_retur', 'unread_order', 'unread_retur'));
    }


    function viewAdminUser() {
        return view('admin.user.adminuser',['listuser'=>user::withTrashed()->where('user_role', 'customer')->get()]);
    }
    function viewAdminAddUser(){
        return view('admin.user.adminadduser');
    }
    function viewAdminEditUser(Request $request){
        return view('admin.user.adminedituser',['IdUser'=>user::find($request->id)]);
    }


    function viewAdminProduct(){
        return view('admin.product.adminproduct',['listproduk'=>sepatu::orderBy('created_at','desc')->get()]);
    }
    function viewAdminVarianProduct($id){
        return view('admin.product.adminvarianproduct',[
            'sepatu' => sepatu::find($id),
            'listvarian' => DetailSepatu::withTrashed()->where('fk_sepatu', $id)->orderBy('detail_sepatu_ukuran')->get()
        ]);
    }
    function viewAdminAddProduct(){
        return view('admin.product.adminaddproduct' ,[
            'listkategori'=>kategori::all(),
            'listsupplier'=>supplier::all()
        ]);
    }
    function viewAdminAddVarianProduct($id){
        return view('admin.product.adminaddvarianproduk', ['sepatu' => sepatu::find($id)]);
    }
    function getSubKategori($id)
    {
        $temp = SubKategori::where('fk_kategori', $id)->get();
        $listsubkategori = array();
        foreach ($temp as $item) {
            $listsubkategori[] = [
                'id' => $item->subkategori_id,
                'nama' => $item->subkategori_nama
            ];
        }
        return response()->json($listsubkategori ?? []);
    }
    function viewAdminEditProduct(Request $request){
        return view('admin.product.admineditproduct' ,[
            'IdProduct'=>sepatu::find($request->id),
            'listkategori'=>kategori::all(),
            'listsupplier'=>supplier::all(),
        ]);
    }
    function viewAdminEditVarianProduct(Request $request){
        return view('admin.product.admineditvarianproduct' ,['varian'=>DetailSepatu::find($request->varian)]);
    }


    function viewAdminKategori(){
        return view('admin.kategori.adminkategori',['listkategori'=>kategori::withTrashed()->get()]);
    }
    function viewAdminVarianKategori($id){
        return view('admin.kategori.adminsubkategori',[
            'kategori'=>kategori::find($id),
            'listsubkategori'=>SubKategori::where('fk_kategori', $id)->get()
        ]);
    }
    function viewAdminAddKategori(){
        return view('admin.kategori.adminaddkategori');
    }
    function viewAdminAddVarianKategori($id){
        return view('admin.kategori.adminaddvariankategori',[
            'id'=>$id
        ]);
    }
    function viewAdminEditKategori(Request $request){
        return view('admin.kategori.admineditkategori',['IdKategori'=>kategori::find($request->id)]);
    }
    function viewAdminEditSubKategori(Request $request){
        return view('admin.kategori.admineditsubkategori',['IdSubKategori'=>SubKategori::find($request->sub)]);
    }

    function viewAdminSupplier(){
        return view('admin.supplier.adminsupplier',['listsupplier'=>supplier::all()]);
    }
    function viewAdminAddSupplier(){
        return view('admin.supplier.adminaddsupplier');
    }
    function viewAdminEditSupplier(Request $request){
        return view('admin.supplier.admineditsupplier',['IdSupplier'=>supplier::find($request->id)]);
    }


    function viewAdminRetur(){
        // $namaFilePhotosJson = $retur->retur_foto;
        // $namaFilePhotos = json_decode($namaFilePhotosJson, true);
        return view('admin.retur.adminretur',['listretur'=>retur::all()]);
    }


    function viewAdminOrder()
    {
        return view('admin.order.adminorder', ['listorder'=>htrans::where('htrans_penjualan_status', 2)->get()]);
    }
    function viewAdminDetailOrder($id)
    {
        return view('admin.order.adminDetailOrder', [
            "listdtrans"=>dtrans::where('fk_htrans_penjualan', $id)->get(),
            "listhtrans"=>htrans::where('htrans_penjualan_id', $id)->get()
        ]);
    }

    // OWNER

    function viewMasterAdmin(){
        return view('owner.ownerAdmin', ['listadmin' => user::where('user_role', 'owner')->get()]);
    }

    function viewLaporanPenjualan(chartPenjualan $chart){
        return view('owner.laporan.laporanpenjualan',['listhtrans' => htrans::where('htrans_penjualan_status', 2)->get(),'chart' => $chart->build()]);
    }
    function viewDetailLaporanPenjualan(Request $request){
        return view('owner.laporan.detailLaporanPenjualan',[
            "listdtrans"=>dtrans::where('fk_htrans_penjualan', $request->id)->get(),
            "listhtrans"=>htrans::where('htrans_penjualan_id', $request->id)->get()
        ]);
    }


    function viewLaporanRetur(){
        return view('owner.laporan.laporanretur',['listretur'=>retur::all()]);
    }


    function viewLaporanProduct(){
        return view('owner.Laporan.laporanProduct',['listproduct'=> Sepatu::all()]);
    }
}
