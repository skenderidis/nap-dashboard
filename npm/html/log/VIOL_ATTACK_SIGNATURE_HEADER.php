<?php if (sizeof($req_violations['header']['header_value']) =="0") $req_violations['header']['header_value']="IA=="; ; ?>

<table class="table table-bordered table-striped table-violation" style=" font-size:12px">
<colgroup>
		<col style="width: 150px">
		<col>
	</colgroup>

	<tbody>
		<tr>
			<td>Violation Type</td>
			<td><b>Attack Signature Detected</b></td>
		</tr>	
		<tr>
			<td>Signature ID</td>
			<td>
				<b><div style="margin-top:3px; float:left"><?php echo '<span id="sig_id_'.$total_count.'">'.$sig_violations["sig_id"].'</span>'; ?></div></b>
				<button type="button" class="btn btn_float_right btn-sm btn-dark learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="attack_sig_global" value="<?php echo $total_count;?>">Disable Signature </button>
			</td>
		</tr>
		<tr>
			<td>Context</td>
			<td><b>Header</b></td>
		</tr>
		<tr>
			<td>Violation</td>
			<td>
            <b><?php 
               if(array_key_exists("buffer",$sig_violations['kw_data']))
               {
                  $temp_attack = base64_decode($sig_violations['kw_data']['buffer']);  
                  $temp_start = substr($temp_attack, 0, $sig_violations['kw_data']['offset'] );
                  $temp_violation = substr($temp_attack, $sig_violations['kw_data']['offset'], $sig_violations['kw_data']['length'] );
                  $temp_end = substr($temp_attack, $sig_violations['kw_data']['offset']+$sig_violations['kw_data']['length'] );
                  echo htmlspecialchars($temp_start). "<span style='color:red; border:1px solid red;'>" . htmlspecialchars($temp_violation) . "</span>" . $temp_end;
               }
               else
               {
                  foreach ($sig_violations['kw_data'] as $kw_data)
                  {
                     $temp_attack = base64_decode($kw_data['buffer']);  
                  $temp_start = substr($temp_attack, 0, $kw_data['offset'] );
                  $temp_violation = substr($temp_attack, $kw_data['offset'], $kw_data['length'] );
                  $temp_end = substr($temp_attack, $kw_data['offset']+$kw_data['length'] );
                  echo htmlspecialchars($temp_start). "<span style='color:red; border:1px solid red;'>" . htmlspecialchars($temp_violation) . "</span>" . $temp_end;
                  echo "<br>";
                  echo "<br>";                  
                  }
               }
				?></b>
         </td>
		</tr>
		<tr>
			<td>Header Name</td>
			<td><b><?php echo '<span id="header_'.$total_count.'">'.htmlspecialchars(base64_decode($req_violations['header']['header_name']),ENT_SUBSTITUTE).'</span>'; ?></b>
				<button type="button" class="btn btn_float_right btn-sm btn-dark learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="attack_sig_header" value="<?php echo $total_count;?>">Disable Signature on Header</button>					
			</td>
		</tr>
		<tr>
			<td>Header Value</td>
			<td><b><?php echo htmlspecialchars(base64_decode($req_violations['header']['header_value']),ENT_SUBSTITUTE);  ?></b></td>
		</tr>		
		<?php 
			if (array_key_exists("header_pattern",$req_violations['header']))
			{echo '
				<tr>
					<td>Matched on Entity</td>
					<td><b>'.($req_violations['header']['header_pattern']) .'</b></td>
				</tr>';
			}
		?>			
	</tbody>

											
</table>					

<?php $total_count++; ?>
