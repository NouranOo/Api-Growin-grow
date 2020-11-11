<?php

namespace App\Http\Controllers;

use App\Models\area;
use App\Models\cartItem;
use App\Models\company;
use App\Models\Governorate;
use App\Models\news;
use App\Models\order;
use App\Models\pharmacy;
use App\Models\product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use validator;

class PharmacyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function Register(Request $request)
    {
        //validate
        $rules = [
            'pharmacy_name' => 'required|unique:pharmacies|max:15|min:3',
            'pharmacy_address' => 'required',
            'email' => 'required|unique:pharmacies',
            'owner_name' => 'required',
            'password' => 'required|min:6|same:confirm_password',
            'confirm_password' => 'required',
            'region' => 'required',
            
            'available_time' => 'required',
            'branch_number' => 'required|numeric',
            'Token' => 'required',
            'doctor_name' => 'required',
            'area_id' => 'required|numeric',
            'mobile_number' => 'required',
            'apiKey' => 'required',

        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            $result = array('status' => false, 'code' => '403', "errors" => $validation->errors(), 'message' => 'You must send data Successfully!');
            return response()->json($result, 404, []);
        }
        // $api_key = env('APP_KEY');
        // if ($api_key != $request->apiKey) {
        //     $result = array('status' => false, 'code' => '401', 'message' => 'Unauthorized!');
        //     return response()->json($result, 401, []);
        // }
        //save data in database

        $data = $request->except(['logo', 'commerical_registration', 'union_license', 'pharmacy_license', 'password']);

        $data['password'] = Hash::make($request->password);
        //upload Files

        if ($request->hasFile('pharmacy_license')) {

            $file = $request->file("pharmacy_license");
            $filename = str_random(6) . '_' . time() . '_' . $file->getClientOriginalName();
            $path = 'projectFiles/pharmacy_license';
            $file->move($path, $filename);
            $data['pharmacy_license'] = $path . '/' . $filename;
        }
        if ($request->hasFile('union_license')) {

            $file = $request->file("union_license");
            $filename = str_random(6) . '_' . time() . '_' . $file->getClientOriginalName();
            $path = 'projectFiles/union_license';
            $file->move($path, $filename);
            $data['union_license'] = $path . '/' . $filename;
        }
        if ($request->hasFile('commerical_registration')) {

            $file = $request->file("commerical_registration");
            $filename = str_random(6) . '_' . time() . '_' . $file->getClientOriginalName();
            $path = 'projectFiles/commerical_registration';
            $file->move($path, $filename);
            $data['commerical_registration'] = $path . '/' . $filename;
        }
        if ($request->hasFile('logo')) {

            $file = $request->file("logo");
            $filename = str_random(6) . '_' . time() . '_' . $file->getClientOriginalName();
            $path = 'projectFiles/pharmacylogos';
            $file->move($path, $filename);
            $data['logo'] = $path . '/' . $filename;
        }

        $data['ApiToken'] = base64_encode(str_random(40));

        $pharmacy = pharmacy::create($data);
        if ($pharmacy) {

            $result = array('status' => true, 'code' => '200', 'pharmacy' => $pharmacy, 'message' => 'pharmacy Account Created Successfully');
            return response()->json($result, 201, []);
        }

    }
    public function Login(Request $request)
    {

        //validate
        $rules = [

            'password' => 'required',
            'pharmacyName' => '',
            'Token' => 'required',
            'apiKey' => 'required',

        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            $result = array('status' => false, "errors" => $validation->errors(), 'message' => 'You must send data Successfully!');
            return response()->json($result, 404, []);
        }

        $api_key = env('APP_KEY');
        if ($api_key != $request->apiKey) {
            $result = array('status' => false, 'code' => '401', 'message' => 'Unauthorized!');
            return response()->json($result, 401, []);
        }

        $pharmacy = pharmacy::where('pharmacy_name', $request->pharmacyName)->orwhere('email',$request->pharmacyName)->first();
        if ($pharmacy) {
            $check = Hash::check($request->password, $pharmacy->password);
            if ($check) {
                $pharmacy->Token = $request->Token;
                $pharmacy->ApiToken = base64_encode(str_random(40));
                $pharmacy->save();
                $result = array('status' => true, "pharmacy" => $pharmacy, 'message' => 'You Logged In Successfully!');
                return response()->json($result, 200);
            } else {
                $result = array('status' => false, 'code' => '404', 'message' => 'You password is incorrect!');
                return response()->json($result, 403, []);
            }
        } else {
            $result = array('status' => false, 'code' => '404', 'message' => 'You Email is incorrect!');
            return response()->json($result, 404, []);
        }

    }
    public function LogOut(Request $request)
    {
        //validate
        $rules = [
            'ApiToken' => 'required',
            'apiKey' => 'required',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            $result = array('status' => false, "errors" => $validation->errors(), 'message' => 'You must send data Successfully!');
            return response()->json($result, 404, []);
        }

        $api_key = env('APP_KEY');
        if ($api_key != $request->apiKey) {
            $result = array('status' => false, 'code' => '401', 'message' => 'Unauthorized!');
            return response()->json($result, 401, []);
        }
        //end validate
        $pharmacy = pharmacy::where('ApiToken', $request->ApiToken)->first();
        if ($pharmacy) {
            $pharmacy->ApiToken = "null";
            $pharmacy->save();
            $result = array('status' => true, 'code' => '200', 'message' => 'Loged Out Successfully!');
            return response()->json($result, 200, []);
        } else {
            $result = array('status' => false, 'code' => '404', 'message' => 'You are not Logging !');
            return response()->json($result, 404, []);
        }

    }
    // show one company
    public function showCompany(Request $request)
    {
        //validate
        $rules = [
            'ApiToken' => 'required',
            'apiKey' => 'required',
            'company_id' => 'required',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            $result = array('status' => false, "errors" => $validation->errors(), 'message' => 'You must send data Successfully!');
            return response()->json($result, 404, []);
        }

        $api_key = env('APP_KEY');
        if ($api_key != $request->apiKey) {
            $result = array('status' => false, 'code' => '401', 'message' => 'Unauthorized!');
            return response()->json($result, 401, []);
        }
        //end validate
          $company = company::where('id', $request->company_id)->first();
          $products=$company->products()->paginate(50);
       
   
        if ($company) {
            $result = array('status' => true, 'code' => '200', 'company' => $company,'products'=>$products , 'message' => 'Show Company success');
            // dd($company);
            return response()->json($result, 200);
        } else {
            $result = array('status' => false, 'code' => '404', 'message' => 'No Company Found :/');
            return response()->json($result, 404, []);
        }

    }
    //show all companies
    public function allCompanies(Request $request)
    {
        //validate
        $rules = [
            'ApiToken' => 'required',
            'apiKey' => 'required',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            $result = array('status' => false, "errors" => $validation->errors(), 'message' => 'You must send data Successfully!');
            return response()->json($result, 404, []);
        }

        $api_key = env('APP_KEY');
        if ($api_key != $request->apiKey) {
            $result = array('status' => false, 'code' => '401', 'message' => 'Unauthorized!');
            return response()->json($result, 401, []);
        }
        //end validate
        // $companies = company::all();
        $companies = company::with('pay_method')->withCount('products')->orderBy('products_count', 'desc')->get();
        // $companies=company::with('pay_method')->orderBy(company::withCount('products'));
        if ($companies) {
            $result = array('status' => true, 'code' => '200', 'companies' => $companies, 'message' => 'Show Company success');

            return response()->json($result, 200);
        } else {
            $result = array('status' => false, 'code' => '404', 'message' => 'No Company Found :/');
            return response()->json($result, 404, []);
        }

    }

    // show one order with cart
    public function showOrder(Request $request)
    {
        //validate
        $rules = [
            'ApiToken' => 'required',
            'apiKey' => 'required',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            $result = array('status' => false, "errors" => $validation->errors(), 'message' => 'You must send data Successfully!');
            return response()->json($result, 404, []);
        }

        $api_key = env('APP_KEY');
        if ($api_key != $request->apiKey) {
            $result = array('status' => false, 'code' => '401', 'message' => 'Unauthorized!');
            return response()->json($result, 401, []);
        }
        //end validate

        $order = order::where('id', $request->order_id)->with(['company', 'cart'])->first();
        if ($order) {
            $result = array('status' => true, 'code' => '200', 'order' => $order, 'message' => 'Show Order success');
            return response()->json($result, 200);
        } else {
            $result = array('status' => false, 'code' => '404', 'message' => 'No order Found :/');
            return response()->json($result, 404, []);
        }

    }

    //show all orders for pharmacy
    public function allOrders(Request $request)
    {
        //validate
        $rules = [
            'ApiToken' => 'required',
            'apiKey' => 'required',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            $result = array('status' => false, "errors" => $validation->errors(), 'message' => 'You must send data Successfully!');
            return response()->json($result, 404, []);
        }

        $api_key = env('APP_KEY');
        if ($api_key != $request->apiKey) {
            $result = array('status' => false, 'code' => '401', 'message' => 'Unauthorized!');
            return response()->json($result, 401, []);
        }
        //end validate

        $pharmacy = pharmacy::where('ApiToken', $request->ApiToken)->first();
        $orders = order::where('pharmacy_id', $pharmacy->id)->with([ 'cart'])->with(['company'=>function($query){
            $query->with('pay_method');
        }])->get();

        if (!empty($orders)) {
            $result = array('status' => true, 'code' => '200', 'orders' => $orders, 'message' => 'Show Orders success');
            return response()->json($result, 200);
        } else {
            $result = array('status' => false, 'code' => '404', 'message' => 'No Orders Found :/');
            return response()->json($result, 200, []);
        }
    }

    public function CompanySearch(Request $request)
    {
        //validate
        $rules = [
            'ApiToken' => 'required',
            'apiKey' => 'required',
            'term' => 'required',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            $result = array('status' => false, "errors" => $validation->errors(), 'message' => 'You must send data Successfully!');
            return response()->json($result, 404, []);
        }

        $api_key = env('APP_KEY');
        if ($api_key != $request->apiKey) {
            $result = array('status' => false, 'code' => '401', 'message' => 'Unauthorized!');
            return response()->json($result, 401, []);
        }
        //end validate
        $key = $request->term;
        $pharmacy = pharmacy::where('ApiToken', $request->ApiToken)->first();
        $products = product::where('name_en_us', 'like', '%' . $key . '%')->orwhere('name_ar_eg', 'like', '%' . $key . '%')->with(['companies'=>function($query){
            $query->with('pay_method');
        }])->get();

        if (!empty($products[0])) {
            $result = array('status' => true, 'code' => '200', 'products' => $products, 'message' => 'Search Success');
            return response()->json($result, 200);

        } else {
            $result = array('status' => false, 'code' => '404', 'message' => 'Search Failed :/');
            return response()->json($result, 404, []);
        }

    }

    public function compareCompanies(Request $request)
    {
        // if the pharmacy choice to compare with only one product (optional) can use to compare with it or no
        // if yes
        //validate
        $rules = [

            'company1' => 'required',
            'company2' => 'required',
            'ApiToken' => 'required',
            'apiKey' => 'required',

        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            $result = array('status' => false, "errors" => $validation->errors(), 'message' => 'You must send data Successfully!');
            return response()->json($result, 404, []);
        }
        $company1_id = $request->company1;
        $company2_id = $request->company2;
        

        if ($request->filled('product')) {
              $product = $request->product;
                $company1 = company::where('id',$company1_id)->with('quota')->with('pay_method')->withCount('products')->first();
                $company2 = company::where('id',$company2_id)->with('quota')->with('pay_method')->withCount('products')->first();
                $product_1=product::where('name_ar_eg',$product)->where('company_id',$company1_id)->first();
                $product_2=product::where('name_ar_eg',$product)->where('company_id',$company2_id)->first();

            // if no just compare with company item count and quota
        } else {
                  $company1 = company::where('id',$company1_id)->with('quota')->with('pay_method')->withCount('products')->first();
                $company2 = company::where('id',$company2_id)->with('quota')->with('pay_method')->withCount('products')->first();
 
             $product_1=null;
            $product_2=null;
        }
        if ($company1 && $company2) {

            $result = array('status' => true, 'code' => '200', 'company1' => $company1, 'company2' => $company2,'product1'=>$product_1,'product2'=>$product_2, 'message' => 'compare Success');
            return response()->json($result, 200);
        } else {
            $result = array('status' => false, 'code' => '404', 'message' => 'compare Failed :/');
            return response()->json($result, 404, []);
        }

    }
    public function CommonProducts(Request $request)
    {
        //validate
        $rules = [
            'ApiToken' => 'required',
            'apiKey' => 'required',
            'company1' => 'required',
            'company2' => 'required',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            $result = array('status' => false, "errors" => $validation->errors(), 'message' => 'You must send data Successfully!');
            return response()->json($result, 404, []);
        }

        $api_key = env('APP_KEY');
        if ($api_key != $request->apiKey) {
            $result = array('status' => false, 'code' => '401', 'message' => 'Unauthorized!');
            return response()->json($result, 401, []);
        }
        //end validate

        $company1_id = $request->company1;
        $company2_id = $request->company2;

        //get common produts in two company (exist in two company )
        $Company1products = company::where('id', $company1_id)->first()->products()->pluck('name_ar_eg')->toArray();
        $Company2products = company::where('id', $company2_id)->first()->products()->pluck('name_ar_eg')->toArray();

        $common_product = product::wherein('name_ar_eg', $Company1products)->where('company_id', $company2_id)->pluck('id')->toArray();
        $common = product::wherein('id', $common_product)->get();
        if (!empty($common[0])) {
            $result = array('status' => true, 'code' => '200', 'Products' => $common, 'message' => 'Products Found');
            return response()->json($result, 200);
        } else {
            $result = array('status' => true, 'code' => '200', 'message' => 'No Common Products found :/');
            return response()->json($result, 200, []);
        }
    }
    public function StoreCart(Request $request)
    {

        $id = $request->product_id;
        $qty = $request->qty;
        //auth::pharmacy_id
        $pharmacy_id = $request->ApiToken;
        $product = product::find($id);
        $discountP = $product->discount_percentage;
        $discountB = $product->discount_buy;
        $bouns = $product->discount_get;

        $tax = $product->tax_card;
        // discount by percentage
        if (($discountP > 0 and $discountB > 0) or $discountP > 0) {
            $price = $product->price * $qty * (1 - ($discountP / 100));
            if ($qty >= $product->discount_buy) {
                $totalBouns = (integer) ($qty / $discountB) * $bouns;

            } else {

                $totalBouns = 0;
            }

        } else if ($discountP <= 0 and $discountB > 0) {
            $price = $product->price * $qty;
            if ($qty >= $product->discount_buy) {
                $totalBouns = (integer) ($qty / $discountB) * $bouns;
            } else {
                $totalBouns = 0;

            }
        }

        if ($tax > 0) {
            $price = $price * (1 - ($tax / 100));

        }
        /**
         * Rules
         * 1) stock >  requested qty
         * 2) qty <= quotaOrderLimit
         */
        if ($product->stock < $qty) {

            Alert::error('Great', 'sorry Not avilable');
        }
        if ($qty > $product->quota_order_limit) {

            Alert::error('Great', 'sorry Not avilable');
        }
        if ($qty > $product->quota_product_limit) {
            Alert::error('Great', 'sorry Not avilable');

        }
        Cart::add(['id' => $product->id, 'name' => $product->name_en_us, 'qty' => $qty, 'price' => $price, 'options' => ['name_ar' => $product->name_ar_eg, 'bouns' => $totalBouns, 'discount_percentage' => $discountP, 'discount_buy' => $discountB, 'area_id' => $product->area_id, 'tax_card' => $product->tax_card, 'company_id' => $product->company_id, 'pharmacy_id' => $pharmacy_id]]);
        Alert::success('Great', 'Added Successfully');

    }
    public function AllProducts(Request $request)
    {
        //validate
        $rules = [
            'apiKey' => 'required',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            $result = array('status' => false, "errors" => $validation->errors(), 'message' => 'You must send data Successfully!');
            return response()->json($result, 404, []);
        }

        $api_key = env('APP_KEY');
        if ($api_key != $request->apiKey) {
            $result = array('status' => false, 'code' => '401', 'message' => 'Unauthorized!');
            return response()->json($result, 401, []);
        }
        //end validate

        $products = product::all();
        if ($products) {
            $result = array('status' => true, 'code' => '200', 'products' => $products, 'message' => 'All Products...');
            return response()->json($result, 200);

        } else {
            $result = array('status' => false, 'code' => '404', 'message' => 'No products exist :/');
            return response()->json($result, 404, []);
        }
    }
    public function SeachInCompany(Request $request)
    {
        //validate
        $rules = [
            'ApiToken' => 'required',
            'apiKey' => 'required',
            'company_id' => 'required',
            'product_name' => 'required',
        ];
        $productName = $request->product_name;

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            $result = array('status' => false, "errors" => $validation->errors(), 'message' => 'You must send data Successfully!');
            return response()->json($result, 404, []);
        }

        $api_key = env('APP_KEY');
        if ($api_key != $request->apiKey) {
            $result = array('status' => false, 'code' => '401', 'message' => 'Unauthorized!');
            return response()->json($result, 401, []);
        }
        //end validate
        $company = company::find($request->company_id);
        $product = $company->load(['products' => function ($query) use ($productName) {
            $query->where('name_ar_eg','Like', '%'.$productName.'%');
        }]);
        if (isset($product->products[0])) {
            $result = array('status' => true, 'code' => '200', 'products' => $product->products, 'message' => 'Product  Founded ');
            return response()->json($result, 200);
        } else {
            $product=array();
            $result = array('status' => false, 'code' => '404','products' => $product, 'message' => 'No product exist :/');
            return response()->json($result, 404, []);
        }

    }
// get all cites
    public function cites(Request $request)
    {
        //validate
        $rules = [

            'apiKey' => 'required',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            $result = array('status' => false, "errors" => $validation->errors(), 'message' => 'You must send data Successfully!');
            return response()->json($result, 404, []);
        }

        $api_key = env('APP_KEY');
        if ($api_key != $request->apiKey) {
            $result = array('status' => false, 'code' => '401', 'message' => 'Unauthorized!');
            return response()->json($result, 401, []);
        }
        //end validate
        $cities = Governorate::all();
        if ($cities) {
            $result = array('status' => true, 'code' => '200', 'cities' => $cities, 'message' => 'cites   Founded ');
            return response()->json($result, 200);
        } else {
               $result = array('status' => true, 'code' => '200', 'cities' => $cities, 'message' => 'cites not Found ');
            return response()->json($result, 200);
        }

    }
//get area by id
    public function getareasByCityId(Request $request)
    {
        //validate
        $rules = [

            'apiKey' => 'required',
            'city_id' => 'required',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            $result = array('status' => false, "errors" => $validation->errors(), 'message' => 'You must send data Successfully!');
            return response()->json($result, 404, []);
        }

        $api_key = env('APP_KEY');
        if ($api_key != $request->apiKey) {
            $result = array('status' => false, 'code' => '401', 'message' => 'Unauthorized!');
            return response()->json($result, 401, []);
        }
        //end validate
        $areas = area::where('governorate_id', $request->city_id)->get();
        if (!empty($areas[0])) {
            $result = array('status' => true, 'code' => '200', 'areas' => $areas, 'message' => 'areas Founded ');
            return response()->json($result, 200);
        } else {
           $result = array('status' => true, 'code' => '200', 'areas' => $areas, 'message' => 'areas not Found ');
            return response()->json($result, 200);
        }

    }
    public function addOrder(Request $request)
    {
        //validate
        $rules = [

            'company_id' => 'required',
            'product_id' => 'required',
            'qty' => 'required',
            'ApiToken' => 'required',
            'apiKey' => 'required',

        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            $result = array('status' => false, "errors" => $validation->errors(), 'message' => 'You must send data Successfully!');
            return response()->json($result, 404, []);
        }
        $api_key = env('APP_KEY');
        if ($api_key != $request->apiKey) {
            $result = array('status' => false, 'code' => '401', 'message' => 'Unauthorized!');
            return response()->json($result, 401, []);
        }
        
    
        $productIds = $request->product_id;
        $productIds = (explode(",", $productIds));
        $qtys = $request->qty;
        $qtys = (explode(",", $qtys));
    
            //validate that qty count same as product count
        if(count($productIds)!=count($qtys)){
            
                     $result = array('status' => false, 'code' => '500', 'message' => 'Ops You sent data in correctly product must equal quantity numbers!');
            return response()->json($result, 500, []);
            
        }
   
        
        $order = pharmacy::where('ApiToken', $request->ApiToken)->first()->orders()->create([
            'notes'=>$request->note,
            'company_id'=>$request->company_id,
            'status' => "pending",
            'seen' => "no",
        ]);

        // $order=new order;

        foreach ($productIds as $key => $proId) {
               $product=product::where('id',$proId)->first();
       if($product){

            $pro = product::find($proId);
            $cartItem = new cartItem;
            $cartItem->name_ar_eg = $pro->name_ar_eg;
            $cartItem->name_en_us = $pro->name_en_us;
            $cartItem->tax_card = $pro->tax_card;
            $cartItem->discount_percentage = $pro->discount_percentage;
            $cartItem->discount_buy = $pro->discount_buy;
            $cartItem->discount_get = $pro->discount_get;
            $cartItem->area_id = $pro->area_id;
            $cartItem->company_id = $request->company_id;
            $cartItem->qty = $qtys[$key];

            $discountP = $pro->discount_percentage;
            $discountB = $pro->discount_buy;
            $bouns = $pro->discount_get;
            $tax = $pro->tax_card;
            $qty = $qtys[$key];
            //validate Equations Price
            // discount by percentage
            if (($discountP > 0 and $discountB > 0)) {
                $price = $pro->price * $qty * (1 - ($discountP / 100));
                if ($qty >= $pro->discount_buy) {
                    $totalBouns = (integer) ($qty / $discountB) * $bouns;

                } else {

                    $totalBouns = 0;
                }

            } else if ($discountP <= 0 and $discountB > 0) {
                $price = $pro->price * $qty;
                if ($qty >= $pro->discount_buy) {
                    $totalBouns = (integer) ($qty / $discountB) * $bouns;
                } else {
                    $totalBouns = 0;

                }
            }
            if ($discountP >= 0 and $discountB == 0) {

                $price = $pro->price * $qty;
                $price = $price * (1 - ($discountP / 100));

                $totalBouns = 0;

            }
            if ($tax > 0) {
               
                $price = $price * (1 - ($tax / 100));

            }
            /**
                 * Rules 1) check stock for order qty 
                 *       2)check order Limit for company
                 *       3) if quota check orderlimit For 1 product
                 */

        //Rule 1) check stock for order qty 
            if ($pro->stock < $qty) {
                $order->delete();
                $result = array('status' => false, 'code' => '404', 'message' => 'quntaty (Avilable is) ' . $pro->stock);
                return response()->json($result, 404, []);

            }
            $cartItem->price = ($price);
             //end validate Equations Price
            $cartItem->order_id = $order->id;

            $cartItem->save();
       }
        }
        //Rule 2

        /**
         * check order Limit for company
         */

        $company = company::find($request->company_id);

        $total_price = 0;

        $carts = order::find($order->id)->cart;
        foreach ($carts as $cart) {
            $total_price += $cart->price;

        }

        // if (($total_price) < ($company->order_limit)) {

        //     $order->delete();
        //     $result = array('status' => false, 'code' => '404', 'message' => ' your order limit must Exceeds ' . $company->order_limit);
        //     return response()->json($result, 404, []);
        // }
//Rule 3

        /**
         *  check orderd quota product
         */

//end validate
if($pro){
       if($pro->quota_product_limit !=0 or $pro->quota_order_limit!=0){
             
            if (($total_price) < ($pro->quota_order_limit )) {

                $order->delete();
                $result = array('status' => false, 'code' => '404', 'message' => '  order limit must exceeds '.$pro->quota_order_limit );
                return response()->json($result, 404, []);
            }

        }
}
     
        
        foreach($productIds as $key => $proId){
            $qty = $qtys[$key];
            $pr=product::where('id',$proId)->first();
            if($pr){
                        $pro=product::where('id',$proId)
                        ->update(['stock'=>($pr->stock-$qty)]);
            }
    

        }

        if ($order) {

            $result = array('status' => true, 'code' => '200', 'order' => $order, 'message' => 'created order Success');
            return response()->json($result, 200);
        } else {
            $order->delete();
            $result = array('status' => false, 'code' => '404', 'message' => 'created Failed :/');
            return response()->json($result, 404, []);
        }

    }

    public function getNews(Request $request)
    {
        //validate
        $rules = [

            'apiKey' => 'required',

        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            $result = array('status' => false, "errors" => $validation->errors(), 'message' => 'You must send data Successfully!');
            return response()->json($result, 404, []);
        }

        $api_key = env('APP_KEY');
        if ($api_key != $request->apiKey) {
            $result = array('status' => false, 'code' => '401', 'message' => 'Unauthorized!');
            return response()->json($result, 401, []);
        }
        //end validate
        $newss = news::all();
        if ($newss) {
            $result = array('status' => true, 'code' => '200', 'newss' => $newss, 'message' => 'news Founded ');
            return response()->json($result, 200);
        } else {
            $result = array('status' => false, 'code' => '404', 'message' => 'No news found :/');
            return response()->json($result, 404, []);
        }

    }
}
