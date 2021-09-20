<?php 
/*
Plugin Name: ACL WLM List Members In Level
Plugin URI: http://askcharlyleetham.com
Description: Shortcode query WLM and return details of members. Set up a list of For Approval members and then approve them to the level. 
Version: 1
Author: Charly Dwyer
Author URI: http://askcharlyleetham.com
License: GPL

Changelog
Version 1.0 - Original Version
Version 1.01 - Add a Decline Function
*/

class acl_wlm_members {

	function acl_get_wlmopts( $atts, $content ) { // This is the function that lists the members to be approved and then approves them.
		
		if(isset($_POST["approvebulk"])){  //If the submit button has been clicked, this runs.
			$result ='';
			$message = '';
			
			// We're going to change members from "For Approval to Pending. That's done using this array			
			$args = array(
				  'Pending' => false
			 );	

			//Loop through each member who had the approve button clicked.
			foreach ($_POST['approvemember'] as $k=>$v) {
				$result = wlmapi_update_level_member_data($_POST['levelid'] , $v , $args);
				$user_info = get_userdata($v); // Get the user info so we can get First and Last Name				
				$message .= 'Member ID: '.$v.' (';
				if ( $user_info->first_name ) {
					$message .= $user_info -> first_name;
				}
				
				if ( $user_info->first_name && $user_info->last_name ){
					$message .= ' ';
				}
				
				if ( $user_info->last_name ) {
					$message .=  $user_info->last_name;
				}
				
				if ( ($user_info->first_name || $user_info->last_name) && $user_info->user_email ) {
					$message .=  ' - ';
				}
				
				if ( $user_info->user_email ) {
					$message .=  $user_info->user_email;
				}
				
				$message .=  ') approved.<br />';
			}

			foreach ($_POST['declinemember'] as $k=>$v) {
				$user_info = get_userdata($v); // Get the user info so we can get First and Last Name				
				$message .= 'Member ID: '.$v.' (';
				if ( $user_info->first_name ) {
					$message .= $user_info -> first_name;
				}
				
				if ( $user_info->first_name && $user_info->last_name ){
					$message .= ' ';
				}
				
				if ( $user_info->last_name ) {
					$message .=  $user_info->last_name;
				}
				
				if ( ($user_info->first_name || $user_info->last_name) && $user_info->user_email ) {
					$message .=  ' - ';
				}
				
				if ( $user_info->user_email ) {
					$message .=  $user_info->user_email;
				}
				
				$message .=  ') declined.<br />';
				$result = wp_delete_user( $v );
			}
			
			echo $message;
			echo '<br />';
		} else {
			
			$alc_wlm_atts = shortcode_atts( array(
					'levelid' => ''
				), $atts, 'aclwlmmem' );			
		
			$i = 0;
			$members = wlmapi_the_level_members($atts['levelid']); // Feed the membership level ID in as as shortcode att.
			$memmore = $members['members']['member'];  //Get the member details based on the Membership level ID.
			$allowed  = [$atts['levelid']];	// this is the Membership level we're looking for. This will need to be fed in as a shortcode att.
			$levelid = $atts['levelid'];
			
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
			
				.headerlisting {
					background-color: #779ccd;
					text-align: center;	
					padding: 10px 5px;
					font-weight: bold;
					color: #ffffff;
				}
				
				.listing, .headerlisting {
				  list-style: none;
				  margin: 2em 0 1em 1em;
				  display: grid;
				  gap: 20px;
				  grid-auto-flow: dense;
				  grid-template-columns: 20% 1fr 10% 10%
				}
				
				.listing {
					padding: 0px 0px 20px 20px;
					border-bottom: 1px dotted #779ccd;
				}
				
				.rowhd {
					font-weight: bold;
				}
				
				.listing .approve {
					text-align: center;
				}

				.listing input.button-primary {
					text-align: center;
					font-size: 1em;
					line-break: auto;
					padding: 2px;
				}
				
				.footerlisting .wide {
					grid-column-end: span 3;
					text-align: right;
				}
				
				.footerlisting input[type=submit]:hover {
					background: #70BF45;
				}
				
				.
				
			</style>
			
			<form method="post">
				<div class="headerlisting">
				  <div>
					<span class="rowhd">Full name</span>
				  </div>
				  <div>
					<span class="rowhd">Details</span>
				  </div>
				  <div>
					<span class="rowhd">Approve</span>
				  </div>
				  <div>
					<span class="rowhd">Decline</span>
				  </div>
				</div>';
				$memkey = 0;
				foreach ( $approvids as $k => $v ) {
					$approvmem = wlmapi_get_member($v);		 
					$user_info = get_userdata($v); // Get the user info so we can get First and Last Name
					$memdata = $approvmem['member'][0]['UserInfo']['wldata'];
					echo ' 			
					<div class="listing">
						<div>
							<span class="rowhd">'.$user_info->first_name .' ' .$user_info->last_name.'</span>
						</div>			
						<div>
						
						<span class="rowhd">Email: </span>'.$approvmem['member'][0]['UserInfo']['user_email'].'<br />
							<span class="rowhd">Gender: </span>'.$memdata->custom_gender.'<br />
							<span class="rowhd">Faculty: </span>';
							if ( $memdata->custom_faculty !== 'Other' ) {
									echo $memdata->custom_faculty;
							} else {
									echo $memdata->custom_other_faculty;
							}
							echo '<br />
							<span class="rowhd">Department: </span>';
							if ( $memdata->custom_department !== 'Other' ) {
								echo $memdata->custom_department;
							} else {
								echo $memdata->custom_other_dept;
							}
							echo '<br />
							<span class="rowhd">Dissertation Defence: </span>'.$memdata->custom_dis_defence.'<br />
						</div> 
						<div class="approve">
							<input type="checkbox" id="'.$v.'" name="approvemember['.$memkey.']" value="'.$v.'">
						</div>
						<div class="approve">
							<input type="checkbox" id="'.$v.'" name="declinemember['.$memkey.']" value="'.$v.'">
						</div>
					</div> <!-- listing -->
					';
					$memkey++;
				};
				echo '
				<div class="footerlisting">
				  <div class="wide">
						<input type="submit" name="approvebulk" class="button-primary" value="Approve / Decline Selected Members" />
						 <input type="hidden" id="levelid" name="levelid" value="'.$levelid.'">
				  </div>

				</div>
			</form>';

			$output = ob_get_contents();
			ob_end_clean();
			echo $output;
		}
	}
}

if ( !isset ($acl_wlm_members) ){
	//setup our extension class
	$acl_wlm_members = new acl_wlm_members;
}

add_shortcode ( 'acl_wlmoptprint', array( &$acl_wlm_members, 'acl_get_wlmopts' ) );	

?>