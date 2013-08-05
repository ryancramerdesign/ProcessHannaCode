$(document).ready(function() {
	$("#hc_export").click(function() { $(this).select(); });

	var hcCode = $("#hc_code"); 
	if(hcCode.size() > 0) {

		$('#HannaCodeEdit').WireTabs({
			items: $(".WireTab"),
			skipRememberTabIDs: ['HannaCodeDelete']
			});


		var hcCodeDiv = $('<div id="hc_code_div"></div>')
			.css('height', hcCode.attr('data-height') + 'px'); 

		hcCode.after(hcCodeDiv); 
		var editor=ace.edit('hc_code_div');

		hcCode.hide();

		editor.getSession().setValue(hcCode.val()); 	
		editor.getSession().on('change', function() { 
			hcCode.val(editor.getSession().getValue())
		}); 

		editor.setTheme('ace/theme/' + hcCode.attr('data-theme')); 

		var hcType = $("input[name=hc_type]"); 

		hcType.change(function() {

			if(!$(this).is(":checked")) return;

			var val = $(this).val();
			var editorMode = 'html';
			var editorValue = editor.getSession().getValue();
			var phpBlankValue = "<?php\n\n";

			if(val == 1) {
				editorMode = 'javascript';
				if(editorValue == phpBlankValue) editor.getSession().setValue('');
			} else if(val == 2) {
				editorMode = 'php';
				if(editorValue.length < 1) editor.getSession().setValue(phpBlankValue);
			} else {
				editorMode = 'plain_text';
				if(editorValue == phpBlankValue) editor.getSession().setValue('');
			}

			editor.getSession().setMode("ace/mode/" + editorMode); 

		}).change();
	}
}); 
