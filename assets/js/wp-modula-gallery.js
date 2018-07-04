wp.Modula = 'undefined' === typeof( wp.Modula ) ? {} : wp.Modula;

var modulaGalleryResizer = Backbone.Model.extend({
	defaults: {
        'columns': 6,
        'gutter': 10,
        'containerSize': false,
        'size': false,
    },
    initialize: function( args ){


        if ( 'undefined' != typeof args && 'undefined' != typeof args.galleryView ) {
            this.set( 'containerSize', args.galleryView.container.width() );
        }else{
           this.set( 'containerSize', wp.Modula.GalleryView.container.width() ); 
        }
    	
        // Get options
        this.set( 'columns', parseInt( wp.Modula.Settings.get('columns') ) );
        this.set( 'gutter', parseInt( wp.Modula.Settings.get('gutter') ) );

    	// calculate block size.
    	this.generateSize();

        // Listen to column and gutter change
        this.listenTo( wp.Modula.Settings, 'change:columns', this.changeColumns );
        this.listenTo( wp.Modula.Settings, 'change:gutter', this.changeGutter );
    },
    generateSize: function(){
    	var columns = this.get( 'columns' ),
    		gutter = this.get( 'gutter' ),
    		containerWidth = this.get( 'containerSize' ),
    		size;

    	/* 
    	   We will calculate the size ( width and height, because every item is a square ) of an item.
		   The formula is : from the container size we will subtract gutter * number of columns and then we will dived by number of columns
    	 */
    	size = Math.floor( ( containerWidth - ( gutter * ( columns -1 ) ) ) / columns );
    	this.set( 'size', size );
    },
    /* 
       Here we will calculate the new size of the item.
       This will be called after resize event, that means the item is resized and we need to check it.
       currentSize is the new size of the item after we resized it.
     */
    calculateSize: function( currentSize ){
    	var size = this.get( 'size' ),
    		columns = Math.round( currentSize / size ),
    		gutter = this.get( 'gutter' ),
            containerColumns = this.get( 'columns' ),
    		correctSize;

        if ( columns > containerColumns ) {
            columns = containerColumns;
        }

    	correctSize = size * columns + ( gutter * ( columns - 1 ) );
    	return correctSize;
    },

    // Get columns from width/height
    getSizeColumns: function( currentSize ){
        var size = this.get( 'size' );
        return Math.round( currentSize / size );
    },

    resizeItems: function(){

        // Generate new sizes.
        this.generateSize();

        if ( 'undefined' != typeof wp.Modula.Items && wp.Modula.Items.length > 0 ) {

            // Resize all items when gutter or columns have changed.
            wp.Modula.Items.each( function( item ){
                // console.log( item );
                item.resize();
            });

        }

        // Change packary columnWidth & columnHeight
        wp.Modula.GalleryView.setPackaryOption( 'columnWidth', this.get( 'size' ) );
        wp.Modula.GalleryView.setPackaryOption( 'rowHeight', this.get( 'size' ) );

        // Update Grid
        wp.Modula.GalleryView.setPackaryOption( 'gutter', parseInt( this.get( 'gutter' ) ) );

        // Reset Packary
        wp.Modula.GalleryView.resetPackary();

    },

    changeColumns: function( model, value ){
        this.set( 'columns', value );

        // Resize all gallery items
        this.resizeItems();

    },

    changeGutter: function( model, value ){
        this.set( 'gutter', parseInt( value ) );

        // Resize all gallery items
        this.resizeItems();

    }

});

var modulaGalleryView = Backbone.View.extend({

	isSortable : false,
	isResizeble: false,
    refreshTimeout: false,
    updateIndexTimeout: false,

	initialize: function( args ) {

		// This is the container where the gallery items are.
		this.container = this.$el.find( '.modula-uploader-inline-content' );

		// Listent when gallery type is changing.
    	this.listenTo( wp.Modula.Settings, 'change:type', this.checkSettingsType );

    	// Enable current gallery type
    	this.checkGalleryType( wp.Modula.Settings.get( 'type' ) );

    },

    checkSettingsType: function( model, value ) {
    	this.checkGalleryType( value );
    },

    checkGalleryType: function( type ) {

        if ( 'creative-gallery' == type ) {

        	// If resizeble is enable we will destroy it
        	if ( this.isResizeble ) {
        		this.disableResizeble();
        	}

        	// If sortable is not enabled, we will initialize it.
        	if ( ! this.isSortable ) {
        		this.enableSortable();
        	}

        }else if ( 'custom-grid' == type ) {

        	// If sortable is enable we will destroy it
        	if ( this.isSortable ) {
        		this.disableSortable();
        	}

        	// If resizeble is not enabled, we will initialize it.
        	if ( ! this.isResizeble ) {
        		this.enableResizeble();
        	}

        }
    },

    enableSortable: function() {
    	this.isSortable = true;
    	this.container.sortable( {
	        items: '.modula-single-image',
	        cursor: 'move',
	        forcePlaceholderSize: true,
	        placeholder: 'modula-single-image-placeholder'
	    } );
    },

    disableSortable: function() {
    	this.isSortable = false;
    	this.container.sortable( 'destroy' );
    },

    enableResizeble: function() {
    	this.isResizeble = true;
        this.$el.addClass( 'modula-resizer-enabled' );

        if ( 'undefined' == typeof wp.Modula.Resizer ) {
            wp.Modula.Resizer = new modulaGalleryResizer({ 'galleryView': this });
        }

        console.log( wp.Modula.Resizer.get( 'size' ) );

    	this.container.packery({
    		itemSelector: '.modula-single-image',
            gutter: parseInt( wp.Modula.Resizer.get( 'gutter' ) ),
            columnWidth: wp.Modula.Resizer.get( 'size' ),
            rowHeight: wp.Modula.Resizer.get( 'size' ),
		});

        this.container.on( 'layoutComplete', this.updateItemsIndex );
        this.container.on( 'dragItemPositioned', this.updateItemsIndex );
    },

    disableResizeble: function() {
		this.isResizeble = false;
        this.$el.removeClass( 'modula-resizer-enabled' );
        this.container.packery( 'destroy' );
    },

    bindDraggabillyEvents: function( item ){
    	if ( this.isResizeble ) {
    		this.container.packery( 'bindUIDraggableEvents', item );
    	}
    },

    resetPackary: function() {
        var view = this;

        if ( this.refreshTimeout ) {
            clearTimeout( this.refreshTimeout );
        }

        this.refreshTimeout = setTimeout(function () {        
            view.container.packery();
        }, 200);

    },

    updateItemsIndex: function(){

        var container = this;

        if ( this.updateIndexTimeout ) {
            clearTimeout( this.updateIndexTimeout );
        }
        
        this.updateIndexTimeout = setTimeout( function() {
            var items = $(container).packery('getItemElements');
            $( items ).each( function( i, itemElem ) {
                $( itemElem ).trigger( 'modula:updateIndex', { 'index': i } );
            });
        }, 200);

    },

    setPackaryOption: function( option, value ){

        var packaryOptions = this.container.data('packery');
        packaryOptions.options[ option ] = value;

    },

});