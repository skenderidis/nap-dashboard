
<table class="table table-bordered table-striped table-violation" style=" font-size:12px">
	<colgroup>
		<col style="width: 150px">
		<col>
	</colgroup>

	<tbody>
		<tr>
			<td>Violation Type</td>
			<td><b>Illegal request content type</b></td>
		</tr>
		<tr>
			<td>Header Name</td>
			<td style="color:red"><b><?php echo $req_violations['header_data']['header_name'];  ?></b></td>
		</tr>
		<tr>
			<td>Header Actual Value</td>
			<td><b><?php echo htmlspecialchars(base64_decode($req_violations['header_data']['header_actual_value']), ENT_SUBSTITUTE);  ?></b></td>
		</tr>												
		<tr>
			<td>Header Matched Value</td>
			<td><b><?php echo htmlspecialchars(base64_decode($req_violations['header_data']['header_matched_value']), ENT_SUBSTITUTE);  ?></b></td>
		</tr>		
	</tbody>						
</table>					
