jQuery(document).ready(function($) {
	$('#eos-user-agent').text(navigator.userAgent);
	var $textArea = $( '#eos-debug-report' ).find( 'textarea' );
	$(".eos-widget.settings-box").each(function(index,element) {
		var title = $(this).find('.eos-label').text();
		var val = $(this).find('li').text().replace(/  /g,'').replace(/\n\n/g,'; ');
		$textArea.val($textArea.val() + title + '\n' + val + '\n\n');
	});
	$('#copy-for-support').on('click', function() {
		$( '#eos-debug-report' ).find( 'textarea' ).select();
		try {
			if(!document.execCommand('copy')) throw 'Not allowed.';
		} catch(e) {
			copyElement.remove();
			console.log("document.execCommand('copy'); is not supported");
			var text = $( '#debug-report' ).find( 'textarea' ).val();
			prompt('Copy the text below. (ctrl c, enter)', text);
		}
	})
});