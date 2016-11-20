(function($) {

  /*
   * Uses WP media popup to upload and select an image
   */
  function pgImageSelect($element) {
    var frame = wp.media({
      title: 'Galeriebild hochladen/auswählen',
      mode: 'select',
      multiple: false,
      library: {
        type: 'image',
        uploadedTo: wp.media.view.settings.post.id
      },
      button: {
        text: 'Auswählen'
      }
    });

    // If an image was already selected, pre-select it when dialog is opened again
    // http://wordpress.stackexchange.com/a/144320/106229
    frame.on('open', function() {
      var selection = frame.state().get('selection');
      var selected = $element.attr('data-image-id'); // the id of the image
      if(selected) {
          selection.add(wp.media.attachment(selected));
      }
    });

    // After a selection happens, add image ID as data attribute
    // and load the image into the grid item
    frame.on('select', function() {
      var json = frame.state().get('selection').first().toJSON();
      var id = json.id;
      $element.attr('data-image-id', id);
      if($element.hasClass('portrait')) {
        $element.find('img').attr('src', json.sizes.projektbild_grid_p.url);
      }
      else {
        $element.find('img').attr('src', json.sizes.projektbild_grid_l.url);
      }

      pgSerialize();
    });

    frame.open();
  }


  /*
   * Resizes the grid layout proportionally, container can be fluid width
   */
  function resizeProjektbilder() {
    var containerWidth = $('.projektbilder').width() - 12;

    var colGutter = 6;
    var colWidth = containerWidth / 3 + colGutter / 2 + 1;

    $('.projektbild').width(colWidth);
    $('.projektbild.two').width(colWidth * 2);
    $('.projektbild-column').width(colWidth);

    $('.projektbild.portrait div').height(colWidth * 4/3);
    $('.projektbild.landscape div').height(colWidth * 2/3 + 0.1);
    $('.projektbild.two.portrait div').height((colWidth * 2) * 4/3);
    $('.projektbild.two.landscape div').height((colWidth * 2) * 2/3);
    $('.projektbild-column').height(colWidth * 4/3);
    $('.projektbild-column.four').height(colWidth * 8/3);
  }
  $(window).on('resize', function() {
    resizeProjektbilder();
  });


  /*
   * Serializes the DOM elements of the grid into JSON
   * and places it in the hidden form element for the field.
   */
  function pgSerialize() {
    var data = { photogrid: [] };

    $('.acf-photo_grid-container > div:not(.clone)').each( function(index) {
      var $self = $(this);

      // Collect image item data
      if( $(this).hasClass('acf-photo_grid-item') ) {
        data.photogrid[index] = {};
        data.photogrid[index].type = 'item';
        data.photogrid[index].image = $(this).attr('data-image-id');

        // Format
        if( $(this).hasClass('portrait') ) {
          data.photogrid[index].format = 'portrait';
        }
        else {
          data.photogrid[index].format = 'landscape';
        }

        // Size
        if( $(this).hasClass('two') ) {
          data.photogrid[index].size = 'two';
        }
        else {
          data.photogrid[index].size = 'one';
        }
      } // if item

      // Collect column data and iterate over its items
      if( $(this).hasClass('acf-photo_grid-column') ) {
        data.photogrid[index] = {};
        data.photogrid[index].type = 'column';

        // Height
        if( $(this).hasClass('four') ) {
          data.photogrid[index].height = 'four';
        }
        else {
          data.photogrid[index].height = 'two';
        }

        // If there are items in the column...
        if($(this).find('.acf-photo_grid-item').length > 0) {
          data.photogrid[index].items = [];

          // ...iterate over them and add to the data object.
          $(this).find('.acf-photo_grid-item').each( function(innerIndex) {
            data.photogrid[index].items[innerIndex] = {};
            data.photogrid[index].items[innerIndex].type = 'item';
            data.photogrid[index].items[innerIndex].image = $(this).attr('data-image-id');

            // Format
            if( $(this).hasClass('portrait') ) {
              data.photogrid[index].items[innerIndex].format = 'portrait';
            }
            else {
              data.photogrid[index].items[innerIndex].format = 'landscape';
            }

            // Size
            if( $(this).hasClass('two') ) {
              data.photogrid[index].items[innerIndex].size = 'two';
            }
            else {
              data.photogrid[index].items[innerIndex].size = 'one';
            }
          }); // .each
        } // if find
      } // if column
    });

    $('#photo_grid-data').val( JSON.stringify(data) );
  }

	function initialize_field( $el ) {
		//$el.do…();
	}


	if( typeof acf.add_action !== 'undefined' ) {
		/*
		*  ready append (ACF5)
		*
		*  These are 2 events which are fired during the page load
		*  ready = on page load similar to $(document).ready()
		*  append = on new DOM elements appended via repeater field
		*
		*  @type	event
		*  @date	20/07/13
		*
		*  @param	$el (jQuery selection) the jQuery element which contains the ACF fields
		*  @return	n/a
		*/
		acf.add_action('ready append', function( $el ){

      resizeProjektbilder();

      /*
       * Delete item click event
       */
      $('body').off('click.remove').on('click.remove', '.acf-photo_grid-remove-item', function(ev) {
        $(this).closest('.acf-photo_grid-item').remove();
        pgSerialize();
      });

      /*
       * Delete column click event
       */
      $('body').off('click.removecol').on('click.removecol', '.acf-photo_grid-remove-column', function(ev) {
        $(this).closest('.acf-photo_grid-column').remove();
        pgSerialize();
      });

      /*
       * Image select click event
       */
      $('body').off('click.select').on('click.select', '.acf-photo_grid-image-select', function(ev) {
        var el = $(ev.target).closest('.acf-photo_grid-item');
        pgImageSelect(el);
      });

      /*
       * Resize item click event
       */
      $('body').off('click.resize').on('click.resize', '.acf-photo_grid-resize-item', function(ev) {
        var $item  = $(this).closest('.acf-photo_grid-item');
        var format = $(this).data('format');
        var width  = $(this).data('width');

        if(width === 'two') {
          $item.addClass('two');
        }
        else {
          $item.removeClass('two');
        }

        if(format === 'landscape') {
          $item.addClass('landscape').removeClass('portrait');
        }
        if(format === 'portrait') {
          $item.addClass('portrait').removeClass('landscape');
        }
        resizeProjektbilder();
        pgSerialize();
      });

			// search $el for fields of type 'photo_grid'
			acf.get_fields({ type: 'photo_grid' }, $el).each(function(){

				initialize_field( $(this) );

        /*
         * Add item click event
         */
        $(this).find('.acf-photo_grid-add-item').on('click', function(ev) {
          var $proto = $el.find('.acf-photo_grid-item.clone');
          var format = $(this).data('format');
          var width  = $(this).data('width');

          var $newItem = $proto.clone();
          $newItem.removeClass('clone').show();
          $newItem.find('button').prop('disabled', false);

          if(width === 'two') {
            $newItem.addClass('two');
          }
          else {
            $newItem.removeClass('two');
          }

          if(format === 'landscape') {
            $newItem.addClass('landscape').removeClass('portrait');
          }
          if(format === 'portrait') {
            $newItem.addClass('portrait').removeClass('landscape');
          }
          $newItem.appendTo('.acf-photo_grid-container');

          resizeProjektbilder();
          pgSerialize();
        });

        /*
         * Add column click event
         */
        $(this).find('.acf-photo_grid-add-column').on('click', function(ev) {
          var $proto = $el.find('.acf-photo_grid-column.clone');
          var height = $(this).data('height');

          var $newItem = $proto.clone();
          $newItem.removeClass('clone').show();
          $newItem.find('button').prop('disabled', false);

          if(height === 'four') {
            $newItem.addClass('four');
          }
          $newItem.appendTo('.acf-photo_grid-container');

          resizeProjektbilder();
          pgSerialize();
        });

			}); // acf.get_fields ...

      $('.sort-container').sortable({
        connectWith: '.sort-container',
        items: '.acf-photo_grid-item, .acf-photo_grid-column',
        dropOnEmpty: true,
        placeholder: 'grid-sort-placeholder',
        start: function(e, ui) {
          ui.placeholder.height(ui.item.height());
          ui.placeholder.width(ui.item.width() - 2);
        },
        update: function() {
          pgSerialize();
        }
      });

		}); // acf.add_action ...

	}

  // ACF4 not supported.

})(jQuery);
