
<table class="table table-bordered table-striped table-violation" style=" font-size:12px">
	<colgroup>
		<col class="col-xs-2">
		<col class="col-xs-6">
	</colgroup>

	<tbody>
		<tr>
			<td>Violation Type</td>
			<td><b>Illegal header length</b></td>
		</tr>
		<tr>
			<td>Cookie</td>
			<td><b><?php echo htmlspecialchars(base64_decode($req_violations['cookie_name']), ENT_SUBSTITUTE);  ?></b></td>
		</tr>
		<tr>
			<td>Request Length</td>
			<td><b><span style="color:red" id="cookie_length_value"><?php echo ($req_violations['cookie_len']);  ?></span></b>
			</td>
		</tr>
		<tr>
			<td>Configured Length</td>
			<td><b><span id="cookie_configured_length_value"><?php echo ($req_violations['cookie_len_limit']);  ?></span></b>
			<button type="button" class="btn btn_float_right btn-sm btn-dark learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="cookie_length">Adjust Length</button>
			</td>
		</tr>												
	
	</tbody>						
</table>					
