jQuery(document).ready(function($) {
    // Set the first tab as active by default and trigger loading
    var $tabs = $('.portfolio-category-tabs li');
    $tabs.first().addClass('active');
    var firstCategoryId = $tabs.first().data('category-id');

    loadPortfolioItems(firstCategoryId);

    // On click, toggle the active class and load filtered items
    $tabs.on('click', function() {
        $tabs.removeClass('active');
        $(this).addClass('active');

        var categoryId = $(this).data('category-id');
        loadPortfolioItems(categoryId);
    });

    function loadPortfolioItems(categoryId) {
        $.ajax({
            url: portfolio_ajax_obj.ajax_url,
            method: 'POST',
            data: {
                action: 'filter_portfolio',
                nonce: portfolio_ajax_obj.nonce,
                category_id: categoryId,
                posts_per_page: 6, // or dynamically get this value from widget settings
            },
            beforeSend: function() {
                $('#portfolio-grid').html('<p>Loading portfolio items...</p>');
            },
            success: function(response) {
                if (response.success) {
                    $('#portfolio-grid').html(response.data);
                } else {
                    $('#portfolio-grid').html('<p>No portfolio items found.</p>');
                }
            },
            error: function() {
                $('#portfolio-grid').html('<p>Failed to load portfolio items.</p>');
            }
        });
    }
});
