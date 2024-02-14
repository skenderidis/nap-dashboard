
<table class="table table-bordered table-striped table-violation" style=" font-size:12px">
	<colgroup>
		<col class="col-xs-2">
		<col class="col-xs-6">
	</colgroup>

	<tbody>
		<tr>
			<td>Violation Type</td>
			<td><b>Threat Campaign detected</b></td>
		</tr>
		<tr>
			<td>Threat Campaign Name</td>
			<td class="red"><b><?php echo htmlspecialchars(($req_violations['threat_campaign_data']['threat_campaign_name']), ENT_SUBSTITUTE);  ?></b></td>
		</tr>
			
		</tr>
	</tbody>						
	
</table>					
