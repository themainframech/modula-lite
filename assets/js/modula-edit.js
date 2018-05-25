/**
* Image Model
*/
var ModulaImage = Backbone.Model.extend( {

    /**
    * Defaults
    * As we always populate this model with existing data, we
    * leave these blank to just show how this model is structured.
    */
    defaults: {
        'id':       '',
        'title':    '',
        'caption':  '',
        'alt':      '',
        'link':     '',
        'halign':   '',
        'valign':   '',
        'target':   '',
        'src':      '',
    },

} );

/**
* Images Collection
* - Comprises of all images in an Modula Gallery
* - Each image is represented by an ModulaImage Model
*/
var ModulaGalleryImages = new Backbone.Collection;

/**
* Modal Window
* - Used by most Modula Backbone views to display information e.g. bulk edit, edit single image etc.
*/
if ( typeof ModulaGalleryModalWindow == 'undefined' ) {
    var ModulaGalleryModalWindow = new wp.media.view.Modal( {
        controller: {
            trigger: function() {
            }
        }
    } );
}

/**
* View
*/
var ModulaGalleryEditView = wp.Backbone.View.extend( {

    /**
    * The Tag Name and Tag's Class(es)
    */
    tagName:    'div',
    className:  'edit-attachment-frame mode-select hide-menu hide-router modula-edit-popup',

    /**
    * Template
    * - The template to load inside the above tagName element
    */
    template:   wp.template( 'modula-image-editor' ),

    /**
    * Events
    * - Functions to call when specific events occur
    */
    events: {
        'click .edit-media-header .left':               'loadPreviousItem',
        'click .edit-media-header .right':              'loadNextItem',

        'keyup input':                                  'updateItem',
        'keyup textarea':                               'updateItem',
        'change input':                                 'updateItem',
        'change textarea':                              'updateItem',
        'blur textarea':                                'updateItem',
        'change select':                                'updateItem',

        'click .actions a.modula-gallery-meta-submit':  'saveItem',

        'keyup input#link-search':                      'searchLinks',
        'click div.query-results li':                   'insertLink',

    },

    /**
    * Initialize
    *
    * @param object model   ModulaImage Backbone Model
    */
    initialize: function( args ) {

        // Define loading and loaded events, which update the UI with what's happening.
        this.on( 'loading', this.loading, this );
        this.on( 'loaded',  this.loaded, this );

        // Set some flags
        this.is_loading = false;
        this.collection = args.collection;
        this.child_views = args.child_views;
        this.attachment_id = args.attachment_id;
        this.attachment_index = 0;
        this.search_timer = '';

        // Get the model from the collection
        var count = 0;
        this.collection.each( function( model ) {
            // If this model's id matches the attachment id, this is the model we want
            if ( model.get( 'id' ) == this.attachment_id ) {
                this.model = model;
                this.attachment_index = count;
                return false;
            }

            // Increment the index count
            count++;
        }, this );

    },

    /**
    * Render
    * - Binds the model to the view, so we populate the view's fields and data
    */
    render: function() {

        // Get HTML
        this.$el.html( this.template( this.model.attributes ) );

        // If any child views exist, render them now
        if ( this.child_views.length > 0 ) {
            this.child_views.forEach( function( view ) {
                // Init with model
                var child_view = new view( {
                    model: this.model
                } );

                // Render view within our main view
                this.$el.find( 'div.modula-addons' ).append( child_view.render().el );
            }, this );
        }

        // Set caption
        this.$el.find( 'textarea[name=caption]' ).val( this.model.get( 'caption' ) );

        // Enable / disable the buttons depending on the index
        if ( this.attachment_index == 0 ) {
            // Disable left button
            this.$el.find( 'button.left' ).addClass( 'disabled' );
        }
        if ( this.attachment_index == ( this.collection.length - 1 ) ) {
            // Disable right button
            this.$el.find( 'button.right' ).addClass( 'disabled' );
        }

        // Return
        return this;

    },

    /**
    * Renders an error using
    * wp.media.view.ModulaGalleryError
    */
    renderError: function( error ) {

        // Define model
        var model = {};
        model.error = error;

        // Define view
        var view = new wp.media.view.ModulaGalleryError( {
            model: model
        } );

        // Return rendered view
        return view.render().el;

    },

    /**
    * Tells the view we're loading by displaying a spinner
    */
    loading: function() {

        // Set a flag so we know we're loading data
        this.is_loading = true;

        // Show the spinner
        this.$el.find( '.spinner' ).css( 'visibility', 'visible' );

    },

    /**
    * Hides the loading spinner
    */
    loaded: function( response ) {

        // Set a flag so we know we're not loading anything now
        this.is_loading = false;

        // Hide the spinner
        this.$el.find( '.spinner' ).css( 'visibility', 'hidden' );

        // Display the error message, if it's provided
        if ( typeof response !== 'undefined' ) {
            this.$el.find( 'div.media-toolbar' ).after( this.renderError( response ) );
        }

    },

    /**
    * Load the previous model in the collection
    */
    loadPreviousItem: function() {

        // Decrement the index
        this.attachment_index--;

        // Get the model at the new index from the collection
        this.model = this.collection.at( this.attachment_index );

        // Update the attachment_id
        this.attachment_id = this.model.get( 'id' );

        // Re-render the view
        this.render();

    },

    /**
    * Load the next model in the collection
    */
    loadNextItem: function() {

        // Increment the index
        this.attachment_index++;

        // Get the model at the new index from the collection
        this.model = this.collection.at( this.attachment_index );

        // Update the attachment_id
        this.attachment_id = this.model.get( 'id' );

        // Re-render the view
        this.render();

    },

    /**
    * Updates the model based on the changed view data
    */
    updateItem: function( event ) {

        // Check if the target has a name. If not, it's not a model value we want to store
        if ( event.target.name == '' ) {
            return;
        }

        // Update the model's value, depending on the input type
        if ( event.target.type == 'checkbox' ) {
            value = ( event.target.checked ? event.target.value : 0 );
        } else {
            value = event.target.value;
        }

        // Update the model
        this.model.set( event.target.name, value );

    },

    /**
    * Saves the image metadata
    */
    saveItem: function( event ) {
        var attach_id,
            data,
            image,
            html,
            template = wp.template( 'modula-single-image' );

	    event.preventDefault();

        // Tell the View we're loading
        this.trigger( 'loading' );


        attach_id = this.model.get( 'id' );
        data = this.model.attributes;
        image = $( '.modula-single-image[data-id="' + attach_id + '"]' );
        html = template( data );
        image.replaceWith( html );

        // Tell the view we've finished successfully
        this.trigger( 'loaded loaded:success' );

        // Show the user the 'saved' notice for 1.5 seconds
        var saved = this.$el.find( '.saved' );
        saved.fadeIn();
        setTimeout( function() {
            saved.fadeOut();
        }, 1500 );

    },

    /**
    * Searches Links
    */
    searchLinks: function( event ) {


    },

    /**
    * Inserts the clicked link into the URL field
    */
    insertLink: function( event ) {



    },

} );

/**
* Sub Views
* - Addons must populate this array with their own Backbone Views, which will be appended
* to the settings region
*/
var ModulaGalleryChildViews = [];

/**
* DOM
*/
jQuery( document ).ready( function( $ ) {

    // Edit Image
    $( document ).on( 'click', '#modula-uploader-container a.modula-edit-image', function( e ) {

        // Prevent default action
        e.preventDefault();

        // Clear the collection
        ModulaGalleryImages.reset();

        // Add images to collection
        $.each( modulaGallery.attachments, function( index, image ){

            // var attachment = wp.media.attachment( image['id'] );
            // image['src'] = attachment['attributes']['sizes']['full']['url']
            image['alt'] = ModulaGalleryStripslashes( image['alt'] );

            // Add the model to the collection
            ModulaGalleryImages.add( new ModulaImage( image ) );
        });

        // Get the selected attachment
        var attachment_id = $( this ).parents( '.modula-single-image' ).data( 'id' );

        // Pass the collection of images for this gallery to the modal view, as well
        // as the selected attachment
        ModulaGalleryModalWindow.content( new ModulaGalleryEditView( {
            collection:     ModulaGalleryImages,
            child_views:    ModulaGalleryChildViews,
            attachment_id:  attachment_id,
        } ) );

        // Open the modal window
        ModulaGalleryModalWindow.open();

    } );

} );

/**
* Strips slashes from the given string, which may have been added to escape certain characters
*
* @since 1.4.2.0
*
* @param    string  str     String
* @return   string          String without slashes
*/
function ModulaGalleryStripslashes( str ) {

    return (str + '').replace(/\\(.?)/g, function(s, n1) {
        switch (n1) {
            case '\\':
                return '\\';
            case '0':
                return '\u0000';
            case '':
                return '';
            default:
                return n1;
        }
    });

}