window.BuilderTemplate = ( function() {

    var resizeTimeout;

    const resizeBuilderMenu = function() {

        clearTimeout(resizeTimeout);

        resizeTimeout = setTimeout( function() {

            if($(document).width() <= 992) {
                if ($('#top-menu.container-menu').length > 0) {
                    $('#navbar-builder-top-submenu-collapse').insertAfter('#navbar-builder-top-menu-collapse').addClass('builder-top-module-menu-mobile');
                }
                $('#navbar-builder-top-menu-collapse').removeClass("show-arrows");
                return;
            } else {
                $('.header-track').css('display', 'flex');
                $('.builder-top-module-menu-mobile').insertBefore($('#adianti_content')).removeClass('builder-top-module-menu-mobile');
            }

            var topMenu = $('#navbar-builder-top-menu-collapse>#top-menu,#navbar-builder-top-menu-collapse>#top-submenu');

            if(typeof topMenu[0] != 'undefined' && topMenu[0].scrollWidth > topMenu[0].clientWidth ) 
            {
                $('#navbar-builder-top-menu-collapse').addClass("show-arrows");
            } 
            else 
            {
                $('#navbar-builder-top-menu-collapse').removeClass("show-arrows");
            }

            var topSubMenu = $('#navbar-builder-top-submenu-collapse #top-submenu');

            if(typeof topSubMenu[0] != 'undefined' && topSubMenu[0].scrollWidth > topSubMenu[0].clientWidth ) 
            {
                $('#navbar-builder-top-submenu-collapse').addClass("show-arrows");
            } 
            else 
            {
                $('#navbar-builder-top-submenu-collapse').removeClass("show-arrows");
            }
        }, 500);
    }
    
    const clickBuilderMenuOption = function(element) 
    {
        let left = $(element).offset().left;
        $(element).closest('li').find('ul:first').css('left', left  + 'px');
    }
    
    const initTopMenu = function()
    {
        $(document).ready(function () {
            setTimeout(BuilderTemplate.resizeBuilderMenu, 500);

            $(window).on('resize', function(){
                BuilderTemplate.resizeBuilderMenu()
            });

            $('.navbar-custom-menu').remove();

            $('#navbar-builder-top-menu-collapse>.container-submenu>ul>li').on('mouseenter', function(){
                if ($('body').width() > 992) {
                    $(this).find('ul:first').css('left', $(this).offset().left + 'px');
                }
            });
            
            $('.container-menu a[top-menu-target]').on('click', function(){
                const target = $(this).attr('top-menu-target');
                const checked = $(this).hasClass('checked');

                BuilderTemplate.resizeBuilderMenu();

                if ($(`.container-submenu [top-module-menu="${target}"]`).length > 0 && ! checked) {
                    $('#top-submenu.container-submenu').show();

                    $(`.container-submenu [top-module-menu]:not([top-module-menu="${target}"])`).removeClass('open');
                    $(`.container-submenu [top-module-menu="${target}"]`).addClass('open');
            
                    $(`.container-menu a[top-menu-target]:not([top-menu-target="${target}"])`).removeClass('checked');
                    $(`.container-menu a[top-menu-target="${target}"]`).addClass('checked'); 
                } else {
                    $('#top-submenu.container-submenu').hide();
                }

                if (checked) {
                    $(this).removeClass('checked')
                }
            });

            $('#navbar-builder-top-submenu-collapse .container-submenu>ul>li').on('click', function(evt) {
                if (this === evt.target.parentNode) {
                    $('#navbar-builder-top-submenu-collapse .container-submenu ul li').removeClass('checked')
                } else {
                    $('#navbar-builder-top-submenu-collapse .container-submenu>ul>li').removeClass('checked')
                    $(this).find("ul li").each(function(i,e){
                        if (e != evt.target.parentNode) {
                            $(e).removeClass('checked');
                        }
                    });
                }
                
                $(this).addClass('checked');
            });

            $('#navbar-builder-top-menu-collapse .container-submenu ul li').on('click', function(evt) {
                if (this === evt.target.parentNode) {
                    $('#navbar-builder-top-menu-collapse .container-submenu ul li').removeClass('checked')
                    const nativeElement = this;
                    
                    if ($(this).attr('top-module-menu') ) {
                        $(this).closest('ul').find('li').each(function(i, e){
                            if(nativeElement===e) {
                                $(e).addClass('checked');
                            } else {
                                if (this !== evt.target.parentNode) {
                                    $(e).removeClass('checked');
                                }
                            }
                        });
                    } else {
                        $(this).addClass('checked');
                    }
    
                    if (this === evt.target.parentNode) {
                        $(this).addClass('checked');
                    }
                } else {
                    $(this).addClass('checked');
                }
            });

            $('#navbar-builder-top-submenu-collapse li').on('click', function() {
                $(this).addClass('checked');
            });

            $('#navbar-builder-top-menu-collapse .arrow-menus-scroll:last').click(function(e){
                const menu = $('#navbar-builder-top-menu-collapse>#top-menu')[0] ?? $('.container-submenu#top-submenu')[0];
                menu.scrollLeft += 75; 
            });
             
            $('#navbar-builder-top-menu-collapse .arrow-menus-scroll:first').click(function(e){
                const menu = $('#navbar-builder-top-menu-collapse>#top-menu')[0] ?? $('.container-submenu#top-submenu')[0];
                menu.scrollLeft -= 75
            });

            $('#navbar-builder-top-submenu-collapse .arrow-menus-scroll:last').click(function(e){
                $('#navbar-builder-top-submenu-collapse>#top-submenu')[0].scrollLeft += 75; 
            });
             
            $('#navbar-builder-top-submenu-collapse .arrow-menus-scroll:first').click(function(e){
                $('#navbar-builder-top-submenu-collapse>#top-submenu')[0].scrollLeft -= 75
            });
        });
    }
    
    const updateMessagesMenu = function() {
        $.get('engine.php?class=MessageList&theme=theme-builder', function(data) {
            $('#envelope_messages').html(data);
        });
    }
    
    const updateNotificationsMenu = function() {
        $.get('engine.php?class=NotificationList&theme=theme-builder', function(data) {
            $('#envelope_notifications').html(data);
        });
    }

    const initLeftMenu = function() {
        $(document).ready(function(){
            $('.toggle-menu').on('click', function(){
                $('.master-menu-content').toggleClass('open');
                $('.header-track').hide();
                if ($('.master-menu-content').hasClass('open')) {
                    $(this).find('i').removeClass('fa-bars');
                    $(this).find('i').addClass('fa-times');
                    $(".menu-elastic").slimScroll({height: "auto", position: "left", "distance": '0px'});
                } else {
                    $(this).find('i').addClass('fa-bars');
                    $(this).find('i').removeClass('fa-times');
                }
            });

            $('.toggle-top-menu').on('click', function(){
                if ( $('.header-track').is(':visible') ) {
                    $('.header-track').hide();
                } else {
                    $('.header-track').css('display', 'flex');
                }
            });

            $('.master-menu-content .container-submenu>ul>li').on('click', function(evt) {
                if (this === evt.target.parentNode) {
                    $('.master-menu-content .container-submenu ul li').removeClass('checked')
                }
            });

            $('.master-menu-content .container-submenu ul li').on('click', function() {
                $(this).addClass('checked');
            });

            if ( $('.container-menu .menu-elastic a[menu-target]').length == 0) {
                $('.container-menu:not(#top-menu)').hide();
            }

            $('.container-menu a[menu-target]').on('click', function(){
                const target = $(this).attr('menu-target');

                if ($(`.container-submenu [module-menu=${target}]`).length > 0) {
                    $(`.container-submenu [module-menu]:not([module-menu=${target}])`).removeClass('open');
                    $(`.container-submenu [module-menu=${target}]`).addClass('open');
            
                    $(`.container-menu a[menu-target]:not([menu-target=${target}])`).removeClass('checked');
                    $(`.container-menu a[menu-target=${target}]`).addClass('checked'); 
                }
            });
        
            $('.container-submenu a.sub').on('click', function(){
                const action = $(this);

                if (action.hasClass('open')) {
                    action.removeClass('open');
                    action.parent().removeClass('checked');
                    action.next().slideUp(300);
                } else {
                    action.addClass('open');
                    action.next().slideDown(300);
                }
            });
        
            $('.container-submenu a').on('click', function(e){
                if (! $(this).hasClass('sub')) {
                    if ($('body').width() < 992) {
                        $('.master-menu-content').removeClass('open');
                        $('.header-track').hide();
    
                        $('.toggle-menu i').addClass('fa-bars');
                        $('.toggle-menu i').removeClass('fa-times');
                    }
                }

                $('.master-menu-content .container-submenu li').removeClass('checked');
                $(this).closest('li').addClass('checked');
            });
        });

        $('.sidebar-mini .master-menu-content,.header-logo').on('mouseenter', function() {
            if ($('body').width() > 992) {
                $('.sidebar-mini .master-menu-content,.header-logo').addClass('open');
            }
        });

        $('.sidebar-mini .master-menu-content,.header-logo').on('mouseleave', function() {
            if ($('body').width() > 992) {
                $('.sidebar-mini .master-menu-content,.header-logo').removeClass('open');
            }
        });

        $('.fixed-sidebar-mini-menu').on('click', function() {
            if ($('body').width() > 992) {
                BuilderTemplate.resizeBuilderMenu();
                if ($('.sidebar-mini').hasClass('fixed')) {
                    $('.sidebar-mini').removeClass('fixed');

                    $('.fixed-sidebar-mini-menu i').removeClass('fa-bars');
                    $('.fixed-sidebar-mini-menu i').addClass('fa-thumbtack');
                } else {
                    $('.sidebar-mini').addClass('fixed');

                    $('.fixed-sidebar-mini-menu i').removeClass('fa-thumbtack');
                    $('.fixed-sidebar-mini-menu i').addClass('fa-bars');
                }
            }
        });

        $(".menu-elastic").slimScroll({height: "auto", position: "left", "distance": '0px'});

        $(window).on('resize', function(){
            $(".menu-elastic").slimScroll({height: "auto", position: "left", "distance": '0px'});
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
        
        BuilderTemplate.initLeftMenu();
        BuilderTemplate.processTheme(options);
        BuilderTemplate.processFastDrop();

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

        if(!options.use_tabs && ! options.use_mdi_windows)
        {
            $('.adianti_tabs_container').hide();
            $('.adianti_tabs_container').css('height', '0px');
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

    const changeStyle = function(button)
    {
        var theme = $(button).attr('theme');
        window.localStorage.setItem('style_template', theme);
        
        if(theme)
        {
            BuilderTemplate.defineTheme(theme);
            __adianti_load_page('engine.php?class=BuilderConfigForm&method=setTheme&static=1&theme='+theme);
        }
    }

    const defineTheme = function(theme)
    {
        let time = new Date().getTime();
        
        $('.change-theme').removeClass('checked');
        $('[theme-style]').attr('href', 'app/templates/theme-builder/themes/' + theme + '.css?time='+time);
        $('[theme=' + theme + ']').addClass('checked');
    }

    const processTheme = function(options) {
        //Drop do menu
       $(".change-theme").click(function() 
       {
           BuilderTemplate.changeStyle(this);
       });

       if (options.theme)
       {
            BuilderTemplate.defineTheme(options.theme);
       }

       if (! options.mobile)
       {
           if ( $('.builder-list-themes .fast-drop-open a').length <= 1)
           {
               $('.builder-list-themes').remove();
           }
       }
   }

    const processFastDrop = function() {
        setTimeout(function(){
            $('.fast-drop').off('mouseenter');
            $('.fast-drop').on('mouseenter', function(e) {
                const positionTop = $(this).offset().top;
                const positionLeft = $(this).offset().left;
                
                const vertivalMiddle = $(window).height() / 2;
                const horizontalMiddle = $(window).width() / 2;
        
                if ( positionTop > vertivalMiddle ) {
                    $(this).addClass('force-top');
                    $(this).removeClass('force-bottom');
                } else {
                    $(this).removeClass('force-top');
                    $(this).addClass('force-bottom');
                }
        
                if ( positionLeft > horizontalMiddle ) {
                    $(this).removeClass('force-left');
                    $(this).addClass('force-right');
                } else {
                    $(this).addClass('force-left');
                    $(this).removeClass('force-right');
                }
            });
        }, 500);
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
        initLeftMenu: initLeftMenu,
        processTheme: processTheme,
        changeStyle: changeStyle,
        defineTheme: defineTheme,
        processFastDrop: processFastDrop,
        enableSweetAlert: enableSweetAlert,
    };

})();