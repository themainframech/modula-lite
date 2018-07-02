wp.Modula = 'undefined' === typeof( wp.Modula ) ? {} : wp.Modula;

wp.Modula.uploadHandler = {
	uploaderOptions: {
		container: $( '#modula-uploader-container' ),
		browser: $( '#modula-uploader-browser' ),
		dropzone: $( '#modula-uploader-container' )
	},
	dropzone: $( '#modula-dropzone-container' ),
	progressBar: $( '.modula-progress-bar' ),
	containerUploader: $( '.modula-upload-actions' ),
	errorContainer: $( '.modula-error-container' ),
	galleryCotainer: $( '#modula-uploader-container .modula-uploader-inline-content' ),

	init: function(){
		var modulaGalleryObject = this,
			uploader,
			dropzone,
			attachments,
			modula_files_count = 0;

		uploader = new wp.Uploader( modulaGalleryObject.uploaderOptions );

		// Uploader events
		// Files Added for Uploading - show progress bar
		uploader.uploader.bind( 'FilesAdded', function ( up, files ) {

			// Hide any existing errors
            modulaGalleryObject.errorContainer.html( '' );

			// Get the number of files to be uploaded
            modula_files_count = files.length;

            // Set the status text, to tell the user what's happening
            $( '.modula-upload-numbers .modula-current', modulaGalleryObject.containerUploader ).text( '1' );
            $( '.modula-upload-numbers .modula-total', modulaGalleryObject.containerUploader ).text( modula_files_count );

            // Show progress bar
            modulaGalleryObject.containerUploader.addClass( 'show-progress' );

		});

		// File Uploading - update progress bar
		uploader.uploader.bind( 'UploadProgress', function( up, file ) {

			// Update the status text
            $( '.modula-upload-numbers .modula-current', modulaGalleryObject.containerUploader ).text( ( modula_files_count - up.total.queued ) + 1 );

            // Update the progress bar
            $( '.modula-progress-bar-inner', modulaGalleryObject.progressBar ).css({ 'width': up.total.percent + '%' });

		});

		// File Uploaded - add images to the screen
		uploader.uploader.bind( 'FileUploaded', function( up, file, info ) {
			var response = JSON.parse( info.response );
			modulaGalleryObject.generateSingleImage( response['data'] );
		});

		// Files Uploaded - hide progress bar
		uploader.uploader.bind( 'UploadComplete', function() {
			setTimeout( function() {
                modulaGalleryObject.containerUploader.removeClass( 'show-progress' );
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
            wp.Modula.Items.each( function( item ) {
            	var image = wp.media.attachment( item.get( 'id' ) );
                selection.add( image ? [ image ] : [] );
            } );
        } );

        // Insert into Gallery Button Clicked
        wp.media.frames.modula.on( 'insert', function( selection ) {

            // Get state
            var state = wp.media.frames.modula.state();
            var oldItemsCollection = wp.Modula.Items;

            wp.Modula.Items = new modulaItemsCollection();

            // Iterate through selected images, building an images array
            selection.each( function( attachment ) {
            	var attachmentAtts = attachment.toJSON(),
            		currentModel = oldItemsCollection.get( attachmentAtts['id'] );

            	if ( currentModel ) {
            		wp.Modula.Items.add( currentModel );
            		oldItemsCollection.remove( currentModel );
            	}else{
            		modulaGalleryObject.generateSingleImage( attachmentAtts );
            	}
            }, this );

            while ( model = oldItemsCollection.first() ) {
			  model.delete();
			}

        } );

        // Open WordPress Media Gallery
        $( '#modula-wp-gallery' ).click( function( e ){
        	e.preventDefault();
        	wp.media.frames.modula.open();
        });

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

	generateSingleImage: function( attachment ){
		var data = { halign: 'center', valign: 'middle', link: '', target: '' }
			captionSource = wp.Modula.Settings.get( 'wp_field_caption' ),
			titleSource = wp.Modula.Settings.get( 'wp_field_title' );

		data['full']      = attachment['sizes']['full']['url'];
		data['thumbnail'] = attachment['sizes']['thumbnail']['url'];
		data['id']        = attachment['id'];
		data['alt']       = attachment['alt'];

		// Check from where to populate image title
		if ( 'none' == titleSource ) {
			data['title'] = '';
		}else if ( 'title' == titleSource ) {
			data['title'] = attachment['title'];
		}else if ( 'description' == titleSource ) {
			data['title'] = attachment['description'];
		}

		// Check from where to populate image caption
		if ( 'none' == captionSource ) {
			data['caption'] = '';
		}else if ( 'title' == captionSource ) {
			data['caption'] = attachment['title'];
		}else if ( 'caption' == captionSource ) {
			data['caption'] = attachment['caption'];
		}else if ( 'description' == captionSource ) {
			data['caption'] = attachment['description'];
		}

		new modulaItem( data );
	}

};