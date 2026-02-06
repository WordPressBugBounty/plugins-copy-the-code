window.CTCWP = (function (window, document, navigator) {
	var textArea,
		copy;

	function isOS() {
		return navigator.userAgent.match( /ipad|iphone/i );
	}

	function createTextArea(text) {
		textArea       = document.createElement( 'textArea' );
		textArea.value = text;
		document.body.appendChild( textArea );
	}

	function selectText() {
		var range,
			selection;

		if (isOS()) {
			range = document.createRange();
			range.selectNodeContents( textArea );
			selection = window.getSelection();
			selection.removeAllRanges();
			selection.addRange( range );
			textArea.setSelectionRange( 0, 999999 );
		} else {
			textArea.select();
		}
	}

	function copyToClipboard() {
		// Use modern Clipboard API (iOS 26+ compatible)
		if ( navigator.clipboard && navigator.clipboard.writeText ) {
			navigator.clipboard.writeText( textArea.value ).then(
				function() {
					document.body.removeChild( textArea );
					// Redirect to page.
					if ( typeof copyTheCode !== 'undefined' && copyTheCode.redirect_url ) {
						window.location.href = copyTheCode.redirect_url;
					}
				}
			).catch(
				function() {
					// Fallback to legacy method if Clipboard API fails
					document.execCommand( 'copy' );
					document.body.removeChild( textArea );
					// Redirect to page.
					if ( typeof copyTheCode !== 'undefined' && copyTheCode.redirect_url ) {
						window.location.href = copyTheCode.redirect_url;
					}
				}
			);
		} else {
			// Fallback for older browsers
			document.execCommand( 'copy' );
			document.body.removeChild( textArea );
			// Redirect to page.
			if ( typeof copyTheCode !== 'undefined' && copyTheCode.redirect_url ) {
				window.location.href = copyTheCode.redirect_url;
			}
		}
	}

	copy = function (text) {
		createTextArea( text );
		selectText();
		copyToClipboard();
	}

	copySelection = function ( source ) {
		const selection = window.getSelection()
		const range     = document.createRange()
		range.selectNodeContents( source[0] )
		selection.removeAllRanges()
		selection.addRange( range )
		
		// Get selected text
		const selectedText = selection.toString();
		
		// Use modern Clipboard API (iOS 26+ compatible)
		if ( navigator.clipboard && navigator.clipboard.writeText ) {
			navigator.clipboard.writeText( selectedText ).then(
				function() {
					selection.removeAllRanges();
				}
			).catch(
				function() {
					// Fallback to legacy method if Clipboard API fails
					document.execCommand( 'copy' );
					selection.removeAllRanges();
				}
			);
		} else {
			// Fallback for older browsers
			document.execCommand( 'copy' );
			selection.removeAllRanges();
		}
	}

	return {
		copy: copy,
		copySelection: copySelection,
	};
})( window, document, navigator );
