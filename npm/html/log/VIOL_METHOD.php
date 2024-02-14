<table class="table table-bordered table-striped table-violation" style=" font-size:12px; ">
	<colgroup>
			<col style="width: 150px">
			<col>
	</colgroup>
		<tbody>
			<tr>
				<td>Violation Type</td>
				<td><b>Illegal HTTP method</b></td>
			</tr>
			<tr>
				<td>Method</td>
				<td style="color:red"><b><span id="method"><?php echo htmlspecialchars(($json_data['method']), ENT_SUBSTITUTE);  ?></span></b>
				<button type="button" class="btn btn_float_right btn-sm btn-dark learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="illegal_method">Add Method</button>

				</td>
			</tr>
			<tr>
				<td>Violation Details</td>
				<td><b><?php echo htmlspecialchars(base64_decode($req_violations['method_violation_details']), ENT_SUBSTITUTE);  ?></b></td>
			</tr>
		</tbody>	

</table>
