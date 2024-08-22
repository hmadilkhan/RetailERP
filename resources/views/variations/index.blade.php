@extends('layouts.master-layout')

@section('title','Variations')

@section('breadcrumtitle','View Variations')
@section('navinventory','active')
@section('navinvent_variation','active')

@section('content')

<section class="panels-wells">

  @if(Session::has('success'))
       <div class="alert alert-success">{{ Session::get('success') }}</div>
  @endif

  @if(Session::has('error'))
       <div class="alert alert-danger">{{ Session::get('error') }}</div>
  @endif

               <div class="card">
                  <div class="card-header">
                     <h5 class="card-header-text" id="title-hcard"> Create Variation</h5>
                  </div>
                  <div class="card-block">

    <form method="POST" id="variatform" class="form-horizontal" enctype="multipart/form-data">
      @csrf
       
        <div class="row">

        <div class="col-lg-3 col-md-3">
              <div class="form-group">
                  <label class="form-control-label">Name</label>
                  <input class="form-control" type="text"
                   name="variat_name" id="variat_name" />
                   <div class="form-control-feedback text-danger" id="variat_name_alert"></div>
              </div>
            </div>

        <div class="col-lg-3 col-md-3">
              <div class="form-group">
                  <label class="form-control-label">Show website name</label>
                  <input class="form-control" type="text"
                   name="show_website_name" id="show_website_name" />
                   <div class="form-control-feedback text-danger" id="show_website_name_alert"></div>
              </div>
            </div>            

             <div class="col-lg-3 col-md-3">
              <div class="form-group">
                  <label class="form-control-label">Values</label>
                   <div class="tags_add">
                      <input class="form-control" id="variat_values" name="variat_values" type="text"  />
                    </div>
                   <span class="form-control-feedback text-danger" id="variat_values_alert"></span>
              </div>
            </div>
      
      <div class="col-lg-3 col-md-3">
        <label class="form-control-label">Required option</label>
        <select name="required_status" id="required_status" data-placeholder="Select" class="form-control">
          <option value="">Select</option>
              <option {{ old('required_status') == 1 ? 'selected' : '' }} value="1">Required</option>
              <option {{ old('required_status') == 0 ? 'selected' : '' }} value="0">Optional</option>
        </select>
          <span class="form-control-feedback text-danger" id="required_status_alert"></span>
       </div>             
      </div>
      
              <div class="form-group row">
                <button class="btn btn-circle btn-primary f-left m-t-30 m-l-20"  type="button" id="btn_save" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Variation"><i class="icofont icofont-plus" 
                  ></i>&nbsp; Save</button>.
                    <button class="btn btn-circle btn-danger f-left m-t-30 m-l-10" id="btn_clear" type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Clear"><i class="icofont icofont-error" 
                  ></i> Clear</button>
              </div>
         </form>
            
                  </div>
               </div>
            </section>    

 <section class="panels-wells">
  <div class="row">

           <div class="col-lg-12 col-md-12">
              <div class="form-group">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Lists</h5>
           <button type="button" id="btn_removeall"  class="btn btn-danger f-right m-r-10 invisible"><i class="icofont icofont-ui-delete f-18 "></i>&nbsp;Remove</button>
         </div>      
       <div class="card-block">
       

    
           <div class="project-table">
     <table id="mainTable" class="table table-striped full-width">
         <thead>
            <tr>
               <th class="d-none">Code</th>
               <th>Name</th>
               <th>Values</th>
               <th>Required Status</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
              @if($variations)
                @foreach($variations as $val)
                       <tr>
  
                         <td class="d-none">{{ $val->id }}</td>
                         <td style="cursor: pointer;" onclick="">{{ $val->name }}</td>
                         <td style="cursor: pointer;" onclick="" >
                            @if($variat_value)
                              @foreach($variat_value as $sb_val)
                                 @if($sb_val->parent == $val->id)
                                 <label>{{ $sb_val->name }}</label>,
                                 @endif
                              @endforeach
                            @endif  
                         </td>
                         <td>{{ $val->required_status == 1 ? 'Requried' : 'Optional' }}</td>
                         <td class="action-icon">
                             <i onclick="" class="text-warning text-center icofont icofont-edit" 
                             data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"></i>

                             <i onclick="remove('{{ $val->name }}',{{ $val->id }})" class="text-danger text-center icofont icofont-trash" 
                             data-toggle="tooltip" data-placement="top" title="" data-original-title="Remove"></i>

                             <form action="{{ route('DestroyVariat',$val->id) }}" method="post" id="removeForm{{ $val->id }}">
                               @csrf
                               @method('DELETE')
                             </form>
                       </td>            
                     </tr>
               @endforeach
             @endif
                     </tbody>
                 </table>
        </div>
         </div>
          

    </div>
      </div>
          </div>
    


    </div>

   
   
</section>
@endsection

@section('scriptcode_three')

<script type="text/javascript">


   $(".select2").select2();

   $("#variat_values").tagsinput({
     maxTags: 20
    });
      $('#mainTable').DataTable( {

        bLengthChange: true,
        displayLength: 50,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Variations',
          lengthMenu: '<span></span> _MENU_'
   
        }
        
 
    } );
  



 $("#btn_save").on('click',function(event){
 // $("#deptform").on('submit',function(event){
// event.preventDefault();
     
  var process_md = true;

    if($("#variat_name").val().length === 0 ){
         $("#variat_name").focus();
         $("#variat_name_alert").html('Variation name is required.');
         process_md = false;
    }

    if($("#variat_values").val().length === 0 ){
         $("#variat_values").focus();
         $("#variat_values_alert").html('Variation values is required.');   
         process_md = false;   
    }

    if($("#show_website_name").val().length === 0 ){
         $("#show_website_name").focus();
         $("#show_website_name_alert").html('Variation values is required.');   
         process_md = false;   
    }

    if($("#required_status").val().length === 0 ){
         $("#required_status").focus();
         $("#required_status_alert").html('Select field is required.');   
         process_md = false;   
    }
    
    if(process_md){
                   $.ajax({
                    url:'{{ route("CreateVariat") }}',
                    type:"POST",
                    data:$('#variatform').serialize(),
                    dataType:"json",
           // contentType: false,
           // processData: false,
                    success:function(resp,status){
                       if(status == 'success'){
                            window.location = "{{ route('listVariation') }}";
                       }
                      // if(resp == 1){
                      //     if(r.contrl != ""){
                      //       $("#"+r.contrl).focus();
                      //       $("#"+r.contrl+"_alert").html(r.msg);
                      //     }
                      //     swal_alert('Alert!',r.msg,'error',false); 

                      // }else {
                      //    $("#deptname_alert").html('');
                      //   swal_alert('Successfully!',r.msg,'success',true);
                      //    $("#subdpt").tagsinput('removeAll');
                      // }
                    }
                  });
              
            }
     });



        //Alert confirm
        function remove(name,id){
            swal({
                    title: "Are you sure?",
                    text: "Do you want to Delete "+ name +" variation?",
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
                       $("#removeForm"+id).submit();
                        // $.ajax({
                        //     url: "{{-- url('DestroyVariat') --}}",
                        //     type: 'DELETE',
                        //     data:{_token:"{{-- csrf_token() --}}",
                        //         subid:id,
                        //     },
                        //     success:function(resp){
                        //         if(resp == 1){
                        //             swal({
                        //                 title: "Deleted",
                        //                 text: "POS Product Deleted Successfully!",
                        //                 type: "success"
                        //             },function(isConfirm){
                        //                 if(isConfirm){
                        //                     window.location="{{-- url('/posproducts') --}}";
                        //                 }
                        //             });
                        //         }
                        //     }

                        // });

                    }else {
                        swal("Cancelled", "Variations Safe :)", "error");
                    }
                });
        }
</script>

@endsection
