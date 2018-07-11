wp.Modula = 'undefined' === typeof( wp.Modula ) ? {} : wp.Modula;

var ModulaToolbar = wp.media.view.Toolbar.Select.extend({
	clickSelect: function() {

		var controller = this.controller,
			state = controller.state(),
			selection = state.get('selection');

		controller.close();
		state.trigger( 'insert', selection ).reset();

	}
});

var ModulaAttachmentsBrowser = wp.media.view.AttachmentsBrowser.extend({
	createToolbar: function() {
		var LibraryViewSwitcher, Filters, toolbarOptions;

		toolbarOptions = {
			controller: this.controller
		};

		if ( this.controller.isModeActive( 'grid' ) ) {
			toolbarOptions.className = 'media-toolbar wp-filter';
		}

		/**
		* @member {wp.media.view.Toolbar}
		*/
		this.toolbar = new wp.media.view.Toolbar( toolbarOptions );

		this.views.add( this.toolbar );

		this.toolbar.set( 'spinner', new wp.media.view.Spinner({
			priority: -60
		}) );

		this.toolbar.set( 'modula-error', new ModulaError({
			controller: this.controller,
			priority: -80
		}) );

		if ( this.options.search ) {
			// Search is an input, screen reader text needs to be rendered before
			this.toolbar.set( 'searchLabel', new wp.media.view.Label({
				value: wp.media.view.l10n.searchMediaLabel,
				attributes: {
					'for': 'media-search-input'
				},
				priority:   60
			}).render() );
			this.toolbar.set( 'search', new wp.media.view.Search({
				controller: this.controller,
				model:      this.collection.props,
				priority:   60
			}).render() );
		}
	},
});

var ModulaFrame = wp.media.view.MediaFrame.Select.extend({

	className: 'media-frame modula-media-modal',

	createStates: function() {
		var options = this.options;

		if ( this.options.states ) {
			return;
		}

		// Add the default states.
		this.states.add([
			// Main states.
			new ModulaLibrary({
				library:   wp.media.query( options.library ),
				multiple:  options.multiple,
				title:     options.title,
				priority:  20
			})
		]);
	},

	createSelectToolbar: function( toolbar, options ) {
		options = options || this.options.button || {};
		options.controller = this;

		toolbar.view = new ModulaToolbar( options );
	},

	browseContent: function( contentRegion ) {
		var state = this.state();

		// this.$el.removeClass('hide-toolbar');

		// Browse our library of attachments.
		contentRegion.view = new ModulaAttachmentsBrowser({
			controller: this,
			collection: state.get('library'),
			selection:  state.get('selection'),
			model:      state,
			sortable:   state.get('sortable'),
			search:     state.get('searchable'),
			filters:    state.get('filterable'),
			date:       state.get('date'),
			display:    state.has('display') ? state.get('display') : state.get('displaySettings'),
			dragInfo:   state.get('dragInfo'),

			idealColumnWidth: state.get('idealColumnWidth'),
			suggestedWidth:   state.get('suggestedWidth'),
			suggestedHeight:  state.get('suggestedHeight'),

			AttachmentView: state.get('AttachmentView')
		});
	},

});

var ModulaSelection = wp.media.model.Selection.extend({

	add: function( models, options ) {
		var needed, differences;

		if ( ! this.multiple ) {
			this.remove( this.models );
		}

		if ( this.length >= 20 ) {
			models = [];
			wp.media.frames.modula.trigger( 'modula:show-error', {'message' : modulaHelper.strings.limitExceeded } );
		}else{

			needed = 20 - this.length;

			if ( Array.isArray( models ) && models.length > 1 ) {
				// Create an array with elements that we don't have in our selection
				differences = _.difference( _.pluck(models, 'cid'), _.pluck(this.models, 'cid') );

				// Check if we have mode elements that we need
				if ( differences.length > needed ) {
					// Filter our models, to have only that we don't have already
					models = _.filter( models, function( model ){
						return _.contains( differences, model.cid );
					});
					// Get only how many we need.
					models = models.slice( 0, needed );
					wp.media.frames.modula.trigger( 'modula:show-error', {'message' : modulaHelper.strings.limitExceeded } );
				}

			}

		}

		/**
		 * call 'add' directly on the parent class
		 */
		return wp.media.model.Attachments.prototype.add.call( this, models, options );
	},

});

var ModulaLibrary = wp.media.controller.Library.extend({

	initialize: function() {
		var selection = this.get('selection'),
			props;

		if ( ! this.get('library') ) {
			this.set( 'library', wp.media.query() );
		}

		if ( ! ( selection instanceof ModulaSelection ) ) {
			props = selection;

			if ( ! props ) {
				props = this.get('library').props.toJSON();
				props = _.omit( props, 'orderby', 'query' );
			}

			this.set( 'selection', new ModulaSelection( null, {
				multiple: this.get('multiple'),
				props: props
			}) );
		}

		this.resetDisplays();
	},

});

var ModulaError = wp.media.View.extend({
	tagName:   'div',
	className: 'modula-error-container hide',
	errorTimeout: false,
	delay: 400,
	message: '',

	initialize: function() {

		this.controller.on( 'modula:show-error', this.show, this );
		this.controller.on( 'modula:hide-error', this.hide, this );

		this.render();
	},

	show: function( e ) {

		if ( 'undefined' !== typeof e.message ) {
			this.message = e.message;
		}

		if ( '' != this.message ) {
			this.render();
			this.$el.removeClass( 'hide' );
		}

	},

	hide: function() {
		this.$el.addClass( 'hide' );
	},

	render: function() {
		var html = '<div class="modula-error"><span>' + this.message + '</span></div>';
		this.$el.html( html );
	}
});

wp.Modula.uploadHandler = {
	uploaderOptions: {
		container: $( '#modula-uploader-container' ),
		browser: $( '#modula-uploader-browser' ),
		dropzone: $( '#modula-uploader-container' ),
		max_files: 20,
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
			limitExceeded = false,
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
			if ( wp.Modula.Items.length < 20 ) {
				modulaGalleryObject.generateSingleImage( response['data'] );
			}else{
				modulaGalleryObject.limitExceeded = true;
			}
			
		});

		// Files Uploaded - hide progress bar
		uploader.uploader.bind( 'UploadComplete', function() {
			setTimeout( function() {
                modulaGalleryObject.containerUploader.removeClass( 'show-progress' );
            }, 1000 );

			if ( modulaGalleryObject.limitExceeded ) {
				modulaGalleryObject.limitExceeded= false;
				wp.media.frames.modula.open();
				wp.media.frames.modula.trigger( 'modula:show-error', {'message' : modulaHelper.strings.limitExceeded } );
			}

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
        wp.media.frames.modula = new ModulaFrame( {
            frame: 'select',
            reset: false,
            title:  wp.media.view.l10n.addToGalleryTitle,
            button: {
                text: wp.media.view.l10n.addToGallery,
            },
            multiple: 'add',
        } );

        // Mark existing Gallery images as selected when the modal is opened
        wp.media.frames.modula.on( 'open', function() {

            // Get any previously selected images
            var selection = wp.media.frames.modula.state().get( 'selection' );
            selection.reset();

            // Get images that already exist in the gallery, and select each one in the modal
            wp.Modula.Items.each( function( item ) {
            	var image = wp.media.attachment( item.get( 'id' ) );
                selection.add( image ? [ image ] : [] );
            });

            selection.single( selection.last() );

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
            		wp.Modula.Items.addItem( currentModel );
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
		if ( undefined == attachment['sizes']['thumbnail'] ) {
			data['thumbnail']      = attachment['sizes']['full']['url'];
		}else{
			data['thumbnail'] = attachment['sizes']['thumbnail']['url'];
		}
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