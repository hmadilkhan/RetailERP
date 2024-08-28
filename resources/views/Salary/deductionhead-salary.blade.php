<?php
$deduction = 0;
?>
  <div class="col-md-6 col-lg-6">
   <table id="tbldeduction" class="table nowrap" width="100%" cellspacing="0" style="border:2px solid">
         <thead>
            <tr>

              <th style="border-bottom:2px solid">Deduction Head</th>
               <th style="border-bottom:2px solid">Amount</th>
            </tr>
         </thead>
         <tbody>
          @if($advance)
          <tr>
            <?php $deduction = $deduction + $advance[0]->advance ?>
            <td>Advance</td>
            <td >{{number_format($advance[0]->advance,2)}}</td>
          </tr>
          <input type="hidden" id="SETadvanceamt" value="{{$advance[0]->advance}}">
            <input type="hidden" id="SETadvanceID" value="{{$advance[0]->advance_id}}">
          @endif
           @if($loan)
            <tr>
			<?php $deduction = $deduction + $loan[0]->loanamt ?>
            <td>Loan</td>
            <td >{{number_format($loan[0]->loanamt,2)}}</td>
          </tr>
          <input type="hidden" id="SETloanamt" value="{{$loan[0]->loanamt}}">
            <input type="hidden" id="SETLoandedeuctiondays" value="{{$loan[0]->deduction_amount}}">
          @endif
          @if($absentamt)
             <tr>
           <?php $deduction = $deduction + $absentamt[0]->absent_amt; ?>
            <td>Absent</td>
            <td >{{number_format($absentamt[0]->absent_amt,2)}}</td>
          </tr>
           <input type="hidden" id="SETabsentamt" value="{{$absentamt[0]->absent_amt}}">
          @endif
           @if($tax)
             <tr>
           <?php $deduction = $deduction + $tax[0]->tax_amount; ?>
            <td>Tax</td>
            <td >{{number_format($tax[0]->tax_amount,2)}}</td>
          </tr>
           <input type="hidden" id="SETtaxamt" value="{{$tax[0]->tax_amount}}">
          @endif
		   @if(!$eobi->isEmpty())
             <tr>
           <?php $deduction = $deduction + (!empty($eobi) ? $eobi[0]->amount : 0); ?>
            <td>EOBI</td>
            <td >{{number_format($eobi[0]->amount,2)}}</td>
          </tr>
		   <input type="hidden" id="SETEobi" value="{{$eobi[0]->amount}}">
		   @endif
		   @if($employee[0]->security_deposit == 1)
		  <tr>
            <?php $deduction = $deduction + $security_deposit ?>
            <td>Security Deposit</td>
            <td>{{number_format($security_deposit,2)}}</td>
          </tr>	
		  <input type="hidden" id="SetSecurityDeposit" value="{{$security_deposit}}">
		  @endif

             <tr>
           <?php $deduction = $deduction + $lateamount; ?>
            <td>Late</td>
            <td >{{number_format($lateamount,2)}}</td>
          </tr>
           <input type="hidden" id="SETlateamount" value="{{$lateamount}}">


             <tr>
           <?php $deduction = $deduction + $earlyamount; ?>
            <td>Early</td>
            <td >{{number_format($earlyamount,2)}}</td>
          </tr>
           <input type="hidden" id="SETearlyamount" value="{{$earlyamount}}">
		  <tr>
            <td style="border-top:2px solid"><b>Total Deduction</b></td>
            <td style="border-top:2px solid"><b>{{number_format($deduction,2)}}</b></td>
          </tr>
          
         </tbody>
       </table>
       <input type="hidden" name="deduct" id="deduct" value="{{$deduction}}">

       </div>
