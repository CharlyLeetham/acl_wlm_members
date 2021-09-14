<?php 
/*
Plugin Name: ACL WLM List Members In Level
Plugin URI: http://askcharlyleetham.com
Description: Shortcode query WLM and return details of members.
Version: 1
Author: Charly Dwyer
Author URI: http://askcharlyleetham.com
License: GPL

Changelog
Version 1.0 - Original Version
*/

function acl_get_wlmopts( $atts, $content ) {
	$members = wlmapi_the_level_members(1631561301);
	$memmore = $members['members']['member'];
	$i = 0;
	foreach ( $memmore as $k=>$v ) {
		echo 'Key: '.$k.'<br />';
		var_dump ( $v['id'] );
		$memlev[$i] = $v['id'];
		$i++;
		$levels = wlmapi_get_member_levels($v['id']);
		// echo '<pre />';
			// var_dump($levels);
			// echo '<br /><br />';
		// echo '</pre>';
		
		$allowed  = [1631561301];
		$filtered = array_filter(
			$levels,
			fn ($key) => in_array($key, $allowed),
			ARRAY_FILTER_USE_KEY
		);	

		var_dump ($filtered);
		echo '<br />';
		
	}
	// var_dump ( $memlev );
	


}
add_shortcode ( 'acl_wlmoptprint', 'acl_get_wlmopts' );


function acl_listwlmopts( $atts,$content ) {
	extract(shortcode_atts(array(
				'inclevelid' => '',
				'inclevelname' => '',
				'exclevelid' => '',
				'exclevelname' => '',
				'messagetodisplay' => '',
	), $atts));

		$inclevelname = explode(',',$inclevelname);
		$inclevelid = explode(',',$inclevelid);
		$exclevelid = explode(',',$exclevelid);
		$exclevelname = explode(',',$exclevelname);

	$returnvalue=$this->twpw_getwlmlevel_tags($inclevelid,$inclevelname,$exclevelid,$exclevelname,$messagetodisplay,$content);

	if (is_array($returnvalue)) {
		return $returnvalue;
	} elseif ($returnvalue == ' ') {
		return;
	} else {
		return $returnvalue;
	}
}
add_shortcode ( 'acl_listwlmopts', 'acl_listwlmopts' );

?>