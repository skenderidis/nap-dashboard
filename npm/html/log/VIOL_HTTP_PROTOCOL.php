
<table class="table table-bordered table-striped table-violation" style=" font-size:12px; ">
	<colgroup>
		<col style="width: 150px">
		<col>
	</colgroup>

		<tbody>
			<tr>
				<td>Violation Type</td>
				<td><b>HTTP protocol compliance failed</b></td>
			</tr>
			<tr>
				<td>Details</td>
				<td><b><?php echo htmlspecialchars(base64_decode($req_violations['http_sub_violation']), ENT_SUBSTITUTE);  ?></b></td>
			</tr>
			<tr>
				<td colspan=2><i>You can disable this violation from the "Sub-violations" section</i></td>
			</tr>			
		</tbody>	

</table>
