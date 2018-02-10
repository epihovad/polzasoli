/*
JS for the quality selector
*/

/*
Define the base class for the quality selector button.
Most of this code is copied from the _V_.TextTrackButton
class.

https://github.com/zencoder/video-js/blob/master/src/tracks.js#L560)
*/
_V_.ResolutionSelector = _V_.Button.extend({
	
	kind: "quality",
	className: "vjs-quality-button",

	init: function(player, options) {
		
		this._super(player, options);
		
		// Save the starting resolution as a property of the player object
		player.options.currentResolution = this.buttonText;		
		
		this.menu = this.createMenu();
		
		if (this.items.length === 0) {
			this.hide();
		}
	},
	
	createMenu: function() {
		
		var menu = new _V_.Menu(this.player);
		
		// Add a title list item to the top
		menu.el.appendChild(_V_.createElement("li", {
			className: "vjs-menu-title",
			innerHTML: _V_.uc(this.kind)
		}));
		
		this.items = this.createItems();
		
		// Add menu items to the menu
		this.each(this.items, function(item){
			menu.addItem(item);
		});
		
		// Add list to element
		this.addComponent(menu);
		
		return menu;
	},
	
	// Override the default _V_.Button createElement so the button text isn't hidden
	createElement: function(type, attrs) {
		
		// Add standard Aria and Tabindex info
		attrs = _V_.merge({
			className: this.buildCSSClass(),
			innerHTML: '<div><span class="vjs-quality-text">' + this.buttonText + '</span></div>',
			role: "button",
			tabIndex: 0
		}, attrs);
		
		return this._super(type, attrs);
	},
	
	// Create a menu item for each text track
	createItems: function() {
		
		var items = [];
		
		this.each( this.availableRes, function( res ) {

			items.push( new _V_.ResolutionMenuItem( this.player, {
				
				label: res[0].res + 'p',
				src: res
			}));
		});
			
		return items;
	},
	
	buildCSSClass: function() {
		
		return this.className + " vjs-menu-button " + this._super();
	},
	
	// Focus - Add keyboard functionality to element
	onFocus: function() {
		
		// Show the menu, and keep showing when the menu items are in focus
		this.menu.lockShowing();
		this.menu.el.style.display = "block";
		
		// When tabbing through, the menu should hide when focus goes from the last menu item to the next tabbed element.
		_V_.one(this.menu.el.childNodes[this.menu.el.childNodes.length - 1], "blur", this.proxy(function() {
			
			this.menu.unlockShowing();
		}));
	},
	
	// Can't turn off list display that we turned on with focus, because list would go away.
	onBlur: function(){},
	
	onClick: function() {
		
		/*
		When you click the button it adds focus, which will show the menu indefinitely.
		So we'll remove focus when the mouse leaves the button.
		Focus is needed for tab navigation.
		*/
		this.one( 'mouseout', this.proxy(function() {
			
			this.menu.unlockShowing();
			this.el.blur();
		}));
	}
});

/*
Define the base class for the quality menu items
*/
_V_.ResolutionMenuItem = _V_.MenuItem.extend({

	init: function(player, options){
		
		// Modify options for parent MenuItem class's init.
		options.selected = ( options.label === player.options.currentResolution );
		this._super( player, options );
		
		this.player.addEvent( 'changeRes', _V_.proxy( this, this.update ) );
	},
	
	onClick: function() {
		
		// Check that we are changing to a new quality (not the one we are already on)
		if ( this.options.label === this.player.options.currentResolution )
			return;
		
		var current_time = this.player.currentTime();
		
		// Set the button text to the newly chosen quality
		jQuery( this.player.controlBar.el ).find( '.vjs-quality-text' ).html( this.options.label );
		
		// Change the source and make sure we don't start the video over
		this.player.src( this.options.src ).one( 'loadedmetadata', function() {
				
			this.currentTime( current_time );
			this.play();
		});
		
		// Save the newly selected resolution in our player options property
		this.player.options.currentResolution = this.options.label;
		
		// Update the classes to reflect the currently selected resolution
		this.player.triggerEvent( 'changeRes' );
	},
	
	update: function() {
		
		if ( this.options.label === this.player.options.currentResolution ) {
			
			this.selected( true );
			
		} else {
			
			this.selected( false );
		}
	}
});

// jQuery no conflict and dollar sign function
jQuery(function ($) {
	
	// Setup some globally accessible variables
	var globals = {
		
		default_res		: '480',		//	(string)	The default resolution
		aspect_ratio	: ( 16 / 9 ),	//	(float)		The aspect ratio of our videos
		valid_res		: [				//	(array)		A list of valid video resolutions
			'1080',
			'480',
			'240'
		],
		valid_formats	: [				//	(array)		A list of valid video formats
			'mp4',
			'webm'
		]
	},
	
	methods = {
		
		/*
		Add our video quality selector button to the videojs controls. This takes
		a mandatory jQuery object of the <video> element we are setting up the
		videojs video for.
		*/
		setup_video : function( $video ) {

			var vid_id = $video.attr( 'id' ),
				$available_srcs,
				button_object = {},
				available_res = [],
				default_res,
				vjs_sources = [], // This will be an array of arrays of objects, see the video.js api documentation for myPlayer.src()
				vjs_source = {};
			
			// Save all of the available sources for later use
			$available_srcs = $video.children( 'source' );
			
			// Determine the available resoultions
			$.each( globals.valid_res, function( i, res ) {
				
				// We have to have at least as many <source> elements for each res as we have formats.
				if ( $available_srcs.filter( '[data-res="' + res + '"]' ).length >= globals.valid_formats.length ) {
					
					available_res.push( res );
				}
			});
			
			// Setup the quality button for this video (using the determined default) if we have more than 1 available resolution
			if ( available_res.length > 1 ) {
				
				// Determine this video's default res (it might not have the globally determined default available)
				default_res = ( $.inArray( globals.default_res, available_res ) > -1 ) ? globals.default_res : available_res[0];
				
				// Sort the available_res array so it is in descending order
				available_res.sort( function( a, b ) {
					
					return ( parseFloat( b ) - parseFloat( a ) );
				});
				
				// Put together the videojs source arrays for each available resolution
				$.each( available_res, function( i, res ) {
					
					vjs_sources[i] = [];
					
					$available_srcs.filter( '[data-res="' + res + '"]' ).each(function() {
						
						vjs_source = {};
						
						vjs_source.res = res;
						vjs_source.type = $(this).attr( 'type' );
						vjs_source.src = $(this).attr( 'src' );
						
						vjs_sources[i].push( vjs_source );
					});
				});

				_V_.ResolutionSelectorButton = _V_.ResolutionSelector.extend({
					
					buttonText: default_res + 'p',
					availableRes: vjs_sources
				});
				
				// Add the resolution selector button. TODO: test this with multiple videos that have different available resolutions on the same page (i.e. one has 240, 480, 1080 and one just has 240, 480)
				_V_.merge( _V_.ControlBar.prototype.options.components, { ResolutionSelectorButton : {} } );
				
				// Remove all the currently unused source elements
				$available_srcs.filter( '[data-res!="' + default_res + '"]' ).remove();
				
			} else {
				
				// Remove the resolution selector button, if it's not needed
				delete _V_.ControlBar.prototype.options.components.ResolutionSelectorButton;
			}
			
			var options;
			
			if ( 'vjs-test-multi-res-html5' === $video.attr( 'id' ) ) {
				
				options = { 'techOrder' : ['html5', 'flash'] };
				
			} else {
				
				options = { 'techOrder' : ['flash', 'html5'] };
			}
			
			console.log( options );
			
			// Initialize video.js and resize the video to fit its container		
			_V_( vid_id, options, function() {
				
				var myPlayer = this;
				
				// Attach our fullscreen change event here if we have more than one resolution available
				if ( available_res.length > 1 ) {
				
					myPlayer.addEvent( 'fullscreenchange', function() {
						
						methods.full_screen_change( available_res[available_res.length - 1] );
					});
				}
			});
		},
		
		/*
		Handler for the change to and from full screen. Bascially,
		we will want to change to the highest res available when we
		go to full screen and then go back to what ever it was before
		when we exti full screen.
		
		Arguments:
		- highest_res	(string)	The highest res available for this video
		*/
		full_screen_change : function( highest_res ) {
			
			myPlayer = this;
			
			// Detect if we are expanding to full screen or going back to regular
			
		}
	};
	
	$(document).ready(function() {
		
		
		methods.setup_video( $( '#vjs-test-multi-res' ) );
		methods.setup_video( $( '#vjs-test-multi-res-html5' ) );
		
	}); // End document.ready
	
}); // End jQuery $ function