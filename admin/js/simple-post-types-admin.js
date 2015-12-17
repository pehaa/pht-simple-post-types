(function( $ ) {
	'use strict';
	$(function() {
		$( ".phtspt-items" ).accordion({
			header: ".js-phtspt-acordion-trigger-edit",
			collapsible: true,
			active: false,
			heightStyle: "content"

		});
		$( ".phtspt-sections-accordion" ).accordion({
			header: ".js-phtspt-acordion-trigger",
			collapsible: true,
			heightStyle: "content"

		});

		var 
		$form = $( '.phtspt-form-add' ),
		
		registeredPostTypesArray = $.map( pehaathemes_spt_data.reserved_terms.post_type, function( value, key ) {
			return value;
		}),
		
		registeredTaxonomiesArray = $.map( pehaathemes_spt_data.reserved_terms.taxonomy, function( value, key ) {
			return value;
		}),
		
		errorMessages = $.map( pehaathemes_spt_data.error_messages, function( value, key ) {
			return value;
		}),
		
		registeredItems = {
			'post_type' : registeredPostTypesArray,
			'taxonomy' : registeredTaxonomiesArray,
		},
		
		keyRegex = {
			'post_type-slug' : /^[a-z][a-z0-9_]{0,19}$/,
			'taxonomy-slug' : /^[a-z][a-z0-9_]{0,31}$/,
			'label': /^[a-zA-Z-\s_]{1,15}$/			
		},
		
		$requiredField = $('.phtspt-required');
		

		$(document).on( 'click', '.js-phtspt-confirm', function() {
			
			var confirmation = confirm( pehaathemes_spt_data.confirmation );
			
			if ( !confirmation ) return false;
			
		});

		$form.submit( function( event ) {
			
			var $that = $(this),
			key = $('#phtspt_field-' + $(this).data( 'itemtype' ) + '-key').val(), 
			error = '';	
				
			$that.find($requiredField).each( function( index, element ) {
				if ( '' === $(element).val().trim() ) {
					$(element).addClass( 'phtspt-error' );
					error = errorMessages[5];
				} else {
					$(element).removeClass( 'error' );
				}
			} );
						
			// prevent the items duplication			
			if ( 'add' === $that.data( 'actiontype' ) ) {
				if ( -1 !== $.inArray( key, registeredItems[ $that.data( 'itemtype' ) ] ) ) {
					error += error ? '\n\n' : '';
					error += 'post_type' === $that.data( 'itemtype' ) ? errorMessages[6] : errorMessages[7];
				}				
			}
			$that.find('.phtspt-regex').each( function( index, element ) {
				if ( ! keyRegex[  $(element).data( 'regex' ) ].test( $(element).val().trim() ) ) {
					if ( '' !== $(element).val().trim() ) {
						error += error ? '\n\n' : '';
						if ( 'label' === $(element).data( 'regex' ) ) {
							error += errorMessages[8]; 
						} else {
							error += 'post_type' === $that.data( 'itemtype' ) ? errorMessages[3] : errorMessages[4];
						}
						
					}
				}

			} );	
		
			
			if ( error ) {
				alert( error );
				event.preventDefault();
			}
			
		});
		
	});

})( jQuery );
