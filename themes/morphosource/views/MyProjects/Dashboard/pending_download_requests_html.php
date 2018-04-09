<?php 
	$t_project = $this->getVar("project");
	$pn_user_id = $this->request->user->get('user_id');
	$t_req = new ms_media_download_requests();
 	$t_media = new ms_media();
	
	if (is_array($va_pending_download_requests = $t_project->getDownloadRequestsForProject(null, array('status' => __MS_DOWNLOAD_REQUEST_NEW__))) && (sizeof($va_pending_download_requests) > 0)) {
		print "<div id='msMediaDownloadRequestDashboardContainer'>";
		print '<div class="tealRule"><!-- empty --></div>';
		print "<h2 class='msMediaDownloadRequestDashboard'>"._t('Pending download requests')."</h2>\n";
		// Split download requests into categories based on reviewer/no reviewer
		$no_reviewer_requests = [];
		$reviewer_requests = [];
		foreach($va_pending_download_requests as $va_request) {
			$t_req->load($va_request['request_id']);
 			$t_media->load($t_req->get('media_id'));
 			if ($vn_reviewer_id = $t_media->get('reviewer_id'))
 			{
 				$t_reviewer = new ca_users($vn_reviewer_id);
 				$reviewer_requests[$vn_reviewer_id]['fname'] = $t_reviewer->get('fname');
 				$reviewer_requests[$vn_reviewer_id]['lname'] = $t_reviewer->get('lname');
 				$reviewer_requests[$vn_reviewer_id]['requests'][] = $va_request;
 			} else {
 				$no_reviewer_requests[] = $va_request;
 			}

		}

		print "<div>";
		print "<h5>Download requests that can be approved by all project members. To change this, edit publication status for media groups.</br></h5>";
		foreach ($no_reviewer_requests as $va_request) {	
 			$vs_email = $va_request['email'] ? $va_request['email'] : $va_request['user_name'];
 			print "<div class='listItemLtBlue'>";
 			print "<div class='listItemRightCol'><a href='#' class='button buttonSmall' onclick='msMediaDownloadRequestDashboardApprove({$va_request['request_id']}); return false;'>"._t('Approve')."</a>&nbsp;&nbsp;&nbsp;<a href='#' class='button buttonSmall' onclick='msMediaDownloadRequestDashboardDeny({$va_request['request_id']}); return false;'>"._t('Deny')."</a></div>";
 			print caNavLink($this->request, "M{$va_request['media_id']}", '', 'Detail', 'MediaDetail', 'Show', array('media_id' => $va_request['media_id'])).", ".caGetLocalizedDate($va_request['requested_on'], array('dateFormat' => 'delimited', 'timeOmit' => true));
			print "<br/>{$va_request['lname']}, {$va_request['fname']} (<a href='mailto:{$vs_email}'>{$vs_email}</a>)";
			if($va_request['request']){
				print "<br/>".$va_request['request'];
			}
 			print "</div>\n";
		}
		print "</div>";

		foreach ($reviewer_requests as $vn_reviewer_id => $va_reviewer) {
			print "<div>";
			print "<br/><h5>Download requests that must be approved by ".$va_reviewer['fname']." ".$va_reviewer['lname'].".<br/></h5>";
			foreach($va_reviewer['requests'] as $va_request) {
				print "<div class='listItemLtBlue'>";

				$vb_disabled = 0;
 				if ($pn_user_id != $vn_reviewer_id)
 				{
 					$vb_disabled = 1;
 				}
 				$vs_email = $va_request['email'] ? $va_request['email'] : $va_request['user_name'];

	 			if ($vb_disabled){
	 				print "<div class='listItemRightCol'><a title='Not authorized to manage this request' href='#' class='button buttonSmall buttonGray' onclick='return false;'>"._t('Approve')."</a>&nbsp;&nbsp;&nbsp;<a title='Not authorized to manage this request' href='#' class='button buttonSmall buttonGray' onclick='return false;'>"._t('Deny')."</a></div>";
	 			} else {
	 				print "<div class='listItemRightCol'><a href='#' class='button buttonSmall' onclick='msMediaDownloadRequestDashboardApprove({$va_request['request_id']}); return false;'>"._t('Approve')."</a>&nbsp;&nbsp;&nbsp;<a href='#' class='button buttonSmall' onclick='msMediaDownloadRequestDashboardDeny({$va_request['request_id']}); return false;'>"._t('Deny')."</a></div>";
	 			}
				print caNavLink($this->request, "M{$va_request['media_id']}", '', 'Detail', 'MediaDetail', 'Show', array('media_id' => $va_request['media_id'])).", ".caGetLocalizedDate($va_request['requested_on'], array('dateFormat' => 'delimited', 'timeOmit' => true));
				print "<br/>{$va_request['lname']}, {$va_request['fname']} (<a href='mailto:{$vs_email}'>{$vs_email}</a>)";
				if($va_request['request']){
					print "<br/>".$va_request['request'];
				}
				print "</div>\n";
			}
			print "</div>";
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