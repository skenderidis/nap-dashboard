<?php


	switch (strtolower($req_violations['context'])) {
	case 'request':
	
		if(array_key_exists("0",$req_violations['sig_data']))
		{
			foreach ($req_violations['sig_data'] as $sig_violations)
			{
				include ("log/VIOL_ATTACK_SIGNATURE_REQUEST.php");
			}
		}
		else
		{
			$sig_violations = $req_violations['sig_data'];
			include ("log/VIOL_ATTACK_SIGNATURE_REQUEST.php");
			
		}			

		break;

	case 'AMF body':
	
		if(array_key_exists("0",$req_violations['sig_data']))
		{
			foreach ($req_violations['sig_data'] as $sig_violations)
			{
				include ("log/VIOL_ATTACK_SIGNATURE_BODY.php");
			}
		}
		else
		{
			$sig_violations = $req_violations['sig_data'];
			include ("log/VIOL_ATTACK_SIGNATURE_BODY.php");
			
		}	
		break;		
	case 'parameter':
	
		if(array_key_exists("0",$req_violations['sig_data']))
		{
			foreach ($req_violations['sig_data'] as $sig_violations)
			{
				include ("log/VIOL_ATTACK_SIGNATURE_PARAMETER.php");
			}
		}
		else
		{
			$sig_violations = $req_violations['sig_data'];
			include ("log/VIOL_ATTACK_SIGNATURE_PARAMETER.php");
			
		}	
		break;
	case 'url':
	
			if(array_key_exists("0",$req_violations['sig_data']))
			{
				foreach ($req_violations['sig_data'] as $sig_violations)
				{
					include ("log/VIOL_ATTACK_SIGNATURE_URL.php");
				}
			}
			else
			{
				$sig_violations = $req_violations['sig_data'];
				include ("log/VIOL_ATTACK_SIGNATURE_URL.php");
				
			}	
			break;		
	case 'cookie':
	
		if(array_key_exists("0",$req_violations['sig_data']))
		{
			foreach ($req_violations['sig_data'] as $sig_violations)
			{
				include ("log/VIOL_ATTACK_SIGNATURE_COOKIE.php");
			}
		}
		else
		{
			$sig_violations = $req_violations['sig_data'];
			include ("log/VIOL_ATTACK_SIGNATURE_COOKIE.php");
			
		}	
		break;
	case 'header':
	
		if(array_key_exists("0",$req_violations['sig_data']))
		{
			foreach ($req_violations['sig_data'] as $sig_violations)
			{
				include ("log/VIOL_ATTACK_SIGNATURE_HEADER.php");
			}
		}
		else
		{
			$sig_violations = $req_violations['sig_data'];
			include ("log/VIOL_ATTACK_SIGNATURE_HEADER.php");
			
		}	
		break;
		
	case (stripos($req_violations['context'],'unparsed') !== false):

		if(array_key_exists("0",$req_violations['sig_data']))
		{
			foreach ($req_violations['sig_data'] as $sig_violations)
			{
				include ("log/VIOL_ATTACK_SIGNATURE_REQUEST_BODY.php");
			}
		}
		else
		{
			$sig_violations = $req_violations['sig_data'];
			include ("log/VIOL_ATTACK_SIGNATURE_REQUEST_BODY.php");
			
		}	
		break;
			

			

	default:
		if(array_key_exists("0",$req_violations['sig_data']))
		{
			foreach ($req_violations['sig_data'] as $sig_violations)
			{
				include ("log/VIOL_ATTACK_SIGNATURE_UNDEFINED.php");	
			}
		}
		else
		{
			$sig_violations = $req_violations['sig_data'];
			include ("log/VIOL_ATTACK_SIGNATURE_UNDEFINED.php");	
		
		}	

		
	}		
	
?>								


