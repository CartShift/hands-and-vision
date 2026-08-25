(function ($) {
	'use strict';

	function renumberSections($wrap) {
		$wrap.find('[data-hv-artist-section]').each(function (sectionIndex) {
			$(this).find('[name]').each(function () {
				this.name = this.name.replace(/hv_service_artist_sections\[[^\]]+\]/, 'hv_service_artist_sections[' + sectionIndex + ']');
			});
		});
	}

	function emptyPreview($slot) {
		var slotIndex = $slot.index() + 1;
		$slot.removeClass('is-filled');
		$slot.find('[data-hv-image-id]').val('');
		$slot.find('[data-hv-image-preview]').html('<span>תמונה ' + String(slotIndex).padStart(2, '0') + '</span>');
		$slot.find('[data-hv-remove-image]').prop('disabled', true);
	}

	$(document).on('click', '[data-hv-add-artist-section]', function () {
		var $wrap = $('[data-hv-service-artist-sections]');
		var template = $('#hv-service-artist-section-template').html();

		if (!template) {
			return;
		}

		$wrap.append(template.replace(/__index__/g, $wrap.find('[data-hv-artist-section]').length));
		renumberSections($wrap);
	});

	$(document).on('click', '[data-hv-remove-artist-section]', function () {
		var $wrap = $('[data-hv-service-artist-sections]');

		$(this).closest('[data-hv-artist-section]').remove();
		renumberSections($wrap);
	});

	$(document).on('click', '[data-hv-move-artist-section]', function () {
		var direction = $(this).data('hv-move-artist-section');
		var $section = $(this).closest('[data-hv-artist-section]');
		var $wrap = $('[data-hv-service-artist-sections]');

		if (direction === 'up') {
			$section.prev('[data-hv-artist-section]').before($section);
		}

		if (direction === 'down') {
			$section.next('[data-hv-artist-section]').after($section);
		}

		renumberSections($wrap);
	});

	$(document).on('click', '[data-hv-select-image]', function () {
		var $slot = $(this).closest('[data-hv-image-slot]');
		var frame = wp.media({
			title: 'בחירת תמונה לעבודה',
			button: {
				text: 'השתמשי בתמונה'
			},
			multiple: false
		});

		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			var preview = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;

			$slot.addClass('is-filled');
			$slot.find('[data-hv-image-id]').val(attachment.id);
			$slot.find('[data-hv-image-preview]').html('<img src="' + preview + '" alt="">');
			$slot.find('[data-hv-remove-image]').prop('disabled', false);
		});

		frame.open();
	});

	$(document).on('click', '[data-hv-remove-image]', function () {
		emptyPreview($(this).closest('[data-hv-image-slot]'));
	});
})(jQuery);
