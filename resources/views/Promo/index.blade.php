@extends('layouts.master-layout')

@section('title','Promotion')

@section('breadcrumtitle','Branches')

@section('navbranchoperation','active')
@section('navpromo','active')

@section('content')
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Promotions</h5>
                <a href="{{url('/create-promotion')}}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Create Promotion
                </a>

            </div>
            <div class="card-block">

                <table  class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Promo Code</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Days</th>
                        <th>Limited</th>
                        <th>Status</th>
                        <th>Message</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    @if($promotions)
                        @foreach($promotions as $value)
                            <tr>
                                <td>{{$value->id}}</td>
                                <td>{{$value->promo_code}}</td>
                                <td>{{$value->generation_date}}</td>
                                <td>{{$value->expiration_date}}</td>
                                <td>{{$value->day}}</td>
                                <td>{{$value->name}}</td>
                                <td>{{$value->status_name}}</td>
                                <td>{{$value->message}}</td>
                                <td></td>
                            </tr>
                        @endforeach
                    @endif



                    </tbody>


                </table>
            </div>
        </div>
    </section>
@endsection
@section('scriptcode_three')
    <script type="text/javascript">

        $('.table').DataTable({

            bLengthChange: true,
            displayLength: 10,
            info: false,
            language: {
                search:'',
                searchPlaceholder: 'Search Branch',
                lengthMenu: '<span></span> _MENU_'

            }

        });

        //Alert confirm
        $('.alert-confirm').on('click',function(){
            var id= $(this).data("id");

            swal({
                    title: "Are you sure?",
                    text: "Your will not be able to recover this imaginary file!",
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
                            url: "{{url('/removebranch')}}",
                            type: 'PUT',
                            data:{_token:"{{ csrf_token() }}",
                                id:id,
                            },
                            success:function(resp){
                                if(resp == 1){
                                    swal({
                                        title: "Deleted",
                                        text: "Do you want to remove branch.",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{ url('/branches') }}";
                                        }
                                    });
                                }
                            }

                        });

                    }else {
                        swal("Cancelled", "Your branch is safe :)", "error");
                    }
                });
        });

    </script>
@endsection

