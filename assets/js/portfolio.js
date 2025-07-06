jQuery(document).ready(function($) {
    // Set the first tab as active by default
    $('.portfolio-category-tabs li:first').addClass('active');

    // On click, toggle the active class
    $('.portfolio-category-tabs li').on('click', function() {
        // Remove 'active' class from all tabs
        $('.portfolio-category-tabs li').removeClass('active');
        // Add 'active' class to the clicked tab
        $(this).addClass('active');
    });
});
