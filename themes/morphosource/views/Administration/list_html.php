<?php
	$t_item = $this->getVar("item");
	$ps_primary_key = $this->getVar("primary_key");
	$pa_list_fields = $this->getVar("list_fields");
	$q_listings = $this->getVar("listings");

?>
<script language="JavaScript" type="text/javascript">
/* <![CDATA[ */
	$(document).ready(function(){
		$('#msItemList').caFormatListTable();
	});
/* ]]> */
</script>
<div class="blueRule"><!-- empty --></div>
	<H1>
		<?php print "Manage ".$this->getVar("name_plural"); ?>
	</H1>
	
	<?php 
		print caFormControlBox(
			'', 
			'',
			'<div class="list-filter">'._t('Filter').': <input type="text" name="filter" value="" onkeyup="$(\'#msItemList\').caFilterTable(this.value); return false;" size="20" style="border:1px solid #828282;"/></div>'
		); 
	?>
<div id='formArea'>		
		<br style="clear: both"/>
	<table id="msItemList" class="listtable" border="0" cellpadding="0" cellspacing="1">
		<thead>
			<tr>
				<th class="list-header-unsorted">
					<?php print _t('Name'); ?>
				</th>
				<th class="list-header-unsorted">
					<?php print _t('Created by'); ?>
				</th>
				<th class="{sorter: false} list-header-nosort" width="80">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
<?php
	$t_user = new ca_users();
	if ($q_listings->numRows()) {
		while($q_listings->nextRow()) {
?>	
			<tr>
				<td>
<?php
					if($this->getVar("table") == "ms_bibliography"){
						$o_datamodel = $this->getVar("o_datamodel");
						$t_bib = $o_datamodel->getInstanceByTableName("ms_bibliography", true);
						print $t_bib->getCitationText($q_listings->getRow());
					}else{
						$i = 0;
						foreach($pa_list_fields as $vs_field){
							$i++;
							if($q_listings->get($vs_field)){
								print $q_listings->get($vs_field);
								if($i < sizeof($pa_list_fields)){
									print $t_item->getProperty("LIST_DELIMITER");
								}
							}
						}
					}
?>
				</td>
				<td>
<?php
					if($q_listings->get("user_id")){
						$t_user->load($q_listings->get("user_id"));
						print trim($t_user->get("fname")." ".$t_user->get("lname")).", (".$t_user->get("email").")";
					}
?>
				</td>
				<td>
<?php				
					print caNavLink($this->request, _t("Edit"), "button buttonSmall", "Administration", "List", "form", array("table" => $this->getVar("table"), $ps_primary_key => $q_listings->get($ps_primary_key)));
					print "&nbsp;&nbsp;&nbsp;".caNavLink($this->request, _t("Delete"), "button buttonSmall", "Administration", "List", "Delete", array("table" => $this->getVar("table"), $ps_primary_key => $q_listings->get($ps_primary_key)));
?>						
				</td>
			</tr>
<?php
		}
	} else {
?>
		<tr>
			<td colspan='4'>
				<div align="center">
					<?php print _t('No %1 have been entered', $this->getVar("name_plural")); ?>
				</div>
			</td>
		</tr>
<?php			
	}
?>
		</tbody>
	</table>
</div>