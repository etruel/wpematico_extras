<?php
/**
 * Helper Functions
 *
 * @package     WPeMatico\Extras\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
		/**
		 * Static function save_file_from_url 
		 * We need this custom function to avoid memory overflow problems on BIG files, 
		 * this allow partial uploads by resuming file downloads in parts.
		 * 
		 * @access public
		 * @param $url_origin String contain the URL of File will be uploaded.
		 * @param $new_file String contain the Path of File where it will be saved.
		 * @return bool true if is success
		 * @since 1.9.0
		 */
		function wpematico_extras_save_file_from_url($url_origin, $new_file) {
			global $wp_filesystem;

			$ch			 = curl_init($url_origin);
			if(!$ch)
				return false;
			$dest_file	 = apply_filters('wpematico_overwrite_file', $new_file);
			if($dest_file === FALSE)
				return $new_file;  // Don't upload it and return the name like it was uploaded
			$new_file	 = $dest_file;
			$i			 = 1;
			while (file_exists($new_file)) {
				$file_extension = strrchr($new_file, '.'); //Will return .JPEG
				if($i == 1) {
					$file_name	 = substr($new_file, 0, strlen($new_file) - strlen($file_extension));
					$new_file	 = $file_name . "-$i" . $file_extension;
				}else {
					$file_name	 = substr($new_file, 0, strlen($new_file) - strlen($file_extension) - strlen("-$i"));
					$new_file	 = $file_name . "-$i" . $file_extension;
				}
				$i++;
			}
			$fs_file = fopen($new_file, "w");
			//curl_setopt ($ch, CURLOPT_URL, $url_origin);
			curl_setopt($ch, CURLOPT_FILE, $fs_file);
			curl_setopt($ch, CURLOPT_HEADER, 0);

			/**
			 * It could be used to add cURL options to request.
			 * @since 1.9.0
			 */
			$ch = apply_filters('wpematico_save_file_from_url_params', $ch, $url_origin);

			curl_exec($ch);

			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			fclose($fs_file);

			if(!($httpcode >= 200 && $httpcode < 300))
				unlink($new_file);
			return ($httpcode >= 200 && $httpcode < 300) ? $new_file : false;
		}

function wpematico_extras_aux_curl($data, $curl, $url) {
	if(is_bool($curl)) {
		$args = array(
			'curl' => $curl,
		);
	} else {
		$args = $curl;
	}
	$defaults = array(
		'curl'			 => true,
		'curl_setopt'	 => array(
			'CURLOPT_HEADER'		 => 0,
			'CURLOPT_RETURNTRANSFER' => 1,
			'CURLOPT_FOLLOWLOCATION' => 0,
			'CURLOPT_ENCODING'		 => '',
			//'CURLOPT_USERAGENT'=> "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1",
			'CURLOPT_USERAGENT'		 => "Mozilla/5.0 (Windows NT 5.1; rv:5.0) Gecko/20100101 Firefox/5.0 Firefox/5.0",
		),
	);


	$r = wp_parse_args($args, $defaults);

	if($r['curl'] && function_exists('curl_version')) {
	    $data	 = wpematico_extras_file_get_contents_curl($url, $r);
	} 

	return $data;
	
}
function wpematico_extras_file_get_contents_curl($url, $args = '') {
	if(empty($args)) {
		$args = array();
	}
	$defaults	 = array(
		'safemode'		 => false,
		'curl_setopt'	 => array(
			'CURLOPT_HEADER'		 => 0,
			'CURLOPT_RETURNTRANSFER' => 1,
			'CURLOPT_FOLLOWLOCATION' => 0,
			//'CURLOPT_SSL_VERIFYPEER'=> 0,
			'CURLOPT_USERAGENT'		 => "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1",
		),
	);
	$r			 = wp_parse_args($args, $defaults);
	$ch			 = curl_init();
	if(!$ch)
		return false;

	$safemode = ini_get('safe_mode');
	if(!$r['safemode']) {
		ini_set('safe_mode', false);
	}else {
		ini_set('safe_mode', true);
	}

	curl_setopt($ch, CURLOPT_URL, $url);
	foreach($r['curl_setopt'] as $key => $value) {
		curl_setopt($ch, @constant($key), $value);
	}

	$data		 = curl_exec($ch);
	$httpcode	 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if(function_exists('ini_set')) {
		ini_set('safe_mode', $safemode);
	}


	return ($httpcode >= 200 && $httpcode < 300) ? $data : false;
}

?>