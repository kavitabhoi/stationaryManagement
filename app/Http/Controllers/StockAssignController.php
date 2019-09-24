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

class StockAssignController extends Controller
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
  
	
	/**
     * view the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function view($id) {
	
     $stockout = StockOut::find($id);
        return $stockout;
    }
	

    public function update(Request $request)
    {
		
		 $validator = Validator::make($request->all(), [
            'noofitems' => 'required|max:255',
		
			
        ]);
		  $input = $request->all();
		    if ($validator->passes()) {
		$data = array();

		$data['qty'] = $request->noofitems;
		$data['remarks'] = $request->popupremarks;
		StockOut::where('id',$request->id)
		->update($data);
		return Response::json(['success' => '1']);
		//return redirect('/stockStatus/index')->with('success', 'Successfully assigned!');
        }

	
        return Response::json(['errors' => $validator->errors()]);
       // return redirect('/stockAssign/index')->with('success', 'Record has been updated!!');
    }
  
	
	public function index()
    {
        $users = Users::select('firstname','lastname', 'id')->get();
		$locations = Location::select('name','id')->get();
		$stocks = DB::table('sm_supply_out')
		->leftJoin('sm_supply_in_items', 'sm_supply_in_items.id', '=', 'sm_supply_out.sm_sup_in_item_id')
		->leftJoin('sm_locations', 'sm_locations.id', '=', 'sm_supply_out.location_id')
		->leftJoin('users', 'users.id', '=', 'sm_supply_out.employee_id')
		->leftJoin('sm_items', 'sm_items.id', '=', 'sm_supply_out.item_id')
		->leftJoin('sm_brands', 'sm_brands.id', '=', 'sm_items.brand_id')
		->select('sm_supply_out.*', 'sm_supply_out.qty as assign_qty', 'sm_locations.name as location_name', 'sm_supply_in_items.qty as avl_qty', 'sm_supply_in_items.price as price', 'users.firstname', 'users.lastname', 'sm_items.name as item_name', 'sm_brands.name as brand_name')
		->where('sm_supply_out.is_active', '=', '1')
		->paginate(15);
		
        return view('stock.indexAssign',compact('stocks','users','locations'));
		
      
    }
	

	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
			 $data = array();
		 $data['is_active'] = 0;
        StockOut::where('id',$id)
        ->update($data);
	
        return redirect('/stockAssign/index')->with('success', 'Stock In record has been deleted!!');
    }
	
	
}
