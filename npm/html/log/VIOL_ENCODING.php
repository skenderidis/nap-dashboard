
<table class="table table-bordered table-striped table-violation" style=" font-size:12px; ">
	<colgroup>
		<col style="width: 150px">
		<col>
	</colgroup>

		<tbody>
			<tr>
				<td>Violation Type</td>
				<td><b>Failed to convert character</b><input type="text" class="hidden" id="violation_encoding" value="VIOL_ENCODING"</input>
					<button type="button" class="btn btn_float_right btn-sm btn-dark learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="encoding">Disable</button>
				</td>
			</tr>
			<tr>
				<td>Context</td>
				<td><b><?php echo $req_violations['context'];  ?></b></td>
			</tr>
			<tr>
				<td>Buffer</td>
				<td><b><?php echo htmlspecialchars(base64_decode($req_violations['buffer']), ENT_SUBSTITUTE);  ?></b></td>
			</tr>
			<tr>
				<td>Offset</td>
				<td><b><?php echo $req_violations['offset']; ?></b></td>
			</tr>			
		</tbody>	

</table>
