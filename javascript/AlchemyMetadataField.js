;(function($) {
	var applyMetadata = function() {
		var container = $(this);
		
		$('div.field.alchemy-populated').each (function () {
			var $this = $(this);
			var fieldName = $this.attr('id').replace(/AlchemyMetadata-/, '');
			
			if ($this.hasClass('multivaluetext')) {

				var dataField = $("#AlchemyMetadata-" + fieldName + " .mvtextfield");

				$("input.alchemy-add-" + fieldName + ":checked").each(function() {
					var word = $(this).attr("data-" + fieldName);
					var last    = dataField.last().parent();
					last.clone().prependTo(last.parent()).find('input').val(word);
				});

				$("input.alchemy-rm-" + fieldName + ":checked").each(function() {
					var word = $(this).attr("data-" + fieldName);
					dataField.filter(function() { return $(this).val() == word; }).remove();
				});
			} else {
				// find checkbox, see if checked, set val if so
				if ($('#alchemy-change-' + fieldName + ':checked').length > 0) {
					$(this).find('input').val($('#alchemy-change-' + fieldName + ':checked').attr('data-' + fieldName));
				}
			}
		})
		
		$(this).dialog("close");
		
		return;
		
		$("#alchemy-change-category:checked").each(function() {
			$("#AlchemyMetadata-Category input").val($(this).attr("data-category"));
		});

		var keywords = $("#AlchemyMetadata-Keywords .mvtextfield");

		$("input.alchemy-add-keyword:checked").each(function() {
			var keyword = $(this).attr("data-keyword");
			var last    = keywords.last().parent();
			last.clone().prependTo(last.parent()).find('input').val(keyword);
		});

		$("input.alchemy-rm-keyword:checked").each(function() {
			var keyword = $(this).attr("data-keyword");
			keywords.filter(function() { return $(this).val() == keyword; }).remove();
		});

		$("input.alchemy-add-entity:checked").each(function() {
			var type = $(this).parents(".entity").attr("data-field");
			var val  = $(this).attr("data-entity");
			var last = $("#" + type + " li").last();
			last.clone().prependTo(last.parent()).find('input').val(val);
		});

		$("input.alchemy-rm-entity:checked").each(function() {
			var type = $(this).parents(".entity").attr("data-field");
			var val  = $(this).attr("data-entity");
			var vals = $("#" + type + " .mvtextfield");
			vals.filter(function() { return $(this).val() == val; }).remove();
		});

		
	};

	$("a.alchemy-analyse").live("click", function() {
		var link = $(this);
		var text = link.text();
		var form = link.parents("form");

		if (form.get(0).isChanged && form.get(0).isChanged()) {
			var msg = 'There are unsaved changes, which metadata will not be'
				+ ' extracted from. Are you sure you wish to continue?';

			if (!confirm(msg)) {
				return false;
			}
		}

		link.text("Loading...");
		$.ajax({
			url: link.attr("href"),
			success: function(data) {
				$("<div></div>").html(data).dialog({
					title: text,
					modal: true,
					resizable: false,
					draggable: false,
					width: 500,
					height: 550,
					buttons: {
						"Apply": applyMetadata,
						"Cancel": function() { $(this).dialog("close"); }
					},
					close: function(e) {
						$(e.target).remove();
					}
				});
			},
			error: function() {
				alert('Could not analyze content.');
			},
			complete: function() {
				link.text(text);
			}
		});

		return false;
	});
})(jQuery);