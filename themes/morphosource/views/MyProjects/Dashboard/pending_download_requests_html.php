<?php 
	$t_project = $this->getVar("project");
	$pn_user_id = $this->request->user->get('user_id');
	$t_req = new ms_media_download_requests();
 	$t_media = new ms_media();
	
	if (is_array($va_pending_download_requests = $t_project->getDownloadRequestsForProject(null, array('status' => __MS_DOWNLOAD_REQUEST_NEW__))) && (sizeof($va_pending_download_requests) > 0)) {
		print "<div id='msMediaDownloadRequestDashboardContainer'>";
		print '<div class="tealRule"><!-- empty --></div>';
		print "<h2 class='msMediaDownloadRequestDashboard'>"._t('Pending download requests')."</h2>\n";
		foreach($va_pending_download_requests as $va_pending_download_request) {
			$t_req->load($va_pending_download_request['request_id']);
 			$t_media->load($t_req->get('media_id'));

 			$vs_email = $va_pending_download_request['email'] ? $va_pending_download_request['email'] : $va_pending_download_request['user_name'];

 			$vb_disabled = 0;
 			if ($t_media->get('reviewer_id') 
 				&& ($pn_user_id != $t_media->get('reviewer_id')))
 			{
 				$vb_disabled = 1;
 			}

			print "<div class='listItemLtBlue'>";
 			if ($vb_disabled){
 				// $t_reviewer = new ca_user($t_media->get('reviewer_id'));
 				print "<div class='listItemRightCol'><a title='Not authorized to manage this request' href='#' class='button buttonSmall buttonGray' onclick='return false;'>"._t('Approve')."</a>&nbsp;&nbsp;&nbsp;<a title='Not authorized to manage this request' href='#' class='button buttonSmall buttonGray' onclick='return false;'>"._t('Deny')."</a></div>";
 			} else {
 				print "<div class='listItemRightCol'><a href='#' class='button buttonSmall' onclick='msMediaDownloadRequestDashboardApprove({$va_pending_download_request['request_id']}); return false;'>"._t('Approve')."</a>&nbsp;&nbsp;&nbsp;<a href='#' class='button buttonSmall' onclick='msMediaDownloadRequestDashboardDeny({$va_pending_download_request['request_id']}); return false;'>"._t('Deny')."</a></div>";
 			}
			print caNavLink($this->request, "M{$va_pending_download_request['media_id']}", '', 'Detail', 'MediaDetail', 'Show', array('media_id' => $va_pending_download_request['media_id'])).", ".caGetLocalizedDate($va_pending_download_request['requested_on'], array('dateFormat' => 'delimited', 'timeOmit' => true));
			print "<br/>{$va_pending_download_request['lname']}, {$va_pending_download_request['fname']} (<a href='mailto:{$vs_email}'>{$vs_email}</a>)";
			if($va_pending_download_request['request']){
				print "<br/>".$va_pending_download_request['request'];
			}
			if($vb_disabled){
				$t_reviewer = new ca_users($t_media->get('reviewer_id'));
				print "<br/><i>This download request must be approved by ".
				$t_reviewer->get('fname')." ".$t_reviewer->get('lname')."</i>";
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