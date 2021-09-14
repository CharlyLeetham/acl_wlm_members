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
	foreach ($members as $k=>$v) {
		echo 'Key: '.$k.'<br />';
		var_dump ($v);
		echo '<br />';
	}
     //print_r($members);
	// return $wlmoptprint;
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