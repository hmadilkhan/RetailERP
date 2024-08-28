
  <?php
  $gross = 0;
  ?>
  <div class="col-md-6 col-lg-6">
   <table id="tblsalary" class="table  nowrap" style="border:2px solid" width="100%" cellspacing="0">
         <thead>
            <tr >
              <th style="border-bottom:2px solid">Gross Head </th>
               <th style="border-bottom:2px solid">Amount</th>
            </tr>
         </thead>
         <tbody>
          @if($allowances)
          @foreach($allowances as $value)
          <tr>
            <?php $gross = $gross + $value->amount ?>
            <td>{{$value->allowance_name}}</td>
            <td>{{number_format($value->amount,2)}}</td>
          </tr>
          <input type="hidden" id="SETallowanceSUM" value="{{$gross}}">
          @endforeach
          @endif
          
		   <tr>
            <?php $gross = $gross + $salarydetails[0]->basic_pay ?>
            <td>Basic Salary</td>
            <td>{{number_format($salarydetails[0]->basic_pay,2)}}</td>
          </tr>
		  @if($employee[0]->pf_enable == 1)
		  <tr>
            <?php $gross = $gross + $salarydetails[0]->pf_fund ?>
            <td>PF Fund</td>
            <td>{{number_format($salarydetails[0]->pf_fund,2)}}</td>
          </tr>	
		  @endif
		  <tr>
            <?php $gross = $gross + $salarydetails[0]->allowance ?>
            <td>Allowance</td>
            <td>{{number_format($salarydetails[0]->allowance,2)}}</td>
          </tr>
		   @if($bonus)		  
          <tr>
            <?php $gross = $gross + $bonus[0]->bonus_amount ?>
            <td>Bonus</td>
            <td>{{number_format($bonus[0]->bonus_amount,2)}}</td>
          </tr>
		  <tr>
            <?php $gross = $gross + $otamount ?>
            <td>OT (per min)</td>
            <td>{{number_format($otamount,2)}}</td>
          </tr>
		  @endif
           <input type="hidden"  id="setbasicsalary" value="{{$salarydetails[0]->basic_pay}}">
           <input type="hidden"  id="setpffund" value="{{$salarydetails[0]->pf_fund}}">
           <input type="hidden"  id="setallowance" value="{{$salarydetails[0]->allowance}}">
           <input type="hidden"  id="setbonusamt" value="{{$bonus[0]->bonus_amount}}">
           <input type="hidden" name="bonusid" id="bonusid" value="{{$bonus[0]->bonus_id}}">
           <input type="hidden" name="otamount" id="otamount" value="{{$otamount}}">
          

           @if($overtime)
            <tr>
            <?php $gross = $gross + $overtime[0]->otamount ?>
            <td>Over Time</td>
            <td >{{number_format($overtime[0]->otamount,2)}}</td>
          </tr>
		  <tr>
            <td style="border-top:2px solid"><b>Aggregate Salary</b></td>
            <td style="border-top:2px solid"><b>{{number_format($gross,2)}}</b></td>
          </tr>
           <input type="hidden" id="setotamt" value="{{$overtime[0]->otamount}}">
          <input type="hidden" name="overtimeID" id="overtimeID" value="{{$overtime[0]->id}}">
          @endif
         </tbody>
       </table>
       <input type="hidden"  id="setgrossalary" value="{{$gross}}">
       <input type="hidden" name="grossamt" id="grossamt" value="{{$gross}}">  
       <input type="hidden" name="basicamt" id="basicamt" value="{{$salarydetails[0]->basic_pay}}">  
</div>
