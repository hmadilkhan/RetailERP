@extends('layouts.master-layout')

@section('title','Dashboard')

@section('breadcrumtitle','Dashboard')

@section('navdashboard','active')

@section('content')


<a href="{{ route('home') }}" class="btn btn-success text-white" ><i class="icofont icofont-arrow-left"></i>Back</a>

<div class="row">
  <input type="hidden" id="terminalID">
  <input type="hidden" id="openingID">
  <div class="col-xl-12">
   
    

   <div class="card-block outer">
    <div class="row">
      <div class="wrapper">
       <div id="draggablePanelList ">
       <!-- All Ative and Closed Sales -->
        <div class="row ml-row">
         <div class="col-md-12" >
           <ul class="nav nav-tabs " role="tablist">
             <li class="nav-item p-5">
              <a class="nav-link active-active active" onclick="showHide(this,'tab-active')">Active Sales</a>
            </li>
            <li class="nav-item-self p-5">
              <a class="nav-link closed-active" onclick="showHide(this,'tab-closed')">Closed Sales</a>
            </li>
			<li class="nav-item-self f-right p-5" >
				<input type="date" class="form-control" id="dateselection" style="display:none" value="{{date('Y-m-d',strtotime('-1 days'))}}" />
			</li>
          </ul>
        </div>
      </div>
      <div id="tab-active" class="mt-1">
        @foreach($branches as $value)
        <div class="col-xl-3 col-lg-6 inner" style="cursor: pointer;"  onclick="getdetails('{{(session('roleId') == 2 ? $value->branch_id : $value->terminal_id)}}','{{$value->identify}}','open')" >
         <div class="card">
          <div class="card-block">
            <div class="media d-flex">
              <div class="media-left media-middle">
               <a href="#">
                <img class="media-object img-circle" src="{{ asset('storage/images/branch/'.(!empty($value->branch_logo) ? $value->branch_logo : 'placeholder.jpg').'') }}" width="50" height="50">
              </a>
            </div>
            <div class="media-body p-t-10">
             <span class="counter-txt f-w-600 f-20">
              <span class="text-primary"> {{session("currency")}} {{number_format($value->sales,0)}} /=</span>
            </span>
            <h6 class="f-w-300 m-t-1">{{
              (session("roleId") == 2 ? $value->branch_name : $value->terminal_name)
            }}
          </h6>
        </div>
      </div>
      <ul>
        <li class="new-users">
        </li>
      </ul>
    </div>
  </div>
</div>
@endforeach
      </div>
      <div id="tab-closed" style="display:none">
       @foreach($branchesClosedSales as $value)
        <div class="col-xl-3 col-lg-6 inner" style="cursor: pointer;"  onclick="getdetails('{{(session('roleId') == 2 ? $value->branch_id : $value->terminal_id)}}','{{$value->identify}}','close')" >
         <div class="card">
          <div class="card-block">
            <div class="media d-flex">
              <div class="media-left media-middle">
               <a href="#">
                <img class="media-object img-circle" src="{{ asset('storage/images/branch/'.(!empty($value->branch_logo) ? $value->branch_logo : 'placeholder.jpg').'') }}" width="50" height="50">
              </a>
            </div>
            <div class="media-body p-t-10">
             <span class="counter-txt f-w-600 f-20">
              <span class="text-primary"> {{session("currency")}} {{number_format($value->sales,0)}} /=</span>
            </span>
            <h6 class="f-w-300 m-t-1">{{
              (session("roleId") == 2 ? $value->branch_name : $value->terminal_name)
            }}
          </h6>
        </div>
      </div>
      <ul>
        <li class="new-users">
        </li>
      </ul>
    </div>
  </div>
</div>
@endforeach
      </div>
      </div>
      <!-- All Ative and Closed Sales -->
        
</div>
</div>
</div>


</div>
</div>
</div>
<div class="container-fluid">
<div class="card">
  <div class="row ">
    <div class="col-md-12" >
		<ul class="nav nav-tabs horizontal-tabs" role="tablist" id="terminalTab">
		</ul>
    </div>
	<div class="col-md-12 m-t-4" >
		<ul class="nav nav-tabs " role="tablist" id="declartionTab">
		</ul>
	</div>
  <div id="div_details"></div>
</div>
</div>
</div>

@endsection

@section('scriptcode_three')
<script type="text/javascript">
	var activeStatus = "";
  function showHide(arg,id){
    if(id == 'tab-closed'){
      $('.active-active').removeClass('active');
      $('.closed-active').addClass('active');
      $('#tab-closed').show();
      $('#tab-active').hide();
	  activeStatus = "close"
	  $("#dateselection").css("display","block");
	  $("#declartionTab").html("");
    }else{
      $('.closed-active').removeClass('active');
      $('.active-active').addClass('active');
      $('#tab-closed').hide();
      $('#tab-active').show();
	  activeStatus = "open"
	  $("#dateselection").css("display","none");
	  $("#declartionTab").html("");
    }
  }
	var terminal = 0 ;
	var terminal_name = "";
	function getdetails(branch,status,branchstatus){
		if(branchstatus == "close"){
			getCloseTerminals(branch,status)
		}else{
			getTerminals(branch,status);
		}
		
   $('#div_details').empty();
       //getPartial(terminal)
      // getPermission(terminal);
    }

    getTerminals('{{$branches[0]->branch_id}}');

    function getTerminals(branch,status)
    {
		
      $.ajax({
        url  : "{{url('/getTerminals')}}",
        type : "POST",
        data : {_token : "{{csrf_token()}}",branch:branch,status:status},
        dataType : 'json',
        async:false,
        success : function(result){
         $('#terminalTab').html('');
         $.each(result, function( index, value ) {
          if (index == 0) {
           $('#terminalName').html(value.terminal_name);
           terminal = value.terminal_id;
           terminal_name = value.terminal_name;
           $('#terminalName').html(value.terminal_name);
            // getPermission(value.terminal_id);
            getPartial(value.terminal_id)
            // getHeads(value.terminal_id);
               	}
                 $('#terminalTab').append(
                  "<li id="+value.terminal_id+" onclick='getPartial("+value.terminal_id+",1)' class='nav-item m-t-5 f-24'><a id="+value.terminal_id+" class='nav-link "+(index == 0 ? "active" : "")+"'  data-toggle='tab' href='#tab-home' role='tab'>" +value.terminal_name+"</a></li>"
                  );
               });
       }
     });
    }
	
	function getCloseTerminals(branch,status)
    {
		
      $.ajax({
        url  : "{{url('/getTerminals')}}",
        type : "POST",
        data : {_token : "{{csrf_token()}}",branch:branch,status:status},
        dataType : 'json',
        async:false,
        success : function(result){
         $('#terminalTab').html('');
         $.each(result, function( index, value ) {
			if (index == 0) {
			   $('#terminalName').html(value.terminal_name);
			   terminal = value.terminal_id;
			   terminal_name = value.terminal_name;
			   $('#terminalName').html(value.terminal_name);
            }
			 $('#terminalTab').append(
			   "<li id="+value.terminal_id+" onclick='getDeclarations("+value.terminal_id+")' class='nav-item m-t-5 f-24'><a id="+value.terminal_id+" class='nav-link "+(index == 0 ? "active" : "")+"'  data-toggle='tab' href='#tab-home' role='tab'>" +value.terminal_name+"</a></li>"
			  );
		   });
       }
     });
    }
	function getDeclarations(terminalId){
		let date = $("#dateselection").val();
		if(date == ""){
			alert("Please select date")
		}else{
			$.ajax({
				url : "{{url('/get-close-declarations')}}",
				type : "POST",
				data : {_token : "{{csrf_token()}}",terminal:terminalId,date:date},
				dataType : 'json',
				success : function(result){
					$('#declartionTab').html('');
					$('#div_details').html('');
					$.each(result, function( index, value ) {
						$('#declartionTab').append(
							"<li id="+value.opening_id+" onclick='getLastDayPartial("+terminalId+","+value.opening_id+")' class='nav-item m-t-5 f-24'><a id="+value.opening_id+" class='nav-link "+(index == 0 ? "active" : "")+"'  data-toggle='tab' href='#tab-home' role='tab'>D#" +value.opening_id+"</a></li>"
						);
					});
				}
			});
		}
	}
   // getHeads(terminal);
   // getPermission(terminal);
      getPartial(terminal)  //ye wala comment

      function getHeads(terminal,index)
      {

        getPermission(terminal);
        clearControls();
        $('#terminalID').val(terminal);
        if (index > 0) {

          $('#terminalName').html($("#"+terminal+"").text());

        }

        $.ajax({
         url : "{{url('/heads-details')}}",
         type : "POST",
         data : {_token : "{{csrf_token()}}",terminal:terminal},
         dataType : 'json',
         success : function(result){

           if (result != "") {
            $('#openingID').val(result[0].opening_id);
            if (result[0].closingBal == 0)
            {
             $('#status').html("ACTIVE")
             $('#status').removeClass("tag-danger");
             $('#status').addClass("tag-success");
           }
           else
           {
             $('#status').html("CLOSED");
             $('#status').removeClass("tag-success");
             $('#status').addClass("tag-danger");
           }

           $('#ob').html("Rs. "+parseInt(result[0].bal).toLocaleString());
           $('#odate').html(getDateInWords(result[0].date)+" | "+result[0].time);
           $('#cdate').html(getDateInWords(result[0].closingDate)+" "+(result[0].closingTime == null ? "" : " | "+result[0].closingTime));
           $('#totalSales').html("Rs. "+parseInt(result[0].TotalSales).toLocaleString());
           $('#cb').html("Rs. "+parseInt(result[0].closingBal).toLocaleString());
           $('#takeaway').html("Rs. "+parseInt(result[0].TakeAway).toLocaleString());
           $('#delivery').html("Rs. "+parseInt(result[0].Delivery).toLocaleString());
           $('#online').html("Rs. "+parseInt(result[0].Online).toLocaleString());
           $('#opening').html("Rs. "+parseInt(result[0].bal).toLocaleString());
           $('#cashSales').html("Rs. "+(parseInt(result[0].Cash + parseInt(result[0].Discount))).toLocaleString());
           $('#creditCard').html("Rs. "+parseInt(result[0].CreditCard).toLocaleString());
           $('#customerCredit').html("Rs. "+parseInt(result[0].CustomerCredit).toLocaleString());
           $('#Sales').html("Rs. "+parseInt(result[0].TotalSales).toLocaleString());
           $('#totalCost').html("Rs. "+parseInt(result[0].cost).toLocaleString());
           $('#discount').html("Rs. "+parseInt(result[0].Discount).toLocaleString());
           $('#salesReturn').html("Rs. "+parseInt(result[0].SalesReturn).toLocaleString());
           $('#cashReturn').html("Rs. "+parseInt(result[0].CashReturn).toLocaleString());
           $('#cardReturn').html("Rs. "+parseInt(result[0].CardReturn).toLocaleString());
           $('#chequeReturn').html("Rs. "+parseInt(result[0].ChequeReturn).toLocaleString());
           $('#cashIn').html("Rs. "+parseInt(result[0].cashIn).toLocaleString());
           $('#cashOut').html("Rs. "+parseInt(result[0].cashOut).toLocaleString());

           var positive = parseInt(result[0].bal) + parseInt(result[0].Cash) + parseInt(result[0].CreditCard) + parseInt(result[0].CustomerCredit) + parseInt(result[0].cashIn) + parseInt(result[0].CashReturn) + parseInt(result[0].CardReturn) + parseInt(result[0].ChequeReturn);
           var negative = parseInt(result[0].cost) + parseInt(result[0].SalesReturn) + parseInt(result[0].cashOut) ;
           var CashInHand = positive - negative;
           $('#CIH').html("Rs. "+CashInHand.toLocaleString());
         }else{
          clearControls();
        }


      }
    });
      }

      function getPermission(terminal)
      {
       {{--$.ajax({--}}
         {{--    url : "{{url('/get-permission')}}",--}}
         {{--    type : "POST",--}}
         {{--   data : {_token : "{{csrf_token()}}",id:terminal},--}}
         {{--   dataType : 'json',--}}
         {{--   success : function(result){--}}
         {{--     // result[0].card_sale == 1 ? $('#card_sale').css('display','block') : ''--}}
         {{--  }--}}
         {{-- });--}}
     }



     function clearControls()
     {
       $('#ob').html("Rs. 0");
       $('#totalSales').html("Rs. 0");
       $('#cb').html("Rs. 0");
       $('#takeaway').html("Rs. 0");
       $('#delivery').html("Rs. 0");
       $('#online').html("Rs. 0");
       $('#opening').html("Rs. 0");
       $('#cashSales').html("Rs. 0");
       $('#creditCard').html("Rs. 0");
       $('#customerCredit').html("Rs. 0");
       $('#Sales').html("Rs. 0");
       $('#totalCost').html("Rs. 0");
       $('#discount').html("Rs. 0");
       $('#salesReturn').html("Rs. 0");
       $('#cashReturn').html("Rs. 0");
       $('#cardReturn').html("Rs. 0");
       $('#chequeReturn').html("Rs. 0");
       $('#cashIn').html("Rs. 0");
       $('#cashOut').html("Rs. 0");
       $('#CIH').html("Rs. 0");
     }



     function getDateInWords(date)
     {
       if(date != null)
       {
        var d = new Date(date);
        var days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        var month = ["January", "February", "March", "April", "May", "June", "July"];
        return days[d.getDay()]+", "+d.getDay()+" "+month[d.getMonth()]+" "+d.getFullYear();
      }
      else
      {
        return "";
      }

    }
    function getPartial(terminal)
    {
		$.ajax({
			url : "{{url('/heads')}}",
			type : "POST",
			data : {_token : "{{csrf_token()}}",terminal:terminal,status:activeStatus},
			beforeSend: function( xhr ) {
			  $('#div_details').append(
				"<center><div class='col-xl-2 col-md-4 col-sm-6'>"+
				"<h6 class='sub-title'>Large</h6>"+
				"<div class='preloader3 loader-block'>"+
				"<div class='circ1 bg-success loader-lg'></div>"+
				"<div class='circ2 bg-success loader-lg'></div>"+
				"<div class='circ3 bg-success loader-lg'></div>"+
				"<div class='circ4 bg-success loader-lg'></div>"+
				"</div>"+
				"</div></center>"
				)
			},
			success : function(result){
				$('#div_details').html();
				$('#div_details').html(result);
		    },
		    error: function (request, error) {
				$('#div_details').empty();
		    }
		});
    }
	function getLastDayPartial(terminal,openingId)
    {
		$.ajax({
			url : "{{url('/last-day-heads')}}",
			type : "POST",
			data : {_token : "{{csrf_token()}}",terminal:terminal,openingId:openingId},
			beforeSend: function( xhr ) {
			  $('#div_details').append(
				"<center><div class='col-xl-2 col-md-4 col-sm-6'>"+
				"<h6 class='sub-title'>Large</h6>"+
				"<div class='preloader3 loader-block'>"+
				"<div class='circ1 bg-success loader-lg'></div>"+
				"<div class='circ2 bg-success loader-lg'></div>"+
				"<div class='circ3 bg-success loader-lg'></div>"+
				"<div class='circ4 bg-success loader-lg'></div>"+
				"</div>"+
				"</div></center>"
				)
			},
			success : function(result){
				$('#div_details').html();
				$('#div_details').html(result);
		    },
		    error: function (request, error) {
				$('#div_details').empty();
		    }
		});
    }
  </script>
  @endsection

  @section('css_code')
   <style>
/*        @media (max-width: 1000px){
         section {
                margin-top: 30px;
            }
            body {
                line-height: 0;
            }

        }*/
    </style>    
  @endsection
