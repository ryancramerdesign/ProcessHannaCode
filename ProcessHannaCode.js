$(document).ready(function() {
	$("#hc_export").click(function() { $(this).select(); });

	var hcCode = $("#hc_code"); 
	if(hcCode.size() > 0) {

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
			if(val == 1) editorMode = 'javascript';
				else if(val == 2) editorMode = 'php';
				else editorMode = 'plain_text';
			editor.getSession().setMode("ace/mode/" + editorMode); 
		}).change();
	}
}); 
