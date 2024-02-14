<table class="table table-bordered table-striped table-violation" style=" font-size:12px">
	<colgroup>
		<col style="width: 150px">
		<col>
	</colgroup>
	
<tbody>
	<tr>
		<td>ViolationType</td>
		<td><b>Illegal repeated parameter</b></td>
	</tr>
	<tr>
		<td>Parameter Name</td>
		<td style="color:red"><b><span id="parameter_repeat_name"><?php echo htmlspecialchars_decode(base64_decode($req_violations['parameter_data']['name']), ENT_SUBSTITUTE);  ?></span></b>
		<button type="button" class="btn btn_float_right btn-sm btn-dark learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="repeat">Allow Repeat</button>
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
	<tr>
		<td>Location</td>
		<td><b><?php echo ($req_violations['parameter_data']['location']);  ?></b></td>
	</tr>
	</tbody>
</table>
