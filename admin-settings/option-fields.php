<?php
class UPL_Option{
	public function __construct(){
		add_action( 'admin_init', array( $this, 'upl_setup_sections' ) );
		add_action( 'admin_init', array( $this, 'upl_setup_fields' ) );
		add_action( 'admin_footer', array( $this, 'media_fields' ) );
		add_action( 'admin_enqueue_scripts', 'wp_enqueue_media' );
	}
	public function upl_setup_sections() {
		//create setting section as you want
		$args = array(
			array(
				'id' => 'general_section',
				'title' => 'General Settings',
				'callback' => array(),
				'page' => 'option_settion_general',
			),
			array(
				'id' => 'header_section',
				'title' => 'Header Settings',
				'callback' => array(),
				'page' => 'option_settion_header',
			),
			array(
				'id' => 'footer_section',
				'title' => 'Footer Settings',
				'callback' => array(),
				'page' => 'option_settion_footer',
			),
			array(
				'id' => 'social_section',
				'title' => 'Social Settings',
				'callback' => array(),
				'page' => 'option_settion_social',
			),
			);
		
		foreach ($args as $arg) {
		add_settings_section( $arg['id'], $arg['title'], $arg['callback'], $arg['page'] );
		}
		
	}
	public function upl_setup_fields() {
		$fields = array(
			array(
				'label' => 'Name',
				'id' => 'name',
				'type' => 'text',
				'section' => 'general_section',
				'desc' => 'Add your name',
				'placeholder' => 'Name',
				'page' => 'option_settion_general',
				'option_group' => 'general_fields',
			),
			array(
				'label' => 'Date',
				'id' => 'date',
				'type' => 'date',
				'section' => 'general_section',
				'desc' => 'Add a date',
				'placeholder' => 'Date',
				'page' => 'option_settion_general',
				'option_group' => 'general_fields',
			),
			array(
				'label' => 'Description',
				'id' => 'desc',
				'type' => 'wysiwyg',
				'section' => 'header_section',
				'desc' => 'Add a description',
				'placeholder' => 'Description',
				'page' => 'option_settion_header',
				'option_group' => 'header_fields',
			),
			array(
				'label' => 'Image',
				'id' => 'image',
				'type' => 'media',
				'section' => 'header_section',
				'desc' => 'Add an Image',
				'placeholder' => 'Image',
				'page' => 'option_settion_header',
				'option_group' => 'header_fields',
			),
			array(
				'label' => 'Email',
				'id' => 'email',
				'type' => 'email',
				'section' => 'footer_section',
				'desc' => 'Add an email',
				'placeholder' => 'Email',
				'page' => 'option_settion_footer',
				'option_group' => 'footer_fields',
			),
			array(
				'label' => 'Smaill Description',
				'id' => 'Desc',
				'type' => 'textarea',
				'section' => 'footer_section',
				'desc' => 'Description',
				'placeholder' => 'Some Content',
				'page' => 'option_settion_footer',
				'option_group' => 'footer_fields',
			),
			array(
				'label' => 'Gender',
				'id' => 'gender',
				'type' => 'radio',
				'section' => 'social_section',
				'options' => array(
					'Male' => 'Male',
					'Female' => 'Female',
					'Others' => 'Others',
				),
				'desc' => 'Add your gender',
				'placeholder' => 'Gender',
				'page' => 'option_settion_social',
				'option_group' => 'soical_fields',
			),
			array(
				'label' => 'Religion',
				'id' => 'religion',
				'type' => 'select',
				'section' => 'social_section',
				'options' => array(
					'Hindu' => 'Hindu',
					'Muslim' => 'Muslim',
					'Shikh' => 'Shikh',
					'' => '',
				),
				'desc' => 'Add your religion',
				'placeholder' => 'Relegion',
				'page' => 'option_settion_social',
				'option_group' => 'soical_fields',
			),
			array(
				'label' => 'Color',
				'id' => 'color',
				'type' => 'color',
				'section' => 'social_section',
				'desc' => 'Pick a color',
				'placeholder' => 'Color',
				'page' => 'option_settion_social',
				'option_group' => 'soical_fields',
			),
		);
		foreach( $fields as $field ){
			//popularpost will be dynamic
			add_settings_field( $field['id'], $field['label'], array( $this, 'upl_field_callback' ), $field['page'], $field['section'], $field );
			register_setting( $field['page'], $field['id'] );
		}

		// code to show all the fields of a setting section
		// settings_fields( 'option_settion_social' );
		// 	do_settings_sections( 'option_settion_social' );
		// 	submit_button();
	}
	public function upl_field_callback( $field ) {
		$value = get_option( $field['id'] );
		switch ( $field['type'] ) {
				case 'media':
					printf(
						'<input style="width: 40%%" id="%s" name="%s" type="text" value="%s"> <input style="width: 19%%" class="button popularpost-media" id="%s_button" name="%s_button" type="button" value="Upload" />',
						$field['id'],
						$field['id'],
						$value,
						$field['id'],
						$field['id']
					);
					printf('<br><img src="%s" style="width: 350px; height:350px">',$value);
					break;
				case 'radio':
				case 'checkbox':
					if( ! empty ( $field['options'] ) && is_array( $field['options'] ) ) {
						$options_markup = '';
						$iterator = 0;
						foreach( $field['options'] as $key => $label ) {
							$iterator++;
							$options_markup.= sprintf('<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>',
							$field['id'],
							$field['type'],
							$key,
							checked($value[array_search($key, $value, true)], $key, false),
							$label,
							$iterator
							);
							}
							printf( '<fieldset>%s</fieldset>',
							$options_markup
							);
					}
					break;
				case 'select':
				case 'multiselect':
					if( ! empty ( $field['options'] ) && is_array( $field['options'] ) ) {
						$attr = '';
						$options = '';
						foreach( $field['options'] as $key => $label ) {
							$options.= sprintf('<option value="%s" %s>%s</option>',
								$key,
								selected($value[array_search($key, $value, true)], $key, false),
								$label
							);
						}
						if( $field['type'] === 'multiselect' ){
							$attr = ' multiple="multiple" ';
						}
						printf( '<select name="%1$s[]" id="%1$s" %2$s>%3$s</select>',
							$field['id'],
							$attr,
							$options
						);
					}
					break;
				case 'textarea':
				printf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>',
					$field['id'],
					$field['placeholder'],
					$value
					);
					break;
				case 'wysiwyg':
					wp_editor($value, $field['id']);
					break;
			default:
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />',
					$field['id'],
					$field['type'],
					$field['placeholder'],
					$value
				);
		}
		if( $desc = $field['desc'] ) {
			printf( '<p class="description">%s </p>', $desc );
		}
	}	public function media_fields() {
		?><script>
			jQuery(document).ready(function($){
				if ( typeof wp.media !== 'undefined' ) {
					var _custom_media = true,
					_orig_send_attachment = wp.media.editor.send.attachment;
					$('.popularpost-media').click(function(e) {
						var send_attachment_bkp = wp.media.editor.send.attachment;
						var button = $(this);
						var id = button.attr('id').replace('_button', '');
						_custom_media = true;
							wp.media.editor.send.attachment = function(props, attachment){
							if ( _custom_media ) {
								$('input#'+id).val(attachment.url);
							} else {
								return _orig_send_attachment.apply( this, [props, attachment] );
							};
						}
						wp.media.editor.open(button);
						return false;
					});
					$('.add_media').on('click', function(){
						_custom_media = false;
					});
				}
			});
		</script><?php
	}
}
new UPL_Option();