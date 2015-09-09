jQuery(document).ready(function($) {
    var data = {
        'action': 'wpuviewedposts_track_view',
        'date': Date.now() ,
        'post_id': ajax_object.post_id
    };
    jQuery.post(ajax_object.ajax_url, data, function(response) {});
});