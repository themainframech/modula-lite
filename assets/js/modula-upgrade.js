( function( $ ){
	"use strict";

	var modulaUpgrader = {
		counts: 0,
		completed: 0,

		init: function(){
			$('#modula-upgrade-v2').click(function(e){
				e.preventDefault();
				$(this).addClass( 'updating-message' );
				modulaUpgrader.getGalleries();
			});
		},

		getGalleries: function(){

			var opts = {
	            url:      ajaxurl,
	            type:     'post',
	            async:    true,
	            cache:    false,
	            dataType: 'json',
	            data: {
	                action: 'modula-get-old-galleries',
	                nonce:  modulaUpgraderHelper.get_galleries_nonce,
	            },
	            success: function( response ) {
	            	if ( response.status != 'succes' ) {
	            		return;
	            	}

	            	modulaUpgrader.counts = response.galleries_ids.length + 1;
	            	modulaUpgrader.ajaxGalary( response.galleries_ids );

	            	$('#modula-upgrade-v2').hide();
	            	$('.modula-upgrader-progress-bar-container').show();
	            }
	        };
	        $.ajax(opts);

		},

		ajaxGalary: function( galleries_ids ){
			galleries_ids.forEach( function( gallery_id ){
				
				var opts = {
		            url:      ajaxurl,
		            type:     'post',
		            async:    true,
		            cache:    false,
		            dataType: 'json',
		            data: {
		                action: 'modula-upgrade-gallery',
		                nonce:  modulaUpgraderHelper.upgrade_gallery_nonce,
		                gallery_id: gallery_id
		            },
		            success: function( response ) {
		            	if ( response.status != 'succes' ) {
		            		return;
		            	}

		            	modulaUpgrader.completed = modulaUpgrader.completed + 1;
		            	$('.modula-ajax-output').append( '<p>' + response.message + '</p>' );
		            	modulaUpgrader.updateProgress();
		            }
		        };
		        $.ajax(opts);

			});
		},

		updateProgress: function() {
			var width = modulaUpgrader.completed * ( 100 / modulaUpgrader.counts ) + '%';
			$('.modula-upgrader-progress-bar-container .modula-upgrader-progress-bar').css( 'width', width );
			$('.modula-upgrader-progress-bar-container span').text( width );

			if ( modulaUpgrader.completed == modulaUpgrader.counts - 1 ) {
				modulaUpgrader.completeUpgrade();
			}

		},

		completeUpgrade: function() {

			var opts = {
	            url:      ajaxurl,
	            type:     'post',
	            async:    true,
	            cache:    false,
	            dataType: 'json',
	            data: {
	                action: 'modula-complete-upgrade',
	                nonce:  modulaUpgraderHelper.upgrade_complete_nonce,
	            },
	            success: function( response ) {
	            	if ( response.status != 'succes' ) {
	            		return;
	            	}

	            	modulaUpgrader.completed = modulaUpgrader.completed + 1;
	            	$('.modula-ajax-output').append( '<p>' + response.message + '</p>' );
	            	modulaUpgrader.updateProgress();
	            }
	        };
	        $.ajax(opts);
			
		}
	};

	$( document ).ready(function(){
		modulaUpgrader.init();
	});

})( jQuery );