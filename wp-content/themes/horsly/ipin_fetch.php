<?php
error_reporting(0);
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php');

$url = esc_url_raw($_GET['url']);

if ($url) {
	$response = wp_remote_get($url);

	if(is_wp_error($response)) {
	   echo 'error' . $response->get_error_message();
	} else {
		preg_match("/content=\"text\/(.*)>/i", $response['body'], $content_type);

		if (strpos($response['headers']['content-type'], 'text/') !== false && ($response['response']['code'] == '200')) {
			preg_match("/<title>(.*)<\/title>/i", $response['body'], $title);

			//for multiple languages in title ref: http://php.net/manual/en/function.htmlentities.php
			if (strpos($content_type[0], '8859-1') !== false) {
				$ipintitle = '<ipintitle>' . htmlentities($title[1], ENT_QUOTES, 'ISO-8859-1') . '</ipintitle>';
			} else if (strpos($content_type[0], '8859-5') !== false) {
				$ipintitle = '<ipintitle>' . htmlentities($title[1], ENT_QUOTES, 'ISO-8859-5') . '</ipintitle>';
			} else if (strpos($content_type[0], '8859-15') !== false) {
				$ipintitle = '<ipintitle>' . htmlentities($title[1], ENT_QUOTES, 'ISO-8859-15') . '</ipintitle>';
			} else if (strpos($content_type[0], '866') !== false) {
				$ipintitle = '<ipintitle>' . htmlentities($title[1], ENT_QUOTES, 'cp866') . '</ipintitle>';
			} else if (strpos($content_type[0], '1251') !== false) {
				$ipintitle = '<ipintitle>' . htmlentities($title[1], ENT_QUOTES, 'cp1251') . '</ipintitle>';
			} else if (strpos($content_type[0], '1252') !== false) {
				$ipintitle = '<ipintitle>' . htmlentities($title[1], ENT_QUOTES, 'cp1252') . '</ipintitle>';
			} else if (stripos($content_type[0], 'koi8') !== false) {
				$ipintitle = '<ipintitle>' . htmlentities($title[1], ENT_QUOTES, 'KOI8-R') . '</ipintitle>';
			} else if (stripos($content_type[0], 'hkscs') !== false) {
				$ipintitle = '<ipintitle>' . mb_convert_encoding($title[1], 'UTF-8', 'BIG5-HKSCS') . '</ipintitle>';
			} else if (stripos($content_type[0], 'big5') !== false || strpos($content_type[0], '950') !== false ) {
				$ipintitle = '<ipintitle>' . mb_convert_encoding($title[1], 'UTF-8', 'BIG5') . '</ipintitle>';
			} else if (strpos($content_type[0], '2312') !== false || strpos($content_type[0], '936') !== false ) {
				$ipintitle = '<ipintitle>' . mb_convert_encoding($title[1], 'UTF-8', 'GB2312') . '</ipintitle>';
			} else if (stripos($content_type[0], 'jis') !== false || strpos($content_type[0], '932') !== false ) {
				$ipintitle = '<ipintitle>' . mb_convert_encoding($title[1], 'UTF-8', 'Shift_JIS') . '</ipintitle>';
			} else if (stripos($content_type[0], 'jp') !== false) {
				$ipintitle = '<ipintitle>' . mb_convert_encoding($title[1], 'UTF-8', 'EUC-JP') . '</ipintitle>';
			} else {
				$ipintitle = '<ipintitle>' . htmlentities($title[1], ENT_QUOTES, 'UTF-8') . '</ipintitle>';
			}
			
			echo str_ireplace('</head>', $ipintitle . "\r\n" . '</head>', absolute_url($response['body'], parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST)));
		} else {
			echo 'error' . __('We are sorry but we are not able to share this story :(.. Try a different story.', 'ipin');
		}
	}
} else {
	echo 'error' . __('We are sorry but we are not able to share this story :(.. Try a different story.', 'ipin');
}

//convert relative to absolute path for images
//ref: http://www.howtoforge.com/forums/showthread.php?t=4
function absolute_url($txt, $base_url){
  $needles = array('src="');
  $new_txt = '';
  if(substr($base_url,-1) != '/') $base_url .= '/';
  $new_base_url = $base_url;
  $base_url_parts = parse_url($base_url);

  foreach($needles as $needle){
    while($pos = strpos($txt, $needle)){
      $pos += strlen($needle);
      if(substr($txt,$pos,7) != 'http://' && substr($txt,$pos,8) != 'https://' && substr($txt,$pos,6) != 'ftp://' && substr($txt,$pos,9) != 'mailto://'){
        if(substr($txt,$pos,1) == '/') $new_base_url = $base_url_parts['scheme'].'://'.$base_url_parts['host'];
        $new_txt .= substr($txt,0,$pos).$new_base_url;
      } else {
        $new_txt .= substr($txt,0,$pos);
      }
      $txt = substr($txt,$pos);
    }
    $txt = $new_txt.$txt;
    $new_txt = '';
  }
  return $txt;
}
?>