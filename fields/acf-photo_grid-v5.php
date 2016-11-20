<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

// check if class already exists
if( !class_exists('acf_field_photo_grid') ) :

class acf_field_photo_grid extends acf_field {

	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	function __construct( $settings ) {

		$this->name = 'photo_grid';
		$this->label = __('Photo Grid', 'acf-photo_grid');

		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/
		$this->category = 'layout';

		/*
		*  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		*/
		$this->defaults = array();

		/*
		*  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		*  var message = acf._e('photo_grid', 'error');
		*/
		$this->l10n = array(
			'error'	=> __('Error! Please enter a higher value', 'acf-photo_grid'),
		);

    // filters (file upload)
		//add_filter('get_media_item_args', array($this, 'get_media_item_args'));
		//add_filter('wp_prepare_attachment_for_js', array($this, 'wp_prepare_attachment_for_js'), 10, 3);

		/*
		*  settings (array) Store plugin settings (url, path, version) as a reference for later use with assets
		*/
		$this->settings = $settings;

		// do not delete!
    parent::__construct();
	}


	/*
	*  render_field_settings()
	*
	*  Create extra settings for your field. These are visible when editing a field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	function render_field_settings( $field ) {

		/*
		*  acf_render_field_setting
		*
		*  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
		*  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
		*
		*  More than one setting can be added by copy/paste the above code.
		*  Please note that you must also have a matching $defaults value for the field name (font_size)
		*/
		// acf_render_field_setting( $field, array(
		// 	'label'			=> __('Font Size', 'acf-photo_grid'),
		// 	'instructions'	=> __('Customise the input font size','acf-photo_grid'),
		// 	'type'			=> 'number',
		// 	'name'			=> 'font_size'
		// ));
	}


	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	function render_field( $field ) {
    // Plugin assets url
    $url = $this->settings['url'] . 'assets/';
    $uploader = acf_get_setting('uploader');

    // enqueue
		if( $uploader == 'wp' ) {
			acf_enqueue_uploader();
		}

    /*
     * The hidden form element that stores the "serialized" JSON data
     * The grid gets iterated over on change, the data converted to JSON
     * and inserted into this hidden input.
     * Complete grid data gets stored in this single field.
     */
		?>
		<input type="hidden" id="photo_grid-data" name="<?php echo esc_attr($field['name']) ?>" value="<?php echo esc_attr($field['value']) ?>">
		<?php

    /*
     * Field Toolbar
     * Buttons to add items and columns
     */
		?>
    <div id="acf-photo_grid-toolbar">
      Hinzufügen:
      <button type="button" class="acf-button button acf-photo_grid-add-item" data-width="one" data-format="landscape">q</button>
      <button type="button" class="acf-button button acf-photo_grid-add-item" data-width="one" data-format="portrait">h</button>
  		<button type="button" class="acf-button button acf-photo_grid-add-item" data-width="two" data-format="landscape">Q</button>
  		<button type="button" class="acf-button button acf-photo_grid-add-item" data-width="two" data-format="portrait">H</button>
      <button type="button" class="acf-button button acf-photo_grid-add-column" data-height="two">Spalte 1x2</button>
      <button type="button" class="acf-button button acf-photo_grid-add-column" data-height="four">Spalte 1x4</button>
    </div>
		<?php

    /*
     * Main Container with template elements to clone
     * Classes to format image items: [ landscape | portrait ] [ {one} | two ]
     * Class to format columns:       [ {two} | four ]
     *
     * The column is preloaded with two image items because that's its use anyway
     * plus you can't drop items on empty columns...
     */
    ?>
    <div class="projektbilder acf-photo_grid-container sort-container">
      <div class="projektbild landscape acf-photo_grid-item clone" data-image-id="">
        <div>
          <span class="item-tools">
            <button type="button" class="acf-button button button-primary acf-photo_grid-image-select" disabled>Bild…</button>
            <span class="button-group">
              <button type="button" class="acf-button button acf-photo_grid-resize-item" disabled data-width="one" data-format="landscape">q</button>
              <button type="button" class="acf-button button acf-photo_grid-resize-item" disabled data-width="one" data-format="portrait">h</button>
              <button type="button" class="acf-button button acf-photo_grid-resize-item" disabled data-width="two" data-format="landscape">Q</button>
          		<button type="button" class="acf-button button acf-photo_grid-resize-item" disabled data-width="two" data-format="portrait">H</button>
            </span>
            <button type="button" class="acf-button button acf-photo_grid-remove-item" disabled>X</button>
          </span>
          <img src="" alt="">
        </div>
      </div>

      <div class="projektbild-column acf-photo_grid-column clone sort-container">
        <span class="column-handle"></span>
        <span class="column-tools">
          <button type="button" class="acf-button button acf-photo_grid-remove-column" disabled>X</button>
        </span>
        <div class="projektbild landscape acf-photo_grid-item " data-image-id="">
          <div>
            <span class="item-tools">
              <button type="button" class="acf-button button button-primary acf-photo_grid-image-select" disabled>Bild…</button>
              <span class="button-group">
                <button type="button" class="acf-button button acf-photo_grid-resize-item" disabled data-width="one" data-format="landscape">q</button>
                <button type="button" class="acf-button button acf-photo_grid-resize-item" disabled data-width="one" data-format="portrait">h</button>
                <button type="button" class="acf-button button acf-photo_grid-resize-item" disabled data-width="two" data-format="landscape">Q</button>
            		<button type="button" class="acf-button button acf-photo_grid-resize-item" disabled data-width="two" data-format="portrait">H</button>
              </span>
              <button type="button" class="acf-button button acf-photo_grid-remove-item" disabled>X</button>
            </span>
            <img src="" alt="">
          </div>
        </div>
        <div class="projektbild landscape acf-photo_grid-item " data-image-id="">
          <div>
            <span class="item-tools">
              <button type="button" class="acf-button button button-primary acf-photo_grid-image-select" disabled>Bild…</button>
              <span class="button-group">
                <button type="button" class="acf-button button acf-photo_grid-resize-item" disabled data-width="one" data-format="landscape">q</button>
                <button type="button" class="acf-button button acf-photo_grid-resize-item" disabled data-width="one" data-format="portrait">h</button>
                <button type="button" class="acf-button button acf-photo_grid-resize-item" disabled data-width="two" data-format="landscape">Q</button>
            		<button type="button" class="acf-button button acf-photo_grid-resize-item" disabled data-width="two" data-format="portrait">H</button>
              </span>
              <button type="button" class="acf-button button acf-photo_grid-remove-item" disabled>X</button>
            </span>
            <img src="" alt="">
          </div>
        </div>
      </div><!-- /column clone -->
    <?php

    /*
     * Load stored grid items from JSON field value.
     * Turns the stored JSON to an array and iterates it.
     */
    $gridArray = json_decode($field['value'], true);

    // Iterate all items
    foreach($gridArray['photogrid'] as $index) {

      // Type: item (image)
      if($index['type'] === 'item') {
        // Get appropriate WP image size
        if($index['format'] === 'portrait') {
          $imageSize = 'projektbild_grid_p';
        }
        else {
          $imageSize = 'projektbild_grid_l';
        }

        // Get attachment and meta data
        $imageUrl = wp_get_attachment_image_src($index['image'], $imageSize);
        $imageAlt = get_post_meta($index['image'], '_wp_attachment_image_alt', true);

        ?>
        <div class="projektbild <?php echo $index['format'] ?> <?php echo $index['size'] ?> acf-photo_grid-item" data-image-id="<?php echo $index['image'] ?>">
          <div>
            <span class="item-tools">
              <button type="button" class="acf-button button button-primary acf-photo_grid-image-select">Bild…</button>
              <span class="button-group">
                <button type="button" class="acf-button button acf-photo_grid-resize-item" data-width="one" data-format="landscape">q</button>
                <button type="button" class="acf-button button acf-photo_grid-resize-item" data-width="one" data-format="portrait">h</button>
                <button type="button" class="acf-button button acf-photo_grid-resize-item" data-width="two" data-format="landscape">Q</button>
            		<button type="button" class="acf-button button acf-photo_grid-resize-item" data-width="two" data-format="portrait">H</button>
              </span>
              <button type="button" class="acf-button button acf-photo_grid-remove-item">X</button>
            </span>
            <img src="<?php echo $imageUrl[0] ?>" alt="<?php echo $imageAlt ?>">
          </div>
        </div>
        <?php
      } // type item (image)

      // Type: column
      if($index['type'] === 'column') {
        ?>
        <div class="projektbild-column <?php echo $index['height'] ?> acf-photo_grid-column sort-container">
          <span class="column-handle"></span>
          <span class="column-tools">
            <button type="button" class="acf-button button acf-photo_grid-remove-column">X</button>
          </span>
        <?php

        // Iterate over image items inside column
        foreach($index['items'] as $item) {
          // Get appropriate WP image size
          if($item['format'] === 'portrait') {
            $imageSize = 'projektbild_grid_p';
          }
          else {
            $imageSize = 'projektbild_grid_l';
          }

          // Get attachment and meta data
          $imageUrl = wp_get_attachment_image_src($item['image'], $imageSize);
          $imageAlt = get_post_meta($item['image'], '_wp_attachment_image_alt', true);

          ?>
          <div class="projektbild <?php echo $item['format'] ?> <?php echo $item['size'] ?> acf-photo_grid-item" data-image-id="<?php echo $item['image'] ?>">
            <div>
              <span class="item-tools">
                <button type="button" class="acf-button button button-primary acf-photo_grid-image-select">Bild…</button>
                <span class="button-group">
                  <button type="button" class="acf-button button acf-photo_grid-resize-item" data-width="one" data-format="landscape">q</button>
                  <button type="button" class="acf-button button acf-photo_grid-resize-item" data-width="one" data-format="portrait">h</button>
                  <button type="button" class="acf-button button acf-photo_grid-resize-item" data-width="two" data-format="landscape">Q</button>
              		<button type="button" class="acf-button button acf-photo_grid-resize-item" data-width="two" data-format="portrait">H</button>
                </span>
                <button type="button" class="acf-button button acf-photo_grid-remove-item">X</button>
              </span>
              <img src="<?php echo $imageUrl[0] ?>" alt="<?php echo $imageAlt ?>">
            </div>
          </div>
          <?php
        }
        ?>
        </div>
        <?php
      } // type column
    } // foreach photogrid
    ?>
    </div>
    <?php
	}


	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/
	function input_admin_enqueue_scripts() {

		// vars
		$url = $this->settings['url'];
		$version = $this->settings['version'];


		// register & include JS
		wp_register_script( 'acf-input-photo_grid', "{$url}assets/js/input.js", array('acf-input'), $version );
		wp_enqueue_script( 'acf-input-photo_grid' );
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-sortable' );


		// register & include CSS
		wp_register_style( 'acf-input-photo_grid', "{$url}assets/css/input.css", array('acf-input'), $version );
		wp_enqueue_style( 'acf-input-photo_grid' );

	}


	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/
	function input_admin_head() { }


	/*
 	*  input_form_data()
 	*
 	*  This function is called once on the 'input' page between the head and footer
 	*  There are 2 situations where ACF did not load during the 'acf/input_admin_enqueue_scripts' and
 	*  'acf/input_admin_head' actions because ACF did not know it was going to be used. These situations are
 	*  seen on comments / user edit forms on the front end. This function will always be called, and includes
 	*  $args that related to the current screen such as $args['post_id']
 	*
 	*  @type	function
 	*  @date	6/03/2014
 	*  @since	5.0.0
 	*
 	*  @param	$args (array)
 	*  @return	n/a
 	*/
  function input_form_data( $args ) { }


	/*
	*  input_admin_footer()
	*
	*  This action is called in the admin_footer action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_footer)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/
  function input_admin_footer() { }


	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add CSS + JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/
  function field_group_admin_enqueue_scripts() { }


	/*
	*  field_group_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is edited.
	*  Use this action to add CSS and JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/
  function field_group_admin_head() { }


	/*
	*  load_value()
	*
	*  This filter is applied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/
  function load_value( $value, $post_id, $field ) {
    return $value;
  }


	/*
	*  update_value()
	*
	*  This filter is applied to the $value before it is saved in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/
  function update_value( $value, $post_id, $field ) {
    return $value;
  }


	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/
  function format_value( $value, $post_id, $field ) {

		// bail early if no value
		if( empty($value) ) {
			return $value;
		}

		// apply setting
		if( $field['font_size'] > 12 ) {

			// format the value
			// $value = 'something';

		}

		// return
		return $value;
	}


	/*
	*  validate_value()
	*
	*  This filter is used to perform validation on the value prior to saving.
	*  All values are validated regardless of the field's required setting. This allows you to validate and return
	*  messages to the user if the value is not correct
	*
	*  @type	filter
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$valid (boolean) validation status based on the value and the field's required setting
	*  @param	$value (mixed) the $_POST value
	*  @param	$field (array) the field array holding all the field options
	*  @param	$input (string) the corresponding input name for $_POST value
	*  @return	$valid
	*/
  function validate_value( $valid, $value, $field, $input ) {
    $valid = true;
		// // Basic usage
		// if( $value < $field['custom_minimum_setting'] ) {
		// 	$valid = false;
		// }
    //
    //
		// // Advanced usage
		// if( $value < $field['custom_minimum_setting'] ) {
		// 	$valid = __('The value is too little!','acf-photo_grid');
		// }
    //
		// // return
		return $valid;
	}


	/*
	*  delete_value()
	*
	*  This action is fired after a value has been deleted from the db.
	*  Please note that saving a blank value is treated as an update, not a delete
	*
	*  @type	action
	*  @date	6/03/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (mixed) the $post_id from which the value was deleted
	*  @param	$key (string) the $meta_key which the value was deleted
	*  @return	n/a
	*/
  function delete_value( $post_id, $key ) { }


	/*
	*  load_field()
	*
	*  This filter is applied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/
  function load_field( $field ) {
		return $field;
	}


	/*
	*  update_field()
	*
	*  This filter is applied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/
  function update_field( $field ) {
		return $field;
	}


	/*
	*  delete_field()
	*
	*  This action is fired after a field is deleted from the database
	*
	*  @type	action
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	n/a
	*/
  function delete_field( $field ) { }

}

// initialize
new acf_field_photo_grid( $this->settings );

// class_exists check
endif;
