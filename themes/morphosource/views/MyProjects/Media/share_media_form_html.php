<div id="shareMediaContainer">
	<div class="tealRule"><!-- empty --></div>
		<H2>Share Media</H2>
		<div id="mediaShare">
<?php
			$pn_media_id = $this->getVar("item_id");
			$t_media = $this->getVar("item");
			$o_db = new Db();
			if($this->getVar("show_comment_form")){

				print caFormTag($this->request, 'shareMedia', 'mediaShareFormComment', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));
?>
				<b>You are requesting to share this media record with a project you are not a member of, <?php print $this->getVar("share_project_name"); ?>.</b>  Your request will be sent to the project administrator, <?php print $this->getVar("share_project_admin_name").", ".$this->getVar("share_project_admin_email"); ?> with the following message:<br/>
				<textarea name="shareComment" rows="4" style="width:100%; margin-bottom:3px;"></textarea>
				<a href='#' name='save' class='button buttonSmall' onclick='jQuery("#mediaShareFormComment").submit(); return false;'>Send Request</a>
				<input type="hidden" name="media_id" value="<?php print $this->getVar("media_id"); ?>">
				<input type="hidden" name="share_project_id" value="<?php print $this->getVar("share_project_id"); ?>">
				<input type="hidden" name="share_form_submit" value="1">
				<br/><br/><i>You will be notified via email when your request has been granted or denied.</i>
			</form>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('#mediaShareFormComment').submit(function(e){		
						jQuery('#shareMediaContainer').load(
							'<?php print caNavUrl($this->request, 'MyProjects', 'Media', 'shareMedia'); ?>',
							jQuery('#mediaShareFormComment').serialize()
						);
						e.preventDefault();
						return false;
					});
				});
			</script>
<?php
			}else{
				$va_exclude_project_ids = array();
				$q_new_share_requests = $o_db->query("SELECT r.*, p.project_id, p.name, u.fname, u.lname, u.email, m.specimen_id from ms_media_movement_requests r INNER JOIN ms_projects as p ON p.project_id = r.to_project_id INNER JOIN ca_users AS u ON p.user_id = u.user_id INNER JOIN ms_media as m ON r.media_id = m.media_id WHERE r.media_id = ? AND r.status = 0 AND type = 2 ORDER BY r.requested_on DESC", $t_media->get("media_id"));
				$q_share_projects = $o_db->query("SELECT p.project_id, p.name, mp.link_id FROM ms_media_x_projects mp INNER JOIN ms_projects AS p ON mp.project_id = p.project_id WHERE mp.media_id = ? AND mp.project_id != ? ORDER BY p.name", $t_media->get("media_id"), $t_media->get("project_id"));
				if($q_share_projects->numRows()){
					while($q_share_projects->nextRow()){
						$va_exclude_project_ids[] = $q_share_projects->get("project_id");
					}
				}
				if($q_new_share_requests->numRows()){
					while($q_new_share_requests->nextRow()){
						$va_exclude_project_ids[] = $q_new_share_requests->get("project_id");
					}
				}
				$q_projects = $o_db->query("SELECT project_id, name from ms_projects WHERE project_id != ? AND deleted = 0 ORDER BY name", $t_media->get("project_id"));
				if($q_projects->numRows()){
					print caFormTag($this->request, 'shareMedia', 'mediaShareForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));
?>
						Grant read only access to this media to:<br/><select name="share_project_id" style="width:250px;">
<?php					
					while($q_projects->nextRow()){
						if(!in_array($q_projects->get("project_id"), $va_exclude_project_ids)){
							print "<option value='".$q_projects->get("project_id")."'>".$q_projects->get("name").", P".$q_projects->get("project_id")."</option>";
						}
					}
?>
					</select>&nbsp;&nbsp;<a href='#' name='save' class='button buttonSmall' onclick='jQuery("#mediaShareForm").submit(); return false;'>Share</a>
					<input type="hidden" name="media_id" value="<?php print $pn_media_id; ?>">
					<div style="font-size:10px; font-style:italic;">Read only media will be available for download to all project members.</div>
					</form>
					<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery('#mediaShareForm').submit(function(e){		
								jQuery('#shareMediaContainer').load(
									'<?php print caNavUrl($this->request, 'MyProjects', 'Media', 'shareMedia'); ?>',
									jQuery('#mediaShareForm').serialize()
								);
								e.preventDefault();
								return false;
							});
						});
					</script>
<?php
					# --- list pending share requests
					if($q_new_share_requests->numRows()){
						$q_new_share_requests->seek(0);
						print "<div class='ltBlueTopRule'><br/><b>The following share requests are pending review:</b>";
						while($q_new_share_requests->nextRow()){
							print "<div class='listItemLtBlue'>";
							print "<b>".caGetLocalizedDate($q_new_share_requests->get("requested_on"), array('dateFormat' => 'delimited', 'timeOmit' => true))."</b>, (P".$q_new_share_requests->get("project_id").") ".$q_new_share_requests->get("name").", request sent to ".trim($q_new_share_requests->get("fname")." ".$q_new_share_requests->get("lname"))." (".$q_new_share_requests->get("email").")";
							print "</div>";
						}
						print "</div>";
					}
	
	
					
					# --- list out any projects with read only access
					$q_share_projects = $o_db->query("SELECT p.project_id, p.name, mp.link_id FROM ms_media_x_projects mp INNER JOIN ms_projects AS p ON mp.project_id = p.project_id WHERE mp.media_id = ? AND mp.project_id != ? ORDER BY p.name", $t_media->get("media_id"), $t_media->get("project_id"));
					if($q_share_projects->numRows()){
						$q_share_projects->seek(0);
						print "<div class='ltBlueTopRule'><br/><b>This record has been shared with the following projects:</b>";
						while($q_share_projects->nextRow()){
							print "<div class='listItemLtBlue'>";
							print "<div class='listItemRightCol'>".caNavLink($this->request, "Remove", "button buttonSmall", "MyProjects", "Media", "removeShareMedia", array("media_id" => $t_media->get("media_id"), "link_id" => $q_share_projects->get("link_id"), ))."</div>";			
							print "(P".$q_share_projects->get("project_id").") ".$q_share_projects->get("name");
							print "</div>";
						}
						print "</div>";
					}
				}
			}
?>
	</div><!-- end mediaShare -->
</div>