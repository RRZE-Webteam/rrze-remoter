jQuery(document).ready(function($) {
    var $listItems = $('.pagination.pagebreaks .subpages a .number');
    $listItems.click(function() {
        $listItems.removeClass('active');
        $(this).addClass('active');
    });
    $('a.lightbox').fancybox({
        helpers: {
            title: {
                type: 'outside'
            }
        }
    });
});
