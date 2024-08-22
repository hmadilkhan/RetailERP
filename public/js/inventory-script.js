$(document).ready(function() {

   $('#inventorytb').DataTable( {
        scrollX: true,
        displayLength: 50,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Inventory',
          lengthMenu: '<span></span> _MENU_'
   
        },
        
 
    } );



});