// ( function( $ ){

// 	"use strict";
	var Modula = {
		settingsMetabox: $( '.modula-settings-container' ),
		initTabs: function(){
			var modulaObject = this,
			    modulaTabs = modulaObject.settingsMetabox.find( '.modula-tabs .modula-tab' ),
			    modulaTabsContent = modulaObject.settingsMetabox.find( '.modula-tabs-content > div' );

			modulaTabs.click( function(){
				var currentTab = $( this ).data( 'tab' );
				modulaTabs.removeClass( 'active-tab' );
				modulaTabsContent.removeClass( 'active-tab' );
				$( this ).addClass( 'active-tab' );
				modulaTabsContent.filter( '#' + currentTab ).addClass( 'active-tab' );
			});
		},
		initSliders: function(){
			var modulaObject = this,
			    sliders = modulaObject.settingsMetabox.find( '.modula-ui-slider' );
			if ( sliders.length > 0 ) {
				sliders.each( function( $index, $slider ) {
	                var input = $( $slider ).parent().find( '.modula-ui-slider-input' ),
	                    max = input.data( 'max' ),
	                    min = input.data( 'min' ),
	                    step = input.data( 'step' ),
	                    value = parseInt( input.val(), 10 );

	                $( $slider ).slider({
	                    value: value,
	                    min: min,
	                    max: max,
	                    step: step,
	                    slide: function( event, ui ) {
	                        input.val( ui.value );
	                    }
	                });
	            });
			}
		},
		initColorPickers: function(){
			var modulaObject = this,
			    colorPickers = modulaObject.settingsMetabox.find( '.modula-colorpickers' );
			if ( colorPickers.length > 0 ) {
	            colorPickers.each( function( $index, colorPicker ) {
	                $( colorPicker ).wpColorPicker();
	            });
	        }

		},
		initHoverEffects: function() {
			var modulaObject = this,
			    input = modulaObject.settingsMetabox.find( '[name="modula-settings[effect]"]' ),
			    hoverBoxes = modulaObject.settingsMetabox.find( '.modula-effects-preview > div' );

			input.change( function(){
				var effect = $(this).val();
				hoverBoxes.hide();
				hoverBoxes.filter( '.panel-' + effect ).show();
			});
		}
	};

	var modulaGallery = {
		uploaderOptions: {
			container: $( '#modula-uploader-container' ),
			browser: $( '#modula-uploader-browser' ),
			dropzone: $( '#modula-uploader-container' )
		},
		dropzone: $( '#modula-dropzone-container' ),
		progressBar: $( '.modula-progress-bar' ),
		containerFooter: $( '.modula-uploader-footer' ),
		errorContainer: $( '.modula-error-container' ),
		galleryCotainer: $( '#modula-uploader-container .modula-uploader-inline-content' ),
		attachments: [],

		init: function(){
			var modulaGalleryObject = this,
				uploader,
				dropzone,
				attachments,
				modula_files_count = 0;

			// Check if we have attachments and add it to our list
			attachments = modulaGalleryObject.galleryCotainer.find( '.modula-single-image' );
			if ( attachments.length > 0 ) {

				$.each( attachments, function( index, attachmentElement ){
					var attachment = {};

					attachment['id'] = $( attachmentElement ).find( '.modula-image-id' ).val();
					attachment['alt'] = $( attachmentElement ).find( '.modula-image-alt' ).val();
					attachment['title'] = $( attachmentElement ).find( '.modula-image-title' ).val();
					attachment['caption'] = $( attachmentElement ).find( '.modula-image-caption' ).val();
					attachment['halign'] = $( attachmentElement ).find( '.modula-image-halign' ).val();
					attachment['valign'] = $( attachmentElement ).find( '.modula-image-valign' ).val();
					attachment['link'] = $( attachmentElement ).find( '.modula-image-link' ).val();
					attachment['target'] = $( attachmentElement ).find( '.modula-image-target' ).val();
					attachment['src'] = $( attachmentElement ).find( 'img' ).data( 'image' );
					attachment['image'] = $( attachmentElement ).find( 'img' ).attr( 'src' );

					modulaGalleryObject.attachments.push( attachment );

				});

			}

			uploader = new wp.Uploader( modulaGalleryObject.uploaderOptions );

			// Uploader events
			// Files Added for Uploading - show progress bar
			uploader.uploader.bind( 'FilesAdded', function ( up, files ) {

				// Hide any existing errors
                modulaGalleryObject.errorContainer.html( '' );

				// Get the number of files to be uploaded
                modula_files_count = files.length;

                // Set the status text, to tell the user what's happening
                $( '.modula-upload-numbers .modula-current', modulaGalleryObject.containerFooter ).text( '1' );
                $( '.modula-upload-numbers .modula-total', modulaGalleryObject.containerFooter ).text( modula_files_count );

                // Show progress bar
                modulaGalleryObject.containerFooter.addClass( 'show-progress' );

			});

			// File Uploading - update progress bar
			uploader.uploader.bind( 'UploadProgress', function( up, file ) {

				// Update the status text
                $( '.modula-upload-numbers .modula-current', modulaGalleryObject.containerFooter ).text( ( modula_files_count - up.total.queued ) + 1 );

                // Update the progress bar
                $( '.modula-progress-bar-inner', modulaGalleryObject.progressBar ).css({ 'width': up.total.percent + '%' });

			});

			// File Uploaded - add images to the screen
			uploader.uploader.bind( 'FileUploaded', function( up, file, info ) {
				var response = JSON.parse( info.response );
				modulaGalleryObject.generateSingleImage( modulaGalleryObject.galleryCotainer, response['data'] );
			});

			// Files Uploaded - hide progress bar
			uploader.uploader.bind( 'UploadComplete', function() {
				setTimeout( function() {
                    modulaGalleryObject.containerFooter.removeClass( 'show-progress' );
                }, 1000 );
			});

			// File Upload Error - show errors
			uploader.uploader.bind( 'Error', function( up, err ) {

				// Show message
                modulaGalleryObject.errorContainer.html( '<div class="error fade"><p>' + err.file.name + ': ' + err.message + '</p></div>' );
                up.refresh();

			});

			// Dropzone events
			dropzone = uploader.dropzone;
			dropzone.on( 'dropzone:enter', modulaGalleryObject.show );
			dropzone.on( 'dropzone:leave', modulaGalleryObject.hide );

			// Single Image Actions ( Delete/Edit )
			modulaGalleryObject.galleryCotainer.on( 'click', '.modula-delete-image', function( e ){
				e.preventDefault();
				$(this).parents( '.modula-single-image' ).remove();
			});

			// Modula WordPress Media Library
            wp.media.frames.modula = wp.media( {
                frame: 'post',
                title:  wp.media.view.l10n.addToGalleryTitle,
                button: {
                    text: wp.media.view.l10n.addToGallery,
                },
                multiple: true
            } );

            // Mark existing Gallery images as selected when the modal is opened
	        wp.media.frames.modula.on( 'open', function() {
	            // Get any previously selected images
	            var selection = wp.media.frames.modula.state().get( 'selection' );

	            // Get images that already exist in the gallery, and select each one in the modal
	            $.each( modulaGalleryObject.attachments, function( index, attachment ) {
	            	var image = wp.media.attachment( attachment.id );
	                selection.add( image ? [ image ] : [] );
	            } );
	        } );

            // Insert into Gallery Button Clicked
	        wp.media.frames.modula.on( 'insert', function( selection ) {

	            // Get state
	            var state = wp.media.frames.modula.state();

	            // Reset all images saved
	            modulaGalleryObject.attachments = [];

	            // Removed old images from container
	            modulaGalleryObject.galleryCotainer.find( '.modula-single-image' ).remove();

	            // Iterate through selected images, building an images array
	            selection.each( function( attachment ) {
	                modulaGalleryObject.generateSingleImage( modulaGalleryObject.galleryCotainer, attachment.toJSON() );

	            }, this );

	        } );

	        // Open WordPress Media Gallery
	        $( '#modula-wp-gallery' ).click( function( e ){
	        	e.preventDefault();
	        	wp.media.frames.modula.open();
	        });


	        // Add sortable support to Modula Gallery Media items
		    $( modulaGalleryObject.galleryCotainer ).sortable( {
		        items: '.modula-single-image',
		        cursor: 'move',
		        forcePlaceholderSize: true,
		        placeholder: 'modula-single-image-placeholder'
		    } );

		},

		show: function() {
			var $el = $( '#modula-dropzone-container' ).show();

			// Ensure that the animation is triggered by waiting until
			// the transparent element is painted into the DOM.
			_.defer( function() {
				$el.css({ opacity: 1 });
			});
		},

		hide: function() {
			var $el = $( '#modula-dropzone-container' ).css({ opacity: 0 });

			wp.media.transition( $el ).done( function() {
				// Transition end events are subject to race conditions.
				// Make sure that the value is set as intended.
				if ( '0' === $el.css('opacity') ) {
					$el.hide();
				}
			});

			// https://core.trac.wordpress.org/ticket/27341
			_.delay( function() {
				if ( '0' === $el.css('opacity') && $el.is(':visible') ) {
					$el.hide();
				}
			}, 500 );
		},

		generateSingleImage: function( container, attachment ){
			var data = { halign: 'center', valign: 'middle', link: '', target: '' },
				template = wp.template( 'modula-single-image' ),
				html,
				modulaGalleryObject = this;

			data['image']   = attachment['sizes']['thumbnail']['url'];
			data['src']     = attachment['sizes']['full']['url'];
			data['id']      = attachment['id'];
			data['alt']     = attachment['alt'];
			data['title']   = attachment['title'];
			data['caption'] = attachment['caption'];

			modulaGalleryObject.attachments.push( data );

			html = template( data );
			container.append( html );
		}

	}


	$( document ).ready( function(){
		Modula.initTabs();
		Modula.initSliders();
		Modula.initColorPickers();
		Modula.initHoverEffects();

		// Initiate Modula Gallery Uploader
		modulaGallery.init();
	});

// })( jQuery );