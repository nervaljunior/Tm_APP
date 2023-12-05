window.BuilderTemplate = ( function() {

    var resizeTimeout;

    const resizeBuilderMenu = function() {

        clearTimeout(resizeTimeout);

        resizeTimeout = setTimeout( function() {

            var topMenu = $('.builder-menu>ul');

            if($(document).width() <= 767) 
            {
                topMenu.css('width', '100%');
                $('.builder-menu').removeClass("show-arrows");
                return;
            }

            var sidebar_toggle_btn = 0;

            if($('[data-widget="pushmenu"]').length > 0)
            {
                sidebar_toggle_btn = $('[data-widget="pushmenu"]').innerWidth();
            }

            var size = $(document).width() - ( $('.main-sidebar>.brand-link').innerWidth() + $('.navbar-nav.ml-auto').innerWidth() + 65 + sidebar_toggle_btn);

            topMenu.css('width', size + 'px');
                
            if(typeof topMenu[0] != 'undefined' && topMenu[0].scrollWidth > topMenu[0].clientWidth ) 
            {
                $('.builder-menu').addClass("show-arrows");
                $('.builder-menu .arrow-menus-scroll:last').css('margin-left', (size + 28) + 'px' );  
            } 
            else 
            {
                $('.builder-menu').removeClass("show-arrows");
            }
        }, 500 );
    }
    
    const clickBuilderMenuOption = function(element) 
    {
        let left = $(element).offset().left - 17;
        $(element).closest('li').find('ul:first').css('left', left  + 'px');
    }
    
    const initTopMenu = function()
    {
        $(document).ready(function () {
            BuilderTemplate.resizeBuilderMenu();
            $(window).on('resize', function(){
                BuilderTemplate.resizeBuilderMenu()
            });
            
            $('[data-widget="pushmenu"]').on('click', function(){
                BuilderTemplate.resizeBuilderMenu();
            });
            
            var id = '';
            $('.builder-menu>ul>li').each(function(index, item){
                id = 'builder' + Math.floor(Math.random() * (100000 - 1)) + 1;
                
                $(item).attr('id', id);
            });
            
            $('.builder-menu>ul>li').each(function(index, item) {
                var click = $("#"+ $(item).attr('id') ).find('a:first').attr('onclick');
                if(typeof click == 'undefined')
                {
                    click = '';
                }
                $("#"+ $(item).attr('id') ).find('a:first').attr('onclick', "BuilderTemplate.clickBuilderMenuOption(this);"+click);
            });

            $('.builder-menu .dropdown-menu,dropdown-submenu a.dropdown-toggle').click(function (event) {
                if($(document).width() <= 767) 
                {
                    event.stopPropagation();
                }
            });

            $('.builder-menu a').click(function (event) {
                if($(document).width() <= 767) 
                {
                    if ( $(this).attr('class') == undefined ) {
                        $('.builder-menu').removeClass('show');
                    }
                }
            });

            $('.builder-menu .arrow-menus-scroll:last').click(function(e){
                $('.builder-menu>ul')[0].scrollLeft += 75; 
            });
             
            $('.builder-menu .arrow-menus-scroll:first').click(function(e){
                $('.builder-menu>ul')[0].scrollLeft -= 75
            });
        });
    }
    
    const updateMessagesMenu = function() {
        $.get('engine.php?class=MessageList&theme=theme3-adminlte3', function(data) {
            $('#envelope_messages').html(data);
        });
    }
    
    const updateNotificationsMenu = function() {
        $.get('engine.php?class=NotificationList&theme=theme3-adminlte3', function(data) {
            $('#envelope_notifications').html(data);
        });
    }

    const init = function(options)
    {
        if(options.top_menu)
        {
            BuilderTemplate.initTopMenu();
        }
        else if(options.public_layout == false)
        {
            BuilderTemplate.loadSearchBar();
        }

        if(options.public_layout == false)
        {
            BuilderTemplate.updateMessagesMenu();
            BuilderTemplate.updateNotificationsMenu();
        
            if(options.verify_messages_menu)
            {
                if(options.verify_messages_menu < 5000)
                {
                    options.verify_messages_menu = 5000;
                }
                BuilderTemplate.intervalUpdateMessagesMenu = setInterval( BuilderTemplate.updateMessagesMenu, options.verify_messages_menu);
            }
            
            if(options.verify_notifications_menu)
            {
                if(options.verify_notifications_menu < 5000)
                {
                    options.verify_notifications_menu = 5000;
                }
                BuilderTemplate.intervalUpdateNotificationsMenu = setInterval( BuilderTemplate.updateNotificationsMenu, options.verify_notifications_menu);
            }
        }

        if(typeof options.dialog_box_type != 'undefined' && options.dialog_box_type == 'sweetalert')
        {
            BuilderTemplate.enableSweetAlert();
        }

        __adianti_set_name(options.application_name);
        __adianti_init_tabs(options.use_tabs, options.store_tabs, options.use_mdi_windows);
        __adianti_set_language(options.language);
        __adianti_set_debug(options.debug);
    }

    const loadSearchBar = function()
    {
        $.get('engine.php?class=SearchBox', function(data)
        {
            $('.navbar-custom-menu').append(data).show();
            var search_box = $('.navbar-nav').nextAll('div');
            search_box.css('padding-top', '10px');
            search_box.css('padding-left', '25px');
            search_box.css('display', 'table');
            search_box.css('float', 'right');
            search_box.attr('id', 'search-box');
        });
    }

    const enableSweetAlert = function()
    {
        __adianti_dialog = function ( options )
        {
            setTimeout( function() {
                swal({
                html: true,
                title: options.title,
                text: options.message,
                type: options.type,
                allowEscapeKey: (typeof options.callback == 'undefined'),
                allowOutsideClick: (typeof options.callback == 'undefined')
                },
                function(){
                    if (typeof options.callback != 'undefined') {
                        options.callback();
                    }
                });
            }, 100);
        }

        __adianti_question = function (title, message, callback_yes, callback_no, label_yes, label_no)
        {
            setTimeout( function() {
                swal({
                html: true,
                title: title,
                text: message,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: label_yes,
                cancelButtonText: label_no
                },
                function(isConfirm){
                if (isConfirm) {
                    if (typeof callback_yes != 'undefined') {
                        callback_yes();
                    }
                } else {
                    if (typeof callback_no != 'undefined') {
                        callback_no();
                    }
                }
                });
            }, 100);
        }
    }

    return {
        updateMessagesMenu: updateMessagesMenu,
        updateNotificationsMenu: updateNotificationsMenu,
        loadSearchBar: loadSearchBar,
        init: init,
        resizeBuilderMenu: resizeBuilderMenu,
        clickBuilderMenuOption: clickBuilderMenuOption,
        initTopMenu: initTopMenu,
        enableSweetAlert: enableSweetAlert
    };

})();