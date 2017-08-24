/*jQuery(document).ready(function($){
   alert($("#result").val());
   console.log($("div#result").attr('id'));
   //alert('Hello World');
    $( 'a[href="#sign_up"]' ).on( "click", function() {
        console.log( $("#result").attr('id'));
      });
});

 jQuery(document).ready(function($){
                    
                    $('#result').on('change', function() {
                        alert('hello world');

                  /* $('body').on('change', '#result', function(){
                        alert('changed');
                    });
                    /*$( 'a[href="#sign_up"]' ).on( "click", function() {
                       alert('<?php echo $res; ?>');
                       //var berni = '';
                       //alert(berni);
                     

                   
                    
                });*/
jQuery(document).ready(function($){
    var $listItems = $('.pagination.pagebreaks .subpages a .number');
    $listItems.click(function(){
      $listItems.removeClass('active');
      $(this).addClass('active');  
    });
});