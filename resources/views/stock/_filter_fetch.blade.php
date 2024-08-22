
                <?php
                  $stock = 0;
                 ?>
                @foreach ($report as $value)
                    <?php
                     if($value->narration == 'Stock Opening' ){
                        $stock = $value->qty;
                     }elseif($value->narration == 'Sales'){
                        $stock = $stock - $value->qty;
                     }elseif($value->narration == 'Sales Return'){
                        $stock = $stock + $value->qty;
                     }elseif($value->narration == 'Stock Purchase through Purchase Order'){
                        $stock = $stock + $value->qty;
                     }elseif($value->narration == 'Stock Openend from csv file'){
                        $stock = $stock + $value->qty;
                     }elseif($value->narration == 'Stock Return'){
                        $stock = $stock - $value->qty;
                     }
                     ?>
                    <tr>
                        <td>{{date('d M Y',strtotime($value->date))}}</td>
                        <td>{{$value->narration}}</td>
                        <td>{{$value->qty}}</td>
                        <td>{{$stock}}</td>
                        <td>
                            @if (preg_match('/Purchase/', $value->narration))
                                <a href="{{route('view',$value->foreign_id)}}" class="p-r-10 f-18 text-info" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit">
                                  <i class="icofont icofont icofont-printer text-success"></i>
                                </a>
                            @elseif(preg_match('/Sales Return/', $value->narration))
                                <a href="{{url('sales-return',$value->foreign_id)}}" class="p-r-10 f-18 text-info" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit">  <i class="icofont icofont icofont-printer text-success"></i>
                                </a>
                            @elseif(preg_match('/Sales/', $value->narration))
                                <a href="{{url('print',Custom_Helper::getReceiptID($value->foreign_id))}}" class="p-r-10 f-18 text-info" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit">  <i class="icofont icofont icofont-printer text-success"></i>
                                </a>
                            @elseif(preg_match('/Stock Opening/', $value->narration))

                            @endif
                        </td>

                    </tr>
                @endforeach
