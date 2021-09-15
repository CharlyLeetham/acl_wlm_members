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
	
	foreach ( $memmore as $k=>$v ) { //Cycle through the members and get their ID's. $memmore only has the basic user details for the members in the level. We need to get more.
		$levels = wlmapi_get_member_levels($v['id']); //Using the member ID, get the membership level details. We're going to use this information to find those that need approval. 
		foreach ( $levels as $k2=>$v2 ) { // Because get_member_levels pulls back all levels a member is in, we're going to filter for only the level we're looking.
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
			$levstatus = $v3->Status;
			if ( $levstatus[0] == 'For Approval' ) {
				$approvids[$i] = $v['id'];
				$i++;
			}
		}
	}
	
	// $approvids is the list we need to display
	
	ob_start();
	echo '
	<style>
		.grid {
			display: flex;
			flex-direction: row;
			flex-wrap: wrap;
		}
		
		.grid > * {
			flex: 1 100%;
		}
		
		.grid .header {
			flex: 1;
			background-color: #779ccd;
			text-align: center;
			padding: 10px 0;			
		}

		.grid .header:last-child {
			flex: 0;
			background-color: #779ccd;
			text-align: center;
		}
		
		.grid .header p.item {
			color: #ffffff;
			font-weight: bold;
			margin: 0;
		}
	</style>
	
	<div class="grid">
	  <div class="column header"><p class="item">Full Name</p></div>
	  <div class="column header"><p class="item">LU Email</p></div>
	  <div class="column header"><p class="item">Gender</p></div>
	  <div class="column header"><p class="item">Faculty</p></div>
	  <div class="column header"><p class="item">Department</p></div>
	  <div class="column header"><p class="item">Dissertation Defence</p></div>
	  <div class="column header"><p class="item">Approve</p></div>
	</div>';
	
	foreach ( $approvids as $k => $v ) {
		$approvmem = wlmapi_get_member($v);		 
		$user_info = get_userdata($v); // Get the user info so we can get First and Last Name

		echo 'Name: '. $user_info->first_name .' ' .$user_info->last_name.'<br />';		 	 
		echo 'User Email: '.$approvmem['member'][0]['UserInfo']['user_email'].'<br />';
		$memdata = $approvmem['member'][0]['UserInfo']['wldata'];
		if ( $memdata->custom_department !== 'Other' ) {
		echo 'Department: '.$memdata->custom_department.'<br />';
		} else {
			echo 'Other Department: '.$memdata->custom_other_dept.'<br />';
		}
		
		if ( $memdata->custom_faculty !== 'Other' ) {
			echo 'Faculty: '.$memdata->custom_faculty.'<br />';
		} else {
			echo 'Other Faculty: '.$memdata->custom_other_faculty.'<br />';
		}
		echo 'Dissertation Defence: '.$memdata->custom_dis_defence.'<br />';
		echo 'Gender: '.$memdata->custom_gender.'<br />';
	}
	
	$output = ob_get_contents();
	ob_end_clean();
	echo $output;
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