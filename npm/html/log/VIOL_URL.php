<table class="table table-bordered table-striped table-violation" style=" font-size:12px">
	<colgroup>
		<col style="width: 150px">
		<col>
	</colgroup>

	<tbody>
		<tr>
			<td>Violation Type</td>
			<td><b>Illegal URL</b></td>
		</tr>
		<tr>
			<td>URL</td>
			<td style="color:red"><b><span id="url_value"><?php echo htmlspecialchars(($json_data['uri']), ENT_SUBSTITUTE); ?></span></b>
			<button type="button" class="btn btn_float_right btn-sm btn-dark learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="illegal_url">Add URL</button>
		</td>
		</tr>
		<tr>
			<td>Disallowed URL</td>
			<td><b><?php if ($req_violations['flg_disallowed_object']==1) echo "Yes"; else echo "No";  ?></b></td>
		</tr>			
	</tbody>
</table>
