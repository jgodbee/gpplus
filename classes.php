<?php defined('ABSPATH') or die('-1');

/*
 * @desc Helper methods.
 */
class GalleryProPlusHelper
{
	public static $base_path;
	public static $default_slideshow_transition_speed;
	public static $default_slideshow_transition_type;
	public static $default_theme;
	public static $gallery_ids;
	public static $themes;
	
	public static function debugCapture($debug, $use_var_export=false)
	{
		$data = ($use_var_export) ? var_export($debug, true) : print_r($debug, true);
		if (($socket = fsockopen(GALLERYPROPLUS_DEBUG_HOST, GALLERYPROPLUS_DEBUG_PORT, $a, $b, 5)) !== false)
		{
			fwrite ($socket, "<debugCapture><name view='text'>debug</name><text overwrite='false'><![CDATA[" . base64_encode($data) . "]]></text></debugCapture>");
			fclose ($socket);
		}
	}
	
	public static function endsWith($haystack, $needle)
	{
		return $needle === '' || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
	}
	
	public static function getBasePath()
	{
		if (empty(self::$base_path))
		{
			$options = get_option('galleryproplus_settings');
			self::$base_path = (isset($options['base_path']) && !empty($options['base_path'])) ? $options['base_path'] : GALLERYPROPLUS_PATH;
		}
		return self::$base_path;
	}
	
	public static function getDebug()
	{
		return array(
			GALLERYPROPLUS_BASENAME,
			GALLERYPROPLUS_DIR,
			GALLERYPROPLUS_NAME,
			GALLERYPROPLUS_PATH,
			GALLERYPROPLUS_TYPE,
			GALLERYPROPLUS_TABLE_REQUESTS,
			GALLERYPROPLUS_URL
		);
	}
	
	public static function getDefaultSlideshowTransitionSpeed()
	{
		if (empty(self::$default_slideshow_transition_speed))
		{
			$options = get_option('galleryproplus_settings');
			self::$default_slideshow_transition_speed = (isset($options['default_slideshow_transition_speed']) && !empty($options['default_slideshow_transition_speed'])) ? $options['default_slideshow_transition_speed'] : '';
		}
		return self::$default_slideshow_transition_speed;
	}
	
	public static function getDefaultSlideshowTransitionType()
	{
		if (empty(self::$default_slideshow_transition_type))
		{
			$options = get_option('galleryproplus_settings');
			self::$default_slideshow_transition_type = (isset($options['default_slideshow_transition_type']) && !empty($options['default_slideshow_transition_type'])) ? $options['default_slideshow_transition_type'] : '';
		}
		return self::$default_slideshow_transition_type;
	}
	
	public static function getDefaultTheme()
	{
		if (empty(self::$default_theme))
		{
			$options = get_option('galleryproplus_settings');
			self::$default_theme = (isset($options['default_theme']) && !empty($options['default_theme'])) ? $options['default_theme'] : '';
		}
		return self::$default_theme;
	}
	
	public static function getGalleryIds()
	{
		if (empty(self::$gallery_ids))
		{
			$gallery_ids = array();
			
			$query = new WP_Query(array('post_type'=>GALLERYPROPLUS_TYPE, 'posts_per_page'=>-1, 'post_status'=>array('private','publish')));
			if ($query->have_posts())
			{
				while ($query->have_posts())
				{
					$query->the_post();
					$gallery_ids[] = $query->post->ID;
				}
				wp_reset_query();
			}
			
			self::$gallery_ids = $gallery_ids;
		}
		
		return self::$gallery_ids;
	}
	
	public static function getImageProperty($image_id, $property)
	{
		$value = '';
		
		$image = wp_get_attachment_metadata($image_id);
		if (!is_null($image))
		{
			switch ($property)
			{
				case 'filename':
				case 'filename-with-extension':
					$value = array_pop(explode('/', $image['file']));
					break;
				
				case 'filename-without-extension':
					$values = explode('.', array_pop(explode('/', $image['file'])));
					array_pop($values);
					$value = implode('.', $values);
					break;
			}
		}
		
		return $value;
	}
	
	public static function getSeoFriendly($name, $case_insensitive=true)
	{
		if ($case_insensitive)
		{
			$name = strtolower($name);
		}
		$name = str_replace('&', 'and', $name);
		$name = str_replace(array(chr(34), chr(39), chr(96), chr(147), chr(148), chr(180)), '', $name); // remove all types of quotes
		$name = preg_replace("/[^a-zA-Z0-9]/", '-', $name);
		$name = str_replace(' ', '-', $name);
		$name = preg_replace("/-+/", '-', $name);
		$name = trim($name, '-');
		return $name;
	}
	
	public static function getSlideshowTransitionSpeeds()
	{
		return array(
			'Speed1' => 'Speed #1',
			'Speed2' => 'Speed #2',
			'Speed3' => 'Speed #3',
			'Speed4' => 'Speed #4',
			'Speed5' => 'Speed #5'
		);
	}
	
	public static function getSlideshowTransitionTypes()
	{
		return array(
			'transition1' => 'Transition #1',
			'transition2' => 'Transition #2',
			'transition3' => 'Transition #3',
			'transition4' => 'Transition #4',
			'transition5' => 'Transition #5'
		);
	}
	
	public static function getTheme($gallery_id, $return_friendly=false)
	{
		$themes = self::getThemes();
		$option = get_post_meta($gallery_id, 'theme', true);
		$theme = (array_key_exists($option, $themes)) ? $option : array_shift(array_keys($themes));
		return ($return_friendly) ? $themes[$theme] : $theme;
	}
	
	public static function getThemes()
	{
		if (empty(self::$themes))
		{
			$themes = array();
			
			$themes_dir = GALLERYPROPLUS_DIR.'/themes';
			if (is_dir($themes_dir))
			{
				$files = scandir($themes_dir);
				if (count($files) > 0)
				{
					foreach ($files As $file)
					{
						if (substr($file, 0, 1) != '.' && is_dir($themes_dir.'/'.$file))
						{
							$themes[$file] = ucwords(str_replace(array('.','-','_'), ' ', strtolower($file)));
						}
					}
					asort($themes);
				}
			}
			
			self::$themes = $themes;
		}
		
		return self::$themes;
	}
	
	public static function isAdmin()
	{
		return is_user_logged_in() && current_user_can('publish_posts');
	}
	
	public static function isBrowsable($gallery_id)
	{
		$exp = get_post_meta($gallery_id, 'expiration_date', true);
		$exp = (!empty($exp)) ? strtotime($exp) : null;
		
		$now = time();
		
		$pub = get_post_meta($gallery_id, 'publication_date', true);
		$pub = (!empty($pub)) ? strtotime($pub) : null;
		
		if (!is_null($pub) && $now < $pub)
		{
			return false;
		}
		
		if (!is_null($exp) && $now > $exp)
		{
			return false;
		}
		
		return true;
	}
	
	public static function processRequest()
	{
		global $wp;
		
		$basepath = trim(self::getBasePath(), '/');
		if (self::startsWith($wp->request, $basepath))
		{
			if ($wp->request != $basepath)
			{
				$slug = substr($wp->request, strlen($basepath)+1);
				$gallery = get_page_by_path($slug, OBJECT, GALLERYPROPLUS_TYPE);
			}
			
			if (!is_null($gallery) && in_array($gallery->post_status, array('private','publish')))
			{
				if (self::isAdmin() || self::isBrowsable($gallery->ID))
				{
					if (self::isAdmin() || !post_password_required($gallery))
					{
						self::showGallery($gallery);
					}
					else if (!empty($gallery->post_password))
					{
						self::showPasswordForm($gallery);
					}
				}
				else
				{
					self::showAccessWarning($gallery);
				}
			}
			else
			{
				self::showPageNotFound();
			}
			exit();
		}
	}
	
	public static function sanitize($string)
	{
		// Remove whitespaces (not a must though)
		$string = trim($string); 
		
		// Apply stripslashes if magic_quotes_gpc is enabled
		if(get_magic_quotes_gpc())
		{
			$string = stripslashes($string);
		}
		
		return esc_sql($string);
	}
	
	public static function sanitizeData($data, $type)
	{
		if (strlen($data) != 0)
		{
			switch ($type)
			{
				case 'email' : return filter_var($data, FILTER_SANITIZE_EMAIL);
					break;
				case 'float' : return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION|FILTER_FLAG_ALLOW_THOUSAND);
					break;
				case 'int' : return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
					break;
				case 'string' : return filter_var($data, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
					break;
				case 'url' : return filter_var($data, FILTER_SANITIZE_URL);
					break;
			}
		}
		
		return $data;
	}
	
	public static function sendToBrowser(&$file_data, $file_name, $file_type='application/octet-stream')
	{
		header('Cache-control: private');
		header('Content-Disposition: attachment; filename=' . $file_name);
		header('Content-Length: ' . strlen($file_data));
		header('Content-Transfer-Encoding: binary');
		header('Content-Type: ' . $file_type);
		header('Pragma: private');
		@flush();
		echo $file_data;
		@flush();
		exit();
	}
	
	public static function showAccessWarning($gallery)
	{
		$exp = get_post_meta($gallery->ID, 'expiration_date', true);
		$exp = (!empty($exp)) ? strtotime($exp) : null;
		
		$now = time();
		
		$pub = get_post_meta($gallery->ID, 'publication_date', true);
		$pub = (!empty($pub)) ? strtotime($pub) : null;
		
		$theme_path = self::getTheme($gallery->ID);
		
		require(GALLERYPROPLUS_DIR . '/themes/' . $theme_path . '/access-warning.php');
	}
	
	public static function showGallery($gallery)
	{
		$html_head = get_post_meta($gallery->ID, 'html_head', true);
		$body_header = get_post_meta($gallery->ID, 'body_header', true);
		$body_footer = get_post_meta($gallery->ID, 'body_footer', true);
		
		$audio_id = get_post_meta($gallery->ID, 'music', true);
		if (!empty($audio_id))
		{
			$audio_meta = wp_get_attachment_metadata($audio_id);
			if (in_array($audio_meta['dataformat'], array('mp3','m4a')))
			{
				$audio_path = wp_get_attachment_url($audio_id);
			}
		}
		
		$images = get_post_meta($gallery->ID, 'images', true);
		
		$logo_id = get_post_meta($gallery->ID, 'logo', true); 
		if (!empty($logo_id))
		{
			$logo_path = wp_get_attachment_url($logo_id);
		}
		
		$theme_path = self::getTheme($gallery->ID);
		
		self::trackView($gallery->ID);
		
		require(GALLERYPROPLUS_DIR . '/themes/' . $theme_path . '/gallery.php');
	}
	
	public static function showPageNotFound()
	{
		status_header(404);
		nocache_headers();
		include(get_404_template());
	}
	
	public static function showPasswordForm($gallery)
	{
		$theme_path = self::getTheme($gallery->ID);
		require(GALLERYPROPLUS_DIR . '/themes/' . $theme_path . '/password-form.php');
	}
	
	public static function startsWith($haystack, $needle)
	{
		return $needle === '' || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
	}
	
	public static function trackView($gallery_id)
	{
		if (!self::isAdmin())
		{
			$cookie_name = 'wp_plugin_galleryproplus_trackview_' . $gallery_id;
			if (!isset($_COOKIE[$cookie_name]))
			{
				$views = get_post_meta($gallery_id, 'views', true);
				if (empty($views))
				{
					$views = 0;
				}
				else
				{
					$views = intval($views);
				}
				$views++;
				update_post_meta($gallery_id, 'views', $views);
				
				setcookie($cookie_name, md5(rand(1000000, 9999999) . $gallery_id . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . time()), mktime(23, 59, 59, 12, 31, 2037), '/');	
			}
		}
	}
	
	public static function truncate($data, $length)
	{
		return self::truncateData($data, $length);
	}
	
	public static function truncateData($data, $length)
	{
		if (strlen($data) > $length)
		{
			$data = substr($data, 0, $length);
		}
		
		return $data;
	}
	
	public static function validateData($data, $type)
	{
		if (strlen($data) != 0)
		{
			switch ($type)
			{
				case 'boolean' : return (filter_var($data, FILTER_VALIDATE_BOOLEAN) !== false);
					break;
				case 'email' : return (filter_var($data, FILTER_VALIDATE_EMAIL) !== false);
					break;
				case 'float' : return (filter_var($data, FILTER_VALIDATE_FLOAT) !== false);
					break;
				case 'int' : return (filter_var($data, FILTER_VALIDATE_INT) !== false);
					break;
				case 'ip' : return (filter_var($data, FILTER_VALIDATE_IP) !== false);
					break;
				case 'url' : return (filter_var($data, FILTER_VALIDATE_URL) !== false);
					break;
			}
		}
		
		return false;
	}
}

/*
 * @desc Main driver.
 */
class GalleryProPlus
{
	/*
	 * @desc Activates plugin.
	 */
	public function activate()
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
	public function admin_head()
	{
		printf('<link rel="stylesheet" type="text/css" href="%s/admin/styles.css" />', GALLERYPROPLUS_URL);
	}
	
	/*
	 * @desc Triggered before any other hook when a user accesses the admin area.
	 */
	public function admin_init()
	{
		$this->register_settings();
	}
	
	/*
	 * @desc Add extra submenus and menu options to the admin panel's menu structure. It runs after the basic admin panel menu structure is in place.
	 */
	public function admin_menu()
	{
		add_submenu_page('edit.php?post_type=galleryproplus', 'GP+ Settings', 'Settings', 'manage_options', 'settings', array($this, 'menu'));
	}
	
	public function before_delete_post($pid)
	{
		global $wpdb;
		
		$rows = $wpdb->get_results(sprintf("SELECT `id` FROM `%sposts` WHERE `post_parent`=%d", $wpdb->prefix, $pid), ARRAY_A);
		if (is_array($rows) && count($rows) > 0)
		{
			foreach ($rows As $row)
			{
				wp_delete_attachment($row['id'], true);
			}
		}
	}
	
	/*
	 * @desc Tweak custom post type list columns.
	 */
	public function columns($columns)
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
	 * @desc Create custom post type.
	 */
	private function create_post_type()
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
			'rewrite'            => array('slug'=>GalleryProPlusHelper::getBasePath()),
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
	 * @desc Tweak custom post type list column data.
	 */
	public function custom_column($column, $post_id)
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
				echo GalleryProPlusHelper::getTheme($post_id, true);
				break;
			
			case 'url':
				if (get_post_field('post_status', $post_id) == 'draft')
				{
					echo 'Unavailable';
					return;
				}
				$path = get_site_url().GalleryProPlusHelper::getBasePath().'/'.get_post_field('post_name', $post_id); ?>
				<a href="#TB_inline?width=300&height=100&inlineId=gallery-url-<?php echo $post_id ?>" class="thickbox"><img src="https://cdn0.iconfinder.com/data/icons/feather/96/591256-link-16.png" alt="" /></a>
				&nbsp;
				<a href="<?php echo $path ?>" target="_blank"><img src="https://cdn0.iconfinder.com/data/icons/octicons/1024/link-external-16.png" alt="" /></a>
				<div id="gallery-url-<?php echo $post_id ?>" style="display:none;">
					<p><strong>Click to select your Gallery Pro Plus URL:</strong></p>
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
	 * @desc Deactivates plugin.
	 */
	public function deactivate()
	{
		;
	}
	
	/*
	 * @desc Fires after WordPress has finished loading but before any headers are sent.
	 */
	public function init()
	{
		$this->create_post_type();
		$this->register_field_groups();
	}
	
	/*
	 * @desc Processes admin menu requests.
	 */
	public function menu()
	{
		$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
		if ($page !== false && in_array($page, array('settings')))
		{
			include(GALLERYPROPLUS_DIR . '/admin/' . $page . '.php');
		}
	}
	
	public function parse_request($query)
	{
		GalleryProPlusHelper::processRequest();
	}
	
	/*
	 * @desc Applies to the posts where clause and restricts which posts will show up in various areas of the site.
	 */
	public function posts_where($where)
	{
		$where .= " AND (wp_posts.post_parent=0 OR wp_posts.post_parent NOT IN (SELECT wp_posts.ID FROM wp_posts WHERE wp_posts.post_type='" . GALLERYPROPLUS_TYPE . "'))";
		remove_filter('posts_where', array($this, 'posts_where'));
		return $where;
	}
	
	/*
	 * @desc Called after the query variable object is created, but before the actual query is run.
	 */
	public function pre_get_posts($query)
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
					$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
					if (!($pagenow == 'edit.php' || $pagenow == 'upload.php' || ($pagenow == 'admin-ajax.php' && $action !== false && $action == 'query-attachments')))
					{
						return;
					}
					
					$suppress_filters = $query->get('suppress_filters');
					if (!$suppress_filters)
					{
						add_filter('posts_where', array($this, 'posts_where'));
					}
				}
			}
		}
	}
	
	/*
	 * @desc This hook is called once any activated plugins have been loaded. Is generally used for immediate filter setup, or plugin overrides.
	 */
	public function plugins_loaded()
	{
		;
	}
	
	/*
	 * @desc Registers field groups for custom post type.
	 */
	private function register_field_groups()
	{
		$settings = get_option('galleryproplus_settings');
		$slideshow_transition_speeds = GalleryProPlusHelper::getSlideshowTransitionSpeeds();
		$slideshow_transition_types = GalleryProPlusHelper::getSlideshowTransitionTypes();
		$themes = GalleryProPlusHelper::getThemes();
		
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
					'default_value' => GalleryProPlusHelper::getDefaultTheme(),
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
	private function register_settings()
	{
		register_setting('settings', 'galleryproplus_settings');
		
		add_settings_section(
			'galleryproplus_settings_section',
			'Common Settings',
			array($this, 'settings_section_callback'),
			'settings'
		);
		
		add_settings_field(
			'base_path',
			'Base Page Path',
			array($this, 'settings_field_base_path_render'),
			'settings',
			'galleryproplus_settings_section'
		);
		
		add_settings_field(
			'default_theme',
			'Default Theme',
			array($this, 'settings_field_default_theme_render'),
			'settings',
			'galleryproplus_settings_section'
		);
		
		add_settings_field(
			'default_slideshow_transition_speed',
			'Default Slideshow Speed',
			array($this, 'settings_field_default_slideshow_transition_speed_render'),
			'settings',
			'galleryproplus_settings_section'
		);
		
		add_settings_field(
			'default_slideshow_transition_type',
			'Default Slideshow Transition',
			array($this, 'settings_field_default_slideshow_transition_type_render'),
			'settings',
			'galleryproplus_settings_section'
		);
		
		add_settings_field(
			'default_recipients',
			'Default Email Recipient(s)',
			array($this, 'settings_field_default_recipients_render'),
			'settings',
			'galleryproplus_settings_section'
		);
	}
	
	/*
	 * @desc Renders `basepath` setting field.
	 */
	public function settings_field_base_path_render()
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
	public function settings_field_default_theme_render()
	{
		$settings = get_option('galleryproplus_settings');
		$options = GalleryProPlusHelper::getThemes();
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
	public function settings_field_default_slideshow_transition_speed_render()
	{
		$settings = get_option('galleryproplus_settings');
		$options = GalleryProPlusHelper::getSlideshowTransitionSpeeds();
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
	public function settings_field_default_recipients_render()
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
	public function settings_field_default_slideshow_transition_type_render()
	{
		$settings = get_option('galleryproplus_settings');
		$options = GalleryProPlusHelper::getSlideshowTransitionTypes();
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
	public function settings_section_callback($args)
	{
		echo 'Tweak the settings below to fit the requirements for the average gallery.';
	}
	
	/*
	 * @desc Enqueue items that are meant to appear on the front end, i.e., scripts and styles.
	 */
	public function wp_enqueue_scripts()
	{
		//wp_enqueue_style(GALLERYPROPLUS_TYPE, GALLERYPROPLUS_URL . '/css/styles.css');
	}
}
