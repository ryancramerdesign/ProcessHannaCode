function initHannaCodeAce() {

	var hcCode = $("#hc_code"); 
	if(hcCode.size() < 1) return;

	var hcCodeDiv = $('<div id="hc_code_div"></div>')
		.css('height', hcCode.attr('data-height') + 'px'); 

	hcCode.after(hcCodeDiv); 
	var editor=ace.edit('hc_code_div');

	hcCode.hide();

	editor.getSession().setValue(hcCode.val()); 	
	editor.getSession().on('change', function() { 
		hcCode.val(editor.getSession().getValue())
	}); 

	var hcTheme = hcCode.attr('data-theme');
	var _hcTheme = editor.getTheme();
	if(hcTheme) editor.setTheme('ace/theme/' + hcTheme); 

	var hcKeybinding = hcCode.attr('data-keybinding'); 
	var _hcKeybinding = editor.getKeyboardHandler(); // original
	if(hcKeybinding && hcKeybinding != 'none') editor.setKeyboardHandler('ace/keyboard/' + hcKeybinding);

	var hcBehaviors = parseInt(hcCode.attr('data-behaviors')); 
	editor.setBehavioursEnabled(hcBehaviors & 2); 
	editor.setWrapBehavioursEnabled(hcBehaviors & 4); 

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

	var setAceBehaviors = function() {
		var behaviors = 0; 
		$("#wrap_aceBehaviors input").each(function() {
			var val = parseInt($(this).val());
			var enabled = $(this).is(":checked");
			if(val == 2) editor.setBehavioursEnabled(enabled); 
			if(val == 4) editor.setWrapBehavioursEnabled(enabled); 
			
		}); 
	};

	$("#aceTheme").change(function() {
		var val = $(this).val();
		if(val.length == 0) editor.setTheme(_hcTheme); 
			else editor.setTheme('ace/theme/' + $(this).val()); 
	});
	$("#aceHeight").change(function() {
		var height = $(this).val();
		if(height < 100) height = 100;
		if(height > 2000) height = 2000; 
		$("#hc_code_div").css('height', height + 'px'); 
		editor.resize();
	});
	$("#aceKeybinding").change(function() {
		var val = $(this).val();
		if(val == 'none' || val.length == 0) editor.setKeyboardHandler(_hcKeybinding); 
			else editor.setKeyboardHandler('ace/keyboard/' + $(this).val()); 
	});
	$("#wrap_aceBehaviors input").change(function() {
		setTimeout(setAceBehaviors, 250); 
	});
}

$(document).ready(function() {
	$("#hc_export").click(function() { $(this).select(); });
	var $edit = $('#HannaCodeEdit'); 
	if($edit.size()) $('#HannaCodeEdit').WireTabs({
		items: $(".WireTab"),
		skipRememberTabIDs: ['HannaCodeDelete']
		});

	setTimeout('initHannaCodeAce()', 250);
}); 
