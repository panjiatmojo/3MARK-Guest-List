jQuery(document).ready(function($) {

	var updateInterval = ($('#emgl-update-interval').val()) ? parseInt($('#emgl-update-interval').val(),10) : 10;
    ajaxUrl = '/wp-admin/admin-ajax.php';
    var pageTitle = $('#emgl-page-title').val();
    var pageUrl = $('#emgl-page-url').val();
    var referer = $('#emgl-referer').val();
    var pageTitle = $('#emgl-page-title').val();

    var data = {
        'page_title': pageTitle,
        'page_url': pageUrl,
        'referer': referer,
        'action': 'emgl_store_visitor_data'
    }


    setInterval(function() {
        var lastIndex = $('#emgl-last-id').val();
        var data = {
            'action': 'emgl_get_visitor_data',
            'last_index': lastIndex
        };
        $.post(ajaxUrl, data, function(response) {
            jsonString = response.match(/({.+})/); //	extract only json string part
            jsonArray = JSON.parse(jsonString[0]); //	parse the json string into json array

            try {
                //	only execute if new visitor is detected
                if (jsonArray.visitor_data !== "") {
                    $('#emgl-active-visitor .content').html(jsonArray.active_visitor[0].active_visitor);
                    $('#emgl-total-page-view .content').html(jsonArray.total_page_view[0].total_page_view);
                    $('#emgl-total-visitor .content').html(jsonArray.total_visitor[0].total_visitor);
					
					var newRow = jsonArray.visitor_data;


                    var maxVisitorData = $('#emgl-max-row').val();

                    //	add new row
                    $('#emgl-vis-anl table tbody').prepend(newRow).hide().fadeIn('slow');

                    //	remove row more than allowed
                    var target = $('#emgl-vis-anl table tbody tr:gt(' + 5*(maxVisitorData - 1) + ')');
                    target.hide('slow', function() {
                        target.remove();
                    });
					
                    $('#emgl-last-id').val(jsonArray.last_id);


                    //	update the last update information
                    $('#emgl-last-update .content').html(jsonArray.last_timestamp);
                }
            } catch (e) {}


        });
    }, 1000 * updateInterval);
});