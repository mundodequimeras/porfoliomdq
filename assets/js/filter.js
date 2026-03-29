jQuery(document).ready(function($) {
    'use strict';

    if (typeof mdqData === 'undefined') return;

    const $grid = $('#mdq-portfolio-grid');
    const $loadMoreBtn = $('#mdq-load-more');
    let currentCategory = 'all';

    /**
     * AJAX Filter function
     */
    function filterPortfolio(category, append = false) {
        const page = append ? parseInt($loadMoreBtn.attr('data-page')) + 1 : 1;
        const limit = $loadMoreBtn.attr('data-limit') || 6;

        if (!append) {
            $grid.addClass('loading').css('opacity', '0.5');
        } else {
            $loadMoreBtn.addClass('loading').prop('disabled', true).text('Cargando...');
        }

        $.ajax({
            url: mdqData.ajax_url,
            type: 'POST',
            data: {
                action: 'mdq_filter_portfolio',
                nonce: mdqData.nonce,
                category: category,
                page: page,
                limit: limit
            },
            success: function(response) {
                if (response.success) {
                    if (append) {
                        $grid.append(response.data.html);
                        $loadMoreBtn.attr('data-page', response.data.current_page);
                    } else {
                        $grid.html(response.data.html).css('opacity', '1').removeClass('loading');
                    }

                    // Update Load More visibility
                    if (response.data.current_page >= response.data.max_pages) {
                        $loadMoreBtn.hide();
                    } else {
                        $loadMoreBtn.show().removeClass('loading').prop('disabled', false).html('Cargar Más Proyectos <i class="fas fa-plus"></i>');
                    }
                }
            },
            error: function() {
                $grid.css('opacity', '1').removeClass('loading');
                $loadMoreBtn.removeClass('loading').prop('disabled', false).text('Error al cargar');
            }
        });
    }

    /**
     * Category Button Clicks
     */
    $('.mdq-filter-btn').on('click', function() {
        if ($(this).hasClass('active') && !$(this).hasClass('all')) return;

        $('.mdq-filter-btn').removeClass('active');
        $(this).addClass('active');

        currentCategory = $(this).attr('data-filter');
        filterPortfolio(currentCategory);
    });

    /**
     * Load More Button Click
     */
    $loadMoreBtn.on('click', function(e) {
        e.preventDefault();
        filterPortfolio(currentCategory, true);
    });

    console.log('Porfolio MDQ: AJAX filters and Load More initialized.');
});
