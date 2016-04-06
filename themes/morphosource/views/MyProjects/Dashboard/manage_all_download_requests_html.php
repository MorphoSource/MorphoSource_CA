	<div id='msMediaDownloadRequestContainer'>
<?php 
 	$t_project = new ms_projects();
 	$va_all_requests = $t_project->getDownloadRequestsForUser($this->request->user->get("user_id"), array('status' => __MS_DOWNLOAD_REQUEST_NEW__));
?>
	<div class="blueRule"><!-- empty --></div>
	<H1>
<?php
	if (is_array($va_all_requests) && (sizeof($va_all_requests) > 0)) {
?>
		<div style="float:right;"><a href="#" onClick="jQuery('#msMediaDownloadRequestContainer').load('<?php print caNavUrl($this->request, 'MyProjects', 'Dashboard', 'approveAllDownloadRequests'); ?>'); return false;" class="button buttonLarge" title="Since an email is sent to notify each user their request has been approved, this action may take a while."><?php print _t("Approve All Requests"); ?></a></div>
<?php
	}
?>
		Manage All Download Requests
	</H1>
<?php
	if (is_array($va_all_requests) && (sizeof($va_all_requests) > 0)) {
		$vn_project_id = "";
		foreach($va_all_requests as $va_pending_download_request) {		
			$vs_email = $va_pending_download_request['email'] ? $va_pending_download_request['email'] : $va_pending_download_request['user_name'];
			print "<div class='listItemLtBlue'>";
			if($va_pending_download_request["project_id"] != $vn_project_id){
				$vn_project_id = $va_pending_download_request["project_id"];
				print "<H2><b>P".$vn_project_id.": ".$va_pending_download_request["name"]."</b></H2>";
			}
			print "<div class='listItemRightCol'><a href='#' class='button buttonSmall' onclick='msMediaDownloadRequestDashboardApprove({$va_pending_download_request['request_id']}); return false;'>"._t('Approve')."</a>&nbsp;&nbsp;&nbsp;<a href='#' class='button buttonSmall' onclick='msMediaDownloadRequestDashboardDeny({$va_pending_download_request['request_id']}); return false;'>"._t('Deny')."</a></div>";
			print caNavLink($this->request, "M{$va_pending_download_request['media_id']}", '', 'Detail', 'MediaDetail', 'Show', array('media_id' => $va_pending_download_request['media_id'])).", ".caGetLocalizedDate($va_pending_download_request['requested_on'], array('dateFormat' => 'delimited', 'timeOmit' => true));
			print "<br/>{$va_pending_download_request['lname']}, {$va_pending_download_request['fname']} (<a href='mailto:{$vs_email}'>{$vs_email}</a>)";
			if($va_pending_download_request['request']){
				print "<br/>".$va_pending_download_request['request'];
			}
			print "</div>\n";
		}
	}else{
		if($this->getVar("approval_success")){
			print "<H2><b>"._t("All pending requests have been approved.")."</b></H2>";
		}
		print "<p>There are no pending download requests associated with any of your projects.</p>";
	}
?>
	</div>
	<script type="text/javascript">
		function msMediaDownloadRequestDashboardApprove(request_id) {
			jQuery('#msMediaDownloadRequestContainer').load('<?php print caNavUrl($this->request, 'MyProjects', 'Dashboard', 'ApproveDownloadRequest'); ?>/request_id/' + request_id + '/manage_all/1');
		}
		function msMediaDownloadRequestDashboardDeny(request_id) {
			jQuery('#msMediaDownloadRequestContainer').load('<?php print caNavUrl($this->request, 'MyProjects', 'Dashboard', 'DenyDownloadRequest'); ?>/request_id/' + request_id + '/manage_all/1');
		}
	</script>
