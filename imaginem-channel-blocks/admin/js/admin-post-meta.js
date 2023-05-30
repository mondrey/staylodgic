jQuery(document).ready(function($) {
	"use strict";

    $(".metabox-image-radio-selector").on('click', function() {
    	var check_radio_selector = $(this).data('holder');
    	$("#"+check_radio_selector).prop("checked", true);
    });

  /**
    * Google Fonts
    */
  function MetaBoxGoogleFontSelect( slctr, mainID ){

    var _selected = $(slctr).val();
    var _fontname = $(slctr).find(':selected').data('font');
    var _linkclass = 'style_link_'+ mainID;
    var _previewer = mainID +'_metabox_googlefont_previewer';

    if( _selected ){
      $('.'+ _previewer ).fadeIn();

      if ( _selected !== '0' && _selected !== 'Default Font' ) {

        $( '.'+ _linkclass ).remove();

        var the_font = _selected.replace(/\s+/g, '+');

        $('head').append('<link href="https://fonts.googleapis.com/css?family='+ the_font +'" rel="stylesheet" class="'+ _linkclass +'">');

        $('.'+ _previewer ).css('font-family', _fontname +', sans-serif' );

      } else {
        $('.'+ _previewer ).css('font-family', '' );
        $('.'+ _previewer ).fadeOut();
      }

    }

  }

  //init for each element
  jQuery( '.metabox_google_font_select' ).each(function(){
    var mainID = jQuery(this).attr('id');
    MetaBoxGoogleFontSelect( this, mainID );
  });

  //init when value is changed
  jQuery( '.metabox_google_font_select' ).change(function(){
    var mainID = jQuery(this).attr('id');
    MetaBoxGoogleFontSelect( this, mainID );
  });

	//Implement color picker
	jQuery('.colorSwatch').wpColorPicker();

	if ($.fn.chosen) {
		$('.chosen-select-metabox').chosen();
	}

	var sidebarlist;
	sidebarlist = $('.page_style img.of-radio-img-selected').attr("data-value");
	if (sidebarlist=="nosidebar") {
		$('.sidebar_choice').hide();
	}

	var videoembedcode;
	videoembedcode = $('.portfolio_header img.of-radio-img-selected').attr("data-value");

	if (videoembedcode!="Video") {
		$('.videoembed').hide();
	}

	var linkmethod;
	linkmethod = $('.thumbnail_linktype img.of-radio-img-selected').attr("data-value");

	if (linkmethod=="meta_thumbnail_direct") {
		$('.portfoliolinktype').hide();
	}

	$(".of-radio-img-selected").each(function(){
		var toggleClass=$(this).parent().find('span').attr("data-toggleClass");
		var toggleAction=$(this).parent().find('span').attr("data-toggleAction");
		var toggleTrigger=$(this).parent().find('span').attr("data-toggleTrigger");
		var toggleID=$(this).parent().find('span').attr("data-toggleID");
		var toggleClass=$(this).parent().find('span').attr("data-toggleClass");
		var parentclass=$(this).parent().find('span').attr("data-parentclass");
		var SteppingStone=$(this).attr("data-value");

		if ( $(this).parent().hasClass('trigger_element') ) {
			$('.'+parentclass+'-trigger').hide();
			$('.'+parentclass+'-'+SteppingStone).show();
		}
	});

	// Image Options
	$('.of-radio-img-img').on('click', function() {
		$(this).parent().find('.of-radio-img-img').removeClass('of-radio-img-selected');
		$(this).addClass('of-radio-img-selected');

		var toggleClass=$(this).parent().find('span').attr("data-toggleClass");
		var toggleAction=$(this).parent().find('span').attr("data-toggleAction");
		var toggleTrigger=$(this).parent().find('span').attr("data-toggleTrigger");
		var toggleID=$(this).parent().find('span').attr("data-toggleID");
		var toggleClass=$(this).parent().find('span').attr("data-toggleClass");
		var parentclass=$(this).parent().find('span').attr("data-parentclass");
		var SteppingStone=$(this).attr("data-value");

		if ( $(this).parent().hasClass('trigger_element') ) {
			$('.'+parentclass+'-trigger').hide();
			$('.'+parentclass+'-'+SteppingStone).show();
		}
	});


	//jQuery( ".ranger-bar :text" ).slider();
	$('.ranger-bar :text').each(function(index) {
	                // get input ID
					var inputField = $(this);
	                var inputId = $(this).attr('id');
	                // get input value
	                var inputValue = parseInt($(this).val());
	                // get input max
	                var inputMin = parseInt($(this).attr('min'));
					var inputMax = parseInt($(this).attr('max'));

	                $('#'+inputId+'_slider').slider({
						range: "min",
	                    value: inputValue,
	                    max: inputMax,
	                    min: inputMin,
	                    slide: function(event, ui){

	                        $(inputField).val(ui.value);

	                    }
	                });
	            });
	
				$( ".ranger-bar :text" ).change(function() {
					var inputField = $(this);
					var inputId = $(this).attr('id');
					var inputMin = parseInt($(this).attr('min'));
					var inputMax = parseInt($(this).attr('max'));
					var inputValue = parseInt($(this).val());
					
					if (inputValue > inputMax ) { 
						inputValue=inputMax;
						$(inputField).val(inputValue);
					}
					if (inputValue < inputMin ) { 
						inputValue=inputMin;
						$(inputField).val(inputValue);
					}
				    $('#'+inputId+'_slider').slider( "value", inputValue );
				});
	

	jQuery(".selectbox-wrap select").each(function(){
	  jQuery(this).wrap('<div class="selectbox"/>');
		jQuery(this).after("<span class='selecttext'></span><span class='select-arrow'></span>");
		var val = jQuery(this).children("option:selected").text();
		jQuery(this).next(".selecttext").text(val);
		jQuery(this).change(function(){
			var val = jQuery(this).children("option:selected").text();
			jQuery(this).next(".selecttext").text(val);
		});
	});

    function responsiveDataFields() {
    	
		$(document).on('focus', '.responsive-data-text', function(){
	    	var inResponsiveCue = $(this).prev('.responsive-data-media');
			$(this).keyup(function(e) {
				var responsiveText = $(this).val();
				var responsiveData = responsiveText.split(",");

				var responsiveDataDesktop = responsiveData[0];
				var responsiveDataTablet = responsiveData[0];
				var responsiveDataMobile = responsiveData[0];

				if(typeof responsiveData[1] !== 'undefined') {
					responsiveDataTablet = responsiveData[1];
				}
				if ( responsiveDataTablet ) {
					responsiveDataMobile = responsiveDataTablet;
				}
				if(typeof responsiveData[2] !== 'undefined') {
					responsiveDataMobile = responsiveData[2];
				}

				inResponsiveCue.find(".responsive-data-desktop").text(responsiveDataDesktop.trim());
				inResponsiveCue.find(".responsive-data-tablet").text(responsiveDataTablet.trim());
				inResponsiveCue.find(".responsive-data-mobile").text(responsiveDataMobile.trim());

			});
	    });

    }
    responsiveDataFields();



    function singleImageUpload() {
	    // ******* Uploader Function
	    var custom_uploader,curr_upload_button,input_field_id,attachment;

	    $('.button-shortcodegen-uploader').on('click', function(e) {
	        e.preventDefault();

	        curr_upload_button = $(this);

	        input_field_id = $(this).data("id");

	        //If the uploader object has already been created, reopen the dialog
	        if (custom_uploader) {
	            custom_uploader.open();
	            return;
	        }

	        //Extend the wp.media object
	        custom_uploader = wp.media.frames.file_frame = wp.media({
	            title: 'Choose Image',
	            button: {
	                text: 'Choose Image'
	            },
	            multiple: false
	        });

	        //When a file is selected, grab the URL and set it as the text field's value
	        custom_uploader.on('select', function() {
	            attachment = custom_uploader.state().get('selection').first().toJSON();
	            $(curr_upload_button).prev('input#' + input_field_id ).val(attachment.url);
	            //$('#' + input_field_id ).val(attachment.url);
	        });

	        //Open the uploader dialog
	        custom_uploader.open();

	    });
	}
	singleImageUpload();
    
    // ******* Multi Upload Function
	var frame,
	    images = themecore_admin_vars.post_gallery,
	    proofingimages = themecore_admin_vars.proofing_gallery,
	    proofingSelection = proofingLoadImages(proofingimages),
	    selection = loadImages(images);

	$('body').addClass('mtheme-admin-core-on');

	$('#mtheme_images_upload').on('click', function(e) {
		e.preventDefault();

		// Set options for 1st frame render
		var options = {
			title: 'Create Featured Gallery',
			state: 'gallery-edit',
			frame: 'post',
			selection: selection
		};

		// Check if frame or gallery already exist
		if( frame || selection ) {
			options['title'] = 'Edit Featured Gallery';
		}

		frame = wp.media(options).open();
		
		// Tweak views
		frame.menu.get('view').unset('cancel');
		frame.menu.get('view').unset('separateCancel');
		frame.menu.get('view').get('gallery-edit').el.innerHTML = 'Edit Featured Gallery';
		frame.content.get('view').sidebar.unset('gallery'); // Hide Gallery Settings in sidebar

		// When we are editing a gallery
		overrideGalleryInsert();
		frame.on( 'toolbar:render:gallery-edit', function() {
			overrideGalleryInsert();
		});
		
		frame.on( 'content:render:browse', function( browser ) {
		    if ( !browser ) return;
		    // Hide Gallery Settings in sidebar
		    browser.sidebar.on('ready', function(){
		        browser.sidebar.unset('gallery');
		    });
		});
		
		// All images removed
		frame.state().get('library').on( 'remove', function() {
		    var models = frame.state().get('library');
			if(models.length == 0){
			    selection = false;
				$.post(ajaxurl, { ids: '', action: 'themecore_save_images', post_id: themecore_admin_vars.post_id, nonce: themecore_admin_vars.nonce });
			}
		});
		
		// Override insert button
		function overrideGalleryInsert() {
			frame.toolbar.get('view').set({
				insert: {
					style: 'primary',
					text: 'Save Featured Gallery',

					click: function() {
						var models = frame.state().get('library'),
						    ids = '';

						models.each( function( attachment ) {
						    ids += attachment.id + ','
						});

						this.el.innerHTML = 'Saving...';
						
						$.ajax({
							type: 'POST',
							url: ajaxurl,
							data: { 
								ids: ids, 
								action: 'themecore_save_images', 
								post_id: themecore_admin_vars.post_id, 
								nonce: themecore_admin_vars.nonce 
							},
							success: function(){
								selection = loadImages(ids);
								$('#_mtheme_image_ids').val( ids );
								frame.close();
							},
							dataType: 'html'
						}).done( function( data ) {
							$('.mtheme-gallery-thumbs').html( data );
						}); 
					}
				}
			});
		}
	});

	$('#mtheme_proofing_images_upload').on('click', function(e) {
		e.preventDefault();

		// Set options for 1st frame render
		var options = {
			title: 'Create Proofing Gallery',
			state: 'gallery-edit',
			frame: 'post',
			selection: proofingSelection
		};

		console.log(options.proofingSelection);

		// Check if frame or gallery already exist
		if( frame || proofingSelection ) {
			options['title'] = 'Edit Proofing Gallery';
		}

		frame = wp.media(options).open();
		
		// Tweak views
		frame.menu.get('view').unset('cancel');
		frame.menu.get('view').unset('separateCancel');
		frame.menu.get('view').get('gallery-edit').el.innerHTML = 'Edit Proofing Gallery';
		frame.content.get('view').sidebar.unset('gallery'); // Hide Gallery Settings in sidebar

		// When we are editing a gallery
		overrideProofingGalleryInsert();
		frame.on( 'toolbar:render:gallery-edit', function() {
			overrideProofingGalleryInsert();
		});
		
		frame.on( 'content:render:browse', function( browser ) {
		    if ( !browser ) return;
		    // Hide Gallery Settings in sidebar
		    browser.sidebar.on('ready', function(){
		        browser.sidebar.unset('gallery');
		    });
		});
		
		// All images removed
		frame.state().get('library').on( 'remove', function() {
		    var models = frame.state().get('library');
			if(models.length == 0){
			    proofingSelection = false;
				$.post(ajaxurl, { ids: '', action: 'themecore_save_proofing_images', post_id: themecore_admin_vars.post_id, nonce: themecore_admin_vars.nonce });
			}
		});
		
		// Override insert button
		function overrideProofingGalleryInsert() {
			frame.toolbar.get('view').set({
				insert: {
					style: 'primary',
					text: 'Save Proofing Gallery',

					click: function() {
						var models = frame.state().get('library'),
						    ids = '';

						models.each( function( attachment ) {
						    ids += attachment.id + ','
						});

						this.el.innerHTML = 'Saving...';
						
						$.ajax({
							type: 'POST',
							url: ajaxurl,
							data: { 
								ids: ids, 
								action: 'themecore_save_proofing_images', 
								post_id: themecore_admin_vars.post_id, 
								nonce: themecore_admin_vars.nonce 
							},
							success: function(){
								proofingSelection = proofingLoadImages(ids);
								$('#_mtheme_proofing_image_ids').val( ids );
								frame.close();
							},
							dataType: 'html'
						}).done( function( data ) {
							$('.mtheme-proofing-gallery-thumbs').html( data );
						}); 
					}
				}
			});
		}
	});
	
	// Load images
	function proofingLoadImages(proofingimages) {
		if( proofingimages ){
		    var shortcode = new wp.shortcode({
				tag:    'gallery',
				attrs:   { ids: proofingimages },
				type:   'single'
			});

		    var attachments = wp.media.gallery.attachments( shortcode );

			var proofingSelection = new wp.media.model.Selection( attachments.models, {
				props:    attachments.props.toJSON(),
				multiple: true
			});

			proofingSelection.gallery = attachments.gallery;
			
			// Fetch the query's attachments, and then break ties from the
			// query to allow for sorting.
			proofingSelection.more().done( function() {
				// Break ties with the query.
				proofingSelection.props.set({ query: false });
				proofingSelection.unmirror();
				proofingSelection.props.unset('orderby');
			});
			
			return proofingSelection;
		}
		
		return false;
	}
	// Load images
	function loadImages(images) {
		if( images ){
		    var shortcode = new wp.shortcode({
				tag:    'gallery',
				attrs:   { ids: images },
				type:   'single'
			});

		    var attachments = wp.media.gallery.attachments( shortcode );

			var selection = new wp.media.model.Selection( attachments.models, {
				props:    attachments.props.toJSON(),
				multiple: true
			});

			selection.gallery = attachments.gallery;
			
			// Fetch the query's attachments, and then break ties from the
			// query to allow for sorting.
			selection.more().done( function() {
				// Break ties with the query.
				selection.props.set({ query: false });
				selection.unmirror();
				selection.props.unset('orderby');
			});
			
			return selection;
		}
		
		return false;
	}



	$('.meta-multi-upload').on('click', function(e) {
		e.preventDefault();

		// Load images
		function multi_loadImages(multi_images) {
			if( multi_images ){
			    var shortcode = new wp.shortcode({
					tag:    'gallery',
					attrs:   { ids: multi_images },
					type:   'single'
				});

			    var attachments = wp.media.gallery.attachments( shortcode );

				var selection = new wp.media.model.Selection( attachments.models, {
					props:    attachments.props.toJSON(),
					multiple: true
				});

				selection.gallery = attachments.gallery;
				
				// Fetch the query's attachments, and then break ties from the
				// query to allow for sorting.
				selection.more().done( function() {
					// Break ties with the query.
					selection.props.set({ query: false });
					selection.unmirror();
					selection.props.unset('orderby');
				});
				
				return selection;
			}
			
			return false;
		}

		var frame,
		    thisInput = $(this),
		    multi_images = $(this).data('imageset'),
		    galleryid = $(this).data('galleryid'),
		    selection = multi_loadImages(multi_images);

		// Set options for 1st frame render
		var options = {
			title: 'Create Featured Gallery',
			state: 'gallery-edit',
			frame: 'post',
			selection: selection
		};

		// Check if frame or gallery already exist
		if( frame || selection ) {
			options['title'] = 'Edit Featured Gallery';
		}

		frame = wp.media(options).open();
		
		// Tweak views
		frame.menu.get('view').unset('cancel');
		frame.menu.get('view').unset('separateCancel');
		frame.menu.get('view').get('gallery-edit').el.innerHTML = 'Edit Featured Gallery';
		frame.content.get('view').sidebar.unset('gallery'); // Hide Gallery Settings in sidebar

		// When we are editing a gallery
		multi_overrideGalleryInsert();
		frame.on( 'toolbar:render:gallery-edit', function() {
			multi_overrideGalleryInsert();
		});
		
		frame.on( 'content:render:browse', function( browser ) {
		    if ( !browser ) return;
		    // Hide Gallery Settings in sidebar
		    browser.sidebar.on('ready', function(){
		        browser.sidebar.unset('gallery');
		    });
		});
		
		// All images removed
		frame.state().get('library').on( 'remove', function() {
		    var models = frame.state().get('library');
			if(models.length == 0){
			    selection = false;
				$.post(ajaxurl, { ids: '', action: 'multo_gallery_save_images', gallerysetid: galleryid, post_id: themecore_admin_vars.post_id, nonce: themecore_admin_vars.nonce });
			}
		});
		
		// Override insert button
		function multi_overrideGalleryInsert() {
			frame.toolbar.get('view').set({
				insert: {
					style: 'primary',
					text: 'Save Featured Gallery',

					click: function() {
						var models = frame.state().get('library'),
						    ids = '';

						models.each( function( attachment ) {
						    ids += attachment.id + ','
						});

						this.el.innerHTML = 'Saving...';
						
						$.ajax({
							type: 'POST',
							url: ajaxurl,
							data: { 
								ids: ids, 
								gallerysetid: galleryid,
								action: 'multo_gallery_save_images', 
								post_id: themecore_admin_vars.post_id, 
								nonce: themecore_admin_vars.nonce 
							},
							success: function(){
								selection = multi_loadImages(ids);
								$('#'+galleryid).val( ids );
								thisInput.data( 'imageset' , ids );
								frame.close();
							},
							dataType: 'html'
						}).done( function( data ) {
							$('.multi-gallery-'+galleryid).html( data );
						}); 
					}
				}
			});
		}
	});

});

(function($) {
    "use strict";

    wp.api.loadPromise.done( function() {

		wp.data.subscribe(function () {

			var imageWrapper = $('#image-meta-box'),linkWrapper = $('#link-meta-box'),videoWrapper = $('#video-meta-box'),quoteWrapper = $('#quote-meta-box'),audioWrapper = $('#audio-meta-box'),videoWrapper = $('#video-meta-box'),galleryWrapper = $('#gallery-meta-box'),imageSelector = $('#post-format-image'),audioSelector = $('#post-format-audio'),quoteSelector = $('#post-format-quote'),linkSelector = $('#post-format-link'),videoSelector = $('#post-format-video'),gallerySelector = $('#post-format-gallery');

			hideAll();
			showCheckedChoice();
	
			$( '.block-editor-page .edit-post-sidebar .components-panel .editor-post-format select' )
				.change(function() {
					var postformatChoice = '';
					postformatChoice = $( this ).val();
					console.log(postformatChoice);
				switch ( postformatChoice ) {
	
					case 'quote' :
					quoteWrapper.css('display', 'block');
					DisplaySelected(quoteWrapper);
					break;
					
					case 'gallery' :
					galleryWrapper.css('display', 'block');
					DisplaySelected(galleryWrapper);
					break;
	
					case 'video' :
					videoWrapper.css('display', 'block');
					DisplaySelected(videoWrapper);
					break;
	
					case 'link' :
					linkWrapper.css('display', 'block');
					DisplaySelected(linkWrapper);
					break;	
					
					case 'image' :
					imageWrapper.css('display', 'block');
					DisplaySelected(imageWrapper);
					break;
					
					case 'audio' :
					audioWrapper.css('display', 'block');
					DisplaySelected(audioWrapper);
					break;
					
					default :
					quoteWrapper.css('display', 'none');
					galleryWrapper.css('display', 'none');
					videoWrapper.css('display', 'none');
					linkWrapper.css('display', 'none');
					audioWrapper.css('display', 'none');
					imageWrapper.css('display', 'none');
	
				}
			})
			.trigger( 'change' );

			var postmetaClassicEditorChoice = jQuery('#post-formats-select input');
			postmetaClassicEditorChoice.change( function() {
			
				var thisElement = jQuery(this).val();
	
				switch ( thisElement ) {
	
					case 'quote' :
					quoteWrapper.css('display', 'block');
					DisplaySelected(quoteWrapper);
					break;
					
					case 'gallery' :
					galleryWrapper.css('display', 'block');
					DisplaySelected(galleryWrapper);
					break;
	
					case 'video' :
					videoWrapper.css('display', 'block');
					DisplaySelected(videoWrapper);
					break;
	
					case 'link' :
					linkWrapper.css('display', 'block');
					DisplaySelected(linkWrapper);
					break;	
					
					case 'image' :
					imageWrapper.css('display', 'block');
					DisplaySelected(imageWrapper);
					break;
					
					case 'audio' :
					audioWrapper.css('display', 'block');
					DisplaySelected(audioWrapper);
					break;
					
					default :
					quoteWrapper.css('display', 'none');
					galleryWrapper.css('display', 'none');
					videoWrapper.css('display', 'none');
					linkWrapper.css('display', 'none');
					audioWrapper.css('display', 'none');
					imageWrapper.css('display', 'none');
	
				}
				
			});
				
			function DisplaySelected(elementSelected) {
				hideAll();
				elementSelected.css('display', 'block');
			}
			
			function hideAll() {
				videoWrapper.css('display', 'none');
				galleryWrapper.css('display', 'none');
				quoteWrapper.css('display', 'none');
				linkWrapper.css('display', 'none');
				audioWrapper.css('display', 'none');
				imageWrapper.css('display', 'none');	
			}
			
			function showCheckedChoice() {
			
				if(quoteSelector.is(':checked')) {
					quoteWrapper.css('display', 'block');
				}
					
				if(linkSelector.is(':checked')) {
					linkWrapper.css('display', 'block');
				}
				
				if(audioSelector.is(':checked')) {
					audioWrapper.css('display', 'block');
				}
				
				if(gallerySelector.is(':checked')) {
					galleryWrapper.css('display', 'block');
				}
				
				if(videoSelector.is(':checked')) {
					videoWrapper.css('display', 'block');
				}
					
				if(imageSelector.is(':checked')) {
					imageWrapper.css('display', 'block');
				}
					
			}
		});

    })
})(jQuery);