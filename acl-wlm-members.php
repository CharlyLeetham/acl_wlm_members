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
	<!--  <style>
	
		.grid {
			display: flex;
			flex-flow: row wrap;
			text-align: center;
		}
		
		.grid .row {
			width: 100%;
		}
		
		.grid .column {
			display: flex;
			flex-direction: column;
		}
		

		.header {
			background-color: #779ccd;
			text-align: center;	
			display: flex;
			flex-flow: row wrap;
			width: 100%;
			padding: 10px 5px;
			font-weight: bold;
			color: #ffffff;
		}

		.main, .details {
			display: flex;
			flex-flow: row wrap;			
		}
		
		.aside-1, .name {
			display: flex;
			flex-flow: row wrap;
			flex: 1 0 0		
		}

		.aside-2, .approve {
			display: flex;
			flex-flow: row wrap;
			flex: 0.25 0 0;		
		}
		
		.main, .details { 
			flex: 3 0px;
		}
		
		.aside, .main {
			padding: 10px 5px;
			text-align: left;
		}
		
		.aside-1, .name { order: 1; } 
		.main, .details    { order: 2; }
		.aside-2, .approve { order: 3; }		
		
	</style>
	
	<div class="grid">
		<div class="header">
			<div class="column name"><div class="item">Full Name</div></div>
			<div class="column details"><div class="item">Details</div></div>
			<div class="column approve"><div class="item">Approve</div></div>
		</div> --> <!-- row --> ';
	
	// foreach ( $approvids as $k => $v ) {
		// $approvmem = wlmapi_get_member($v);		 
		// $user_info = get_userdata($v); // Get the user info so we can get First and Last Name
		// $memdata = $approvmem['member'][0]['UserInfo']['wldata'];
		// echo ' 
		// <div class="main">
			// <div class="row">			
				// <div class="item">'.$approvmem['member'][0]['UserInfo']['user_email'].'</div>
			// </div>
			// <div class="row">
				// <div class="item">'.$memdata->custom_gender.'</div>
			// </div>
			// <div class="row">			
				// <div class="item">';
					// if ( $memdata->custom_faculty !== 'Other' ) {
						// echo $memdata->custom_faculty;
					// } else {
						// echo $memdata->custom_other_faculty;
					// }
				// echo '</div>
			// </div>
			// <div class="row">			
				// <div class="item">';
					// if ( $memdata->custom_department !== 'Other' ) {
						// echo $memdata->custom_department;
					// } else {
						// echo $memdata->custom_other_dept;
					// }
				// echo '</div>
			// </div>
			// <div class="row">
				// <div class="item">'.$memdata->custom_dis_defence.'</div>				
			// </div>
		// </div> <!-- /main -->
		// <div class="aside aside-1">
			// <div class="item">'.$user_info->first_name .' ' .$user_info->last_name.'</div>	
		// </div>
		// <div class="aside aside-2"?
			// <div class="item">chk</div>
		// </div>
		// ';
	// };
	// echo '</div>';
	
	echo '
	
	<style>
	
		.boxwrapper {
			display: grid;
			grid-template-columns: repeat(12, [col-start] 1fr);
			gap: 20px;
		}
		
		.boxwrapper > * {
			grid-column: col-start / span 12;
		}

		@media (min-width: 500px) {
		  .side {
			grid-column: col-start / span 3;
			grid-row: 3;
		  }
		  .ad {
			grid-column: col-start / span 3;
			grid-row: 4;
		  }
		  .content, .main-footer {
			grid-column: col-start 4 / span 9;
		  }
		  nav ul {
			display: flex;
			justify-content: space-between;
		  }
		}		

		@media (min-width: 700px) {
		  .boxwrapper .main-nav {
			grid-column: col-start / span 2;
			grid-row: 2 / 4;
		  }
		  .boxwrapper .content {
			grid-column: col-start 3 / span 8;
			grid-row: 2 / 4;
		  }
		  .boxwrapper .side {
			grid-column: col-start 11 / span 2;
			grid-row: 2;
		  }
		  .boxwrapper .ad {
			grid-column: col-start 11 / span 2;
			grid-row: 3;
		  }
		  .boxwrapper .main-footer {
			grid-column: col-start / span 12;
		  }
		  .boxwrapper nav ul {
			flex-direction: column;
		  }
		}
	</style>
	<div class="boxwrapper">
	  <header class="main-head">The header</header>
	  <nav class="main-nav">
		<ul>
		  <li><a href="">Nav 1</a></li>
		  <li><a href="">Nav 2</a></li>
		  <li><a href="">Nav 3</a></li>
		</ul>
	  </nav>
	  <article class="content">
		<h1>Main article area</h1>
		<p>In this layout, we display the areas in source order for any screen less that 500 pixels wide. We go to a two column layout, and then to a three column layout by redefining the grid, and the placement of items on the grid.</p>
	  </article>
	  <aside class="side">Sidebar</aside>
	  <div class="ad">Advertising</div>
	  
	  <article class="content">
		<h1>Main article area2</h1>
		<p>In this layout, we dddddisplay the areas in source order for any screen less that 500 pixels wide. We go to a two column layout, and then to a three column layout by redefining the grid, and the placement of items on the grid.</p>
	  </article>
	  <aside class="side">Sidebar2</aside>
	  <div class="ad">Advertising444</div>	  
	  <footer class="main-footer">The footer</footer>
	</div>
	';
	




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