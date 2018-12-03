	<div id='msMediaDownloadRequestContainer'>
<?php 
 	$t_project = new ms_projects();
 	$t_specimen = new ms_specimens();
 	$t_media = new ms_media();
 	$t_user = new ca_users();
 	$va_all_requests = $this->getVar('all_requests');
?>
	<div class="blueRule"><!-- empty --></div>
	<H1>
<?php
	if (is_array($va_all_requests) && (sizeof($va_all_requests) > 0)) {
?>

<?php
	}
?>
		Manage All Download Requests
	</H1>
	<div class='tealRule'></div> 
<?php
	if (is_array($va_all_requests) && (sizeof($va_all_requests) > 0)) {
		print caFormTag($this->request, 'manageDownloadsApproveDeny', 'manageDownloadsForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));

		// Approve or deny all buttons
		print "<div style='text-align: center; margin-top: 10px;'>";
		print "<a href='#' id='approveRequestsButton' name='save' class='button buttonLarge' style='margin-top:5px; margin-right: 10px;'>"._t("Approve Selected Requests")."</a>";
		print "<a href='#' id='denyRequestsButton' name='save' class='button buttonLarge' style='margin-top:5px; margin-left: 10px' onclick='jQuery(\"#manageDownloadsForm\").submit(); return false;'>"._t("Deny Selected Requests")."</a>";
		print "<input type='hidden' id='approve_or_deny' name='approve_or_deny' value=''>";
		print "</div>";

		// Select or unselect all buttons
		print "<div style='margin: 10px; margin-top: 20px; margin-left: 25px; text-align: center;'>";
		print "<H1 style='max-width: 600px; display: inline-block; vertical-align: middle; padding: 0; font-size: 32px; margin-right: 10px; '>".sizeof($va_all_requests)." Download Request".( sizeof($va_all_requests) > 1 ? "s" : "")."</H1>";
		print "<input style='vertical-align: middle; height: 20px; width: 20px;' type='checkbox' name='all_requests' id='allCheck' value=''>";
		print "</div>";

		// Download requests (plus section headers)
		$vn_project_id = "";
		$vn_user_id = null;
		foreach($va_all_requests as $va_pending_download_request) {		
			$vs_email = $va_pending_download_request['email'] ? $va_pending_download_request['email'] : $va_pending_download_request['user_name'];
			
			print "<div class=''>";
			if($va_pending_download_request["project_id"] != $vn_project_id){
				$vn_project_id = $va_pending_download_request["project_id"];
				$vn_user_id = null;

				print "<div style='margin: 10px; margin-top: 20px; margin-left: 25px; text-align: center;'>";
				print "<H1 style='max-width: 600px; display: inline-block; vertical-align: middle; padding: 0; font-size: 26px; margin-right: 10px'>".$va_pending_download_request["name"]."</H1>";
				print "<input style='vertical-align: middle; height: 20px; width: 20px;' type='checkbox' name='proj_requests' class='projectCheck' id='{$vn_project_id}' value=''>";
				
				print "</div>";
			}
			if($va_pending_download_request['request_user_id'] != $vn_user_id) {
				$vn_user_id = $va_pending_download_request['request_user_id'];

				print "<div style='margin: 10px; margin-top: 25px; margin-left: 25px; text-align: center;'>";
				print "<H2 style='max-width: 600px; display: inline-block; vertical-align: middle; padding: 0; font-size: 20px; margin-right: 10px;'>Requesting user: {$va_pending_download_request['fname']} {$va_pending_download_request['lname']} (<a href='mailto:{$vs_email}'>{$vs_email}</a>)</H2>";
				print "<input style='vertical-align: middle; height: 20px; width: 20px;' type='checkbox' name='proj_user_requests' class='projectUserCheck' id='{$vn_project_id}-{$vn_user_id}' value=''>";
				print "</div>";
			}
			print "</div>";

			print "<div class='projectItem' id='{$vn_project_id}'>";
			print "<div class='requestUserItem' id='{$vn_user_id}'>";

			print "<div class='listItemLtBlue' id='' style='background-color: #f7f7f7; border:1px solid #689899; margin: 10px; margin-bottom: 30px;'>";
			print "<div>";
			
			print "<div class='' style='margin-left: 10px; margin-right: 30px; width: 650px; display: inline-block; vertical-align: middle;'>";

			# media details
			$vs_media_link = caNavLink($this->request, "M{$va_pending_download_request['media_id']}", '', 'Detail', 'MediaDetail', 'Show', array('media_id' => $va_pending_download_request['media_id']));
			$vs_title = ( $va_pending_download_request['title'] ? ", ".$va_pending_download_request['title'] : "No title");
			$vs_element = ( $va_pending_download_request['element'] ? "".$va_pending_download_request['element']."" : "");

			print "<div>";
			print "<input style='vertical-align: middle; height: 20px; width: 20px;' type='checkbox' name='request_ids[]' class='requestCheck' value='".$va_pending_download_request['request_id']."'>";
			print "<H1 style='font-size: 22px; display: inline-block; vertical-align: middle;'>Media: ".$vs_media_link.$vs_title."</H1>";
			print "</div>";

			print "<b>Element description:</b> ".$vs_element."</br>";

			if ($va_pending_download_request['media_user_id']){
				$t_user->load($va_pending_download_request['media_user_id']);
				print "<b>Data contributor:</b> ".$t_user->get('fname')." ".$t_user->get('lname')." (".$t_user->get('email').")";
			}

			# specimen details
			if ($va_pending_download_request['specimen_id']){
				$t_specimen->load($va_pending_download_request['specimen_id']);
				$vs_specimen_name = $t_specimen->getSpecimenName(null, array('omitTaxonomy' => true));
				$vs_specimen_link = caNavLink($this->request, $vs_specimen_name, '', 'Detail', 'SpecimenDetail', 'Show', array('specimen_id' => $va_pending_download_request['specimen_id']));

				$vs_specimen_taxonomy = 
					$t_specimen->getSpecimenTaxonomy();
				if ($vs_specimen_taxonomy) { 
					$vs_specimen_taxonomy = ", <i>".join(" ", $vs_specimen_taxonomy)."</i>"; 
				} else {
					$vs_specimen_taxonomy = "";
				}
				
				print "<br/><b>Specimen:</b> ".$vs_specimen_link.$vs_specimen_taxonomy."";
			}

			print "<br/><br/><b>Request date:</b> ".caGetLocalizedDate(
				$va_pending_download_request['requested_on'], 
				array('dateFormat' => 'delimited', 'timeOmit' => true));
			if($va_pending_download_request['request']){
				print "<br/><b>Request text:</b> ".$va_pending_download_request['request'];
			}

			print "</div>";

			print "<div class='' style='display: inline-block; vertical-align: middle;'>";
			$t_media->load($va_pending_download_request['media_id']);
			print "<div style='border: 1px solid #e8e8e8; margin-top: 15px; margin-right: 15px;'>".$t_media->getPreviewMediaFile(null, array('icon', 'thumbnail'))['media']['thumbnail']."</div>";
			print "</div>";

			print "</div>\n";

			print "<div style='text-align: center; margin-top: 20px; margin-bottom: 5px;'><a href='#' class='button buttonMedium' onclick='msMediaDownloadRequestDashboardApprove({$va_pending_download_request['request_id']}); return false;'>"._t('Approve')."</a>&nbsp;&nbsp;&nbsp;<a href='#' class='button buttonMedium' onclick='msMediaDownloadRequestDashboardDeny({$va_pending_download_request['request_id']}); return false;'>"._t('Deny')."</a></div>";

			print "</div>";
			print "</div>"; 
			print "</div>";  
		}
		print "</form>";
	}else{
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

		jQuery('#allCheck').change(function () {
			if (this.checked) {
				jQuery('input').prop('checked', true);
			} else {
				jQuery('input').prop('checked', false);
			}
		})

		jQuery('.projectCheck').change(function () {
			if (this.checked) {
				jQuery('#'+this.id+'.projectItem').find('input').prop('checked', true);
				jQuery('[id*="' + this.id + '"].projectUserCheck').prop('checked', true);
			} else {
				jQuery('#'+this.id+'.projectItem').find('input').prop('checked', false);
				jQuery('[id*="' + this.id + '"].projectUserCheck').prop('checked', false);
				jQuery('#allCheck').prop('checked', false);
			}
		})

		jQuery('.projectUserCheck').change(function () {
			var id_combo = this.id;
			var id_array = id_combo.split('-');
			if (this.checked) {
				jQuery('#'+id_array[0]+'.projectItem').children('#'+id_array[1]+'.requestUserItem').find('input').prop('checked', true);
			} else {
				jQuery('#'+id_array[0]+'.projectItem').children('#'+id_array[1]+'.requestUserItem').find('input').prop('checked', false);
				jQuery('#'+id_array[0]+'.projectCheck').prop('checked', false);
				jQuery('#allCheck').prop('checked', false);
			}
		})

		jQuery('.requestCheck').change(function () {
			if (!this.checked) {
				if (jQuery('#allCheck').prop('checked')) {
					jQuery('#allCheck').prop('checked', false);
				}

				var proj_id = $(this).parents('.projectItem').attr('id');
				if (jQuery('#' + proj_id + '.projectCheck').prop('checked')) {
					jQuery('#' + proj_id + '.projectCheck').prop('checked', false);
				}

				var user_id = $(this).parents('.requestUserItem').attr('id');
				if ($('#' + proj_id + '-' + user_id + '.projectUserCheck').prop('checked')) {
					$('#' + proj_id + '-' + user_id + '.projectUserCheck').prop('checked', false);
				}
			}
		})

		jQuery('#approveRequestsButton').click(function () {
			jQuery('#approve_or_deny').val('approve');
			jQuery('#manageDownloadsForm').submit();
		});

		jQuery('#denyRequestsButton').click(function () {
			jQuery('#approve_or_deny').val('deny');
			jQuery('#manageDownloadsForm').submit();
		});
	</script>
