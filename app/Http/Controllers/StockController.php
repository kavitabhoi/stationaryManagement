<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Response;
use Carbon\Carbon;
use App\Supplier;
use App\Item;
use App\StockIn;
use App\StockInItems;
use App\Users;
use App\Location;
use App\StockOut;


class StockController extends Controller
{
    //
	/**
	  * Show the form for creating a new resource.
	  *
	  * @return \Illuminate\Http\Response
	  */
	public function __construct()
	{
		$this->middleware('auth');
	}
  
	public function create()
	{
		//$data = array("Andaman and Nicobar Islands", "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chandigarh", "Chhattisgarh", "Dadra and Nagar Haveli", "Daman and Diu", "Delhi", "Goa", "Gujarat", "Haryana", "Himachal Pradesh", "Jammu and Kashmir", "Jharkhand", "Karnataka", "Kerala", "Lakshadweep", "Madhya Pradesh", "Maharashtra", "Manipur", "Meghalaya", "Mizoram", "Nagaland", "Orissa", "Pondicherry", "Punjab", "Rajasthan", "Sikkim", "Tamil Nadu", "Tripura", "Uttaranchal", "Uttar Pradesh", "West Bengal");
		$suppliers = Supplier::select('name','id')->get();

		$items = DB::table('sm_items')
			->leftJoin('sm_brands', 'sm_brands.id', '=', 'sm_items.brand_id')
			->select('sm_items.id','sm_items.name','sm_brands.name as brand_name')
			->get();
		 
	   return view('stock.createStockIn', compact('suppliers','items'));
	}
	
	/**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		
		 $validator = Validator::make($request->all(), [
            'noofitems' => 'required|max:255',
			'issue_date' => 'required|max:255',
			"employee" => "required_if:issue_to,==,employee",
			"location" => "required_if:issue_to,==,location",
			"other_name" => "required_if:issue_to,==,other",
			
        ]);
		
		
        $input = $request->all();

        if ($validator->passes()) {

            // Store your user in database 
			
			 $StockOut = StockOut::create([
			'supply_out_date' => Carbon::createFromFormat('d/m/Y', $request->issue_date)->format('Y-m-d'),
			'sm_supply_in_id'  => $request->input('popupsupinid'),
			'sm_sup_in_item_id' => $request->input('popupsmid'),
			'item_id' => $request->input('popupitemid'),
			'qty' => $request->input('noofitems'),
			'employee_id' => $request->input('employee'),
			'location_id' => $request->input('location'),
			'other_name' => $request->input('other_name'),
			'other_mobile' => $request->input('other_mobile'),
			'remarks' => $request->input('popupremarks'),
			'ip_address' => request()->ip()
		]);

          return Response::json(['success' => '1']);
		//return redirect('/stockStatus/index')->with('success', 'Successfully assigned!');
        }
        
       // return Response::json(['errors' =>'Assigned Qty should be less than avl qty']);
        return Response::json(['errors' => $validator->errors()]);
		
    }

	
	public function index()
    {
			//DB::enableQueryLog();
		$users = Users::select('firstname','lastname', 'id')->get();
		$locations = Location::select('name','id')->get();
		$stocks = DB::table('sm_supply_in_items AS SI')
			->leftJoin('sm_supply_in AS CI', 'CI.id', '=', 'SI.sm_supply_in_id')
			->leftJoin('sm_items AS I', 'I.id', '=', 'SI.item_id')
			->leftJoin('sm_brands AS B', 'B.id', '=', 'I.brand_id')
			->select('SI.*','CI.check_in_date','I.name AS item_name','B.name as brand_name','SI.id AS sm_id')
			->where('SI.is_active', '=', 1)
			->paginate(15);
			
			$stockOut = StockOut::select('*')->where('is_active', '=', 1)->get();
		//$stockOut = StockOut::find('is_active','=',1);

        return view('stock.index',compact('stocks','users','locations','stockOut'));
    }
	

}
