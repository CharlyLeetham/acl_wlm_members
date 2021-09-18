# acl_wlm_members

This plugin was created to allow an organisation to provide someone with access to the membership list of a specific Wishlist Member Level.

Specifically, this plugin allows:

Members join a level using a Wishlist Member registration form and are marked as "Requires Approval"
Using a shortcode, the list of members who "Requires Approval" are listed, detailing the custom information gathered on the form. (At the time of release, this plugin was hardcoded to get specific custom fields)
A checkbox is shown next to each participant and a button displayed at the bottom of the table. When the button at the bottom of the field is checked, the process moves all checked members from "Requires Approval" to "Approved" and displays the list of members who have been approved during the process.
Usage
Usage is achieved by a shortcode.

[acl_wlmoptprint levelid=""]

Where level is the numeric identifier of the Wishlist Member level.
