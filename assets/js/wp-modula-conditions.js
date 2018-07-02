wp.Modula = 'undefined' === typeof( wp.Modula ) ? {} : wp.Modula;

var modulaGalleryConditions = Backbone.Model.extend({

	initialize: function( args ){

		var rows = $('.modula-settings-container tr[data-container]');
		this.set( 'rows', rows );

		this.initEvents();
		this.initValues();

	},

	initEvents: function(){

		this.listenTo( wp.Modula.Settings, 'change:type', this.changedType );

	},

	initValues: function(){

		this.changedType( false, wp.Modula.Settings.get( 'type' ) );

	},

	changedType: function( settings, value ){
		var rows = this.get( 'rows' );
		
		if ( 'custom-grid' == value ) {
			
			rows.filter( '[data-container="columns"], [data-container="gutter"]' ).show();
			rows.filter( '[data-container="width"], [data-container="height"], [data-container="img_size"], [data-container="margin"], [data-container="randomFactor"], [data-container="shuffle"]' ).hide();

		}else if ( 'creative-gallery' ) {

			rows.filter( '[data-container="columns"], [data-container="gutter"]' ).hide();
			rows.filter( '[data-container="width"], [data-container="height"], [data-container="img_size"], [data-container="margin"], [data-container="randomFactor"], [data-container="shuffle"]' ).show();

		}

	}



});