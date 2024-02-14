<table class="table table-bordered table-striped table-violation" style=" font-size:12px">
	<colgroup>
		<col class="col-xs-2">
		<col class="col-xs-6">
	</colgroup>

	<tbody>
		<tr>
			<td>Violation Type</td>
			<td><b>Cookie expired timestamp</b></td>
		</tr>
		<tr>
			<td>Cookie Name</td>
			<td><b><?php echo htmlspecialchars(base64_decode($req_violations['cookie_name']), ENT_SUBSTITUTE);  ?></b></td>
		</tr>
		<tr>
			<td>Expiration Period</td>
			<td><b><?php echo $req_violations['expiration_period'];  ?></b></td>
		</tr>
		<tr>
			<td>Time Passed</td>
			<td style="color:red"><b><?php echo $req_violations['time_passed'];  ?></b></td>
		</tr>
	</tbody>
</table>					
