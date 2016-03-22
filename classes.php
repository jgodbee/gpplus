<?php defined('ABSPATH') or die('-1');

/*
 * @desc Helper methods.
 */
class GalleryProPlus
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
		if (empty(GalleryProPlus::$base_path))
		{
			$options = get_option('galleryproplus_settings');
			GalleryProPlus::$base_path = (isset($options['base_path']) && !empty($options['base_path'])) ? $options['base_path'] : GALLERYPROPLUS_PATH;
		}
		return GalleryProPlus::$base_path;
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
		if (empty(GalleryProPlus::$default_slideshow_transition_speed))
		{
			$options = get_option('galleryproplus_settings');
			GalleryProPlus::$default_slideshow_transition_speed = (isset($options['default_slideshow_transition_speed']) && !empty($options['default_slideshow_transition_speed'])) ? $options['default_slideshow_transition_speed'] : '';
		}
		return GalleryProPlus::$default_slideshow_transition_speed;
	}
	
	public static function getDefaultSlideshowTransitionType()
	{
		if (empty(GalleryProPlus::$default_slideshow_transition_type))
		{
			$options = get_option('galleryproplus_settings');
			GalleryProPlus::$default_slideshow_transition_type = (isset($options['default_slideshow_transition_type']) && !empty($options['default_slideshow_transition_type'])) ? $options['default_slideshow_transition_type'] : '';
		}
		return GalleryProPlus::$default_slideshow_transition_type;
	}
	
	public static function getDefaultTheme()
	{
		if (empty(GalleryProPlus::$default_theme))
		{
			$options = get_option('galleryproplus_settings');
			GalleryProPlus::$default_theme = (isset($options['default_theme']) && !empty($options['default_theme'])) ? $options['default_theme'] : '';
		}
		return GalleryProPlus::$default_theme;
	}
	
	public static function getGalleryIds()
	{
		if (empty(GalleryProPlus::$gallery_ids))
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
			
			GalleryProPlus::$gallery_ids = $gallery_ids;
		}
		
		return GalleryProPlus::$gallery_ids;
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
		if (empty(GalleryProPlus::$themes))
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
			
			GalleryProPlus::$themes = $themes;
		}
		
		return GalleryProPlus::$themes;
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
