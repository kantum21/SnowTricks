$(function () {
    $('.soft-link').click(function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        var start = link.indexOf('#');
        var target = link.slice(start);
        $('html, body')
            .stop()
            .animate({scrollTop: $(target).offset().top}, 1000 );
    });
});