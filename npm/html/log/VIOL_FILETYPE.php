<table class="table table-bordered table-striped table-violation" style=" font-size:12px">
	<colgroup>
		<col class="col-xs-2">
		<col class="col-xs-6">
	</colgroup>

	<tbody>
		<tr>
			<td>Violation Type</td>
			<td><b>Illegal file type</b></td>
		</tr>
		<tr>
			<td>Extension</td>
			<?php
				echo ' <td><b><span style="display:none" id="filetype">'. base64_decode($req_violations['extension']). '</span><span style="color:red"> .'. htmlspecialchars(base64_decode($req_violations['extension']), ENT_SUBSTITUTE) . '</span></b>';
				echo '
						<button type="button" class="btn btn_float_right btn-sm btn-dark learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="illegal_filetype">Add FileType</button>
					</td>
				</tr>';
			?>
		<tr>
			<td>Disallowed File Type</td>
			<td><b><?php if ($req_violations['flg_disallowed_file_type']==1) echo "Yes"; else echo "No";  ?></b></td>
		</tr>	
	</tbody>
</table>
