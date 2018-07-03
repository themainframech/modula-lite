wp.Modula = 'undefined' === typeof( wp.Modula ) ? {} : wp.Modula;

var modulaItemsCollection = Backbone.Collection.extend({

    moveItem: function( model, index ){
        var currentIndex = this.indexOf( model );

        if ( currentIndex != index ) {

            // silence this to stop excess event triggers
            this.remove(model, {silent: true}); 
            this.add(model, {at: index-1});
        }

    }

});

var modulaItem = Backbone.Model.extend( {

    /**
    * Defaults
    * As we always populate this model with existing data, we
    * leave these blank to just show how this model is structured.
    */
    defaults: {
        'id':        '',
        'title':     '',
        'caption':   '',
        'alt':       '',
        'link':      '',
        'halign':    '',
        'valign':    '',
        'target':    '',
        'src':       '',
        'type':      'image',
        'width':     1,
        'height':    1,
        'full' :     '',
        'thumbnail': '',
        'resize':    false,
        'index':     '',
    },

    initialize: function( args ){

  		// Check if wp.Modula.Items exist
  		wp.Modula.Items = 'undefined' === typeof( wp.Modula.Items ) ? new modulaItemsCollection() : wp.Modula.Items;

  		// Add this model to items
  		wp.Modula.Items.add( this );

  		// Set collection index to this model
  		this.set( 'index', wp.Modula.Items.indexOf( this ) );

        // Create item HTML
        var view = new modulaItemView({ model: this });
  		this.set( 'view', view );

        if ( 'custom-grid' == wp.Modula.Settings.get( 'type' ) ) {
            this.set( 'resize', true );
            this.resize();
        }
     

    },

    resize: function() {
        var size = wp.Modula.Resizer.get( 'size' ),
            gutter = wp.Modula.Resizer.get( 'gutter' ),
            columns = wp.Modula.Resizer.get( 'columns' ),
            currentWidth = this.get( 'width' ),
            currentHeight = this.get( 'height' ),
            view = this.get( 'view' ),
            width, height;

        // We will check to see if the image columns is bigger than container columns.
        if ( parseInt( currentWidth ) > parseInt( columns ) ) {
            this.set( 'width', columns );
            currentWidth = columns;
        }

        // We will calculate item width and height based on new gutter and columns
        width = ( size * currentWidth ) + ( ( currentWidth - 1 ) * gutter );
        height = ( size * currentHeight ) + ( ( currentHeight - 1 ) * gutter );

        view.$el.width( width );
        view.$el.height( height );

        // We need to render our view with new attributes
        this.get( 'view' ).render();

    },

    delete: function(){

    	this.trigger('destroy', this, this.collection, {});
    	this.get( 'view' ).remove();

    },

} );

var modulaItemView = Backbone.View.extend({

	/**
    * The Tag Name and Tag's Class(es)
    */
    tagName:    'div',
    className:  'modula-single-image',
    fitTimeout: false,


	/**
    * Template
    * - The template to load inside the above tagName element
    */
    template:   wp.template( 'modula-image' ),

    /**
    * Events
    * - Functions to call when specific events occur
    */
	events: {
		'click .modula-edit-image'  :   'editImage',
		'click .modula-delete-image':   'deleteImage',
        'resize'                    :   'resizeImage',
        'resizestop'                :   'resizeStop',
        'modula:updateIndex'        :   'updateIndex',
    },

    initialize: function( args ) {

        // append element to DOM
        wp.Modula.GalleryView.container.append( this.render().$el );

    	// Listen if we need to enable/disable resize.
        this.listenTo( wp.Modula.Settings, 'change:type', this.checkSettingsType );

        // Enable current gallery type
        this.checkGalleryType( wp.Modula.Settings.get( 'type' ) );

        if ( this.model.get( 'resize' ) ) {
            wp.Modula.GalleryView.container.packery( 'appended', this.$el );
            wp.Modula.GalleryView.container.packery();
        }

    },

    editImage: function( event ){
    	event.preventDefault();
    	// Open Modula Modal
    	wp.Modula.EditModal.open( this.model );
    },

    deleteImage: function( event ){
    	event.preventDefault();

    	this.model.delete();
    },

    checkSettingsType: function( model, value ) {
        this.checkGalleryType( value );
    },

    checkGalleryType: function( type ) {
        var isResizeble = this.model.get( 'resize' ),
            view = this;

        if ( 'custom-grid' == type && ! isResizeble ) {
            var size = wp.Modula.Resizer.get( 'size' ),
                gutter = wp.Modula.Resizer.get( 'gutter' ),
                columns = wp.Modula.Resizer.get( 'columns' ),
                currentWidth = this.model.get( 'width' ),
                currentHeight = this.model.get( 'height' ),
                width, height;

            view.model.set( 'resize', true );

            width = ( size * currentWidth ) + ( ( currentWidth - 1 ) * gutter );
            height = ( size * currentHeight ) + ( ( currentHeight - 1 ) * gutter );

            this.$el.draggable();
            this.initResizable();
            
            this.$el.height( height );
            this.$el.width( width );

            wp.Modula.GalleryView.bindDraggabillyEvents( view.$el );
            wp.Modula.GalleryView.resetPackary();
            
        }else if ( 'custom-grid' != type && isResizeble ) {
            this.destroyResizible();
        }

        view.render();

    },

    initResizable: function(){
        var size = wp.Modula.Resizer.get( 'size' );
        
        this.$el.resizable({
            handles: { 
                'se': this.$('.segrip'), 
            },
            minHeight: size,
            minWidth: size,
            maxWidth: wp.Modula.Resizer.get( 'containerSize' ),
            helper: "ui-resizable-helper",
        });
    },

    resizeImage: function( event, ui ) {

        $(event.target).css('z-index','999');
        
        var snap_width = wp.Modula.Resizer.calculateSize( ui.size.width );
        var snap_height = wp.Modula.Resizer.calculateSize( ui.size.height );

        // We need to snap the helper to a grid
        ui.helper.width( snap_width );
        ui.helper.height( snap_height );

        // The element will increase normally 
        ui.element.width( ui.size.width );
        ui.element.height( ui.size.height );

        // wp.Modula.GalleryView.resetPackary();

    },

    resizeStop: function( event, ui ) {
        $(event.target).css('z-index','auto');

        var width = ui.size.width;
        var height = ui.size.height;
        var newWidth = wp.Modula.Resizer.calculateSize( width );
        var newHeight = wp.Modula.Resizer.calculateSize( height );

        this.$el.width( newWidth );
        this.$el.height( newHeight );

        // Update Model Width & height
        this.model.set( 'width', wp.Modula.Resizer.getSizeColumns( width ) );
        this.model.set( 'height', wp.Modula.Resizer.getSizeColumns( height ) );

        // Render our view in order to update  width/height.
        this.render();

        wp.Modula.GalleryView.resetPackary();
    },

    destroyResizible: function() {

        this.model.set( 'resize', false );
        this.$el.draggable( "destroy" );
        this.$el.resizable( "destroy" );
        this.$el.removeAttr("style");

    },

    updateIndex: function( event, data ) {
        this.model.set( 'index', data.index );
        wp.Modula.Items.moveItem( this.model, data.index );
        this.render();

    },

    render: function() {

        // Destroy resizable
        if ( this.$el.is('.ui-resizable') ) {
            this.$el.resizable( "destroy" );
        }

    	// Get HTML
        this.$el.html( this.template( this.model.attributes ) );

        // Enable Resizeble
        if ( this.model.get( 'resize' ) ) {
            this.initResizable();
        }

        // Return
        return this;
    	
    }

});