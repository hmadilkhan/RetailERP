$(document).ready(function() {

$(".select2").select2();
var mode = "insert";
    var calc;
    $('#expenseform').submit(function(e){
     e.preventDefault();
 if(mode == "insert"){
    
    $.ajax({
          url:'{{ route("expense.store") }}',
          data:$('#expenseform').serialize(),
          type:"POST",
          success:function(result){
            if(result == "1"){
              window.location = "{{ route('expense.index') }}";

            }
          }
        });
   }else{
      
    $.ajax({
          url:'{{ url("/updateExpense") }}',
          data:$('#expenseform').serialize(),
          type:"PUT",
          success:function(result){
            console.log(result)
            if(result == "1"){
              window.location = "{{ route('expense.index') }}";
               $("#title-hcard").html('Create Expense');
            }
          }
        });
   }

   });

    function updateCall(id){
       $("#title-hcard").html('Update Expense');
      mode = "update";
  
      $.ajax({
        url:'{{ url("/getData") }}',
        data:{_token : "{{csrf_token()}}",id:id},
        type:"POST",
        success:function(result){
      
          getCategories(result[0].exp_cat_id);
          $('#amount').val(result[0].amount);
          $('#details').val(result[0].expense_details);
          $('#net_amount').val(result[0].net_amount);
          $('#hidd_id').val(result[0].exp_id);

        }

      });
    }

    function getCategories(id){
      alert(id)
      $.ajax({
        url:'{{ url("/category") }}',
        type:"GET",
        success:function(result){
          $('#exp_cat').html('');
          $.each(result, function (i, value) {
            if(value.exp_cat_id == id){
              $('#exp_cat').append($('<option selected>').text(value.expense_category).attr('value', value.exp_cat_id));  
            }else{
              $('#exp_cat').append($('<option>').text(value.expense_category).attr('value', value.exp_cat_id)); 
            }
        
      });
        }

      });
    }

    function getTax(id){
      $.ajax({
        url:'{{ url("/tax") }}',
        type:"GET",
        success:function(result){
          alert
          $('#tax').html('');
          $.each(result, function (i, value) {
            if(value.id == id){
              $('#tax').append($('<option selected>').text(value.name+" "+value.value+"%").attr('value', value.id));  
            }else{
              $('#tax').append($('<option>').text(value.name+" "+value.value+"%").attr('value', value.id)); 
            }
        
      });
        }

      });
    }

    function selectChange(){
      $('#exp_cat').find('option:selected').val("2");
    }

   function taxVal(){
    $("#exp_cat").val(2).change();;
        var str = $("#tax option:selected").text(); 
            var matches = str.match(/(\d+)/); 
          calc = "";
        calc = (($('#amount').val() / 100) * matches[0]);
        var sum = parseFloat($('#amount').val()) + parseFloat(calc );
        $("#hidd_amt").val(calc);
        $("#net_amount").val(sum);

   }

  
    $('#amount').on('change',function(){
      
      
      var amount = parseFloat($('#amount').val());
      $("#net_amount").val(amount);
    });

    
   $('#expensetb').DataTable({
        displayLength: 50,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Expense',
          lengthMenu: '<span></span> _MENU_'
   
        },
        
 
    });



});

