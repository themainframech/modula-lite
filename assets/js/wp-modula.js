wp.Modula = 'undefined' === typeof( wp.Modula ) ? {} : wp.Modula;

jQuery( document ).ready( function( $ ){

	// Here we will have all gallery's items.
	wp.Modula.Items = new modulaItemsCollection();
	
	// Settings related objects.
	wp.Modula.Settings = new modulaSettings( modulaHelper.settings );

	// Modula conditions
	wp.Modula.Conditions = new modulaGalleryConditions();

	// Initiate Gallery View
	wp.Modula.GalleryView = new modulaGalleryView({
		'el' : $( '#modula-uploader-container' ),
	});

	// Initiate Modula Resizer
	if ( 'undefined' == typeof wp.Modula.Resizer ) {
		wp.Modula.Resizer = new modulaGalleryResizer();
	}

	// Modula edit item modal.
	wp.Modula.EditModal = new modulaModal();

	// Here we will add items for the gallery to collection.
	if ( 'undefined' !== typeof modulaHelper.items ) {
		$.each( modulaHelper.items, function( index, image ){
			var imageModel = new modulaItem( image );
		});
	}

	// Initiate Modula Gallery Upload
	wp.Modula.uploadHandler.init();

});