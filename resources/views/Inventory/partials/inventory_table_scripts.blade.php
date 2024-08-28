<script type="text/javascript">
var rem_id = [];
$(".mainchk").on('click',function(){

            if($(this).is(":checked")){
                $("#ddselect").css("display", "block");

                $(".chkbx").each(function( index ) {

                    $(this).attr("checked",true);
                });

            }else {
                $("#ddselect").css("display", "none");
                $(".chkbx").each(function( index ) {
                    $(this).attr("checked",false);
                });
            }

        });



        // $(".chkbx").on('click',function(){
        function chkbox(id) {
            if ($("#"+id).is(":checked")) {


                $("#ddselect").css("display", "block");

            } else {
               
             

                $("#ddselect").css("display", "none");

            }
        }

        // });

        $(".subchk").on('click',function(){

            if($(this).is(":checked")){

                $("#btn_activeall").css("display", "block");

                $(".chkbx").each(function( index ) {
                    $(this).attr("checked",true);
                });

            }else {
   
                $("#btn_activeall").css("display", "none");
                $(".chkbx").each(function( index ) {
                    $(this).attr("checked",false);
                });
            }

        });


        $(".chkbx").on('click',function(){
            if($(this).is(":checked")){
                // $("#btn_activeall").removeClass('invisible');
                $("#btn_activeall").css("display", "block");

            }
            else {
                // $("#btn_activeall").addClass('invisible');
                $("#btn_activeall").css("display", "none");
            }
        });
		
		$("#btn_removeall").on('click',function(){
            var products = [];
            $(".chkbx").each(function( index ) {
                if($(this).is(":checked")){
					
					console.log($(this).data('id'))
                    if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                        rem_id.push($(this).data('id'));
                    }
                }

            });
			// console.log(rem_id)
            $.ajax({
                url: "{{url('/get_names')}}",
                type: "POST",
                data: {_token:"{{csrf_token()}}",ids:rem_id},
                async:false,
                success:function(resp){
                    for(var s=0;s < resp.length ;s++){
                        products.push(resp[s].product_name);
                    }
                }
            });

            var names = products.join();

            swal({
                title: "INACTIVE PRODUCTS",
                text: "Do you want to inactive  "+names+" ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){

                    if(rem_id.length > 0){
                        $.ajax({
                            url: "{{url('/all_invent_remove')}}",
                            type: "POST",
                            data: {_token:"{{csrf_token()}}",inventid:rem_id,statusid:2},
                            success:function(resp){

                                if (resp == 1) {
                                    swal({
                                        title: "Success!",
                                        text: "All Products In-Active Successfully :)",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            fetch_data(1)
                                        }
                                    });

                                }else{
                                    swal("Alert!", "Products not Deleted:)", "error");
                                }

                            }

                        });
                    }

                }else{
                    swal({
                        title: "Cancel!",
                        text: "All products are still inactive :)",
                        type: "error"
                    },function(isConfirm){
                        if(isConfirm){
                           fetch_data(1)

                        }
                    });

                }

            });
        });
		
		$("#btn_activeall").on('click',function(){
            var products = [];
            $(".chkbx").each(function( index ) {

                if($(this).is(":checked")){
                    if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                        rem_id.push($(this).data('id'));

                    }
                }

            });
			// console.log(rem_id)
            $.ajax({
                url: "{{url('/get_names')}}",
                type: "POST",
                data: {_token:"{{csrf_token()}}",ids:rem_id},
                async:false,
                success:function(resp){
                    for(var s=0;s < resp.length ;s++){
                        products.push(resp[s].product_name);
                    }
                }
            });

            var names = products.join();

            swal({
                title: "RE-ACTIVE",
                text: "Do you want to activate  "+names+" this items?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){

                    if(rem_id.length > 0){

                        $.ajax({
                            url: "{{url('/multiple-active-invent')}}",
                            type: "POST",
                            data: {_token:"{{csrf_token()}}",inventid:rem_id},
                            success:function(resp){

                                if (resp == 1) {
                                    swal({
                                        title: "Success!",
                                        text: "All Products activated Successfully :)",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            fetch_data(1)
                                        }
                                    });

                                }else{
                                    swal("Alert!", "Products not Deleted:)", "error");
                                }

                            }

                        });
                    }

                }else{
                    swal({
                        title: "Cancel!",
                        text: "All products are safe:)",
                        type: "error"
                    },function(isConfirm){
                        if(isConfirm){
                            fetch_data(1)
                            // $('#pro').removeClass("active");
                            // $('#act').addClass("active");
                        }
                    });

                }

            });


        });
</script>