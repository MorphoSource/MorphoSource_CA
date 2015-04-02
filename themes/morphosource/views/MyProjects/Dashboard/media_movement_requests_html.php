<?php 
	if($this->getVar("move_media_message")){
		print "<div class='formErrors'>".$this->getVar("move_media_message")."</div>";
	}
	$t_project = $this->getVar("project");
	# -- get pending/new movement requests
	$o_db = new Db();
	$q_new_movement_requests = $o_db->query("SELECT r.*, u.fname, u.lname, u.email, m.specimen_id from ms_media_movement_requests r INNER JOIN ca_users AS u ON r.user_id = u.user_id INNER JOIN ms_media as m ON r.media_id = m.media_id WHERE r.to_project_id = ? AND r.status = 0 ORDER BY r.requested_on DESC", $t_project->get("project_id"));
	
	
	
	if ($q_new_movement_requests->numRows()) {
		print "<div id='msMediaMovementRequestDashboardContainer'>";
		print '<div class="tealRule"><!-- empty --></div>';
		print "<h2 class='msMediaMovementRequestDashboard'>"._t('Pending media movement requests')."</h2>\n";
		while($q_new_movement_requests->nextRow()) {			
			print "<div class='listItemLtBlue'>";
			print "<div class='listItemRightCol'><a href='#' class='button buttonSmall' onclick='msMediaMovementRequestDashboardApprove({$q_new_movement_requests->get("request_id")}); return false;'>"._t('Approve')."</a>&nbsp;&nbsp;&nbsp;<a href='#' class='button buttonSmall' onclick='msMediaMovementRequestDashboardDeny({$q_new_movement_requests->get("request_id")}); return false;'>"._t('Deny')."</a></div>";
			print "<b>".caGetLocalizedDate($q_new_movement_requests->get("requested_on"), array('dateFormat' => 'delimited', 'timeOmit' => true))."</b> ".trim($q_new_movement_requests->get('fname')." ".$q_new_movement_requests->get('lname'))." (<a href='mailto:".$q_new_movement_requests->get('email')."'>".$q_new_movement_requests->get('email')."</a>) requested to ";
			$vs_specimen_info = "";
			if($q_new_movement_requests->get("specimen_id")){
				$t_specimen = new ms_specimens($q_new_movement_requests->get("specimen_id"));
				$vs_specimen_info = $t_specimen->getSpecimenName();
			}
			if($q_new_movement_requests->get("type") == 1){
				print " <b>move ownership</b> of M".$q_new_movement_requests->get('media_id').", ".$vs_specimen_info." to your project.";
			}else{
				print " <b>share</b> M".$q_new_movement_requests->get('media_id').", ".$vs_specimen_info." with your project.";
			}
			if($q_new_movement_requests->get('request')){
				print "<blockquote>".$q_new_movement_requests->get('request')."</blockquote>";
			}
			print "</div>\n";
		}
		print "</div>\n";
	}
?>
	<script type="text/javascript">
		function msMediaMovementRequestDashboardApprove(request_id) {
			jQuery('#msMediaMovementRequestDashboardContainer').load('<?php print caNavUrl($this->request, 'MyProjects', 'Dashboard', 'ApproveMediaMovementRequest'); ?>/request_id/' + request_id);
		}
		function msMediaMovementRequestDashboardDeny(request_id) {
			jQuery('#msMediaMovementRequestDashboardContainer').load('<?php print caNavUrl($this->request, 'MyProjects', 'Dashboard', 'DenyMediaMovementRequest'); ?>/request_id/' + request_id);
		}
	</script>