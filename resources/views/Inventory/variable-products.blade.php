@extends('layouts.master-layout')

@section('title','Variable Products')

@section('breadcrumtitle','Create Variable Product')

@section('navinventory','active')

@section('content')

<section class="panels-wells m-t-5 p-t-20">
    <h3>Product Name :{{ $generalItem[0]->product_name }}</h3>

    {{-- @if(isset($addonTabBox))
       <script>alert('{{ $addonTabBox }}')</script>
    @endif --}}

    <a href="{{ route('invent-list') }}">
        <i class="text-primary text-center icofont icofont-arrow-left f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back to list">Back to list</i>
    </a>

   <ul class="nav nav-tabs  tabs" role="tablist">
      <li class="nav-item">
         <a class="nav-link" data-toggle="tab" id="tab_btn_variable_product" href="#variableProductTab" role="tab">Variable Products</a>
      </li>
      <li class="nav-item">
         <a class="nav-link" data-toggle="tab" id="tab_btn_addon" href="#addonTab" role="tab">Addon Products</a>
      </li>
   </ul>
   <!-- Tab panes -->
   <div class="tab-content tabs">
      <div class="tab-pane active" id="variableProductTab" role="tabpanel">
         @include('Inventory.variable-product-partial.variable-product-tab')
      </div>
      <div class="tab-pane" id="addonTab" role="tabpanel">
         @include('Inventory.addon-partial.addon-tab')
      </div>
   </div>

</section>


@endsection

@section('scriptcode_one')
 <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
 <link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css"
/>
@endsection

@section('css_code')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


<style type="text/css">
/* Hide default HTML checkbox */
.switch {
  position: relative;
  display: inline-block;
  width: 43px;
  height: 21px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 13px;
  width: 13px;
  left: 2px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
  /*content:'On';*/
}

input+.slider:before {
	/*content: "Off";*/
 }

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>

@endsection

@section('scriptcode_three')

<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>

<script type="text/javascript">

    @if(old('addonTabBox') || isset($addonTabBox))
       $("#tab_btn_addon").trigger('click');
    @else
       $("#tab_btn_variable_product").trigger('click');
    @endif

   $(".select2").select2();


   var table_row_mdId = [];

   var tmp_arrayValue = [];

       $('#tblposproducts,#tbl_variablecpymd').DataTable({
            displayLength: 10,
            info: true,
            order: [[0, 'desc']],
            language: {
                search:'',
                searchPlaceholder: 'Search Product',
                lengthMenu: '<span></span> _MENU_'

            }

        });

        function close_copyModal(){
    		 $("#copy-variation-modal").modal('hide');
    		 $("#lable-variation-"+$("#mode_cpymd").val()).trigger('click');
        }


        $("#btn_copy_variation").on('click',function(){

    		 $("#mode_cpymd").val($("#mode_md").val());
    		 $("#variationId_cpymd").val($("#variationId_md").val());
    		 $("#copy-variationName").text('Variation: '+$("#lable-variation-"+$("#mode_cpymd").val()).text());

            $.ajax({
                url: "{{ route('getVariableProduct') }}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",id:$("input[name='finishgood']").val(),variationId:$("#variationId_md").val()},
                success:function(resp){
                    // console.log(resp == null ? 1 : 0)
                   if(resp == null){
                       close_copyModal();
                   }else{
                    var datatable = $('#tbl_cpymd').DataTable();
                    // Clear all rows in the DataTable
                    datatable.clear();
                    $.each(resp, function( index, value ) {
                        if($("#itemId_md").val() != value.pos_item_id){
                            datatable.row.add(['<label class="pointer"> <input type="checkbox" value="'+value.pos_item_id+'" class="form-control m-r-1" name="tble_chk_cpymd">'+value.item_name+'</label>']);
                        }
                    });
                    datatable.draw();
                   }

                }
            });

            $("#createVariation-modal").modal('hide');
            $("#copy-variation-modal").modal('show');
        });

        $("#btn_past_variation").on('click',function(){
            let Code = [];
            $.each($('input[name="tble_chk_cpymd"]'),function(){
                 if($(this).is(':checked')){
                     if($.inArray($(this).val(),Code) == -1){
                         Code.push($(this).val())
                     }
                 }

            })

            if(Code.length > 0){
                $.ajax({
                    url: "{{ route('set_variationAllVariableProduct') }}",
                    type: 'POST',
                    data:{_token:"{{ csrf_token() }}",itemId:Code,variationId:$("#variationId_cpymd").val()},
                    success:function(resp){
                       if(resp.status == 200){
                             swal("Success!","", "success");
                             window.location= location.origin+"/inventory/"+$("input[name='finishgood']").val()+"/variable-products";
                        }

                        if(resp.status == 500){
                            swal("Cancelled",resp.msg, "error");
                        }
                    }
                });
            }

        });


        function copyVariableProduct_modal(variableId,variableName,productId){

            $("#generalInventoryId_cpymd").val(productId)
    		$("#variableId_cpymd").val(variableId);
    		$("#variableName_cpymd").val(variableName)
    		$("#copy-variableName").text('Variation: '+variableName);

            $("#copy-variable-modal").modal('show');

            $("#tbl_variablecpymd tbody").empty();
            $("#subDepartment_variableTab").val('');
            $("#department_variableTab").val('').change();
        }

        $("#department_variableTab").on('change',function(){
           if($(this).val() != ''){
               load_subdept($(this).val(),'subDepartment_variableTab');
           }else{
               $("#subDepartment_variableTab").val('');
    			 if(!$("#subDepartment_variableTab").attr('disabled')){
    			     $("#subDepartment_variableTab").attr('disabled',true);
    			 }
           }
        });

        $("#subDepartment_variableTab").on('change',function(){
           if($(this).val() != ''){
               get_allGeneralItem($("#department_variableTab").val(),$(this).val());
           }
        });

        function get_allGeneralItem(depart_val,subDepart_val){
              $.ajax({
                url: "{{ route('get_generalItem') }}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",depart:depart_val,subDepart:subDepart_val},
                success:function(resp){
                   if(resp == ''){
                       swal('Product not found!','','error');
                       $('#tbl_variablecpymd tbody').empty();
                   }else{
                    // datatableVariable.destroy();
                    $('#tbl_variablecpymd tbody').empty();
                    $.each(resp, function( index, value ) {
                       if(value.id != $('#m_finishgood').val()){
                            $("#tbl_variablecpymd tbody").append('<tr><td><label class="pointer" onclick="addToVariableProduct_basket()"> <input type="checkbox" value="'+value.id+'" class="form-control pointer m-r-1" name="tble_chk_vcpymd">'+value.product_name+'</label></td></tr>');
                        // datatableVariable.row.add(['<label class="pointer"> <input type="checkbox" value="'+value.id+'" class="form-control pointer m-r-1" name="tble_chk_vcpymd">'+value.product_name+'</label>']);
                       }
                    });
                    //datatableVariable.draw();
                   }

                }
            });
        }

        function addToVariableProduct_basket(){
          tmp_arrayValue = [];
            $.each($('input[name="tble_chk_vcpymd"]'),function(){
                 if($(this).is(':checked')){
                     if($.inArray($(this).val(),tmp_arrayValue) == -1){
                         tmp_arrayValue.push($(this).val())
                     }
                 }
            })
        }

        $("#btn_past_variableTogeneral").on('click',function(){
               //alert(tmp_arrayValue)
            if(tmp_arrayValue.length > 0){
                $.ajax({
                    url: "{{ route('VariableProduct_set_to_generalProduct') }}",
                    type: 'POST',
                    data:{_token:"{{ csrf_token() }}",item_code:tmp_arrayValue,variableId:$("#variableId_cpymd").val(),variableName:$("#variableName_cpymd").val(),generalInventoryCode:$("#generalInventoryId_cpymd").val()},
                    dataType:'json',
                    success:function(resp){
                         //console.log(resp)
                        if(resp.status == 200){
                                    swal({
                                        title: "Success!",
                                        text: "",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location= location.origin+"/inventory/"+$("#m_finishgood").val()+"/variable-products";
                                        }
                                    });

                        }

                        if(resp.status == 500){
                            swal("Cancelled",resp.msg, "error");
                        }
                    }
                });
            }
        });

		function load_subdept(id,elementId){
            $('#'+elementId).empty();
            $.ajax({
                url: "{{ url('get_sub_departments') }}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",id:id},
                success:function(resp){
    				 if($("#"+elementId).attr('disabled')){
    				     $("#"+elementId).attr('disabled',false);
    				 }

                    $('#'+elementId).append("<option value=''>Select</option>");
                    $.each(resp, function( index, value ) {
                        $('#'+elementId).append(
                            "<option value="+value.sub_department_id+">"+value.sub_depart_name+"</option>"
                        );
                    });
                }
            });
        }


        function readURL(input, id) {
    if (input.files && input.files[0]) {
        var file = input.files[0];

        // Validate file size (5MB = 5 * 1024 * 1024 bytes)
        if (file.size > 5 * 1024 * 1024) {
            swal("Error!","File size must be less than 5MB.","error");
            input.value = ""; // Clear the input
            return;
        }

        // Validate file type
        const SUPPORTED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];
        var fileExtension = file.name.split('.').pop().toLowerCase();
        if (!SUPPORTED_EXTENSIONS.includes(fileExtension)) {
            swal("Error!","Only JPG, JPEG, PNG, and WEBP files are allowed.","error");
            input.value = ""; // Clear the input
            return;
        }

        var reader = new FileReader();

        reader.onload = function(e) {
            $('#' + id).attr('src', e.target.result);
        }

        reader.readAsDataURL(file);
    }
  }

        $("#productImage").change(function() {
            readURL(this,'productImages_preview');
        });

		$("#item_image_vpmd").change(function() {
            readURL(this,'modal_previewImage_vpmd');
        });

        function toggle(){
            $('#insert-card').toggle();
        }



//



        $('#btn_help').click(function(){
            $('#help-modal').modal('show');
        });

        //Alert confirm
        function remove(name,unid){
            swal({
                    title: "Are you sure?",
                    text: "Do you want to Delete "+ name +" ?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "delete it!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){
                        $.ajax({
                            url: "{{route('removeVariableProduct')}}",
                            type: 'POST',
                            data:{_token:"{{ csrf_token() }}",
                                id:unid,
                            },
                            success:function(resp){
                                if(resp == 1){
                                    swal({
                                        title: "Deleted",
                                        text: "POS Product Deleted Successfully!",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            $("#tr-"+unid).remove();
                                            swal("Success!", "", "success");
                                        }
                                    });
                                }else{
                                    swal("Cancelled", "Variable product not removed", "error");
                                }
                            }

                        });

                    }else {
                        swal("Cancelled", "POS Product Safe :)", "error");
                    }
                });
        }


        $('#chkactive').change(function(){
            if (this.checked) {
                $.ajax({
                    url: "{{url('/inactive-posproducts')}}",
                    type: 'GET',
                    dataType:"json",
                    data:{_token:"{{ csrf_token() }}",
                    },
                    success:function(result){
                        if(result){
                            $("#tblposproducts tbody").empty();
                            for(var count =0;count < result.length; count++){

                                $("#tblposproducts tbody").append(
                                    "<tr>" +
                                    "<td class='text-center'><img width='42' height='42' src='assets/images/products/"+((result[count].image != "") ? result[count].image : 'placeholder.jpg')+"' alt='"+result[count].image+"'/></td>" +
                                    "<td>"+result[count].branch_name+"</td>" +
                                    "<td>"+result[count].item_name+"</td>" +
                                    "<td>"+result[count].department_name+"</td>" +
                                    "<td>"+result[count].price+"</td>" +
                                    "<td>"+result[count].status_name+"</td>" +
                                    "<td class='action-icon'><a class='m-r-10' onclick='reactive("+result[count].sub_id+")' data-toggle='tooltip' data-placement='top' data-original-title='View'><i class='icofont icofont-check-circled text-primary f-18' ></i></a></td>"+
                                    "</tr>"
                                );
                            }

                        }
                    }
                });
            }
            else{
                window.location="{{ url('/posproducts') }}";
            }
        });

        function reactive(id){
            swal({
                    title: "Are you sure?",
                    text: "You want to Re-Active POS Product!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "yes plx!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){
                        $.ajax({
                            url: "{{url('/reactive-posproducts')}}",
                            type: 'PUT',
                            data:{_token:"{{ csrf_token() }}",
                                subid:id,
                            },
                            success:function(resp){
                                if(resp == 1){
                                    swal({
                                        title: "Re-Active",
                                        text: "POS Product Re-Active Successfully!",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{ url('/posproducts') }}";
                                        }
                                    });
                                }
                            }

                        });

                    }else {
                        swal("Cancelled", "Operation Cancelled:)", "error");
                    }
                });
        }

        function autoCodeGenerate(finishgood,elementId){

                        $.ajax({
                            url: "{{ route('autoGenerateCode_variableProduct') }}",
                            type: 'POST',
                            data:{_token:"{{ csrf_token() }}",product_id:finishgood},
                            success:function(resp){
                                $("#"+elementId).val(resp);
                            }

                        });
        }

        function edit(id,fnhgood,code,name,price,uomId,src,image,priority,attribute,attr_mode){
            $("#attribute_mode_vpmd").prop('checked', false);

            $('#update-modal').modal('show');
            $('#finishgood_vpmd').val(fnhgood);
            $('#item_id_vpmd').val(id);
            $('#item_code_vpmd').val(code);
            $('#item_name_vpmd').val(name);
            $('#price_vpmd').val(price);
            $('#priority_vpmd').val(priority);
            $('#uom_vpmd').val(uomId).change();
			$("#modal_previewImage_vpmd").attr("src",src);
			$("#prevImageName_vpmd").val(image);
            $("#modal_previewImageFancy_vpmd").attr('href',src);
            $("#attribute_vpmd").val(attribute).change();

            if(attr_mode != 0){
                $("#attribute_mode_vpmd").prop('checked', true);
            }
			$("#item_image_vpmd_alert").text('');
        }


        // function update(){
		 $('#update-variableProductForm').on('submit', function(event){
             event.preventDefault();

            var process = true;
            if($("#item_code_vpmd").val() == ''){
                process = false;
                $("#item_code_vpmd").focus();
                $("#item_code_alert").text('Enter item code is required!');
                swal('Error!','Enter item code is required!','error');
            }

            if($("#item_name_vpmd").val() == ''){
                process = false;
                $("#item_name_vpmd").focus();
                $("#item_name_alert_vpmd").text('Enter item name is required!');
                swal('Error!','Enter item name is required!','error');
            }

            if($("#attribute_vpmd").val() == ''){
                process = false;
                $("#attribute_vpmd").focus();
                $("#attribute_alert").text('Select attribute field is required');
                swal('Error!','Select attribute field is required!','error');
            }

            if(process){

                $.ajax({
                    url: "{{route('updateVariableProduct')}}",
                    type: 'POST',
    				data: new FormData(this),
    				contentType: false,
    				cache: false,
    				processData: false,
    				dataType:'json',
                    success:function(resp){

                        if (resp.status == 200) {
                            swal({
                                title: "success",
                                text: "Updated Successfully!",
                                type: "success"
                            },function(isConfirm){
                                if(isConfirm){
                                   window.location = "{{ route('createVariableProduct',$generalItem[0]->id) }}";
                                }
                            });
                        }
                        else{

                             if($.inArray('control',Object.keys(resp))){
                                 $("#"+resp.control+'_alert').text(resp.msg);
                             }
                                swal({
                                    title: "Error!",
                                    text: resp.msg,
                                    type: "error"
                                });
                        }
                    }
                });
            }
        });


        $('#btngen').on('click', function(){
            if ($('#depart').val() == "") {
                swal({
                    title: "Error Message",
                    text: "Select Department First!",
                    type: "error"
                });
            }
            else if ($('#subDepart').val() == "") {
                swal({
                    title: "Error Message",
                    text: "Select Sub Department First!",
                    type: "error"
                });
            }
            else{

                let depart = $('#depart option:selected').text();
                let subdepart = $('#subDepart option:selected').text();

                let d = depart.substring(0,1);
                let sub = subdepart.substring(0,1)
                let rand = Math.floor(Math.random() * 10000);

                let codes = d+ sub + "-" + rand;

                $('#code').val(codes);
            }

        });

        function verifycode() {
            $.ajax({
                url: "{{url('/verifycode')}}",
                type: 'GET',
                data:{_token:"{{ csrf_token() }}",
                    code: $('#code').val(),
                },
                success:function(resp){
                    console.log(resp);
                    if (resp != 0) {
                        swal({
                            title: "Already Exsist!",
                            text: "Item Code Already Exsist, Please try different!",
                            type: "error"
                        });
                        $('#code').val('');
                    }
                }
            });

        }


   $("#btn_uom").on('click',function(){
        $('#txtuom').val('');
        $("#uom-modal").modal("show");
    });

   function adduom(){
           if ($('#txtuom').val() == "") {
             swal({
                    title: "Error Message",
                    text: "Required Field can not be blank!",
                    type: "warning"
               });

          }
          else
          {
             $.ajax({
                    url: "{{url('/adduom')}}",
                    type: 'POST',
                    dataType:"json",
                    data:{_token:"{{ csrf_token() }}",
                    uom:$('#txtuom').val(),
                  },

                    success:function(resp){
                        if(resp != 0){
                             swal({
                                    title: "Operation Performed",
                                    text: "Unit of Measure Added Successfully!",
                                    type: "success"
                               });
                             $("#uom-modal").modal("hide");
                             $("#uom").empty();
                             for(var count=0; count < resp.length; count++){
                              $("#uom").append("<option value=''>Select Unit of Measure</option>");
                              $("#uom").append(
                                "<option value='"+resp[count].uom_id+"'>"+resp[count].name+"</option>");
                             }
                          }
                          else
                          {
                             swal({
                                    title: "Already exsist",
                                    text: "Particular UOM Already exsist!",
                                    type: "warning"
                               });
                              $("#uom-modal").modal("hide");

                          }
                     }

                  });
            }
     }

		$("#department_md").on('change',function(){
		    if($(this).val() == ''){
		        $("#subDepartment_md").val('');
		        if(!$("#subDepartment_md").attr('disabled')){
		            $("#subDepartment_md").attr('disabled',true);
		        }
		    } else{
		       load_subdept($(this).val(),'subDepartment_md');
		    }
		});

		$("#subDepartment_md").on('change',function(){
		    if($(this).val() == ''){
		        $("#product_md").val('');
		        if(!$("#product_md").attr('disabled')){
		            $("#product_md").attr('disabled',true);
		        }
		    } else{
		       productload_department_wise($(this).val(),'product_md');
		    }
		});


		function productload_department_wise(departId,elementId){
			$.ajax({
			  url: "{{ route('invent-list-department') }}",
			  method : "POST",
			  data:{_token:'{{ csrf_token() }}',id:departId},
			  cache: false,
			  success: function(resp){
			    if(resp != null){
				 $("#"+elementId).empty();

				 if($("#"+elementId).attr('disabled')){
				     $("#"+elementId).attr('disabled',false);
				 }

			        $("#"+elementId).append('<option value="">Select</option>')

    			   $.each(resp,function(i,v){
    			       $("#"+elementId).append('<option value="'+v.id+'">'+v.product_name+'</option>');
    			   })
			    }

			  }
			});
		}

       function variationPriority(posProdId,variatId){
        $("#priority_variation_md").empty();
        $.ajax({
			  url: '{{ route("get_variationPriority") }}',
			  method : "POST",
			  data:{_token:'{{ csrf_token() }}',posItemId:posProdId,variationId:variatId},
			  dataType:'json',
			  success: function(resp){
                if(resp != ''){
                    // $("#priority_variation_md").empty();
                    $("#priority_variation_md").append('<option value="">Select</option>');
                    $.each(resp,function(i,v){
                        $("#priority_variation_md").append('<option value="'+v.priority+'">'+v.name+'</option>');
                    });

                    $("#priority_variation_md").append('<option value="0">Last</option>');
                }
			  }
			});
       }

	   function createVariation(id,itemName){
	       $("#modal-title-variation").text('Create Variation');
	       $("#mode_md").val(0);
	       $("#itemId_md").val(id);
	       $("#itemName_md").val(itemName);
	       $("#createVariation-modal").modal('show');
	       $("#department_md").val('').trigger('change');

	       $("#variation_name").val('');
	       $("#variation_type").val('').trigger('change');

           variationPriority(id,''); // get variation priority

	       $.each(table_row_mdId,function(i,v){
	           $("#"+v).remove();
	       });

	       if(!$("#btn_copy_variation").hasClass('d-none')){
	           $("#btn_copy_variation").addClass('d-none');
	       }

	       if(!$("#btn_remove_variation").hasClass('d-none')){
	           $("#btn_remove_variation").addClass('d-none');
	       }

	       $("#btn_submit_variation").text('Submit');

	       if($("#btn_submit_variation").hasClass('btn-success')){
	           $("#btn_submit_variation").removeClass('btn-success');
	           $("#btn_submit_variation").addClass('btn-primary');
	       }
	   }

	   $("#variation_type").on('change',function(){
	       if($(this).val() == 'multiple'){
	           if($("#selection_limited").attr('disabled')){
	               $("#selection_limited").attr('disabled',false);
	           }
	       }else{
	           if(!$("#selection_limited").attr('disabled')){
	               $("#selection_limited").attr('disabled',true);
	           }
	       }

	       $("#selection_limited").val(0);
	   })

	   function modal_add_variation(){
	       //alert($("#"+$("#product_md").val()).length)

	       if($("#product_md").val() != '' && $("#department_md").val() != '' && $("#subDepartment_md").val() != ''){

	       if($("#row_md_"+$("#product_md").val()).length == 0){
	           var price = $("#price_md").val() == '' ? 0 : $("#price_md").val();
	         $("#table_variationLists_md tbody").append('<tr id="row_md_'+$("#product_md").val()+'"><td>'+$("#department_md option:selected").text()+'</td><td>  '+$("#subDepartment_md option:selected").text()+'</td><td id="cel-2-'+$("#product_md").val()+'">'+$("#product_md option:selected").text()+'<input type="hidden" name="products[]" value="'+$("#product_md").val()+'"></td><td><input type="hidden" name="price[]" value="'+price+'">'+price+'</td><td><i class="icofont icofont-trash text-danger pointer m-t-2 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Remove Variation" onclick="modal_remove_variation('+$("#product_md").val()+')"></i></td></tr>');

	         table_row_mdId.push("row_md_"+$("#product_md").val());

	         $("#product_md").val('');
	         $("#price_md").val('');
	       }else{
	                swal({
                            title: "Error",
                            text: "This Product is already taken",
                            type: "error"
                       });
	       }
	     }else{
	                swal({
                            title: "Error",
                            text: "Select the product please",
                            type: "error"
                       });
	     }
	   }

	   function modal_remove_variation(id){
           swal({
                    title: "Are you sure?",
                    text: "You want to remove this "+$("#cel-2-"+id).text()+" variation!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "yes plx!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){
                           $("#row_md_"+id).remove();
                           swal.close();
                    }else {
                        swal("Cancelled", "Operation Cancelled:)", "error");
                    }
                });

	   }

	   $("#btn_submit_variation").on('click',function(){
	      var process = true;

	      if($("#variation_name").val() == ''){
	          process = false;
	          $("#variation_name").focus();
	          $("#variation_name_alert").text('Enter variation field is required!');
	      }

	      if($("#variation_type").val() == ''){
	          process = false;
	          $("#variation_type").focus();
	          $("#variation_type_alert").text('Select variation type field is required!');
	      }

	      if($("#variation_type").val() == 'multiple' && $("#selection_limited").val() <= 1){
	          process = false;
	          $("#selection_limited").focus();
	          $("#selection_limited_alert").text('Limit should be atleast 2');
	      }

	     if($("#mode_md").val() == 0 && table_row_mdId.length == 0){
	          process = false;
	          $("#department_md").focus();
	       //   $("#department_md_alert").text();
	          swal("Alert!", 'field is required!', "error");

	   //   if($("#product_md").val() == ''){
	   //       process = false;
	   //       $("#product_md").focus();
	   //       $("#product_md_alert").text('Select product field is required!');
	   //   }
	     }

	      if(process){

	        var urlMode = $("#mode_md").val() != 0 ? "{{ route('updateVariableProduct_variation') }}" : "{{ route('storeVariableProduct_variation') }}";

			$.ajax({
			  url: urlMode,
			  method : "POST",
			  data:$("#variationForm").serialize(),
			  dataType:'json',
              beforeSend:function(){
                $('#btn_submit_variation').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Please wait');
              },
			  success: function(resp){

                    $('#btn_submit_variation').prop('disabled', false).html(($("#mode_md").val() == 0 ? 'Submit' : 'Update'));

			    if(resp.status == 200){
	                $("#createVariation-modal").modal('hide');
	                variationForm_clear();
	                swal("Success!", "", "success");
	                reCallVariation($("#itemId_md").val());
			    }else{
                   if(resp.status == 409){
                       $("#"+resp.control).focus();
                       $("#"+resp.control).text(resp.msg);
                   }

                   if(resp.status == 500){
                       swal("Cancelled", resp.msg, "error");
                   }
			    }

			  }
			});
	      }
	   });

	   function variationForm_clear(){
	       $("#variationForm")[0].reset();

           $("#product_md").val('').change();

	       $("#variation_name_alert").text('');
	       $("#variation_type_alert").text('');
	       $("#selection_limited_alert").text('');

	       $("#table_variationLists_md tbody").empty();
	       table_row_mdId = [];
	   }

	   $('#createVariation-modal').on('hide.bs.modal', function (e) {
          variationForm_clear();
        });

	   function editVariationValue(unId,variationId,productId,productName,variationName,variationType,variationSelectionLimit){
	       variationForm_clear();
	       $("#modal-title-variation").text('Edit Variation');

	       $("#btn_submit_variation").text('Update');

           variationPriority(productId,variationId);

	       if($("#btn_submit_variation").hasClass('btn-primary')){
	           $("#btn_submit_variation").removeClass('btn-primary');
	           $("#btn_submit_variation").addClass('btn-success');
	       }

	       if($("#btn_copy_variation").hasClass('d-none')){
	           $("#btn_copy_variation").removeClass('d-none');
	       }

	       if($("#btn_remove_variation").hasClass('d-none')){
	           $("#btn_remove_variation").removeClass('d-none');
	       }

	       $("#variation_type").val(variationType).trigger('change');

	       $("#mode_md").val(unId);
	       $("#variationId_md").val(variationId);
	       $("#itemId_md").val(productId);
	       $("#itemName_md").val(productName);
	       $("#createVariation-modal").modal('show');
	       $("#subDepartment_md").val('').trigger('change');
	       $("#department_md").val('').trigger('change');

	       $("#variation_name").val(variationName);

           $("#selection_limited").val(variationSelectionLimit);

            $.ajax({

                url: "{{ route('VariableProduct_VariationValues') }}",
                type: 'POST',
                data:{_token:'{{ csrf_token() }}',id:variationId,fnshGoodProd:$("input[name='finishgood']").val()},
                dataType:"json",
                async : 'false',
                success:function(resp){
                    //console.log(resp.variationPriority);
                if(resp.variationValues != null){

                    $.each(resp.variationValues,function(i,v){
            	       if($("#row_md_"+v.inventory_product_id).length == 0){
            	         $("#table_variationLists_md tbody").append('<tr id="row_md_'+v.inventory_product_id+'"><td>  '+v.department_name+'</td><td>'+v.sub_depart_name+'</td><td id="cel-2-'+v.inventory_product_id+'">'+v.name+'<input type="hidden" name="products[]" value="'+v.inventory_product_id+'"></td><td><input type="hidden" name="price[]" value="'+ v.price+'">'+ v.price+'</td><td><i class="icofont icofont-trash text-danger pointer m-t-2 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Remove Variation" onclick="modal_remove_variation('+v.inventory_product_id+')"></i></td></tr>');

            	         table_row_mdId.push("row_md_"+v.inventory_product_id);

            	         $("#product_md").val('').trigger('change');
            	         $("#price_md").val('');
            	       }
                  });
                }

                 if(resp.posProdCount == 0){
                    if(!$("#btn_copy_variation").hasClass('d-none')){
                        $("#btn_copy_variation").addClass('d-none');
                    }
                 }

                 if(resp.posProdCount > 0){
                    if($("#btn_copy_variation").hasClass('d-none')){
                        $("#btn_copy_variation").removeClass('d-none');
                    }
                 }
                }
            });
	   }

		function reCallVariation(itemId){
            $.ajax({
                url: "{{ route('VariableProduct_reloadVariation') }}",
                type: 'POST',
                data:{_token:'{{ csrf_token() }}',id:itemId},
                dataType:"json",
                async : 'false',
                success:function(resp){
                if(resp.variationHead != null){
                    $("#cell-3-"+itemId).empty();
                    $.each(resp.variationHead,function(i,v){
                        $("#cell-3-"+v.product_id).append("<label class='badge badge-bg-success badge-lg pointer' id='lable-variation-"+v.id+"' onclick='editVariationValue("+v.id+","+v.variation_id+","+ v.product_id+",\""+v.item_name+"\",\""+v.name+"\",\""+v.type+"\",\""+v.addon_limit+"\")'>"+v.name+"</label><span id='variationProdcutCount-"+v.variation_id+"' class=''></span><br/>");
                    });

                    $.each(resp.variationProductCount,function(i,v){
                          $("#variationProdcutCount-"+v.addon_category_id).text(v.countProduct).addClass('badge badge-black badge-header3');
                    });
                  }
                }

            });
		}

		$("#btn_remove_variation").on('click',function(){

             swal({
                    title: "Are you sure?",
                    text: "You want to remove this "+$("#variation_name").val()+" variation!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "yes plx!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){

                           $.ajax({
                                url: "{{ route('removeVariation_variableProduct') }}",
                                type: 'POST',
                                data:{_token:'{{ csrf_token() }}',variation_group_id:$("#variationId_md").val(),item_id:$("#itemId_md").val()},
                                dataType:"json",
                                async : 'false',
                                success:function(resp){
                                    if(resp.status == 200){
                                        $("#lable-variation-"+$("#mode_md").val()).remove();
                                        $("#createVariation-modal").modal('hide');
                                        swal("Success!","", "success");
                                    }

                                    if(resp.status == 500){
                                        swal("Cancelled",resp.msg, "error");
                                    }
                                }
                           })
                    }else {
                        swal("Cancelled", "Operation Cancelled:)", "error");
                    }
                });

		})

   $("#btn_attr_create").on('click',function(){
        $('#attribute_txt').val('');
        $("#create-attribute-modal").modal("show");
    });

    $("#btn_attr_create_vpmd").on('click',function(){
        $('#attribute_txt').val('');
        $("#create-attribute-modal").modal("show");
    });

   function add_attribute(){
           if ($('#attribute_txt').val() == "") {
             swal({
                    title: "Error Message",
                    text: "Required Field can not be blank!",
                    type: "warning"
               });

          }
          else
          {
             $.ajax({
                    url: "{{route('insertProduct_attribute')}}",
                    type: 'POST',
                    dataType:"json",
                    data:{_token:"{{ csrf_token() }}",
                    control:'attribute',
                    value:$('#attribute_txt').val(),
                  },

                    success:function(resp,txtStatus,respStatus){
                        if(respStatus.status == 200){
                             swal({
                                    title: "Operation Performed",
                                    text: "Unit of Measure Added Successfully!",
                                    type: "success"
                               });
                             $("#create-attribute-modal").modal("hide");

                              $("#attribute").append(
                                "<option value='"+resp[count].id+"'>"+resp[count].name+"</option>");
                                $("#attribute_vpmd").append(
                                    "<option value='"+resp[count].id+"'>"+resp[count].name+"</option>");

                        }else{
                             swal({
                                    title: "Error!",
                                    text: resp,
                                    type: "error"
                               });
                              $("#create-attribute-modal").modal("hide");

                          }
                     }

                  });
            }
     }

     $("#item_name").on('change',function(){
        itemName_formatChecking($(this).attr('id'));
     });

     $("#item_name_vpmd").on('change',function(){
        itemName_formatChecking($(this).attr('id'));
     });

     function itemName_formatChecking(inputId){
        let regex = /^[a-zA-Z0-9\s\u0600-\u06FF\u0750-\u077F\-\(\)\.]+$/;
        let inputValue = $("#"+inputId);
        let position = name.indexOf("vpmd");
          if(!regex.test(inputValue.val())){
            swal('Error!','Special characters are not allowed!','error');

            if (position !== -1) {
                $("#"+inputId.replace('vpmd','alert_vpmd')).text('Special characters are not allowed!');
            }else{
             $("#"+inputId+"_alert").text('Special characters are not allowed!');
            }

             if(!inputValue.hasClass('input-danger')){
                inputValue.addClass('input-danger')
             }

             if(inputValue.hasClass('input-success')){
                inputValue.removeClass('input-success')
             }

         }else{
            $("#"+inputId+"_alert").text('Valid format');
            if(inputValue.hasClass('input-danger')){
                inputValue.removeClass('input-danger')
             }

             if(!inputValue.hasClass('input-success')){
                inputValue.addClass('input-success')
             }
          }

     }

 </script>

    {{-- @include('Inventory.variable-product-partial.variable-product-js-script') --}}

    @include('Inventory.addon-partial.addon-js-script')

@endsection



