window.BContainer = ( function() {

    const toggle = function(element)
    {
        var fieldset = $(element).closest('.bContainer-fieldset');

        if(fieldset.hasClass('bContainer-accordion-hide'))
        {
            fieldset.removeClass('bContainer-accordion-hide');
            fieldset.addClass('bContainer-accordion-show');

            fieldset.find('.bContainer-title > .bContainer-accordion-icon-show').hide();
            fieldset.find('.bContainer-title > .bContainer-accordion-icon-hide').show();
        }
        else
        {
            fieldset.removeClass('bContainer-accordion-show');
            fieldset.addClass('bContainer-accordion-hide');

            fieldset.find('.bContainer-title > .bContainer-accordion-icon-show').show();
            fieldset.find('.bContainer-title > .bContainer-accordion-icon-hide').hide();
        }
    }

    return {
        toggle: toggle
    };

})();