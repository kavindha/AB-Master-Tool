(function($) {   
    $(document).ready(function() { 
        $('.show-details').click(function(){                     
            if($(this).children('.section').css('display') == "block"){
                $(this).children('.section').css('display', 'none');                
            }else if($(this).children('.section').css('display') == "none"){                
                $(".payment-details").css('visibility', 'hidden');
                $(this).children('.section').css('display', 'block');
                            }
        });
    });
})(jQuery);