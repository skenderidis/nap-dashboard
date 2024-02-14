<table class="table table-bordered table-striped table-violation" style=" font-size:12px">
	<colgroup>
		<col style="width: 150px">
		<col>
	</colgroup>
	
<tbody>
	<tr>
		<td>ViolationType</td>
		<td><b>Illegal parameter</b></td>
	</tr>
	<tr>
		<td>Parameter Name</td>
		<td style="color:red"><span style="display:none" id="illegal_parameter_name"><?php echo htmlspecialchars_decode(base64_decode($req_violations['parameter_data']['name']), ENT_SUBSTITUTE); ?></span><b><?php echo htmlspecialchars_decode(base64_decode($req_violations['parameter_data']['name']), ENT_SUBSTITUTE);  ?></b>
		<button type="button" class="btn btn_float_right btn-sm btn-dark learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="illegal_parameter">Add Parameter</button></td>

	</tr>
	<tr>
		<td>Parameter value</td>
		<td><b><?php echo htmlspecialchars_decode(base64_decode($req_violations['parameter_data']['value']), ENT_SUBSTITUTE);  ?></b></td>
	</tr>	
	<tr>
		<td>Enforcement</td>
		<td><b><?php echo ($req_violations['parameter_data']['enforcement_level']);  ?></b></td>
	</tr>
	</tbody>
</table>
