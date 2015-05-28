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

		$( '#ac-add-row' ).text( get_add_row_text( rows ) );
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

		var remove_row = $( '<a class="button ac-remove-row" href="#">' + obj.remove_row + '</a>' );

		var rows = container.children( 'div' );

		var options = $( '.ac-options' );

		var toggle_options = $( '<p><a class="ac-toggle_options visible" href="#">' + obj.show_options + '</a></p>' );
		options.before( toggle_options );

		rows.append( remove_row );

		var add_row_text = get_add_row_text( rows );

		var add_row = $( '<p><a id="ac-add-row" class="button" href="#">' + add_row_text + '</a></p>' );

		container.after( add_row );

		var row = $( '#ac_additional_content_template' ).html();

		// Add row click event
		$( '#ac-add-row' ).on( 'click', function( e ) {
			e.preventDefault();
			container.append( row );
			var _row = container.children( 'div' ).last();
			var color = _row.css( 'backgroundColor' );
			_row.append( remove_row.clone() );
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
		container.on( 'click', 'a.ac-remove-row', function( e ) {
			e.preventDefault();
			var _row = $( this ).parent();

			_row.css( 'backgroundColor', '#faa' ).fadeOut( 350, function() {
				_row.remove();
				reset_rows();
			} );
		} );

		// Toggle options click event
		container.on( 'click', '.ac-toggle_options', function( e ) {
			e.preventDefault();
			var curr_options = $( this ).closest( 'div' ).find( '.ac-options' );
			if ( curr_options.length ) {
				$( this ).text( curr_options.hasClass( 'js-visually-hidden' ) ? obj.hide_options : obj.show_options );
				curr_options.toggleClass( 'js-visually-hidden' );
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
			var color = content.css( 'backgroundColor' );
			content.text( label + ':' );
		} );

	} );

} )( jQuery );