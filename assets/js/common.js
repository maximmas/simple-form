jQuery(function ($) {

	$('.client_submit').on('click', function (e) {
		e.preventDefault();
		var form = $(this).parent().parent();
		var form_data = {
			name: form.find('.client_name').val(),
			email: form.find('.client_email').val(),
			message: form.find('.client_message').val(),
			action: 'handler',
		};
		let is_valid = form_validator(form_data);
		if (is_valid) {
			form_sender(form_data);
		};
	});

	function form_validator(data) {
		if (!data.name || !data.email) {
			// ошибка валидации
			return false;
		} else {
			return true;
		};
	};


	function form_sender(data) {

		$.ajax({
			url: SF_AjaxHandler.sf_ajaxurl,
			type: 'POST',
			data: data,
			success: function (resp) {
				modalInit();

			},
			error: function (resp) {
				// вывод ошибки
			}
		});
	};


	function modalInit() {
		$.magnificPopup.open({
			fixedContentPos: true,
			items: {
				src: $('#sf_modal'),
				type: 'inline'
			},
		}, 0);
	};


});
