(function ($) {
	const CTCCore = {

		/**
		 * Init
		 */
		init: function () {
			this._bind();
		},

		/**
		 * Binds events
		 */
		_bind: function () {
			$( document ).on( 'click', 'body:not(.block-editor-page) .ctc-block-copy', this.doCopy );
		},

		/**
		 * Copy text to clipboard using CTC CopyEngine.
		 *
		 * @param {string} text Text to copy.
		 * @return {Promise<boolean>} Success status.
		 */
		copyToClipboard: async function ( text ) {
			// Method 1: Use CTC CopyEngine.
			if ( window.CTC && window.CTC.CopyEngine ) {
				try {
					const copyEngine = new window.CTC.CopyEngine();
					const result = await copyEngine.execute( { value: text } );
					if ( result.success ) {
						return true;
					}
				} catch ( err ) {
					// Fall through to fallbacks.
				}
			}

			// Method 2: Clipboard API.
			if ( navigator.clipboard && navigator.clipboard.writeText ) {
				try {
					await navigator.clipboard.writeText( text );
					return true;
				} catch ( err ) {
					// Fall through.
				}
			}

			// Method 3: execCommand fallback.
			try {
				const textArea = document.createElement( 'textarea' );
				textArea.value = text;
				textArea.style.cssText = 'position:fixed;opacity:0;pointer-events:none;';
				document.body.appendChild( textArea );
				textArea.select();
				const success = document.execCommand( 'copy' );
				document.body.removeChild( textArea );
				return success;
			} catch ( err ) {
				return false;
			}
		},

		/**
		 * Copy selection from element.
		 *
		 * @param {jQuery} $source jQuery element to copy selection from.
		 * @return {Promise<boolean>} Success status.
		 */
		copySelection: async function ( $source ) {
			if ( ! $source || ! $source.length ) {
				return false;
			}

			const element = $source.get( 0 );

			// Get text content, preserving line breaks.
			const clone = element.cloneNode( true );
			clone.querySelectorAll( 'br' ).forEach( ( br ) => br.replaceWith( '\n' ) );
			const text = clone.textContent || '';

			return this.copyToClipboard( text.trim() );
		},

		/**
		 * Detect coarse device type from user agent (for block analytics).
		 *
		 * @return {string} desktop|mobile|tablet
		 */
		_detectDevice: function () {
			const ua = navigator.userAgent;
			if ( /tablet|ipad|playbook|silk/i.test( ua ) ) {
				return 'tablet';
			}
			if ( /mobile|iphone|ipod|android|blackberry|opera|mini|windows\sce|palm|smartphone|iemobile/i.test( ua ) ) {
				return 'mobile';
			}
			return 'desktop';
		},

		/**
		 * Detect browser family (for block analytics).
		 *
		 * @return {string} Browser slug.
		 */
		_detectBrowser: function () {
			const ua = navigator.userAgent;
			if ( ua.indexOf( 'Chrome' ) > -1 ) {
				return 'chrome';
			}
			if ( ua.indexOf( 'Firefox' ) > -1 ) {
				return 'firefox';
			}
			if ( ua.indexOf( 'Safari' ) > -1 ) {
				return 'safari';
			}
			if ( ua.indexOf( 'Edge' ) > -1 ) {
				return 'edge';
			}
			return 'unknown';
		},

		/**
		 * Send analytics event for Gutenberg block copy (non-blocking).
		 *
		 * @param {jQuery} block Block wrapper (.ctc-block).
		 * @param {boolean} success Whether copy succeeded.
		 * @param {string|null} errorReason Optional failure reason.
		 */
		_sendBlockAnalytics: function ( block, success, errorReason ) {
			if ( ! block || ! block.length ) {
				return;
			}
			const analytics = block.attr( 'data-ctc-analytics' );
			const source   = block.attr( 'data-ctc-source' );
			if ( ! analytics || source !== 'gutenberg-block' ) {
				return;
			}
			if ( typeof window.ctcBlockAnalytics === 'undefined' || ! window.ctcBlockAnalytics.eventsUrl ) {
				return;
			}
			const event = {
				success: !! success,
				error_reason: errorReason || null,
				source: 'gutenberg-block',
				device: this._detectDevice(),
				browser: this._detectBrowser(),
				metadata: {
					source: 'gutenberg-block',
					block_type: block.attr( 'data-ctc-block-type' ) || null,
					post_id: window.ctcBlockAnalytics.postId || null,
					post_type: window.ctcBlockAnalytics.postType || null,
					page_url: window.location.href,
				},
			};
			const send = () => {
				fetch( window.ctcBlockAnalytics.eventsUrl, {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify( event ),
				} ).catch( function ( err ) {
					if ( typeof console !== 'undefined' && console.debug ) {
						console.debug( 'CTC: Block analytics event failed:', err );
					}
				} );
			};
			if ( window.requestIdleCallback ) {
				window.requestIdleCallback( send, { timeout: 500 } );
			} else {
				setTimeout( send, 0 );
			}
		},

		/**
		 * Do Copy to Clipboard
		 */
		doCopy: async function (event) {
			event.preventDefault();

			let btn             = $( this ),
				btnText         = btn.find( '.ctc-button-text' ),
				oldText         = btnText.text(),
				copiedText      = btn.attr( 'data-copied' ) || 'Copied',
				copyAsRaw       = btn.attr( 'copy-as-raw' ) || '',
				block           = btn.parents( '.ctc-block' ),
				textarea        = block.find( '.ctc-copy-content' ),
				content         = textarea.val(),
				selectionTarget = textarea.attr( 'selection-target' ) || '';

			let copySuccess = false;

			// Copy as selection.
			if ( selectionTarget ) {
				const source = $( selectionTarget );
				if ( ! source.length ) {
					return;
				}
				copySuccess = await CTCCore.copySelection( source );
			} else {
				if ( ! copyAsRaw ) {
					// Convert the <br/> tags into new line.
					content = content.replace( /<br\s*[\/]?>/gi, "\n" );

					// Convert the <div> tags into new line.
					content = content.replace( /<div\s*[\/]?>/gi, "\n" );

					// Convert the <p> tags into new line.
					content = content.replace( /<p\s*[\/]?>/gi, "\n\n" );

					// Convert the <li> tags into new line.
					content = content.replace( /<li\s*[\/]?>/gi, "\n" );

					// Remove all tags.
					content = content.replace( /(<([^>]+)>)/ig, '' );

					// Remove white spaces.
					content = content.replace( new RegExp( "/^\s+$/" ), "" );
				}

				// Remove first and last new line.
				content = content.trim();

				// Copy using CTC CopyEngine.
				copySuccess = await CTCCore.copyToClipboard( content );
			}

			if ( btn.hasClass( 'ctc-block-copy-icon' ) ) {
				// Copied!
				btn.addClass( 'copied' );
				setTimeout(
					function () {
						btn.removeClass( 'copied' );
					},
					1000
				);
			} else {
				// Copied!
				btnText.text( copiedText );
				block.addClass( 'copied' );
				setTimeout(
					function () {
						btnText.text( oldText );
						block.removeClass( 'copied' );
					},
					1000
				);
			}

			// Block analytics (non-blocking).
			CTCCore._sendBlockAnalytics( block, copySuccess, copySuccess ? null : 'copy_failed' );
		}
	};

	/**
	 * Initialization
	 */
	$(
		function () {
			CTCCore.init();
		}
	);

})( jQuery );
