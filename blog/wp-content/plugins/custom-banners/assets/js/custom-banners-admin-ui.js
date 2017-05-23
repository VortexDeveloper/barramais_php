var gp_initialize_custom_banners_admin = function () {
	var stamp = jQuery('#cbp-expiration-timestamp').html();
	jQuery('.edit-expiration-timestamp').click(function () {
		if (jQuery('#cbp-expiration-timestampdiv').is(":hidden")) {
			jQuery('#cbp-expiration-timestampdiv').slideDown("normal");
			jQuery(this).hide();
		}
		return false;
	});

	jQuery('.cbp-reset-expiration').click(function() {
		var confirmed = confirm('Are you sure you want to remove the expiration time from this banner?');
		
		if ( confirmed )
		{			
			/* Reset time inputs to their original state */
			jQuery('#cbp-expiration-mm').val(jQuery('#cbp-expiration-hidden_mm').val());
			jQuery('#cbp-expiration-jj').val(jQuery('#cbp-expiration-hidden_jj').val());
			jQuery('#cbp-expiration-aa').val(jQuery('#cbp-expiration-hidden_aa').val());
			jQuery('#cbp-expiration-hh').val(jQuery('#cbp-expiration-hidden_hh').val());
			jQuery('#cbp-expiration-mn').val(jQuery('#cbp-expiration-hidden_mn').val());
			
			/* Set the "Reset To Never" flag */
			jQuery('#cbp-reset-to-never').val('1');
			
			/* Reset the UI */
			jQuery('#cbp-expiration-timestampdiv').slideUp("normal");
			jQuery('#cbp-expiration-timestamp').html('Expires: <b>Never</b>');
			jQuery('.edit-expiration-timestamp').show();
			return false;
		}
	});

	jQuery('.expiration-cancel-timestamp').click(function() {
		jQuery('#cbp-expiration-timestampdiv').slideUp("normal");
		jQuery('#cbp-expiration-mm').val(jQuery('#cbp-expiration-hidden_mm').val());
		jQuery('#cbp-expiration-jj').val(jQuery('#cbp-expiration-hidden_jj').val());
		jQuery('#cbp-expiration-aa').val(jQuery('#cbp-expiration-hidden_aa').val());
		jQuery('#cbp-expiration-hh').val(jQuery('#cbp-expiration-hidden_hh').val());
		jQuery('#cbp-expiration-mn').val(jQuery('#cbp-expiration-hidden_mn').val());
		jQuery('#cbp-expiration-timestamp').html(stamp);
		jQuery('.edit-expiration-timestamp').show();
		return false;
	});

	jQuery('.expiration-save-timestamp').click(function () { // crazyhorse - multiple ok cancels
		var aa = jQuery('#cbp-expiration-aa').val(),
			mm = jQuery('#cbp-expiration-mm').val(),
			jj = jQuery('#cbp-expiration-jj').val(),
			hh = jQuery('#cbp-expiration-hh').val(),
			mn = jQuery('#cbp-expiration-mn').val();
		var newD = new Date( aa, mm - 1, jj, hh, mn );

		if ( newD.getFullYear() != aa || (1 + newD.getMonth()) != mm || newD.getDate() != jj || newD.getMinutes() != mn ) {
			jQuery('.cbp-expiration-timestamp-wrap', '#cbp-expiration-timestampdiv').addClass('form-invalid');
			return false;
		} else {
			jQuery('.cbp-expiration-timestamp-wrap', '#cbp-expiration-timestampdiv').removeClass('form-invalid');
		}

		jQuery('#cbp-expiration-timestampdiv').slideUp("normal");
		jQuery('.edit-expiration-timestamp').show();
		jQuery('#cbp-expiration-timestamp').html(
			'&nbsp;' + cbp_expires_L10n.expires + ' <b>' +
			jQuery( '#cbp-expiration-mm option[value="' + mm + '"]' ).text() + ' ' +
			jj + ', ' +
			aa + ' @ ' +
			hh + ':' +
			mn + '</b> '
		);
		
		/* Clear the "Reset To Never" flag */
		jQuery('#cbp-reset-to-never').val('0');			
		return false;
	});
};

jQuery(gp_initialize_custom_banners_admin);


/* Galahad funcs from Easy T */
var custom_banners_submit_ajax_form = function (f) {
	var msg = jQuery('<p><span class="fa fa-refresh fa-spin"></span><em> One moment..</em></p>');	
	var f = jQuery(f).after(msg).detach();
	var enc = f.attr('enctype');
	var act = f.attr('action');
	var meth = f.attr('method');
	var submit_with_ajax = ( f.data('ajax-submit') == 1 );
	var ok_to_send_site_details = ( f.find('input[name="include_wp_info"]:checked').length > 0 );
	
	if ( !ok_to_send_site_details ) {
		f.find('.gp_galahad_site_details').remove();
	}
	
	var wrap = f.wrap('<form></form>').parent();
	wrap.attr('enctype', f.attr('enctype'));
	wrap.attr('action', f.attr('action'));
	wrap.attr('method', f.attr('method'));
	wrap.find('#submit').attr('id', '#notsubmit');

	if ( !submit_with_ajax ) {
		jQuery('body').append(wrap);
		setTimeout(function () {
			wrap.submit();
		}, 500);	
		return false;
	}
	
	data = wrap.serialize();
	
	$.ajax(act,
	{
		crossDomain: true,
		method: 'post',
		data: data,
		dataType: "json",
		success: function (ret) {
			var r = jQuery(ret)[0];
			msg.html('<p class="ajax_response_message">' + r.msg + '</p>');
		}
	});		
};

var custom_banners_submit_ajax_contact_form = function (f) {
	$ = jQuery;
	
	// initialize the form
	var ajax_url = 'https://goldplugins.com/tickets/galahad/catch.php';
	//f.attr('action', ajax_url);
	
	// show 'one moment' emssage
	var msg = '<p><span class="fa fa-refresh fa-spin"></span><em> One moment..</em></p>';
	$('.gp_ajax_contact_form_message').html(msg);
	
	var f = jQuery(f).after(msg).detach();
	var enc = f.attr('enctype');
	var act = f.attr('action');
	var meth = f.attr('method');

	jQuery('body').append(f);	
	var wrap = f.wrap('<form></form>').parent();
	wrap.attr('enctype', f.attr('enctype'));
	wrap.attr('action', f.attr('action'));
	wrap.attr('method', f.attr('method'));	
	wrap.find('#submit').attr('id', '#notsubmit');

	setTimeout(function () {
		wrap.submit();
	}, 100);
	
	
	
	
	
	data = f.serialize();
	
	$.ajax(
		ajax_url,
		'post',
		data,
		function (ret) {
			alert(ret);
		}
	);
	return false; // prevent form from submitting normally
};

var custom_banners_setup_contact_forms = function() {
	$ = jQuery;
	var forms = $('.gp_support_form_wrapper div[data-gp-ajax-contact-form="1"]');
	if (forms.length > 0) {
		forms.each(function () {
			var f = this;
			var btns = $(this).find('.button[type="submit"]').on('click', 
				function () {
					custom_banners_submit_ajax_contact_form(f);
					return false;
				} 
			);
		});
	}
	jQuery('.gp_ajax_contact_form').on('submit', custom_banners_submit_contact_form);
};

var custom_banners_setup_ajax_forms = function() {
	$ = jQuery;
	var forms = $('div[data-gp-ajax-form="1"]');
	if (forms.length > 0) {
		forms.each(function () {
			var f = this;
			var btns = $(this).find('.button[type="submit"]').on('click', 
				function () {
					custom_banners_submit_ajax_form(f);
					return false;
				} 
			);
		});
	}
};
jQuery(function () {
	custom_banners_setup_ajax_forms();
	//custom_banners_setup_contact_forms();
});