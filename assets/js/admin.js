function clone_cdn_item() {

	var $html = jQuery('.primary-cdn-zone').clone();
	$html.removeClass('primary-cdn-zone');

	// reset value for edit screen
	$html.find('input').val('');
	$html.find('select').val('all');
	$html.append('<span class="dashicons dashicons-no remove-cdn-item" ></span>');
	jQuery('#cdn-zones').append($html);

	return false;
}

function remove_cdn_item() {
	console.log('tikladim');
	var el = jQuery(this).parents('.cdn-zone');
	console.log(el);
}


jQuery(document).ready(function ($) {

	$('body').on('click', '.remove-cdn-item', function () {
		$(this).parent().remove();
	});

	$('#pc_mobile_cache').change(function () {
		if (this.checked) {
			$('#separate-mobile-cache').fadeIn('slow');
		} else {
			$('#separate-mobile-cache').fadeOut('slow');
		}
	});


	$("#pc-support-btn").click(function () {
		var serialized_data = $('#powered-cache-settings-form').serializeArray();
		var $submit_btn = $("#pc-support-btn");

		$submit_btn.attr('disabled', 'disabled');

		var data = {
			action   : 'pc_support_request',
			nonce    : pc_vars.nonce,
			form_data: serialized_data
		};

		//the_ajax_script.ajaxurl is a variable that will contain the url to the ajax processing file
		$.post(pc_vars.ajaxurl, data, function (response) {
			resp = JSON.parse(response);
			$submit_btn.removeAttr('disabled');

			var $placeholder = $('#form-message-placeholder');

			if (resp.success == false) {
				$placeholder.addClass('error');
			} else {
				$placeholder.addClass('updated');
				$('#support-form-table').hide();
			}

			$placeholder.html('<p>' + resp.msg + '</p>');
		});
		return false;
	});

});


