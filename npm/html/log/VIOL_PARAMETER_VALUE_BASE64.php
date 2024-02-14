<table class="table table-bordered table-striped table-violation" style=" font-size:12px">
	<colgroup>
		<col style="width: 150px">
		<col>
	</colgroup>
	
<tbody>
	<tr>
		<td>Violation Type</td>
		<td><b>Illegal Base64 value</b></td>
	</tr>
	<tr>
		<td>Parameter Name</td>
		<td style="color:red"><b><span id="parameter_illegal_base64_name"><?php echo htmlspecialchars_decode(base64_decode($req_violations['parameter_data']['name']), ENT_SUBSTITUTE);  ?></span></b>
			<button type="button" class="btn btn_float_right btn-sm btn-dark learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="parameter_illegal_base64">Disable</button>

		</td>
	</tr>
	<tr>
		<td>Value</td>
		<td><b><?php echo htmlspecialchars_decode(base64_decode($req_violations['parameter_data']['value']), ENT_SUBSTITUTE);  ?></b></td>
	</tr>
	<tr>
		<td>Enforcement</td>
		<td><b><?php echo ($req_violations['parameter_data']['enforcement_level']);  ?></b></td>
	</tr>
	</tbody>
</table>
