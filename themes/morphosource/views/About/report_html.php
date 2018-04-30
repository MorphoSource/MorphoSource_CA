<?php 
	$va_item_array = $this->getVar("item_array");
?>

<div>
	<div>
		<h1>Data reporting for MorphoSource media</h1>
		<p>MorphoSource provides summary reports of media, download usage, and download requests for media that represent specimens that have been reported to <a href='https://www.idigbio.org'>iDigBio</a>. The media report is formatted according to the Audubon Core metadata standard, and so can be incorporated into publisher reporting software, such as an IPT. All report files are linked and described in a single RSS feed, which can be used to receive regular report updates via automated download. Additionally, all report files are individually listed below, sorted by iDigBio publisher and recordset. For each report, there is a link to: <b>1)</b> a Comma Separated Values (CSV) spreadsheet with the primary media, download usage, or download request metadata; and <b>2)</b> an XML file encoded in Ecological Markup Language (EML) providing metadata about the CSV spreadsheet. These reports are updated as necessary on a daily basis.</p>
	</div>
	<div>
		<b>RSS Feed: </b><a href='https://www.morphosource.org/rss/ms.rss'>https://www.morphosource.org/rss/ms.rss</a>
	</div>
	<div style='margin-top: 30px;'>
<?php
		// print "<h1>Data publishers</h1>";
		foreach ($va_item_array as $vs_pub_id => $va_pub) {
			print "<div>";
				print "<h2>".($va_pub["name"] ? $va_pub["name"] : "No publisher name available")." (".$vs_pub_id.")</h2>";
			print "</div>";
			print "<div>";
				print "<table class='listtable'>";
				print "<thead>";
				print "<tr>";
				print "<th class='list-header-unsorted' style='width: 55%;'>Recordset</td>";
				print "<th class='list-header-unsorted' style='width: 10%;'>Media</td>";
				print "<th class='list-header-unsorted' style='width: 10%;'>Downloads</td>";
				print "<th class='list-header-unsorted' style='width: 10%;'>Download Requests</td>";
				print "<th class='list-header-unsorted' style='width: 15%;'>Pub Date</td>";
				print "</tr>";
				print "</thead>";
				foreach ($va_pub['recordsets'] as $vs_r_id => $va_recordset) {
					print "<tr>";
					print "<td><a href='https://www.idigbio.org/portal/recordsets/".$va_recordset['id']."' target='_blank'>".($va_recordset['name'] ? $va_recordset['name']: 'No recordset name available')." (".$vs_r_id.")</a></td>";
					print "<td><a href='".$va_recordset['ms.csv']."'>CSV</a></br><a href='".$va_recordset['ms.xml']."'>EML</a></td>";
					print "<td><a href='".$va_recordset['dl.csv']."'>CSV</a></br><a href='".$va_recordset['dl.xml']."'>EML</a></td>";
					print "<td><a href='".$va_recordset['dl_request.csv']."'>CSV</a></br><a href='".$va_recordset['dl_request.xml']."'>EML</a></td>";
					print "<td>".$va_recordset['pubTime']."</td>";
					print "</tr>";
				}
				print "</table>";
			print "</div>";
		}
?>
	</div>
</div>