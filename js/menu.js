(function ($) {
    $.fn.fixedMenu = function () {
        return this.each(function () {
            var menu = $(this);
            menu.find('ul li > a').bind('click', function () {

                $(this).parent().parent().parent().parent().find('.active').removeClass('active');
                if ($(this).parent().hasClass('active')) {
                    $(this).parent().removeClass('active');
                } else {
                    $(this).parent().parent().find('.active').removeClass('active');
                    $(this).parent().addClass('active');
                }
            })

        });
    }
})(jQuery);

$('document').ready(function () {
    $('.menu').fixedMenu();
    var mouse_is_inside = false;
    $('#menu').hover(function () {
        mouse_is_inside = true;
    }, function () {
        mouse_is_inside = false;
    });

    $("body").bind('click', function () {
        if (!mouse_is_inside)
            $('#menu').find('.active').removeClass('active');
    });
});



