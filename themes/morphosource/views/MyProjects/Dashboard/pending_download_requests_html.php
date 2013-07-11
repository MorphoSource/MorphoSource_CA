<?php 
	$t_project = $this->getVar("project");
	
	if (is_array($va_pending_download_requests = $t_project->getDownloadRequestsForProject(null, array('status' => __MS_DOWNLOAD_REQUEST_NEW__))) && (sizeof($va_pending_download_requests) > 0)) {
		print "<div id='msMediaDownloadRequestDashboardContainer'>";
		print '<div class="tealRule"><!-- empty --></div>';
		print "<h2 class='msMediaDownloadRequestDashboard'>"._t('Pending download requests')."</h2>\n";
		foreach($va_pending_download_requests as $va_pending_download_request) {			
			print "<div class='listItemLtBlue'>";
			print "<div class='listItemRightCol'><a href='#' class='button buttonSmall' onclick='msMediaDownloadRequestDashboardApprove({$va_pending_download_request['request_id']}); return false;'>"._t('Approve')."</a>&nbsp;&nbsp;&nbsp;<a href='#' class='button buttonSmall' onclick='msMediaDownloadRequestDashboardDeny({$va_pending_download_request['request_id']}); return false;'>"._t('Deny')."</a></div>";
			print caNavLink($this->request, "M{$va_pending_download_request['media_id']}", '', 'Detail', 'MediaDetail', 'Show', array('media_id' => $va_pending_download_request['media_id'])).", ".caGetLocalizedDate($va_pending_download_request['requested_on'], array('dateFormat' => 'delimited', 'timeOmit' => true));
			print "<br/>{$va_pending_download_request['lname']}, {$va_pending_download_request['fname']} (<a href='mailto:{$va_pending_download_request['email']}'>{$va_pending_download_request['email']}</a>)";
			if($va_pending_download_request['request']){
				print "<br/>".$va_pending_download_request['request'];
			}
			print "</div>\n";
		}
		print "</div>\n";
	}
?>
	<script type="text/javascript">
		function msMediaDownloadRequestDashboardApprove(request_id) {
			jQuery('#msMediaDownloadRequestDashboardContainer').load('<?php print caNavUrl($this->request, 'MyProjects', 'Dashboard', 'ApproveDownloadRequest'); ?>/request_id/' + request_id);
		}
		function msMediaDownloadRequestDashboardDeny(request_id) {
			jQuery('#msMediaDownloadRequestDashboardContainer').load('<?php print caNavUrl($this->request, 'MyProjects', 'Dashboard', 'DenyDownloadRequest'); ?>/request_id/' + request_id);
		}
	</script>