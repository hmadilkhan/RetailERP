<script>

let table_row_AddonTab = [];
let table_row_editAddonmdId = [];

        $("#addonList_tab").DataTable({

            bLengthChange: true,
            displayLength: 10,
            info: true,
            order: [[0, 'desc']],
            language: {
                search:'',
                searchPlaceholder: 'Search Addon',
                lengthMenu: '<span></span> _MENU_'
            }
        });

		function load_subdept(id,elementId){
            $.ajax({
                url: "{{ url('get_sub_departments') }}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",id:id},
                success:function(resp){
                    $('#'+elementId).empty();

    				 if($("#"+elementId).attr('disabled')){
    				     $("#"+elementId).attr('disabled',false);
    				 }

                    $('#'+elementId).append("<option value=''>Select Sub Department</option>");
                    $.each(resp, function( index, value ) {
                        $('#'+elementId).append(
                            "<option value="+value.sub_department_id+">"+value.sub_depart_name+"</option>"
                        );
                    });
                }
            });
        }


		$("#department_addonTab").on('change',function(){
		    if($(this).val() == ''){
		        $("#subDepartment_addonTab").val('change');
		        if(!$("#subDepartment_addonTab").attr('disabled')){
		            $("#subDepartment_addonTab").attr('disabled',true);
		            $("#subDepartment_addonTab").val('').trigger('change');
		        }
		    }else{
		        load_subdept($(this).val(),'subDepartment_addonTab');
		    }
		    $("#subDepartment_addonTab_alert").text('');
		});

		$("#subDepartment_addonTab").on('change',function(){
		    if($(this).val() == ''){
		        $("#product_addonTab").val('').change();
		        if(!$("#product_addonTab").attr('disabled')){
		            $("#product_addonTab").attr('disabled',true);
		            $("#product_addonTab").val('');
		        }
		    } else{
		       productload_department_wise($(this).val(),'product_addonTab');
		    }
		});

		$("#department_editmdAddon").on('change',function(){
		    if($(this).val() == ''){
		        $("#subDepartment_editmdAddon").val('change');
		        if(!$("#subDepartment_editmdAddon").attr('disabled')){
		            $("#subDepartment_editmdAddon").attr('disabled',true);
		            $("#subDepartment_editmdAddon").val('').trigger('change');
		        }
		    }else{
		        load_subdept($(this).val(),'subDepartment_editmdAddon');
		    }
		    $("#subDepartment_editmdAddon_alert").text('');
		});

		$("#subDepartment_editmdAddon").on('change',function(){
		    if($(this).val() == ''){
		        $("#product_editmdAddon").val('').change();
		        if(!$("#product_editmdAddon").attr('disabled')){
		            $("#product_editmdAddon").attr('disabled',true);
		            $("#product_editmdAddon").val('');
		        }
		    } else{
		       productload_department_wise($(this).val(),'product_editmdAddon');
		    }
		});

		function productload_department_wise(departId,elementId){
            $("#"+elementId).empty();

			$.ajax({
			  url: "{{ route('invent-list-department') }}",
			  method : "POST",
			  data:{_token:'{{ csrf_token() }}',id:departId},
			  cache: false,
			  success: function(resp){
			    if(resp != null){

				 if($("#"+elementId).attr('disabled')){
				     $("#"+elementId).attr('disabled',false);
				 }

    			   $.each(resp,function(i,v){
    			       $("#"+elementId).append('<option value="'+v.id+'">'+v.product_name+'</option>');
    			   })
			    }

			  }
			});
	   }

	   $("#addon_type_addonTab").on('change',function(){
	       add_type_value_set($(this).val(),"selection_limit_addonTab")
	   });

	   $("#addon_type_editmdAddon").on('change',function(){
	       add_type_value_set($(this).val(),"selection_limit_editmdAddon")
	   });



	   function add_type_value_set(value,elementId){
		    if(value == 'multiple'){
		        if($('#'+elementId).attr('disabled')){
		            $('#'+elementId).attr('disabled',false);
		        }
		    }else{
		        if(!$('#'+elementId).attr('disabled')){
		            $('#'+elementId).attr('disabled',true);
		        }
		    }

		    $('#'+elementId).val('');
	   }


	   function add_addon_addonTab(){

	       var subDepartmentName = $("#subDepartment_addonTab option:selected").text();
	       var departmentName    = $("#department_addonTab option:selected").text();
	       var productId         = $("#product_addonTab").val();
	       var productName       = $("#product_addonTab option:selected").text();
	       var departmentId      = $("#department_addonTab").val();

	       if(productId != '' &&  departmentId != ''){
    	       if($("#row_addonTable"+productId).length == 0){
    	           var price = $("#price_addonTab").val() == '' ? 0 : $("#price_addonTab").val();
    	         $("#table_generatList_addonTab tbody").append('<tr id="row_addonTable'+productId+'"><td>'+departmentName+'</td><td>'+subDepartmentName+'</td><td id="tableCel-2-'+productId+'">'+productName+'<input type="hidden" name="products[]" value="'+productId+'"></td><td><input type="hidden" name="price[]" value="'+price+'">'+price+'</td><td><i class="icofont icofont-trash text-danger pointer m-t-2 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Remove Addon" onclick="remove_Table('+productId+')"></i></td></tr>');

    	         table_row_AddonTab.push("row_addonTable"+productId);

    	         $("#product_mdAddon").val('').trigger('change');
    	         $("#price_addonTab").val('');
    	         $("#department_mdAddon_alert").text('');
    	         $("#product_mdAddon_alert").text('');
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

	   $("#btn_save_addon").on('click',function(){

	       let process = true;

	       if($("#addon_name_addonTab").val() == ''){
	           process = false;
	           $("#addon_name_addonTab").focus();
	           $("#addon_name_addonTab_alert").text('Field is requried.');
	       }

	       if($("#addon_type_addonTab").val() == ''){
	           process = false;
	           $("#addon_type_addonTab").focus();
	           $("#addon_type_addonTab_alert").text('Field is requried.');
	       }

	       if($("#addon_type_addonTab").val() == 'multiple' && $("#selection_limit_addonTab").val() <= 1){
	           process = false;
	           $("#selection_limit_addonTab").focus();
	           $("#selection_limit_addonTab_alert").text('Limit should be atleast 2');
	       }

	       if(table_row_AddonTab.length < 0){
	           process = false;
	           $("#department_addonTab").focus();
	           $("#department_addonTab_alert").text('Field is requried.');
	       }

	       if(process){
    			$.ajax({
    			  url: "{{ route('storeAddon') }}",
    			  method : "POST",
    			  data:$("#createAddonTabForm").serialize(),
    			  dataType:'json',
                  beforeSend:function(){
                    $('#btn_save_addon').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Please wait');
                  },
    			  success: function(resp){
                    $('#btn_save_addon').prop('disabled', false).html('Create');
    			    if(resp.status == 200){
    			        formClearValue_AddonTab();
    			        swal("Success", "", "success");
    			        loadAddon_table($("input[name='finishgood']").val());
    			    }else{
    			        if(resp.status == 409){
    			            $("#"+resp.control).focus();
    			            $("#"+resp.control+"_alert").text(resp.msg);
    			            swal("Cancelled",resp.msg, "error");
    			        }

    			        if(resp.status == 500){
    			           swal("Cancelled",resp.msg, "error");
    			        }
    			    }

    			  }
    			});
	       }
	   });

	   function formClearValue_AddonTab(){
	      $("#createAddonTabForm")[0].reset();

	      let alert_control = ['addon_name_addonTab_alert','addon_type_addonTab_alert','selection_limit_addonTab_alert','department_addonTab_alert'];

	      $("#addon_type_addonTab").val('').trigger('change');

	      $("#department_addonTab").val('').trigger('change');

	      $("#is_required_addonTab").attr('checked',false);

	      $.each(alert_control,function(i,v){
	          $("#"+v).text('');
	      });

	      $.each(table_row_AddonTab,function(i,v){
	          $("#"+v).remove();
	      });
	   }

	   function loadAddon_table(productId){
            $.ajax({
    			  url: "{{ route('loadAddons') }}",
    			  method : "POST",
    			  data:{_token:'{{ csrf_token() }}',id:productId},
    			  dataType:'json',
    			  success: function(resp){
    			    if(resp != null){
    			        $("#addonList_tab tbody").empty();
    			        $.each(resp,function(i,v){
    			              $("#addonList_tab tbody").append("<tr id='tr-addonTab-table-"+v.id+"'>"+
    			                                           "<td class='d-none'>"+v.id+"</td>"+
    			                                           "<td>"+v.name +"</td>"+
    			                                           "<td id='cell-3-addonTab-"+v.id+"'></td>"+
    			                                           "<td>"+(v.is_required == 1 ? 'Yes' : 'No')+"</td>"+
    			                                           "<td>"+v.type+"</td>"+
    			                                           "<td>"+v.addon_limit+"</td>"+
    			                                           "<td>"+v.priority+"</td>"+
    			                                           "<td class='action-icon'>"+
    			                                             "<a  class='m-r-10' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'><i class='icofont icofont-ui-edit text-primary f-18' onclick='editAddon("+v.id+",\""+v.name+"\",\""+v.type+"\","+v.is_required+","+v.addon_limit+","+v.priority+")'></i></a>"+

    			                                             "<a  class='m-r-10' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'><i class='icofont icofont-ui-delete text-danger f-18' onclick='editAddon("+v.id+",\""+v.name+"\","+v.productId+")'></i></a>"+
    			                                           "</td></tr>");

    			              $.each(v.addons,function(chld_i,chld_v){
    			                  $("#cell-3-addonTab-"+v.id).append("<label class='badge badge-bg-success badge-lg'>"+chld_v.name+" - Rs."+chld_v.price+"</label> <br/>");
    			              });

    			        })
    			    }
    			  }
    			});
	   }

	   function remove_Table(id){
           swal({
                    title: "Are you sure?",
                    text: "You want to remove this "+$("#tableCel-2-"+id).text()+" product!",
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
                           $("#row_addonTable"+id).remove();
                           arrayValue_remove("row_addonTable"+id,0);
                           swal("Success", "", "success");
                    }else {
                        swal("Cancelled", "Operation Cancelled:)", "error");
                    }
                });
	   }

	   function arrayValue_remove(value,md){

          if(md == 1){
            const index = table_row_editAddonmdId.indexOf(value);
            table_row_editAddonmdId.splice(index, 1);
          }else{
            const index = table_row_AddonTab.indexOf(value);
            table_row_AddonTab.splice(index, 1);
          }
	   }

	   function remove_addon(addonheadId,name,productId){
           swal({
                    title: "Are you sure?",
                    text: "You want to remove this "+name+" addon!",
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
            			  url: "{{ route('removeAddon') }}",
            			  method : "POST",
            			  data:{_token:'{{ csrf_token() }}',id:addonheadId,product_id:productId},
            			  success: function(resp){
            			    if(resp.status == 200){
            			        $("#tr-addonTab-table-"+addonheadId).remove();
            			        swal("Success", "", "success");
            			    }else{
            			      swal("Cancelled",resp.msg, "error");
            			    }

            			  }
            			});
                    }else {
                        swal("Cancelled", "Operation Cancelled:)", "error");
                    }
                });
	   }

     function editAddon(headAddonId,addName,type,isRequired,addonSelectionLimit,priority){
           $("#editAddon-modal").modal('show');
           $("#editModel-title-addon").html('<span>Addon:</span>'+addName);
           $("#inventory_id_editmdAddon").val($("input[name='finishgood']").val());
           $("#inventory_name_editmdAddon").val($("input[name='itemName']").val());
           $("#addonheadId_editmdAddon").val(headAddonId);

           $("#addon_name_editmdAddon").val(addName);
        //   $("#showebsite_name_editmdAddon").val(showWebsiteName);
           $("#priority_editmdAddon").val(priority);
           $("#addon_type_editmdAddon").val(type).trigger('change');

           if(isRequired == 1){
               $("#is_required_editmdAddon").attr('checked',true);
           }

           $("#selection_limit_editmdAddon").val(addonSelectionLimit);

                loadAddonValues(headAddonId);
	   }

	   function loadAddonValues(headAddonId){

               $.ajax({
                    url: "{{route('loadAddonValues')}}",
                    type: "POST",
                    data: {_token:'{{ csrf_token() }}',id:headAddonId},
                    success:function(resp){
                         console.log(resp)
                        if (resp != null) {
                            table_row_editAddonmdId=[];
                            $("#table_addonGeneratList_editmd tbody").empty();
                            // $.each(table_row_editAddonmdId,function(i,v){
                            //     $("#"+v).remove();
                            // });

                            $.each(resp,function(i,v){
                              if($("#row_editmdAddon"+v.inventory_product_id).length == 0){

                                	         $("#table_addonGeneratList_editmd tbody").append('<tr id="row_editmdAddon'+v.inventory_product_id+'"><td> '+v.department_name+'</td><td> '+v.sub_depart_name+'</td><td id="cell-2-editmdAddon'+v.inventory_product_id+'">'+v.name+'<input type="hidden" name="products[]" value="'+v.inventory_product_id+'"></td><td><input type="hidden" name="price[]" value="'+v.price+'">'+v.price+'</td><td><i class="icofont icofont-trash text-danger pointer m-t-2 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Remove Addon" onclick="modal_remove_addonTmp('+v.inventory_product_id+','+v.id+')"></i></td></tr>');

                                	         table_row_editAddonmdId.push("row_editmdAddon"+v.inventory_product_id);

                                	         $("#product_editmdAddon").val('').trigger('change');
                                	         $("#price_editmdAddon").val('');
                                	         $("#department_editmdAddon_alert").text('');
                                	         $("#product_editmdAddon_alert").text('');
                               }
                            });
                        }
                    }
                });
	   }

	   function modal_add_editaddon_tmp(){

	       if($("#product_editmdAddon").val() != '' && $("#department_editmdAddon").val() != '' && $("#subDepartment_addonTab").val() != ''){
            			$.ajax({
            			  url: "{{ route('store_singleValueAddon') }}",
            			  method : "POST",
            			  data:{_token:'{{ csrf_token() }}',id:$("#addonheadId_editmdAddon").val(),product_id:$("#product_editmdAddon").val(),
            			  product_name:$("#product_editmdAddon option:selected").text(),price:$("#price_editmdAddon").val()},
            			  success: function(resp){
            			    if(resp.status == 200){
            			        loadAddonValues($("#addonheadId_editmdAddon").val());
            			        swal("Success", "", "success");
            			    }else{
            			      swal("Cancelled",resp.msg, "error");
            			    }

            			  }
            			});
	     }else{
	                swal({
                            title: "Error",
                            text: "Select the product please",
                            type: "error"
                       });
	     }
	   }

	   function modal_remove_addonTmp(addonProductId,addonId){
           swal({
                    title: "Are you sure?",
                    text: "You want to remove this "+$("#cell-2-editmdAddon"+addonProductId).text()+" product!",
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
            			  url: "{{ route('removeAddonValue') }}",
            			  method : "POST",
            			  data:{_token:'{{ csrf_token() }}',id:addonId},
            			  success: function(resp){
            			    if(resp.status == 200){
            			        $("#row_editmdAddon"+addonProductId).remove();
                                arrayValue_remove("row_editmdAddon"+addonProductId,1);
            			        swal("Success", "", "success");
            			        loadAddon_table($("#inventory_id_editmdAddon").val())
            			    }else{
            			      swal("Cancelled",resp.msg, "error");
            			    }

            			  }
            			});
                    }else {
                        swal("Cancelled", "Operation Cancelled:)", "error");
                    }
                });

	   }

	   $("#btn_updateAddon").on('click',function(){
	      let process = true;

	       if($("#addon_name_editmdAddon").val() == ''){
	           process = false;
	           $("#addon_name_editmdAddon").focus();
	           $("#addon_name_editmdAddon_alert").text('Field is requried.');
	       }

	       if($("#addon_type_editmdAddon").val() == ''){
	           process = false;
	           $("#addon_type_editmdAddon").focus();
	           $("#addon_type_editmdAddon_alert").text('Field is requried.');
	       }

	       if($("#addon_type_editmdAddon").val() == 'multiple' && $("#selection_limit_editmdAddon").val() <= 1){
	           process = false;
	           $("#selection_limit_editmdAddon").focus();
	           $("#selection_limit_editmdAddon_alert").text('Limit should be atleast 2');
	       }

	       if(table_row_editAddonmdId.length < 0){
	           process = false;
	           $("#department_editmdAddon").focus();
	           $("#department_editmdAddon_alert").text('Field is requried.');
	       }

	      if(process){

			$.ajax({
			  url: "{{ route('updateAddon') }}",
			  method : "POST",
			  data:$("#editAddon_Form").serialize(),
			  dataType:'json',
			  success: function(resp){
		            console.log(resp)
			    if(resp.status == 200){
	                $("#editAddon-modal").modal('hide');
	                swal("Success!", "", "success");
	                loadAddon_table($("input[name='finishgood']").val())
			    }else{
                   if(resp.status == 409){
                       $("#"+resp.control).focus();
                       $("#"+resp.control+"_alert").text(resp.msg);
                   }

                   if(resp.status == 500){
                       swal("Cancelled", resp.msg, "error");
                   }
			    }

			  }
			});
	      }
	   });

 function copyAddonProduct_modal(addonId,addonName){
    $("#addonId_cpymd").val(addonId);
    $("#addonName_cpymd").val(addonName);
    $("#cpyAddonName_head").text(addonName);

    $("#tbl_productListcpymd tbody").empty();
    $("#subDepartment_cpymd").val('');
    $("#department_cpymd").val('').change();

    $("#copy-addon-modal")
                .modal({
                        backdrop: "static",
                        keyboard: false
                    }).modal('show');
}


$("#department_cpymd").on('change',function(){
           if($(this).val() != ''){
               load_subdept($(this).val(),'subDepartment_cpymd');
           }else{
               $("#subDepartment_cpymd").val('');
    			 if(!$("#subDepartment_cpymd").attr('disabled')){
    			     $("#subDepartment_cpymd").attr('disabled',true);
    			 }
           }
});

$("#subDepartment_cpymd").on('change',function(){
    if($(this).val() != ''){
        get_allGeneralItemWithAddonBind($("#department_cpymd").val(),$(this).val(),$("#addonId_cpymd").val(),$("#addonName_cpymd").val());
    }
});


function get_allGeneralItemWithAddonBind(depart_val,subDepart_val,addonHeadId,addonHeadName){
              $.ajax({
                url: "{{ route('get_generalItem_withoutAddonBind') }}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",depart:depart_val,subDepart:subDepart_val,addonId:addonHeadId,addonName:addonHeadName},
                success:function(resp){
                    $('#tbl_productListcpyaddonmd tbody').empty();
                   if(resp == ''){
                       swal('Product not found!','','error');
                   }else{
                    // datatableVariable.destroy();
                    $('#tbl_productListcpyaddonmd tbody').empty();
                    $.each(resp, function( index, value ) {
                       if(value.id != $('#m_finishgood').val()){
                            $("#tbl_productListcpyaddonmd tbody")
                            .append('<tr><td><div class="form-check">'+
                                 '<label class="form-check-label f-18">'+
                                            '<input class="form-check-input" type="checkbox" value="'+value.id+'" name="tble_chk_prodcpyaddonmd"> '+value.product_name+
                                        '</label>'+
                              '</div></td></tr>');
                        // datatableVariable.row.add(['<label class="pointer"> <input type="checkbox" value="'+value.id+'" class="form-control pointer m-r-1" name="tble_chk_vcpymd">'+value.product_name+'</label>']);

                      }
                    });
                    //datatableVariable.draw();
                   }

                }
            });
        }

        function selectedProduct_bindAddon(){
           let products = [];
           let process = true;

            $.each($('input[name="tble_chk_prodcpyaddonmd"]'),function(){
                 if($(this).is(':checked')){
                     if($.inArray($(this).val(),products) == -1){
                        products.push($(this).val())
                     }
                 }
            })

            if(products.length == 0){
                swal('Error','Product not selected!','error');
                process = false;
            }

            if(process){
                $.ajax({
                url: "{{ route('copyGeneralProduct_bind_addon') }}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",products:products,addonId:$("#addonId_cpymd").val()},
                success:function(resp){
                    console.log(resp)
                }
            });
            }
        }


   function clearSelectedProduct_bindAddon(){
        $("#department_cpymd").val('').change();
        $("#subDepartment_cpymd").val('').attr('disabled',true);
        $('#tbl_productListcpyaddonmd tbody').empty();
        $("#copy-addon-modal").modal('hide');
   }

   $("#tble_chk_allprodcpyaddonmd").on('click',function(){
      if($(this).is(':checked') == true){
           $.each($('input[name="tble_chk_prodcpyaddonmd"]'),function(){
                   $(this).prop('checked', true);
            });
      }else{
           $.each($('input[name="tble_chk_prodcpyaddonmd"]'),function(){
                   $(this).prop('checked', false);
            });
      }
   });

  const hash = window.location.hash.substring(1);
    if(hash==='addonTab'){
        $("#tab_btn_addon").trigger('click');
    }
</script>

