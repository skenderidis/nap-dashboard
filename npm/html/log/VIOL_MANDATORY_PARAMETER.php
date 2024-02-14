
<table class="table table-bordered table-striped table-violation" style=" font-size:12px; ">
	<colgroup>
			<col style="width: 150px">
			<col>
	</colgroup>

	<tbody>
		<tr>
			<td>Violation Type</td>
			<td><b>Missing Mandatory Parameter</b></td>
		</tr>
		<tr>
			<td>Parameter Name</td>
			<td style="color:red"><b><span id="manadatory_parameter_name"><?php echo htmlspecialchars(($req_violations['missing_required_parameter']), ENT_SUBSTITUTE);  ?></span></b>
			<button type="button" class="btn btn_float_right btn-sm btn-dark learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="mandatory_parameter">Disable</button>
			</td>
		</tr>

	</tbody>						
</table>					
