$( document ).on( 'click', 'ul.dropdown-menu a[generator="adianti"]', function() {
    $(this).parents(".dropdown.show").removeClass("show");
    $(this).parents(".dropdown-menu.show").removeClass("show");
});

function __adianti_block_ui(wait_message)
{
    if (typeof $.blockUI == 'function')
    {
        if (typeof Adianti.blockUIConter == 'undefined')
        {
            Adianti.blockUIConter = 0;
        }

        Adianti.blockUIConter = Adianti.blockUIConter + 1;

        if (typeof wait_message == 'undefined')
        {
            wait_message = 'Processando';
        }
        
        $.blockUI({ 
           message: "<div class='loader-content'><div class='loader-live'><div class='cssload-container'><div class='cssload-speeding-wheel'></div></div></div><div class='loader-text'>" + wait_message + "</div></div>",
           fadeIn: 0,
           fadeOut: 0,
        });
        
        $('.blockUI.blockMsg').mycenter();
    }
}