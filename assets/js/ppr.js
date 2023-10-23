jQuery(document).ready(function($) {
    $('.ppr-reaction').on('click', function() {
        var clicked_reaction_id = $(this).attr('id');
        var user_id = $(this).data('user-id');
        var post_type = $(this).data('post-type');
        var post_or_page_id = $(this).data('post-or-page-id');

        
        
        if (clicked_reaction_id == 'reaction-meh') {
            // Handle 'meh' reaction click
            $(this).removeClass('fa-sm');
            $(this).addClass('fa-xl');
            $('#reaction-smile').removeClass('fa-xl');
            $('#reaction-smile').addClass('fa-sm');
            $('#reaction-sad').removeClass('fa-xl');
            $('#reaction-sad').addClass('fa-sm');
        } else if (clicked_reaction_id == 'reaction-smile') {
            // Handle 'smile' reaction click
            $(this).removeClass('fa-sm');
            $(this).addClass('fa-xl');
            $('#reaction-meh').removeClass('fa-xl');
            $('#reaction-meh').addClass('fa-sm');
            $('#reaction-sad').removeClass('fa-xl');
            $('#reaction-sad').addClass('fa-sm');
        } else if (clicked_reaction_id == 'reaction-sad') {
            // Handle 'sad' reaction click
            $(this).removeClass('fa-sm');
            $(this).addClass('fa-xl');
            $('#reaction-meh').removeClass('fa-xl');
            $('#reaction-meh').addClass('fa-sm');
            $('#reaction-smile').removeClass('fa-xl');
            $('#reaction-smile').addClass('fa-sm');
        }

        $.ajax({
            type: 'POST',
            url: pprAjax.ajaxurl,
            data: {
                action: 'ppr_save_reaction_data',
                clicked_reaction_id: clicked_reaction_id,
                user_id: user_id,
                post_type: post_type,
                post_or_page_id: post_or_page_id
            },
            success: function(response) {
                console.log(response);
                $.ajax({
                    type: 'POST',
                    url: pprAjax.ajaxurl, // Replace with the URL for your second AJAX request
                    data: {
                        action: 'ppr_save_reaction_count_data', // Define your second action
                        post_type: post_type,
                        post_or_page_id: post_or_page_id
                    },
                    success: function(secondResponse) {
                        // Access the 'total_straight_face_count' value from secondResponse
                        var totalStraightFaceCount = secondResponse.total_straight_face_count;
                        var totalSmileyFaceCount = secondResponse.total_smiley_face_count;
                        var totalSadFaceCount = secondResponse.total_sad_face_count;
                        
                        // Update the content of the div elements
                        $('#reaction-meh-count').text(totalStraightFaceCount);
                        $('#reaction-smiley-count').text(totalSmileyFaceCount);
                        $('#reaction-sad-count').text(totalSadFaceCount);
                    }
                });
            }
        });
    });
});
