
<table class="table table-bordered table-striped table-violation" style=" font-size:12px; ">
	<colgroup>
			<col style="width: 150px">
			<col>
	</colgroup>

	<tbody>
		<tr>
			<td>Violation Type</td>
			<td><b>Unkown Browser</b>
			<button type="button" class="btn btn_float_right btn-sm btn-dark learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="unknown_browser">Disable</button>
			</td>
		</tr>
		<tr>
			<td>Unkown Browser Action</td>
			<td style="color:red"><b><?php echo htmlspecialchars(($req_violations['unknown_browser_action']), ENT_SUBSTITUTE);  ?></b><input type="text" class="hidden" id="unknown_browser_action" value="VIOL_BROWSER"</input>
			</td>
		</tr>

	</tbody>						
</table>					
