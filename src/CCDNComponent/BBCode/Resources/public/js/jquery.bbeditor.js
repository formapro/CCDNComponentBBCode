<!--
/**
 * This file is part of the CCDNComponent BBCode
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/> 
 * 
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Plugin jQuery.BBEditor
 *
 * @author Reece Fowell <reece at codeconsortium dot com>
 *
 * BBCode Editor for textareas on sites using the BBCode engine.
 *
 * Requires JQuery, make sure to have JQuery included in your JS to use this.
 * JQuery needs to be loaded before this script in order for it to work.
 * @link http://jquery.com/
 *
 */

$(document).ready(function() {
	$('div.bb-editor').bbeditor();
});

!function($) {

	/*
	 *
	 * BBEDITOR PLUGIN DEFINITION
	 */
	$.fn.bbeditor = function () {
		var editors = $([]);

		this.each(function() {
			var $this = $(this);
			
			editors.push(new BBEditor($this));
		});
	};

	/*
	 *
	 * BBEDITOR PUBLIC CLASS DEFINITION
	 */
	function BBEditor(element) {
		this.init(element);
	};
	
	BBEditor.prototype.init = function (container) {
		// Get TextArea.
		this.textarea = $(container).find('textarea').eq(0);
		
		// Get Toolbars
		this.collapse = $(container).find('div.collapse');
		
		// Unhide the bb editor buttons/toolbars
		this.collapse.each(function(i, collapse) {
			$(collapse).removeClass('collapse');
		});
		
		// Get Buttons.
		fn = this.buttonClick;

		// Stupid hack Part 1, because 'this' in a bound click event
		// refers to the button or whatever instead of this freaking class.
		var that = this;

		this.buttons = $(container).find('button').filter('button');
		
		// Bind button click event to all bb editor buttons.
		this.buttons.each(function(i, button) {
			$(button).bind('click', {editor: that}, fn);
		});
	};
		
	BBEditor.prototype.insertToken = function (btn) {
		var txt = this.textarea;
		var tag = btn.data('tag');
		var tagCount = btn.data('tag-count');
		var paramQuestion = btn.data('param-question');
		
		if (paramQuestion) {
			var param = window.prompt(paramQuestion, "");
		} else {
			var param = null;
		}

	    var txtLen = txt.val().length;
	    var selStart = txt[0].selectionStart;
	    var selEnd = txt[0].selectionEnd;
	    var selectedText = txt.val().substring(selStart, selEnd);
		
		if (paramQuestion && (param == null || param == '' || param == false)) {
			txt.focus();
			
			return;
		} else {
			if (param != null) {
				param = '="' + param + '"';
				
				var tagOpen = '[' + tag + param + ']';
			} else {
				var tagOpen = '[' + tag + ']';
			}
		}
		
		
		var tagClose = '[/' + tag + ']';

		if (tagCount > 1) {
		    var replacement = tagOpen + selectedText + tagClose;
		} else {
		    var replacement = selectedText + tagOpen;
		}
		
	    txt.val(txt.val().substring(0, selStart) + replacement + txt.val().substring(selEnd, txtLen));
		
		if ((selEnd - selStart) == 0 && tagCount > 1) {
			// Place Caret between tags.
			caretPosition = selEnd + ((tagOpen.length));
		} else {
			// Place Caret at end of Insert.
			if (tagCount > 1) {
				caretPosition = selEnd + ((tagOpen.length + tagClose.length));
			} else {
				caretPosition = selEnd + ((tagOpen.length));
			}
		}
		
		// Bring focus back to the textarea.
		this.setCaretToPosition(caretPosition, caretPosition);
	}
	
	BBEditor.prototype.setCaretToPosition = function (selStart, selEnd) {
		var txt = this.textarea;

		txt[0].selectionStart = caretPosition;
		txt[0].selectionEnd = caretPosition;
		
		txt.focus();
	}

	BBEditor.prototype.buttonClick = function (event) {
		// Prevent default form submission behaviour.
		event.preventDefault();
		event.stopPropagation();
		
		// Get the button that was clicked.
		var btn = $(this);
		
		// Stupid hack, what arsehole thought this would be a good idea.
		var that = event.data.editor;
		
		// Insert the tag for the button clicked.
		that.insertToken(btn);
		
		return;
	};
	
}(window.jQuery);

// -->
