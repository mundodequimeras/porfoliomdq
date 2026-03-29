jQuery(document).ready(function($) {
    'use strict';

    var $iconInput = $('#mdq_term_icon');
    var $previewIcon = $('#mdq-preview-icon');

    /**
     * Update the preview icon as the user types
     */
    function updatePreview() {
        var val = $iconInput.val().trim();
        if (val) {
            $previewIcon.attr('class', val);
            $previewIcon.closest('.mdq-icon-preview').show();
        } else {
            $previewIcon.attr('class', 'fas fa-question');
        }
    }

    $iconInput.on('input change', updatePreview);

    /**
     * Handle clicking a quick-select icon
     */
    $(document).on('click', '.mdq-quick-icon', function() {
        var iconClass = $(this).data('icon');
        $iconInput.val(iconClass).trigger('change');
        
        // Visual feedback
        $(this).css('background', '#2271b1').css('color', '#fff');
        setTimeout(() => {
            $(this).css('background', '').css('color', '');
        }, 200);
    });

    /**
     * Search/Filter Icons in the grid
     */
    $(document).on('keyup', '.mdq-icon-search', function() {
        var term = $(this).val().toLowerCase();
        $('.mdq-quick-icon').each(function() {
            var iconData = $(this).data('icon').toLowerCase();
            var iconTitle = ($(this).attr('title') || '').toLowerCase();
            
            if (iconData.indexOf(term) > -1 || iconTitle.indexOf(term) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Run on load
    updatePreview();
});
