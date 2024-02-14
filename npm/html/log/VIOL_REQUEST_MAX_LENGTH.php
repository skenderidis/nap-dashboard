
<table class="table table-bordered table-striped table-violation" style=" font-size:12px">
	<colgroup>
		<col style="width: 150px">
		<col>
	</colgroup>

	<tbody>
		<tr>
			<td>Violation Type</td>
			<td><b>Request length exceeds defined buffer size</b><input type="text" class="hidden" id="violation_request_max_length" value="VIOL_REQUEST_MAX_LENGTH"</input>
				<button type="button" class="btn btn_float_right btn-sm btn-dark learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="request_max_length">Disable</button>		
			</td>

		</tr>
		<tr>
			<td>Request Length</td>
			<td style="color:red"><b><?php echo ($req_violations['detected_length']);  ?></b></td>
		</tr>
		<tr>
			<td>Configured Length</td>
			<td><b><?php echo ($req_violations['defined_length']);  ?></b></td>
		</tr>												
	
	</tbody>						
</table>					
