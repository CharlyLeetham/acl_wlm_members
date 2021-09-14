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
	$i = 0;
	$members = wlmapi_the_level_members(1631561301); // Feed the membership level ID in as as shortcode att.
	$memmore = $members['members']['member'];  //Get the member details based on the Membership level ID.
	$allowed  = [1631561301];	// this is the Membership level we're looking for. This will need to be fed in as a shortcode att.
	echo '<pre>';	
	
	foreach ( $memmore as $k=>$v ) { //Cycle through the members and get their ID's. $memmore only has the basic user details for the members in the level. We need to get more.
		// echo 'Key: '.$k.'<br />';
		// var_dump ( $v );
		// $memlev[$i] = $v['id'];
		// $i++;
		$levels = wlmapi_get_member_levels($v['id']); //Using the member ID, get the membership level details. We're going to use this information to find those that need approval. 
		// var_dump($levels);
		// echo '<br /><br />'	
		foreach ( $levels as $k2=>$v2 ) { // Because get_member_levels pulls back all levels a member is in, we're going to filter for only the level we're looking.
			// echo 'Key2: '.$k2.'<br />';
			// var_dump( $v2 );		
			$filtered = array_filter(
				$levels,
				function ($k2) use ($allowed) {
						return in_array($k2, $allowed);
					},
				ARRAY_FILTER_USE_KEY
			);	
		}

		//Now we have an array ($filtered) that has the members we're looking for and only the details of the membership level we're working with.

		foreach ( $filtered as $k3 => $v3 ) { //Loop through $filtered and find the members who require approval.
			// var_dump($v3);
			// echo '<br />';
			$levstatus = $v3->Status;
			if ( $levstatus[0] == 'For Approval' ) {
				// echo 'Member '. $v['id'].' We have a hit <br />';
				$approvids[$i] = $v['id'];
				$i++;
			}
		}
	}
	
	// $approvids is the list we need to display

	
	foreach ( $approvids as $k => $v ) {
		 $approvmem = wlmapi_get_member($v);
		 // var_dump ( $approvmem );
		 
		 
// ["wldata"]=>
        // object(stdClass)#6214 (17) {
          // ["custom_department"]=>
          // &string(8) "Aviation"
          // ["custom_dis_defence"]=>
          // &string(4) "2024"
          // ["custom_dropdown_field"]=>
          // &string(21) "Humanities & Theology"
          // ["custom_faculty"]=>
          // &string(8) "Aviation"
          // ["custom_gender"]=>
          // &string(6) "Female"
          // ["custom_other_dept"]=>
          // &string(0) ""
          // ["custom_other_faculty"]=>
          // &string(0) ""
          // ["custom_text_field"]=>
          // &string(4) "2024"		 

		 $memdata = $approvmem['member'][0]['UserInfo']['wldata'];
		 var_dump ( $memdata );
		 echo 'Department: '.$memdata->custom_department.'<br />';
		 echo 'Other Department: '.$memdata->custom_other_dept.'<br />';
		 echo 'Faculty: '.$memdata->custom_faculty.'<br />';
		 echo 'Other Faculty: '.$memdata->custom_other_faculty.'<br />';
		 echo 'Dissertation Defence: '.$memdata->custom_dis_defence.'<br />';
		 echo 'Gender: '.$memdata->custom_gender.'<br />';
	}

	echo '</pre>';		 

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