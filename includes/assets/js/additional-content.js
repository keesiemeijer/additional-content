( function( $ ) {

	var container;
	var obj;

	/**
	 * Re-numbers the id, for and name attributes for all additional content rows
	 */
	function reset_rows() {
		var id;
		var rows = container.children( 'div' );

		rows.each( function( index ) {
			$( this ).find( 'input, textarea, label' ).each( function() {

				if ( !!$( this ).attr( 'id' ) ) {
					id = $( this ).attr( 'id' ).split( '-' );
					$( this ).attr( 'id', id[ 0 ] + '-' + index );
				}

				if ( !!$( this ).attr( 'for' ) ) {
					id = $( this ).attr( 'for' ).split( '-' );
					$( this ).attr( 'for', id[ 0 ] + '-' + index );
				}

				if ( !!$( this ).attr( 'name' ) ) {
					id = $( this ).attr( 'name' ).split( '[' );
					$( this ).attr( 'name', id[ 0 ] + '[' + index + '][' + id[ 2 ] );
				}

			} );

		} );

		$( '#ac-add-row' ).val( get_add_row_text( rows ) );
	}


	/**
	 * Return text for add row button depending on number of rows.
	 */
	function get_add_row_text( rows ) {
		var text = obj.add_more_row;
		if ( $( rows ).length === 0 ) {
			text = obj.add_row;
		}
		return text;
	}

	$( document ).ready( function() {

		if ( typeof ac_additional_content === 'undefined' ) {
			return;
		}

		if ( !$( ac_additional_content ).length ) {
			return;
		}

		obj = ac_additional_content;

		container = $( '#additional-content-container' );

		if ( !container.length ) {
			return;
		}

		container.sortable( {
			axis: "y",
			cursor: "move",
			containment: "parent",
			items: "> div",
			placeholder: 'sortable-placeholder',
			forcePlaceholderSize: true,
			helper: 'clone',
			distance: 2,
			tolerance: 'pointer',
			opacity: 0.65,
			stop: function( event, ui ) {
				reset_rows();
			}
		} );

		var rows = container.children( 'div' );

		// The template for adding additional content
		var row = $( '#ac_additional_content_template' ).html();

		var toggle_options = $( '<div><a class="ac-toggle_options" aria-expanded="false" href="#">' + obj.show_options + '</a></div>' );

		// Check if there are options to expand
		$( '.ac-options' ).each( function( index ) {
			if ( $( '.ac-option', this ).length ) {
				if ( !$( this ).hasClass( 'js-no-toggle' ) ) {
					var id = $( this ).attr( 'id' ).split( '-' );
					var controls = 'ac-options-' + id[ 2 ] + ' ac-actions-' + id[ 2 ];
					$( 'a', toggle_options ).attr( 'aria-controls', controls );
					$( this ).before( toggle_options.clone() );
				}
			} else {
				var actions = $( this ).closest( '.ac-repeat-container' ).find( '.ac-actions' );
				if ( actions.length ) {
					actions.find( 'input[type=submit]' ).addClass( 'button-small' );
					actions.removeClass( 'js-visually-hidden' );
				}
			}
		} );

		reset_rows();

		// Add row click event
		$( '#ac-add-row' ).on( 'click', function( e ) {
			e.preventDefault();

			container.append( row );
			var _row = container.children( 'div' ).last();

			// remove id for not messing up existing aria-controls
			_row.find( '.ac-options, .ac-actions' ).removeAttr( 'id' );

			if ( !$( '.ac-option', _row ).length ) {
				_row.find( 'input[type=submit]' ).addClass( 'button-small' );
			}

			var color = _row.css( 'backgroundColor' );
			_row.css( 'backgroundColor', '#FFFF33' ).animate( {
				backgroundColor: color
			}, {
				complete: function() {
					$( this ).css( 'backgroundColor', '' );
				}
			} );

			reset_rows();
		} );


		// Remove row click event
		container.on( 'click', '.ac-remove', function( e ) {
			e.preventDefault();
			var _row = $( this ).closest( '.ac-repeat-container' );

			_row.css( 'backgroundColor', '#faa' ).fadeOut( 350, function() {
				_row.remove();
				reset_rows();
			} );
		} );


		// Toggle options click event
		container.on( 'click', '.ac-toggle_options', function( e ) {
			e.preventDefault();
			var curr_options = $( this ).closest( '.ac-repeat-container' ).find( '.ac-options' );

			if ( curr_options.length ) {
				curr_options.toggleClass( 'js-visually-hidden' );
				var hidden = curr_options.hasClass( 'js-visually-hidden' );

				$( this ).text( ( hidden ? obj.show_options : obj.hide_options ) );
				$( this ).closest( '.ac-repeat-container' ).find( '.ac-actions' ).toggleClass( 'js-visually-hidden' );
				$( this ).attr( 'aria-expanded', ( hidden ? 'false' : 'true' ) );
			}
		} );


		// checkboxes change event
		container.on( 'change', '[type="checkbox"]', function( e ) {

			var label = obj.content;
			var checkboxes = $( this ).closest( 'div' ).find( 'input[type="checkbox"]:checked' );

			if ( checkboxes.length ) {
				if ( 2 === checkboxes.length ) {
					label = obj.append_prepend;
				} else {
					if ( checkboxes.attr( 'data-ac-type' ) ) {
						label = obj[ checkboxes.data( 'ac-type' ) ];
					}
				}
			}

			var content = $( this ).closest( '.ac-repeat-container' ).find( '.ac-content' );
			content.text( label + ':' );
		} );
	} );

} )( jQuery );