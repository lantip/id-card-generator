<?php

/*
Plugin Name: Code Auto Escape
Plugin URI: http://priyadi.net/archives/2005/09/27/wordpress-plugin-code-autoescape/
Description: Automatically escape code within &lt;code&gt;...&lt;/code&gt; tag
Version: 2.0
Author: Priyadi Iman Nurcahyo
Author URI: http://priyadi.net/
*/


### mask code ###
function pri_cae_mask($text) {
	$textarr = preg_split("/(<code[^>]*>.*<\\/code>)/Us", $text, -1, PREG_SPLIT_DELIM_CAPTURE); // capture the tags as well as in between
	$stop = count($textarr);// loop stuff
	for ($i = 0; $i < $stop; $i++) {
		$content = $textarr[$i];
		if (preg_match("/^<code[^>]*>(.*)<\\/code>/Us", $content, $code)) { // If it's a code	
			$content = '[code]' . base64_encode($code[1]) . '[/code]';
		}
		$output .= $content;
	}
	return $output;
}

### unmask code ###
function pri_cae_unmask($text, $replace = false, $addpre = false) {
	$textarr = preg_split("/(\\[code\\].*\\[\\/code\\])/Us", $text, -1, PREG_SPLIT_DELIM_CAPTURE); // capture the tags as well as in between
	$stop = count($textarr);// loop stuff
	for ($i = 0; $i < $stop; $i++) {
		$content = $textarr[$i];
		if (preg_match("/^\\[code\\](.*)\\[\\/code\\]/Us", $content, $code)) { // If it's a code
			$content = base64_decode($code[1]);
			if ($replace) {
				$content = preg_replace("/\\r/", '', $content);
				$content = preg_replace("/^\\s*?\\n/", "\n", $content);
				$content = preg_replace("/&/", '&amp;', $content);
				$content = preg_replace("/</", '&lt;', $content);
				$content = preg_replace("/>/", '&gt;', $content);
				$content = '<code>' . $content . '</code>';
				if ($addpre) {
					if (preg_match('/\\n/', $content)) {
						$content = "<pre>" . $content . "</pre>";
					}
				}
			} else {
				$content = "<code>" . $content . "</code>";
			}
		}
		$output .= $content;
	}
	return $output;
}

### unmask and do replacement ###
function pri_cae_unmask_replace($text) {
	return pri_cae_unmask($text, true);
}

### unmask and do replacement, plus enclose in <pre> ###
function pri_cae_unmask_replace_addpre($text) {
	return pri_cae_unmask($text, true, true);
}


add_filter('content_save_pre', 'pri_cae_mask', 28);
add_filter('content_save_pre', 'pri_cae_unmask', 72);
add_filter('the_content', 'pri_cae_mask', 1);
add_filter('the_content', 'pri_cae_unmask_replace', 99);

add_filter('excerpt_save_pre', 'pri_cae_mask', 28);
add_filter('excerpt_save_pre', 'pri_cae_unmask', 72);
add_filter('the_excerpt', 'pri_cae_mask', 1);
add_filter('the_excerpt', 'pri_cae_unmask_replace', 99);

add_filter('pre_comment_content', 'pri_cae_mask', 4);
add_filter('pre_comment_content', 'pri_cae_unmask', 36);
add_filter('comment_save_pre', 'pri_cae_mask', 28);
add_filter('comment_save_pre', 'pri_cae_unmask', 72);
add_filter('comment_text', 'pri_cae_mask', 1);
add_filter('comment_text', 'pri_cae_unmask_replace_addpre', 99);


?>