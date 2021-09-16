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
			margin: 15px;
		}
		
		.row {
			display: flex;
			flex-flow: column wrap;	
			width: 100%;
			padding: 10px 5px;
			text-align: center;
		}
		
		.grid .row.header {
			background-color: #779ccd;
			text-align: center;		
		}
		
		.column {
			display: flex;
			flex: 1;
			flex-direction: column;
		}
		
		.column.name {
			flex-grow: 1;
			height: 100%;
			flex-basis: 30%;
		}

		.column.approve {
			flex-grow: 1;
			height: 100%;
			flex-basis: 30%;
		}

		.grid .column:last-child {
			flex: 0;
		}
		
		.grid .header .item {
			color: #ffffff;
			font-weight: bold;
			margin: 0;
		}
	</style>
	
	<div class="grid">
		<div class="row header">
			<div class="column"><div class="item">Full Name</div></div>
			<div class="column "><div class="item">Details</div></div>
			<div class="column "><div class="item">Approve</div></div>
		</div> <!-- row -->
	</div> <!-- grid -->';		
	foreach ( $approvids as $k => $v ) {
		$approvmem = wlmapi_get_member($v);		 
		$user_info = get_userdata($v); // Get the user info so we can get First and Last Name
		$memdata = $approvmem['member'][0]['UserInfo']['wldata'];
		echo ' 
		<div class="row">
			<div class="column name"><p class="item">'.$user_info->first_name .' ' .$user_info->last_name.'</p></div>
			<div class="column "><p class="item">'.$approvmem['member'][0]['UserInfo']['user_email'].'</p></div>
			<div class="column "><p class="item">'.$memdata->custom_gender.'</p></div>
			<div class="column "><p class="item">';
			if ( $memdata->custom_faculty !== 'Other' ) {
				echo $memdata->custom_faculty;
			} else {
				echo $memdata->custom_other_faculty;
			}
			echo '</p></div>
			<div class="column "><p class="item">';
			if ( $memdata->custom_department !== 'Other' ) {
				echo $memdata->custom_department;
			} else {
				echo $memdata->custom_other_dept;
			}
			echo '</p></div>
			<div class="column "><p class="item">'.$memdata->custom_dis_defence.'</p></div>
			<div class="column approve"><p class="item">chk</p></div>
		</div>';
	}
	
	echo '
	<style>
	
		.grid {
			margin: 15px;
		}
		
		.row {
			display: flex;
			flex-flow: row wrap;	
			width: 100%;
			padding: 10px 5px;
			text-align: center;
		}
		
		.grid .row.header {
			background-color: #779ccd;
			text-align: center;		
		}
		
		.column {
			display: flex;
			flex: 1;
			flex-direction: column;
		}

		.grid .column:last-child {
			flex: 0;
		}
		
		.grid .header .item {
			color: #ffffff;
			font-weight: bold;
			margin: 0;
		}
	</style>
	
	<div class="grid">
		<div class="row header">
			<div class="column"><div class="item">Full Name</div></div>
			<div class="column "><div class="item">LU Email</div></div>
			<div class="column "><div class="item">Gender</div></div>
			<div class="column "><div class="item">Faculty</div></div>
			<div class="column "><div class="item">Department</div></div>
			<div class="column "><div class="item">Dissertation Defence</div></div>
			<div class="column "><div class="item">Approve</div></div>
		</div> <!-- row -->
	</div> <!-- grid -->';		
	foreach ( $approvids as $k => $v ) {
		$approvmem = wlmapi_get_member($v);		 
		$user_info = get_userdata($v); // Get the user info so we can get First and Last Name
		$memdata = $approvmem['member'][0]['UserInfo']['wldata'];
		echo ' 
		<div class="row">
			<div class="column "><p class="item">'.$user_info->first_name .' ' .$user_info->last_name.'</p></div>
			<div class="column "><p class="item">'.$approvmem['member'][0]['UserInfo']['user_email'].'</p></div>
			<div class="column "><p class="item">'.$memdata->custom_gender.'</p></div>
			<div class="column "><p class="item">';
			if ( $memdata->custom_faculty !== 'Other' ) {
				echo $memdata->custom_faculty;
			} else {
				echo $memdata->custom_other_faculty;
			}
			echo '</p></div>
			<div class="column "><p class="item">';
			if ( $memdata->custom_department !== 'Other' ) {
				echo $memdata->custom_department;
			} else {
				echo $memdata->custom_other_dept;
			}
			echo '</p></div>
			<div class="column "><p class="item">'.$memdata->custom_dis_defence.'</p></div>
			<div class="column "><p class="item">chk</p></div>
		</div>';
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