<?php defined('ABSPATH') or die('-1');

/*
 * @desc Activates plugin.
 */
function galleryproplus_activate()
{
	global $wpdb;
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	// Create request history table.
	dbDelta('CREATE TABLE ' . GALLERYPROPLUS_TABLE_REQUESTS . ' (
		`requestId` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`contents` BLOB,
		`requestDate` datetime DEFAULT NULL,
		PRIMARY KEY(`requestId`),
		KEY(`requestDate`)
	) ENGINE=MyISAM;');
}

/*
 * @desc Add content to the head of the dashboard's back end.
 */
function galleryproplus_admin_head()
{
	printf('<link rel="stylesheet" type="text/css" href="%s/admin/styles.css" />', GALLERYPROPLUS_URL);
}

/*
 * @desc Triggered before any other hook when a user accesses the admin area.
 */
function galleryproplus_admin_init()
{
	galleryproplus_register_settings();
}

/*
 * @desc Add extra submenus and menu options to the admin panel's menu structure. It runs after the basic admin panel menu structure is in place.
 */
function galleryproplus_admin_menu()
{
	add_submenu_page('edit.php?post_type=galleryproplus', 'GP+ Settings', 'Settings', 'manage_options', 'settings', 'galleryproplus_menu');
}

/*
 * @desc Create custom post type.
 */
function galleryproplus_create_post_type()
{
	$labels = array(
		'name'               => 'GP+Galleries',
		'singular_name'      => 'GP+Gallery',
		'menu_name'          => 'Gallery Pro Plus',
		'name_admin_bar'     => 'Gallery Pro Plus',
		'add_new'            => 'Add New',
		'add_new_item'       => 'Add New GP+Gallery',
		'new_item'           => 'New GP+Gallery',
		'edit_item'          => 'Edit GP+Gallery',
		'view_item'          => 'View GP+Gallery',
		'all_items'          => 'All Galleries',
		'search_items'       => 'Search GP+Gallery',
		'parent_item_colon'  => 'Parent GP+Gallery',
		'not_found'          => 'No GP+Galleries found',
		'not_found_in_trash' => 'No GP+Galleries found in trash'
	);
	$args = array(
		'labels'             => $labels,
		'description'        => 'Create beautiful photo galleries.',
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array('slug'=>GalleryProPlus::getBasePath()),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array('title'),
		'menu_icon'          => 'dashicons-admin-media'
	);
	
	register_post_type(GALLERYPROPLUS_TYPE, $args);
	
	flush_rewrite_rules();
}

/*
 * @desc Deactivates plugin.
 */
function galleryproplus_deactivate()
{
	;
}

/*
 * @desc Tweak custom post type list columns.
 */
function galleryproplus_columns($columns)
{
	add_thickbox();
	$add_columns = array(
		'image_count' => 'Images',
		'password' => 'Password',
		'publication_date' => 'Publication',
		'expiration_date' => 'Expiration',
		'theme' => 'Theme',
		'views' => 'Views',
		'url' => 'URL',
	);
	unset($columns['date']);
	return array_merge($columns, $add_columns);
}

/*
 * @desc Tweak custom post type list column data.
 */
function galleryproplus_custom_column($column, $post_id)
{
	switch ($column)
	{
		case 'image_count':
			$images = get_post_meta($post_id, 'images', true);
			echo count($images);
			break;
		
		case 'password':
			echo get_post_field('post_password', $post_id);
			break;
		
		case 'publication_date':
		case 'expiration_date':
			$value = get_post_meta($post_id, $column, true);
			echo (empty($value)) ? '-' : $value;
			break;
		
		case 'theme':
			echo GalleryProPlus::getTheme($post_id, true);
			break;
		
		case 'url':
			if (get_post_field('post_status', $post_id) == 'draft')
			{
				echo 'Unavailable';
				return;
			}
			$path = get_site_url().GalleryProPlus::getBasePath().'/'.get_post_field('post_name', $post_id); ?>
			<a href="#TB_inline?width=300&height=100&inlineId=gallery-url-<?php echo $post_id ?>" class="thickbox"><img src="https://cdn0.iconfinder.com/data/icons/feather/96/591256-link-16.png" alt="" /></a>
			&nbsp;
			<a href="<?php echo $path ?>" target="_blank"><img src="https://cdn0.iconfinder.com/data/icons/octicons/1024/link-external-16.png" alt="" /></a>
			<div id="gallery-url-<?php echo $post_id ?>" style="display:none;">
				<p><strong>Click the textbox below to select your Gallery Pro Plus URL:</strong></p>
				<input type="text" onclick="this.select()" style="width:500px;" value="<?php echo htmlspecialchars($path) ?>" />
			</div>
			<?php break;
			
		case 'views':
			$value = get_post_meta($post_id, $column, true);
			echo (empty($value)) ? 0 : $value;
			break;
	}
}

/*
 * @desc Fires after WordPress has finished loading but before any headers are sent.
 */
function galleryproplus_init()
{
	galleryproplus_create_post_type();
	galleryproplus_register_field_groups();
}

/*
 * @desc Processes admin menu requests.
 */
function galleryproplus_menu()
{
	$page = (isset($_GET['page']) && in_array($_GET['page'], array('settings'))) ? $_GET['page'] : null;
	if (!is_null($page))
	{
		include(GALLERYPROPLUS_DIR . '/admin/' . $page . '.php');
	}
}

function galleryproplus_parse_request($query)
{
	GalleryProPlus::processRequest();
}

/*
 * @desc Applies to the posts where clause and restricts which posts will show up in various areas of the site.
 */
function galleryproplus_posts_where($where)
{
	$where .= " AND (wp_posts.post_parent=0 OR wp_posts.post_parent NOT IN (SELECT wp_posts.ID FROM wp_posts WHERE wp_posts.post_type='" . GALLERYPROPLUS_TYPE . "'))";
	remove_filter('posts_where', 'galleryproplus_posts_where');
	return $where;
}

/*
 * @desc Called after the query variable object is created, but before the actual query is run.
 */
function galleryproplus_pre_get_posts($query)
{
	if (is_admin())
	{
		global $pagenow, $post_type;
		
		// Disallow inclusion of GP+ attached images anywhere except for GP+ pages/posts
		$query_post_type = $query->get('post_type');
		if ($post_type != GALLERYPROPLUS_TYPE && $query_post_type == 'attachment')
		{
			// For AJAX requests, detect post parent in query and proceed if not concerning GP+ post types
			$query_post_parent = $query->get('post_parent');
			if (empty($query_post_parent) || get_post_type($query_post_parent) != GALLERYPROPLUS_TYPE)
			{
				if (!($pagenow == 'edit.php' || $pagenow == 'upload.php' || ($pagenow == 'admin-ajax.php' && !empty($_POST['action']) && $_POST['action'] == 'query-attachments')))
				{
					return;
				}
				
				$suppress_filters = $query->get('suppress_filters');
				if (!$suppress_filters)
				{
					add_filter('posts_where', 'galleryproplus_posts_where');
				}
			}
		}
	}
}

/*
 * @desc This hook is called once any activated plugins have been loaded. Is generally used for immediate filter setup, or plugin overrides.
 */
function galleryproplus_plugins_loaded()
{
	;
}

/*
 * @desc Registers field groups for custom post type.
 */
function galleryproplus_register_field_groups()
{
	$settings = get_option('galleryproplus_settings');
	$slideshow_transition_speeds = GalleryProPlus::getSlideshowTransitionSpeeds();
	$slideshow_transition_types = GalleryProPlus::getSlideshowTransitionTypes();
	$themes = GalleryProPlus::getThemes();
	
	register_field_group(array (
		'id' => 'acf_gallery-pro-plus-custom-fields',
		'title' => 'Gallery Pro Plus: Custom Fields',
		'fields' => array (
			array (
				'key' => 'field_55f23ba5273ec',
				'label' => 'Logo',
				'name' => 'logo',
				'type' => 'image',
				'save_format' => 'id',
				'preview_size' => 'thumbnail',
				'library' => 'uploadedTo',
			),
			array (
				'key' => 'field_55f23a3775b08',
				'label' => 'Images',
				'name' => 'images',
				'type' => 'gallery',
				'required' => 1,
				'preview_size' => 'thumbnail',
				'library' => 'uploadedTo',
			),
			array (
				'key' => 'field_55f2413f73ade',
				'label' => 'Gallery',
				'name' => '',
				'type' => 'tab',
			),
			array (
				'key' => 'field_55f23b56273ea',
				'label' => 'Music',
				'name' => 'music',
				'type' => 'file',
				'instructions' => 'MP3 or M4A audio formats only.',
				'save_format' => 'id',
				'library' => 'uploadedTo',
			),
			array (
				'key' => 'field_55f23b87273eb',
				'label' => 'Theme',
				'name' => 'theme',
				'type' => 'select',
				'required' => 1,
				'choices' => $themes,
				'default_value' => GalleryProPlus::getDefaultTheme(),
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_55f2413f7ccde',
				'label' => 'Slideshow',
				'name' => '',
				'type' => 'tab',
			),
			array (
				'key' => 'field_55f23ab2273e9',
				'label' => 'Transition: Speed',
				'name' => 'slideshow_transition_speed',
				'type' => 'select',
				'required' => 1,
				'choices' => $slideshow_transition_speeds,
				'default_value' => $settings['default_slideshow_transition_speed'],
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_55f23ab2273f1',
				'label' => 'Transition: Type',
				'name' => 'slideshow_transition_type',
				'type' => 'select',
				'required' => 1,
				'choices' => $slideshow_transition_types,
				'default_value' => $settings['default_slideshow_transition_type'],
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_55f24110711dd',
				'label' => 'Communications',
				'name' => '',
				'type' => 'tab',
			),
			array (
				'key' => 'field_55f5adf24916d',
				'label' => 'Email Recipients',
				'name' => 'recipients',
				'type' => 'textarea',
				'default_value' => $settings['default_recipients'],
				'placeholder' => '',
				'maxlength' => '',
				'rows' => 4,
				'formatting' => 'none',
			),
			array (
				'key' => 'field_55f241107ccdd',
				'label' => 'Publication',
				'name' => '',
				'type' => 'tab',
			),
			array (
				'key' => 'field_55f23bbf273ed',
				'label' => 'Publication Date',
				'name' => 'publication_date',
				'type' => 'date_picker',
				'instructions' => 'Optional date when gallery access becomes available; leave empty for immediate availability.',
				'date_format' => 'yy-mm-dd',
				'display_format' => 'mm/dd/yy',
				'first_day' => 0,
			),
			array (
				'key' => 'field_55f23c43273ee',
				'label' => 'Expiration Date',
				'name' => 'expiration_date',
				'type' => 'date_picker',
				'instructions' => 'Optional date when gallery access expires; leave empty for no expiration date.',
				'date_format' => 'yy-mm-dd',
				'display_format' => 'mm/dd/yy',
				'first_day' => 0,
			),
			array (
				'key' => 'field_888241107ccdd',
				'label' => 'Analytics/Scripts',
				'name' => '',
				'type' => 'tab',
			),
			array (
				'key' => 'field_55f5aabdd916d',
				'label' => 'HTML Head',
				'name' => 'html_head',
				'type' => 'textarea',
				'instructions' => 'Optional; content to insert into the &lt;head&gt; tag of the page (such as meta tags, scripts, or styles).',
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => 5,
				'formatting' => 'none',
			),
			array (
				'key' => 'field_55f5aabdd916e',
				'label' => 'HTML Body Header',
				'name' => 'body_header',
				'type' => 'textarea',
				'instructions' => 'Optional; content to insert immediately below the opening &lt;body&gt; tag of the page.',
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => 5,
				'formatting' => 'none',
			),
				array (
				'key' => 'field_55f5aabdd916f',
				'label' => 'HTML Body Footer',
				'name' => 'body_footer',
				'type' => 'textarea',
				'instructions' => 'Optional; content to insert immediately above the closing &lt;/body&gt; tag of the page.',
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => 5,
				'formatting' => 'none',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => GALLERYPROPLUS_TYPE,
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}

/*
 * @desc Registers settings fields.
 */
function galleryproplus_register_settings()
{
	register_setting('settings', 'galleryproplus_settings');
	
	add_settings_section(
		'galleryproplus_settings_section',
		'Common Settings',
		'galleryproplus_settings_section_callback',
		'settings'
	);
	
	add_settings_field(
		'base_path',
		'Base Page Path',
		'galleryproplus_settings_field_base_path_render',
		'settings',
		'galleryproplus_settings_section'
	);
	
	add_settings_field(
		'default_theme',
		'Default Theme',
		'galleryproplus_settings_field_default_theme_render',
		'settings',
		'galleryproplus_settings_section'
	);
	
	add_settings_field(
		'default_slideshow_transition_speed',
		'Default Slideshow Speed',
		'galleryproplus_settings_field_default_slideshow_transition_speed_render',
		'settings',
		'galleryproplus_settings_section'
	);
	
	add_settings_field(
		'default_slideshow_transition_type',
		'Default Slideshow Transition',
		'galleryproplus_settings_field_default_slideshow_transition_type_render',
		'settings',
		'galleryproplus_settings_section'
	);
	
	add_settings_field(
		'default_recipients',
		'Default Email Recipient(s)',
		'galleryproplus_settings_field_default_recipients_render',
		'settings',
		'galleryproplus_settings_section'
	);
}

/*
 * @desc Renders `basepath` setting field.
 */
function galleryproplus_settings_field_base_path_render()
{
	$settings = get_option('galleryproplus_settings');
	?>
	<input type="text" name="<?php echo GALLERYPROPLUS_TYPE ?>_settings[base_path]" value="<?php echo $settings['base_path'] ?>"><br />
	<em>Used for generating unique gallery URLs, i.e., /galleries</em>
	<?php
}

/*
 * @desc Renders `default_theme` setting field.
 */
function galleryproplus_settings_field_default_theme_render()
{
	$settings = get_option('galleryproplus_settings');
	$options = GalleryProPlus::getThemes();
	?>
	<select name="<?php echo GALLERYPROPLUS_TYPE ?>_settings[default_theme]">
		<?php foreach ($options As $key => $val) { ?>
		<option value="<?php echo $key ?>" <?php selected($settings['default_theme'], $key) ?>><?php echo $val ?></option>
		<?php } ?>
	</select>
	<?php
}

/*
 * @desc Renders `default_slideshow_transition_speed` setting field.
 */
function galleryproplus_settings_field_default_slideshow_transition_speed_render()
{
	$settings = get_option('galleryproplus_settings');
	$options = GalleryProPlus::getSlideshowTransitionSpeeds();
	?>
	<select name="<?php echo GALLERYPROPLUS_TYPE ?>_settings[default_slideshow_transition_speed]">
		<?php foreach ($options As $key => $val) { ?>
		<option value="<?php echo $key ?>" <?php selected($settings['default_slideshow_transition_speed'], $key) ?>><?php echo $val ?></option>
		<?php } ?>
	</select>
	<?php
}

/*
 * @desc Renders `default_recipients` setting field.
 */
function galleryproplus_settings_field_default_recipients_render()
{
	$settings = get_option('galleryproplus_settings');
	?>
	<textarea cols="40" rows="5" name="<?php echo GALLERYPROPLUS_TYPE ?>_settings[default_recipients]"><?php echo $settings['default_recipients'] ?></textarea><br />
	<em>Limit to one email address per line.</em>
	<?php
}

/*
 * @desc Renders `default_slideshow_transition_type` setting field.
 */
function galleryproplus_settings_field_default_slideshow_transition_type_render()
{
	$settings = get_option('galleryproplus_settings');
	$options = GalleryProPlus::getSlideshowTransitionTypes();
	?>
	<select name="<?php echo GALLERYPROPLUS_TYPE ?>_settings[default_slideshow_transition_type]">
		<?php foreach ($options As $key => $val) { ?>
		<option value="<?php echo $key ?>" <?php selected($settings['default_slideshow_transition_type'], $key) ?>><?php echo $val ?></option>
		<?php } ?>
	</select>
	<?php
}

/*
 * @desc Outputs settings description.
 */
function galleryproplus_settings_section_callback($args)
{
	echo "Tweak the settings below to fit the requirements for the average gallery.";
}

/*
 * @desc Enqueue items that are meant to appear on the front end, i.e., scripts and styles.
 */
function galleryproplus_wp_enqueue_scripts()
{
	//wp_enqueue_style(GALLERYPROPLUS_TYPE, GALLERYPROPLUS_URL . '/css/styles.css');
}
