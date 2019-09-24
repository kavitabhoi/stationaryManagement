@extends('layouts.app')
@section('title','Stock Status')
@section('breadcrumb','Stock Status')
@section('content')
@if(\Session::has('success'))
<div class="alert alert-success alert-dismissible">
   <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   {{\Session::get('success')}}
</div>
@endif
@if ($errors->any())
<div class="alert alert-danger alert-dismissible">
   <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <ul>
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
   </ul>
</div>
<br />
@endif
<!--	<a href="{{url('/stock/createStockIn')}}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add</a> -->
<div id="popup">
   <table class="table table-striped table-bordered" id="stockout_list" style="font-size:11pt">
      <thead>
         <tr>
            <th>Sr. No.</th>
            <th>Item Name</th>
            <th>Check In Date</th>
            <th>Total Qty</th>
            <th>Price Per Item</th>
            <th style="text-align:center">Action</th>
         </tr>
      </thead>
      <tbody>
          @foreach($stocks as $stock)
		@foreach($stockOut as $so)
				@if($so->sm_supply_in_id == $stock->sm_supply_in_id && $so->item_id == $stock->item_id)
					{{$stock->qty = $stock->qty - $so->qty  }}
				@endif
		@endforeach
		@if($stock->qty <= 0 )
			@continue
		@endif
         <tr>
            <td width="10%">{{$loop->iteration}}</td>
            <td width="20%">{{$stock->item_name}} ({{$stock->brand_name}})</td>
            <td width="13%">{{$stock->check_in_date = date("d/m/Y", strtotime($stock->check_in_date))}}</td>
         <td width="17%">{{$stock->qty}}</td>
         <td width="17%">{{$stock->price}}</td>

            <td width="20%" style="text-align:center">
               <a href="javascript:void(0);"  class="btn btn-sm btn-primary up" data-sm_supply_in_id="{{$stock->sm_supply_in_id}}" data-assign_qty="{{$stock->qty}}"  data-item_id="{{$stock->item_id}}" data-sm_id="{{$stock->sm_id}}"  data-qty="{{$stock->qty}}"  data-price="{{$stock->price}}" data-item="{{$stock->item_name}}({{$stock->brand_name}})"><i class="fa fa-pencil"></i> STOCK OUT</a>
            </td>
         </tr>
         @endforeach
      </tbody>
   </table>
</div>
{{ $stocks->links() }}
@endsection
<!-- Edit location modal -->
<div class="modal fade" id="stockout">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Stock Out</h4>
         </div>
         <form id="stockoutform" method="POST">
            <div class="modal-body">
               {{ csrf_field() }}
               <div id="success-msg" class="hide">
                  <div class="alert alert-info alert-dismissible fade in" role="alert">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                     <span aria-hidden="true">×</span>
                     </button>
                     <strong>Successfully assigned!</strong> 
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-12">
                     <div class="form-group">
                        <label for="issue_date">Issue Date:</label>
                        <input id="issue_date" name="issue_date" type="text" class="form-control datepicker" placeholder="Enter issue date" autocomplete="off"  required/>
                        <input id="id" name="id" type="hidden"  required/>
                        <span class="text-danger">
                        <strong id="issue_date-error"></strong>
                        </span>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-12">
                     <div class="form-group">
                        <label>Issue to:</label><br />
                        <label>
                        <input type="radio" id="id_radio1" value="employee" name="issue_to" class="flat-red1" checked>
                        Employee
                        </label>
                        <label>
                        <input type="radio" id="id_radio2" value="location" name="issue_to" class="flat-red1">
                        Location
                        </label>
                        <label>
                        <input type="radio" id="id_radio3" value="other" name="issue_to" class="flat-red1">
                        Other
                        </label>
                     </div>
                  </div>
               </div>
               <div class="row" id="divemp">
                  <div class="col-md-12">
                     <div class="form-group">
                        <label for="employee">Employee:</label><br />
                        <select class="form-control select2" id="employee" name="employee" style="width:100%" autocomplete="off">
                           <option value="">Select Employee</option>
                           @foreach($users as $user)
                           <option value="{{$user->id}}">{{ $user->firstname.' '.$user->lastname}}</option>
                           @endforeach
                        </select>
						 <span class="text-danger">
                        <strong id="employee-error"></strong>
                        </span>
                     </div>
                  </div>
               </div>
               <div class="row" id="divloc" style="display:none">
                  <div class="col-md-12">
                     <div class="form-group">
                        <label for="location">Location:</label><br />
                        <select class="form-control select2" id="location" name="location" style="width:100%" autocomplete="off">
                           <option value="">Select Location</option>
                           @foreach($locations as $location)
                           <option value="{{ $location->id}}">{{ $location->name}}</option>
                           @endforeach
                        </select>
						 <span class="text-danger">
                        <strong id="location-error"></strong>
                        </span>
                     </div>
                  </div>
               </div>
               <div class="row" id="divother" style="display:none">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label for="other_name">Other:</label><br />
                        <input id="other_name" name="other_name" type="text" class="form-control" placeholder="Enter other name" autocomplete="off" />
						 <span class="text-danger">
                        <strong id="other_name-error"></strong>
                        </span>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label for="other_mobile">Mobile:</label><br />
                        <input id="other_mobile" name="other_mobile" type="text" class="form-control" placeholder="Enter other mobile" autocomplete="off" />
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-4">
                     <div class="form-group">
                        <label for="location">Item Name:</label><br />
                        <input id="popupitem" name="popupitem" type="text" class="form-control" readonly required/>
                        <input id="popupitemid" name="popupitemid" type="hidden"/>
                        <input id="popupsmid" name="popupsmid" type="hidden"/>
                        <input id="popupsupinid" name="popupsupinid" type="hidden"/>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label for="location">Avl Qty:</label><br />
                        <input id="popupqty" name="popupqty" type="text" class="form-control"  readonly  required/>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label for="location">Price per item:</label><br />
                        <input id="popupprice" name="popupprice" type="text" class="form-control" readonly  required/>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-12">
                     <div class="form-group">
                        <label for="location">Assign no. of items:</label><br />
                        <input id="noofitems" name="noofitems" type="text" class="form-control" placeholder="Enter no. of items" autocomplete="off" required/>
                        <span class="text-danger">
                        <strong id="name-error"></strong>
                        <strong id="noofitems-error"></strong>
                        </span>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-12">
                     <div class="form-group">
                        <label for="popupremarks">Remarks:</label><br />
                        <textarea class="form-control" name="popupremarks" autocomplete="off"></textarea>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button"  id="submitForm" class="btn btn-success btn-sm">ASSIGN</button>  <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">CLOSE</button>
            </div>
         </form>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>
<script src="http://code.jquery.com/jquery-3.3.1.min.js"
   integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
   crossorigin="anonymous"></script>
<script type="text/javascript">
   $(document).ready(function () 
      {
   	 $.ajaxSetup({
   			headers: {
   				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   			}
   		});
   		
   //DataTable
   var table = $('#stockout_list').DataTable({
   
   "paging":   false
   } );
   
	 /* Single line Reset function executes on click of Reset Button */
	$(".up").click(function(){

	$("#stockoutform")[0].reset();
	$( '#noofitems-error' ).html( "" );
			$( '#issue_date-error' ).html( "" );
			$( '#employee-error' ).html( "" );
			$( '#location-error' ).html( "" );
			$( '#other_name-error' ).html( "" );
	});
	
   //Assign stock
      $('body').on('click', '#submitForm', function(){
	
		var registerForm = $("#stockoutform");
		var formData = registerForm.serialize();
		var popupqty = $("#popupqty").val();
		var noofitems = $("#noofitems").val();

		$( '#noofitems-error' ).html( "" );
		$( '#issue_date-error' ).html( "" );
		$( '#employee-error' ).html( "" );
		$( '#location-error' ).html( "" );
		$( '#other_name-error' ).html( "" );
   		if(parseInt(noofitems) > parseInt(popupqty))
   			{
				alert('Assigned Qty should be less than avl qty');
				return false;
   			}
			
			$.ajax({
			type:'POST',
			url:'./store',
            
              data:formData,
              success:function(data) {
              console.log(data);
   		
                if(data.errors) {     
   				
					if(data.errors.noofitems){
						  $( '#noofitems-error' ).html( data.errors.noofitems[0] );
					}
					if(data.errors.issue_date){
						  $( '#issue_date-error' ).html( data.errors.issue_date[0] );
						  }  
						  	if(data.errors.employee){
						  $( '#employee-error' ).html( data.errors.employee[0] );
						  } 
						  	if(data.errors.location){
						  $( '#location-error' ).html( data.errors.location[0] );
						  } 
						  	if(data.errors.other_name){
						  $( '#other_name-error' ).html( data.errors.other_name[0] );
						  } 
                  }
				 if(data.success) {
				  $('#success-msg').removeClass('hide');
				  setInterval(function(){ 
					  $('#stockout').modal('hide');
					  $('#success-msg').addClass('hide');
				   window.location.replace('./index');  
				  }, 1000);
					table.draw();
				 }
              },
          });
   
      });
});

</script>
<script>
   $(document).ready(function () 
    { 
   
     $("#id_radio1").click(function()
     {
   	  
		$("#divemp:hidden").show('slow');

		$('#location').val('').trigger("change");

		$("#other_name").val("");
		$("#other_mobile").val("");

		$("#divloc").hide();
		$("#divother").hide();
   
      });
      $("#watch-me").click(function()
     {
       if($('id_radio1').prop('checked')===false)
      {
       $('#divemp').hide();
      }
     });
     
     
     $("#id_radio2").click(function()
     {
		$("#divloc:hidden").show('slow');
		
		$('#employee').val('').trigger("change");

		$("#other_name").val("");
		$("#other_mobile").val("");

		$("#divemp").hide();
		$("#divother").hide();
   
      });
	  
      $("#id_radio2").click(function()
     {
       if($('divloc').prop('checked')===false)
      {
       $('#divloc').hide();
      }
     });
     
     
     $("#id_radio3").click(function()
     {
		$("#divother:hidden").show('slow');
		$('#employee').val('').trigger("change");

		$('#location').val('').trigger("change");
		
		$("#divemp").hide();
		$("#divloc").hide();
   
      });
      $("#id_radio3").click(function()
     {
       if($('divother').prop('checked')===false)
      {
       $('#divother').hide();
      }
     });
   
   
    });
   
  	 
   $(document).ready(function() {
   
     //get data for update record
        $('#popup').on('click', '.up', function(){
			$("#stockoutform")[0].reset();
			var popupitem = $(this).data('item');
			var popupitemid = $(this).data('item_id');
			var popupprice = $(this).data('price');
			var popupqty = $(this).data('assign_qty');
			var popupsmid = $(this).data('sm_id');
			var popupsupinid = $(this).data('sm_supply_in_id');
   
		   $('#stockout').modal('show');
		   $('[name="popupitem"]').val(popupitem);
		   $('[name="popupprice"]').val(popupprice);
		   $('[name="popupqty"]').val(popupqty);
		   $('[name="popupitemid"]').val(popupitemid);
		   $('[name="popupsmid"]').val(popupsmid);
		   $('[name="popupsupinid"]').val(popupsupinid);
		   });
   	
      
   });
</script>