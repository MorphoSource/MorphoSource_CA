<?php
	$t_media_share = new ms_media_shares();
?>
<div id="shareMediaUserContainer">
	<div class="tealRule"><!-- empty --></div>
		<H2>Share Media with other MorphoSource users</H2>
		<div style="font-size:11px; margin:-10px 10px 10px 10px;"><i>This allows users outside your project to view and download this media record for 30 days.</i></div>	
		<div id="mediaShareUser">
<?php
			$pn_media_id = $this->getVar("item_id");
			$t_media = $this->getVar("item");
			$o_db = new Db();

				if($vs_message = $this->getVar("message")){
					print "<div class='formErrors'>".$vs_message."</div>";
				}
				print caFormTag($this->request, 'shareMediaUser', 'mediaShareUserForm', null, 'post', 'multipart/form-data', '', array('disableUnsavedChangesWarning' => true));
				print "<div class='formLabel'>Enter the user's name or email<br/>".caHTMLTextInput("user_lookup", array("id" => 'userID', 'class' => 'lookupBg'), array('width' => "200px", 'height' => 1))."</div>";
				print "<div class='formLabel'>Use Restrictions<br/><textarea style='width:100%; height:3em; font-family:arial; font-size:11px;' name='use_restrictions'></textarea></div>";
				print "<div class='formLabel'><a href='#' name='save' class='button buttonSmall' onclick='jQuery(\"#mediaShareUserForm\").submit(); return false;'>"._t("Share")."</a></div>";
				print "<input type='hidden' value='' name='share_user_id' id='share_user_id'>";
				print "<input type='hidden' value='{$pn_media_id}' name='media_id'>";
?>
				</form>
				<script type='text/javascript'>
					jQuery('#userID').autocomplete(
						{ 
							source: '<?php print caNavUrl($this->request, 'lookup', 'User', 'Get', array("max" => 500, "quickadd" => false)); ?>', 
							minLength: 3, delay: 800, html: true,
							select: function(event, ui) {
								var user_id = parseInt(ui.item.id);
								if (user_id > 1) {
									// found an id
									//alert("found user id: " + user_id);
									jQuery('#share_user_id').val(user_id);
									//alert("specimen id set to: " + jQuery('#specimen_id').val());
								}else{
									jQuery('#share_user_id').val(0);
								}
							},
							open: function( event, ui ) {
								jQuery('#share_user_id').val(0);
							}
						}
					).click(function() { this.select(); });

					jQuery(document).ready(function() {
						jQuery('#mediaShareUserForm').submit(function(e){		
							jQuery('#shareMediaUserContainer').load(
								'<?php print caNavUrl($this->request, 'MyProjects', 'Media', 'shareMediaUser'); ?>',
								jQuery('#mediaShareUserForm').serialize()
							);
							e.preventDefault();
							return false;
						});
					});
				</script>
<?php
	
	
					
					# --- list out any users media is shared with
					$q_share_users = $o_db->query("SELECT u.lname, u.fname, u.email, ms.link_id, ms.created_on, ms.use_restrictions FROM ms_media_shares ms INNER JOIN ca_users AS u ON ms.user_id = u.user_id WHERE ms.media_id = ? AND ms.created_on > ".(time() - (60 * 60 * 24 * 30))." ORDER BY ms.link_id desc", $t_media->get("media_id"));
					if($q_share_users->numRows()){
						$q_share_users->seek(0);
						print "<div class='ltBlueTopRule'><br/><b>This record has been shared with the following users:</b>";
						while($q_share_users->nextRow()){
							print "<div class='listItemLtBlue'>";
							print "<div class='listItemRightCol'>".caNavLink($this->request, "Remove", "button buttonSmall", "MyProjects", "Media", "removeShareMediaUser", array("media_id" => $t_media->get("media_id"), "link_id" => $q_share_users->get("link_id"), ))."</div>";			
							print trim($q_share_users->get("name")." ".$q_share_users->get("fname")).", ".$q_share_users->get("email");
							print "<br/><b>Access expires:</b> ".date("m/d/y", $q_share_users->get("created_on") + (60 * 60 * 24 * 30));
							if($q_share_users->get("use_restrictions")){
								print "<br/><b>Use Restrictions:</b> ".$q_share_users->get("use_restrictions");
							}
							print "</div>";
						}
						print "</div>";
					}
?>
	</div><!-- end mediaShareUser -->
</div>