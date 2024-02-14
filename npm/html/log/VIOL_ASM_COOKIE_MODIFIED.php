

	<table class="table table-bordered table-striped table-violation" style=" font-size:12px">
		<colgroup>
			<col style="width: 150px">
			<col>
		</colgroup>

		<tbody>
			<tr>
				<td>Violation Type</td>
				<td><b>Modified ASM cookie</b><input type="text" class="hidden" id="violation_modified_asm_cookie" value="VIOL_ASM_COOKIE_MODIFIED"</input>
					<button type="button" class="btn btn_float_right btn-sm btn-dark learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="modified_asm_cookie">Disable</button>
				</td>
			</tr>
			<tr>
				<td>Cookie Name</td>
				<td ><b><span id="asm_cookie_name"><?php echo htmlspecialchars(base64_decode($req_violations['cookie_name']), ENT_SUBSTITUTE);  ?></span></b></td>
			</tr>
		</tbody>
		
	</table>					
