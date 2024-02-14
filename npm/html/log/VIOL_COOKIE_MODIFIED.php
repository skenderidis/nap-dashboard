
	<table class="table table-bordered table-striped table-violation" style=" font-size:12px">
		<colgroup>
			<col style="width: 150px">
			<col>
		</colgroup>

		<tbody>
			<tr>
				<td>Violation Type</td>
				<td><b>Modified domain cookie(s)</b></td>
			</tr>
			<tr>
				<td>Cookie Name</td>
				<td ><b><span id="cookie_name"><?php echo htmlspecialchars(base64_decode($req_violations['cookie']['cookie_name']), ENT_SUBSTITUTE);  ?></span></b>
					<button type="button" class="btn btn_float_right btn-sm btn-dark learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="cookie_modified">Disable</button>
				</td>
			</tr>
			<tr>
				<td>Cookie Value</td>
				<td style="color:red"><b><?php echo htmlspecialchars(base64_decode($req_violations['cookie']['cookie_value']), ENT_SUBSTITUTE);  ?></b></td>
			</tr>
			<tr>
				<td>Is New Cookie</td>
				<td><b><?php if ($req_violations['is_new_cookie'] == 1) echo "Yes"; else echo "No";  ?></b></td>
			</tr>
		</tbody>
		
	</table>					
