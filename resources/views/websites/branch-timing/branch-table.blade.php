<form id="timingForm" method="post" action="{{route('branchTimingStore')}}">
<table id="demandtb" class="table dt-responsive table-striped nowrap" width="100%" cellspacing="0">
    <thead>
        <tr>
            <th>Day</th>
            <th>Opening Time</th>
            <th>Closing Time</th>
        </tr>
    </thead>
    <tbody>
            @csrf
            <input type='hidden' class="form-control" name="website_id" value="{{$websiteId}}" />
            <input type='hidden' class="form-control" name="branch_id" value="{{$branchId}}" />
            @foreach($days as $day)
            @php
            $filteredArray = collect($timings)
            ->filter(function ($timing) use ($day) {
            return $timing->day === $day;
            })->values();

            @endphp
            <tr>

                <input type='hidden' class="form-control" name="id[]" value="{{count($timings) > 0 ? $filteredArray[0]->id : ''}}" />
                <input type='hidden' class="form-control" name="mode" value="{{count($timings) > 0 ? 'update' : 'insert'}}" />
                <td><input type='hidden' class="form-control" name="dayname[]" value="{{$day}}" />{{$day}}</td>
                <td> <input type='text' class="form-control timepicker" name="starttime[]" id="starttime{{$day}}" placeholder="00:00" value="{{count($timings) > 0 ? date('h:i A', strtotime($filteredArray[0]->opening_time)) : ''}}" /></td>
                <td><input type='text' class="form-control timepicker" name="endtime[]" id="endtime{{$day}}" placeholder="00:00" value="{{count($timings) > 0 ? date('h:i A', strtotime($filteredArray[0]->closing_time)) : ''}}" /></td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3"><button id="btnSubmit" class="btn btn-md btn-primary m-l-1 m-t-2 f-right" type="submit">{{count($timings) > 0 ? 'Update' : 'Save'}}</button></td>
            </tr>
        </tbody>
    </table>
</form>

<script type="text/javascript">
    // $(".select2").select2();

    $(document).ready(function() {
    // Timepicker ko sirf un fields par apply karna jo type="text" ho
    $('input.timepicker').each(function() {
        if ($(this).attr('type') === 'text') {
             console.log($(this).attr('id'));
            $(this).timepicker({
                timeFormat: 'h:mm a',
                interval: 60,
                minTime: '0:00am',
                maxTime: '11:59pm',
                dynamic: false,
                dropdown: true,
                scrollbar: true
            });
        }
    });
});

       {{-- @foreach($days as $day)
            @php
            $filteredArray = collect($timings)
            ->filter(function ($timing) use ($day) {
            return $timing->day === $day;
            })->values();

            @endphp --}}


    // $('#starttime{{$day}},#endtime{{$day}}').datetimepicker({
    //     format: 'LT',
    //     icons: {
    //         time: "icofont icofont-clock-time",
    //         date: "icofont icofont-ui-calendar",
    //         up: "icofont icofont-rounded-up",
    //         down: "icofont icofont-rounded-down",
    //         next: "icofont icofont-rounded-right",
    //         previous: "icofont icofont-rounded-left"
    //     }
    // });


{{--@endforeach--}}

    // $("#btnSubmit").click(function(){
    //     $("#timingForm").submit();
    // })

</script>
