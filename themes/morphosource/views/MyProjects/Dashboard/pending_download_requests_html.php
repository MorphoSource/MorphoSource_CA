<?php 
	$t_project = $this->getVar("project");
	
	if (is_array($va_pending_download_requests = $t_project->getDownloadRequestsForProject(null, array('status' => __MS_DOWNLOAD_REQUEST_NEW__))) && (sizeof($va_pending_download_requests) > 0)) {
		print "<div id='msMediaDownloadRequestDashboardContainer'>";
		print "<h2 class='msMediaDownloadRequestDashboard'>"._t('Pending download requests')."</h2>\n";
		print "<table class='msMediaDownloadRequestDashboard'>\n";
		print "<tr><th>"._t('Media')."</th><th>"._t('User').'</th><th>'._t('Date').'</th><th>'._t('Usage')."</th><th>"._t('Action')."</th><tr>\n";
		foreach($va_pending_download_requests as $va_pending_download_request) {
			print "<tr>";
			print "<td>".caNavLink($this->request, "M{$va_pending_download_request['media_id']}", '', 'Detail', 'MediaDetail', 'Show', array('media_id' => $va_pending_download_request['media_id']))."</td>";
			print "<td>{$va_pending_download_request['lname']}, {$va_pending_download_request['fname']} (<a href='mailto:{$va_pending_download_request['email']}'>{$va_pending_download_request['email']}</a>)</td>";
			print "<td>".caGetLocalizedDate($va_pending_download_request['requested_on'], array('dateFormat' => 'delimited', 'timeOmit' => true))."</td>";
			print "<td>{$va_pending_download_request['request']}</td>";
			print "<td><a href='#' onclick='msMediaDownloadRequestDashboardApprove({$va_pending_download_request['request_id']}); return false;'>"._t('Approve')."</a><br/><a href='#' onclick='msMediaDownloadRequestDashboardDeny({$va_pending_download_request['request_id']}); return false;'>"._t('Deny')."</a></td>";
			print "</tr>\n";	
		}
		print "</table></div>\n";
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