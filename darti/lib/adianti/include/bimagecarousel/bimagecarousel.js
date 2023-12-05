function bimagecarousel_start(idmain, options, idthumbnails, optionsThumb) {
    setTimeout(function() {
        var thumbnails;
        var main = new Splide('#' + idmain, options);
          
        if (idthumbnails) {
            thumbnails = new Splide( '#' + idthumbnails, optionsThumb);
            main.sync( thumbnails );
        }

        main.mount();

        if (idthumbnails) thumbnails.mount();
    }, 10);
}