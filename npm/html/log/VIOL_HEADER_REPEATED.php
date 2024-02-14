
<table class="table table-bordered table-striped table-violation" style=" font-size:12px; ">
	<colgroup>
			<col style="width: 150px">
			<col>
	</colgroup>

	<tbody>
		<tr>
			<td>Violation Type</td>
			<td><b>Repeated Header</b></td>
		</tr>
		<tr>
			<td>Header Name</td>
			<td style="color:red"><b><?php echo htmlspecialchars(base64_decode($req_violations['header_data']['header_name']), ENT_SUBSTITUTE);  ?></b></td>
		</tr>

	</tbody>						
</table>					
