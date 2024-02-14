<?php 
	session_start();

	if (!isset($_SESSION['auth']))
	{
		header("Location: login.php?". $_SERVER['QUERY_STRING']); 
		exit();
	}
	if (!$_SESSION["auth"])
	{
		header("Location: login.php?". $_SERVER['QUERY_STRING']); 
		exit();
	}

	$hide_panels = False;
	if( !(isset($_GET['support_id'])) && !(isset($_SESSION['support_id'])))
	{ 
		$error = True;
		$hide_panels = True;
		$error_msg = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
										No SupportID Set. 
										<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
									</div>';

		$support_id = "Not Set";
	}
	else
	{
		if (isset($_GET['support_id']))
		{
			$support_id = $_GET['support_id'];
			unset($_SESSION['support_id']);
		}
		else
		{
			$support_id = $_SESSION['support_id'];
		}

		$payload = '{
			"_source": [],
			"query": {
			"bool": {
				"must": [
					{ "term" : {"support_id" : "'.$support_id.'"}}
				],
				"must_not": []
			}
			},
			"size": 1
		}';




		$headers = array(
			'Content-Type: application/json',
			'Accept: application/json, text/javascript, */*; '
			);


		$datasource_raw = file_get_contents('/etc/fpm/datasource.json');
		$datasource_data = json_decode($datasource_raw,true);


		$service_url = $datasource_data["url"].'/waf-logs*/_search';

		$curl = curl_init($service_url);
		curl_setopt ($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_TIMEOUT,8);
		curl_setopt($curl, CURLOPT_POSTFIELDS,$payload);

		$curl_response = curl_exec($curl);

		$error=False;
		$error_msg="";

		if ($curl_response === false) {
			$info = curl_getinfo($curl);
			curl_close($curl);
			$error = True;
			$error_msg = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
											Unable to connect to <b>"'.$service_url.'"</b>
											<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
										</div>';
		}

	
		$json_data = json_decode($curl_response, true);



		if (sizeof($json_data["hits"]["hits"])>0)
		{
			$json_data = $json_data["hits"]["hits"][0]["_source"];

			$bot_sig_button = false;
			$bot_button = false;
			if (in_array("Bot Client Detected",$json_data["violations"]))
			{
					$bot_button = true;
					if ($json_data["bot_signature_name"] !=="N/A")
						$bot_sig_button = true;
			}
		}
		else
		{
			$error = True;
			$error_msg = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
											No Match on the SupportID. 
											<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
										</div>';		
		}
	}
		// Read the JSON file 
		$file = file_get_contents('/etc/fpm/git.json');
				
		// Decode the JSON file
		$git_data = json_decode($file,true);

		// Read the Signatures file 
		$signatures_file = file_get_contents('signatures.json');
				
		// Read the Signatures file 
		$signatures = json_decode($signatures_file,true);

      $json_log = json_decode($json_data["json_log"], true);

?>

<!doctype html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="author" content="Kostas Skenderidis">
      <title>NAP</title>
      <link href="css/dataTables.bootstrap5.min.css" rel="stylesheet">
      <link rel="stylesheet" href="css/font-awesome.min.css">
      <!-- Bootstrap core CSS -->
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link href="css/flags16.css" rel="stylesheet">
      <link href="css/flags32.css" rel="stylesheet">
      <style>

      </style>
      <!-- Custom styles for this template -->
      <link href="dashboard.css" rel="stylesheet">

   </head>
   <body style="min-width: 1280px;">
      <nav class="navbar navbar-dark bg-dark sticky-top " style="padding:0px 50px 0px 10px;">
            
            <a class="navbar-brand" href="index.php"><img src="images/app-protect.svg" width=32/> &nbsp; NGINX App Protect</a>


            <li class="nav-item2 dropdown" style="list-style-type: none; ">
         <a class="nav-link dropdown-toggle" href="#" id="user" data-bs-toggle="dropdown"><i class="fa fa-user"></i>&nbsp; User &nbsp;</a>
         <ul class="dropdown-menu" aria-labelledby="user" style="font-size: 12px;left:-25px">
               <li><a class="dropdown-item" href="logout.php">Logout</a></li>
               <li><a class="dropdown-item" href="settings.php">Change Password</a></li>
               </ul>
            </li>
      </nav>
      <div class="container-fluid">
         <div class="row">
				 		<nav id="sidebarMenu" class="col-md-1 col-lg-1 d-md-block bg-light sidebar collapse">
               <div class="position-sticky pt-3" style="width:99%">
                  <ul class="nav flex-column">
							<li class="nav-item" style="background-color:#d2d8dc">
                        <a class="nav-link active" href="#">
                        <span data-feather="file"></span>
                        Violations
                        </a>
                     </li>
							<li class="nav-item">
                        <a class="nav-link " aria-current="page" href="policies.php">
                        <span data-feather="home"></span>
                        	Policies
                        </a>
                     </li>
							<li class="nav-item">
                        <a class="nav-link" aria-current="page" href="settings.php">
                        <span data-feather="home"></span>
                        	Settings
                        </a>
                     </li>


                  </ul>
               </div>
            </nav>


						<main class="col-md-11 ms-sm-auto col-lg-09 px-md-4">
								<div class="row align-items-center" <?php if ($hide_panels) echo "hidden" ?>>
										<div class="title"> SupportID: <b><span class="green" id="support_id"><?php echo $support_id; ?></span></b></div>
								</div>

								<div class="row">
					
									<div class="col-8"  <?php if ($hide_panels) echo "hidden" ?>>
										<div class="panel">
											<div class="title"> Log Details </div>
											<div class="line"></div>
											<div class="content">
												<div id="error_mg" ><?php if ($error) echo $error_msg; ?></div>
												<table id="general" class="table table-striped " style="width:100%" <?php if ($error) echo 'hidden'; ?>>

													<tbody>
														<tr>
															<td width=150px; style="text-align:right">Date/Time:</td>
															<td><b><?php echo $json_data["date_time"]; ?></b></td>
															<td width=150px; style="text-align:right">Policy:</td>
															<td><b><span id="policy_name"><?php echo $json_data["policy_name"]; ?></span></b></td>
														</tr>
														<tr>
															<td style="text-align:right">Protocol:</td>
															<td><b><?php echo $json_data["protocol"]; ?></b></td>
															<td style="text-align:right">Device:</td>
															<td><b><?php echo $json_data["unit_hostname"]; ?></b></td>											
														</tr>
														<tr>
															<td style="text-align:right">Method:</td>
															<td><b><span id="method"><?php echo $json_data["method"]; ?></span></b></td>
															<td style="text-align:right">Bot Class:</td>
															<td><b><span id="bot_class"><?php echo $json_data["client_class"]; ?></span></b> <?php if ($bot_button) echo '<button type="button" class="btn btn_float_right btn-sm btn-success learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="modify_bot_class" value="1">Modify</button>'; ?></td>
														</tr>
														<tr>
															<td style="text-align:right">Client IP:</td>
															<td><b><span id="ip"><?php echo $json_data["ip_client"]; ?></span></b></td>
															<td style="text-align:right">Bot Category</td>
															<td><b><span id="bot_category"><?php echo $json_data["bot_category"]; ?></span></b></td>
														</tr>
														<tr>
															<td style="text-align:right">Server IP:</td>
															<td><b><?php echo $json_data["host"]; ?></b></td>
															<td style="text-align:right">Bot Signature:</td>
															<td><b><span id="bot_signature"><?php echo $json_data["bot_signature_name"]; ?></span></b> <?php if ($bot_sig_button) echo '<button type="button" class="btn btn_float_right btn-sm btn-success learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="modify_bot_signature" value="1">Modify</button>'; ?></td>
														</tr>
														<tr>
															<td style="text-align:right">Response Code:</td>
															<td><b><span id="response_code"><?php echo $json_data["response_code"]; ?></span></b></td>
															<td style="text-align:right">XFF:</td>
															<td><b><?php echo $json_data["x_forwarded_for_header_value"]; ?></b></td>
														</tr>
														<tr>
															<td style="text-align:right">Country:</td>
															<td class="f16"><b><?php echo ' <i class="flag '.  strtolower($json_data["geoip"]["country_code2"]) .'"></i> ' . $json_data["geoip"]["country_name"];  ?></b></td>
															<td style="text-align:right">Bot Anomalies:</td>
															<td><b><?php echo $json_data["bot_anomalies"]; ?></b></td>
														</tr>																				
														<tr>
															<td style="text-align:right">Status:</td>
															<td><b><?php echo '<img class="image_violation" src="images/'.$json_data["request_status"].'.png">&nbsp  '. $json_data["request_status"] ;  ?></b></td>
															<td style="text-align:right">VS Name:</td>
															<td><b><?php echo $json_data["vs_name"]; ?></b></td>											
														</tr>
														<tr>
															<td style="text-align:right">Violation Rating:</td>
															<td><b><?php echo '<img class="image_violation" src="images/rating-' . $json_data["violation_rating"] . '.png">&nbsp '. $json_data["violation_rating"]; ?></b></td>
															<td style="text-align:right">Outcome:</td>
															<td><b><?php echo $json_data["outcome"]; ?></b></td>											
														</tr>
														<tr>
															<td style="text-align:right">Severity:</td>
															<td><b><?php echo $json_data["severity"]; ?></b></td>
															<td style="text-align:right">Outcome Reason:</td>
															<td><b><?php echo $json_data["outcome_reason"]; ?></b></td>											
														</tr>
														<tr>
															<td style="text-align:right">Severity Label:</td>
															<td><b><?php echo $json_data["severity_label"]; ?></b></td>
															<td style="text-align:right">Blocking Exception</td>
															<td><b><?php echo $json_data["blocking_exception_reason"]; ?></b></td>											
														</tr>																				
														<tr>
															<td style="text-align:right">CVEs:</td>
															<td><b><?php echo implode("<br>", $json_data["sig_cves"]); ?></b></td>
															<td style="text-align:right">Threat Campaigns:</td>
															<td><b><?php echo implode("<br>", $json_data["threat_campaign_names"]);  ?></b></td>											
														</tr>
														<tr>
															<td style="text-align:right; vertical-align: middle">Enforcement:</td>
															<td colspan=3>
                                                
                                                <table class="table table-bordered table-striped table-violation" style=" font-size:12px">

                                                   <thead>
                                                      <tr>
                                                         <th>Violation</th>
                                                         <th>Enforcement</th>
                                                         <th>Signature details (ID / Name)</th>
                                                      </tr>

                                                   </thead>

                                                   <tbody>

                                                      <?php 
                                                            foreach ($json_log["json_violations"] as $key_log)
                                                            {
                                                               
                                                               $viol_name = $key_log["json_violation"]["name"];
                                                               if($key_log["enforcementState"]["isBlocked"])
                                                                  $is_blocked = '<i class="fa fa-minus-circle fa-2x red"></i>';
                                                               else
                                                                  $is_blocked = '<i class="fa fa-flag fa-2x black"></i>';

                                                               if(array_key_exists("signature", $key_log))
                                                               {
                                                                  $signature_name =  $key_log["signature"]["name"];
                                                                  $signature_id =  $key_log["signature"]["signatureId"];
                                                               }
                                                               else
                                                               {
                                                                  $signature_name = "-";
                                                                  $signature_id = "-";
                                                               }
                                                               
                                                               echo '<tr><td><b>'.$viol_name.'</b></td><td>'.$is_blocked.'</td><td>'.$signature_id.' / '.$signature_name.'</td></tr>';
                                                                  
                                                            }
                                                      
                                                      ?>

                                                   </tbody>    

										                  </table>
                                             
                                          
                                             </td>
														</tr>			                                          
														<tr>
															<td style="text-align:right">URL:</td>
															<td colspan=3 style="word-break: break-all;"><b><span id="url"><?php echo htmlspecialchars($json_data["uri"]); ?></span></b></td>
														</tr>		
														<tr>
															<td style="text-align:right">Violations:</td>
															<td colspan=3><b><?php echo implode("<br>", $json_data["violations"]); ?></b></td>
														</tr>											
														<tr>
															<td style="text-align:right">Sub-violations:</td>
															<td colspan=3><?php 
																$sub_count = 0;
																if (count($json_data["sub_violations"]>0))
																{
																	foreach ($json_data["sub_violations"] as $sub_violations)
																	{
																		$sub_count++;
																		if (strpos($sub_violations, "compliance")>0)
																		{
																			$type="http_protocol_compliance";
																			$value=substr($sub_violations,32);
																		}
																		else
																		{
																			$type="evasion_technique";
																			$value=substr($sub_violations,27);
																		}
																		echo '<div><b> '.$sub_violations . '</b>';
																		if ($sub_violations !== "N/A")
																		{
																			echo '<button type="button" class="btn btn_float_right btn-sm btn-dark learn_btn modal_open" data-bs-toggle="modal" data-bs-target="#violation_modal" aria-expanded="false" id="'.$type.'" value="'.$sub_count.'">Disable</button>
																			<input type="text" class="hidden" id="'.$sub_count.'" value="'.$value.'"</input></div>';
																		}
																		echo '<div class="clearfix" style="margin-bottom: 5px;"></div>';

																	}
																
																}
																else
																	echo "N/A"; 
																
															echo implode("<br>", $json_data[""]); 
															
															?></td>
														</tr>											
														<tr>
															<td style="text-align:right; vertical-align:middle">Signatures:</td>
															<td colspan=3>
                                                
                                                <table class="table table-bordered table-striped table-violation" style=" font-size:12px">

                                                   <thead>
                                                      <tr>
                                                         <th>ID</th>
                                                         <th>Name</th>
                                                         <th>Accuracy</th>
                                                         <th>Risk</th>
                                                         <th>CVE</th>
                                                         <th>Attack Type</th>
                                                      </tr>

                                                   </thead>

                                                   <tbody>

                                                      <?php 
                                                         foreach ($json_data["sig_ids"] as  $sig_ids)
                                                         {
                                                            foreach ($signatures["signatures"] as $key_sig)
                                                            {
                                                               if ($sig_ids == $key_sig["signatureId"])
                                                               {
                                                                  
                                                                  if(array_key_exists("hasCve",$key_sig))
                                                                  {
                                                                     if ($key_sig["hasCve"])
                                                                        $cve_temp = '<i class="fa fa-check-circle fa-2x green"></i>';
                                                                     else
                                                                        $cve_temp = 'No';
                                                                  }
                                                                  else
                                                                  {
                                                                     $cve_temp = 'No';
                                                                  }
                                                                  if ($key_sig["accuracy"])
                                                                  echo '<tr><td><b>'.$key_sig["signatureId"].'</b></td><td><b>'.$key_sig["name"].'</b></td><td>'.$key_sig["accuracy"].'</td><td>'.$key_sig["risk"].'</td><td>'.$cve_temp.'</td><td>'.$key_sig["attackType"]["name"].'</td></tr>';
                                                               }
                                                                  
                                                            }

                                                         }
                                                      
                                                      ?>


                                                      
                                                   </tbody>    

										                  </table>
                                             
                                          
                                             </td>
														</tr>											
														<tr>
															<td style="text-align:right">Signature-Sets:</td>
															<td colspan=3><b><?php echo implode("<br>", $json_data["sig_set_names"]); ?></b></td>
														</tr>											
														<tr>
															<td style="text-align:right">Violations-Details:</td>
															<td colspan=3>
																
																<div class="accordion" id="accordionViolations">
																	<?php 
																	
																		if(array_key_exists("violation",$json_data['violation_details']['request-violations']))
																		{

																			$array_data = [];
																			$count = 1;
																			$total_count = 1;
																			if(array_key_exists("viol_index",$json_data['violation_details']['request-violations']['violation']))
																			{	
																				array_push($array_data, $json_data['violation_details']['request-violations']['violation']);
																			}
																			else
																			{
																				$array_data =  $json_data['violation_details']['request-violations']['violation'];
																			}
																	
																			foreach ($array_data as $req_violations)
																			{   
																				echo '

																					<div class="accordion-item">
																					<h2 class="accordion-header" id="heading'.$count.'">
																						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$count.'" aria-controls="collapse'.$count.'"> 
																						<span style="color:red"><b>' . $req_violations['viol_name'] . '</b></span>
																						</button>
																					</h2>
																					<div id="collapse'.$count.'" class="accordion-collapse collapse" aria-labelledby="heading'.$count.'" data-bs-parent="#accordionViolations">
																						<div class="accordion-body">';

																						switch ($req_violations['viol_name']) {
																						case 'VIOL_COOKIE_MALFORMED':
																							include ("log/VIOL_COOKIE_MALFORMED.php");
																							break;
																						case 'VIOL_COOKIE_MODIFIED':
																							include ("log/VIOL_COOKIE_MODIFIED.php");
																							break;
																						case 'VIOL_URL':
																							include ("log/VIOL_URL.php");
																							break;
																						case 'VIOL_FILETYPE':
																							include ("log/VIOL_FILETYPE.php");
																							break;
																						case 'VIOL_URL_LENGTH':
																							include ("log/VIOL_URL_LENGTH.php");
																							break;
																						case 'VIOL_REQUEST_LENGTH':
																							include ("log/VIOL_REQUEST_LENGTH.php");
																							break;
																						case 'VIOL_HEADER_METACHAR':
																							include ("log/VIOL_HEADER_METACHAR.php");
																							break;
																						case 'VIOL_URL_METACHAR':
																							include ("log/VIOL_URL_METACHAR.php");
																							break;
																						case 'VIOL_CROSS_ORIGIN_REQUEST':
																							include ("log/VIOL_CROSS_ORIGIN_REQUEST.php");
																							break;
																						case 'VIOL_FLOW':
																							include ("log/VIOL_FLOW.php");
																							break;
																						case 'VIOL_FLOW_ENTRY_POINT':
																							include ("log/VIOL_FLOW_ENTRY_POINT.php");
																							break;
																						case 'VIOL_THREAT_CAMPAIGN':
																							include ("log/VIOL_THREAT_CAMPAIGN.php");
																							break;
																						case 'VIOL_QUERY_STRING_LENGTH':
																							include ("log/VIOL_QUERY_STRING_LENGTH.php");
																							break;
																						case 'VIOL_POST_DATA_LENGTH':
																							include ("log/VIOL_POST_DATA_LENGTH.php");
																							break;
																						case 'VIOL_PARAMETER':
																							include ("log/VIOL_PARAMETER.php");
																							break;
																						case 'VIOL_PARAMETER_DATA_TYPE':
																							include ("log/VIOL_PARAMETER_DATA_TYPE.php");
																							break;																			
																						case 'VIOL_PARAMETER_VALUE_LENGTH':
																							include ("log/VIOL_PARAMETER_VALUE_LENGTH.php");
																							break;																			
																						case 'VIOL_PARAMETER_NUMERIC_VALUE':
																							include ("log/VIOL_PARAMETER_NUMERIC_VALUE.php");
																							break;
																						case 'VIOL_PARAMETER_VALUE_METACHAR':
																							include ("log/VIOL_PARAMETER_VALUE_METACHAR.php");
																							break;
																						case 'VIOL_PARAMETER_NAME_METACHAR':
																							include ("log/VIOL_PARAMETER_NAME_METACHAR.php");
																							break;																			
																						case 'VIOL_METHOD':
																							include ("log/VIOL_METHOD.php");
																							break;
																						case 'VIOL_REQUEST_MAX_LENGTH':
																							include ("log/VIOL_REQUEST_MAX_LENGTH.php");
																							break;																
																						case 'VIOL_MANDATORY_HEADER':
																							include ("log/VIOL_MANDATORY_HEADER.php");
																							break;
																						case 'VIOL_HEADER_REPEATED':
																							include ("log/VIOL_HEADER_REPEATED.php");
																							break;																			
																						case 'VIOL_URL_CONTENT_TYPE':
																							include ("log/VIOL_URL_CONTENT_TYPE.php");
																							break;
																						case 'VIOL_MANDATORY_PARAMETER':
																							include ("log/VIOL_MANDATORY_PARAMETER.php");
																							break;																		
																						case 'VIOL_DATA_GUARD':
																							include ("log/VIOL_DATA_GUARD.php");
																							break;
																						case 'VIOL_COOKIE_EXPIRED':
																							include ("log/VIOL_COOKIE_EXPIRED.php");
																							break;
																						case 'VIOL_PARAMETER_EMPTY_VALUE':
																							include ("log/VIOL_PARAMETER_EMPTY_VALUE.php");
																							break;
																						case 'VIOL_PARAMETER_DYNAMIC_VALUE':
																							include ("log/VIOL_PARAMETER_DYNAMIC_VALUE.php");
																							break;
																						case 'VIOL_COOKIE_LENGTH':
																							include ("log/VIOL_COOKIE_LENGTH.php");
																							break;
																						case 'VIOL_HEADER_LENGTH':
																							include ("log/VIOL_HEADER_LENGTH.php");
																							break;	
																						case 'VIOL_PARAMETER_REPEATED':
																							include ("log/VIOL_PARAMETER_REPEATED.php");
																							break;
																						case 'VIOL_PARAMETER_STATIC_VALUE':
																							include ("log/VIOL_PARAMETER_STATIC_VALUE.php");
																							break;								
																						case 'VIOL_ATTACK_SIGNATURE':
																							include ("log/VIOL_ATTACK_SIGNATURE.php");
																							break;
																						case 'VIOL_PARAMETER_MULTIPART_NULL_VALUE':
																							include ("log/VIOL_PARAMETER_MULTIPART_NULL_VALUE.php");
																							break;																			
																						case 'VIOL_PARAMETER_VALUE_BASE64':
																							include ("log/VIOL_PARAMETER_VALUE_BASE64.php");
																							break;
																						case 'VIOL_BROWSER':
																							include ("log/VIOL_BROWSER.php");
																							break;
																						case 'VIOL_ASM_COOKIE_MODIFIED':
																							include ("log/VIOL_ASM_COOKIE_MODIFIED.php");
																							break;
																						case 'VIOL_HTTP_PROTOCOL':
																							include ("log/VIOL_HTTP_PROTOCOL.php");
																							break;	
																						case 'VIOL_PARAMETER_LOCATION':
																							include ("log/VIOL_PARAMETER_LOCATION.php");
																							break;
																						case 'VIOL_XML_MALFORMED':
																							include ("log/VIOL_XML_MALFORMED.php");
																							break;
																						case 'VIOL_XML_FORMAT':
																							include ("log/VIOL_XML_FORMAT.php");
																							break;
																						case 'VIOL_ENCODING':
																							include ("log/VIOL_ENCODING.php");
																							break;																																																																						
																						case 'VIOL_EVASION':
																							if(array_key_exists("0",$req_violations['evasions']))
																							{
																								foreach ($req_violations['evasions'] as $ev_violations)
																								{
																									include ("log/VIOL_EVASION.php");
																								}
																							}
																							else
																							{
																								$ev_violations = $req_violations['evasions'];
																								include ("log/VIOL_EVASION.php");
																								
																							}																				
																							break;	
																							
																						default:
																							include ("log/VIOL_GENERIC.php");	
																						}						                            


																				echo '
																						</div>
																					</div>
																					</div>';


																				$count++;
																				$total_count++;
																			}


																		}
																		else
																		{
																			echo  "None";
																		}
																	
																	?>
																</div>										
															</td>
														</tr>	
														<tr>
															<td style="text-align:right">Payload:</td>
															<td colspan=3>
																
																<ul class="nav nav-tabs" id="myTab" role="tablist">
																	<li class="nav-item" role="presentation">
																		<button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">HTTP Request</button>
																	</li>
																	<li class="nav-item" role="presentation">
																		<button class="nav-link" id="response-tab" data-bs-toggle="tab" data-bs-target="#response" type="button" role="tab" aria-controls="response" aria-selected="false">HTTP Response</button>
																	</li>
																</ul>
																<div class="tab-content" id="myTabContent">
																	<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
																		<textarea style=" font-size:12px; width:100%; background-color:#F7F7F7" rows="10" readonly><?php echo htmlspecialchars(str_replace("\\r\\n","\r\n",($json_data['request'])),ENT_SUBSTITUTE);  ?>
																		</textarea>

																	</div>
																	<div class="tab-pane fade" id="response" role="tabpanel" aria-labelledby="response-tab">
																		<textarea style=" font-size:11px; width:100%; background-color:#F7F7F7" rows="10" readonly><?php 
																				if ($json_data['reponse'] != "")
																					echo htmlspecialchars(str_replace("\\r\\n","\r\n",($json_data['reponse'])),ENT_SUBSTITUTE);  
																				else 
																					echo "Response not Logged"; 
																			?>
																		</textarea>
																	</div>
																</div> 										
														
															</td>
														</tr>	
													</tbody>
												</table>
											</div>
										</div>
									</div>

									<div class="col-4">
										<div class="panel">
											<div class="title">Search for violation </div>
											<div class="line"></div>
											<div class="content">


												<form action="violation.php">
													<div class="row g-3">
														<div class="col-8" style="margin-top: 0px;">
														<label for="support_id" class="form-label">Support ID</label>
														<input type="text" class="form-control" name="support_id" placeholder="" value="" required>
														<div class="invalid-feedback">
															Valid support ID is required.
														</div>
														</div>

														<div class="col-md-4">
															<button class="btn btn-primary btn-xs" style="float:right; margin-top:8px;" type="submit">Search</button>
														</div>

													</div>
													<br>

												</form>		


											</div>
										</div>
										
										<div class="results">

										</div>
									</div>

								</div>

            </main>
         </div>
      </div>

			<script src="js/bootstrap.bundle.min.js"></script>
      <script src="js/jquery-3.5.1.js"></script>
      <script src="js/jquery.dataTables.min.js"></script>
      <script src="js/dataTables.bootstrap5.min.js"></script>
   </body>


<script type="text/javascript">
	$(document).ready(function(){
		$('.modal_open').on( 'click', function (e) {

		$('#method_form').val($('#method').html());
		$('#policy_form').val($('#policy_name').html());
		$('#support_id_form').val($('#support_id').html());
		$('#url_form').val($('#url').html());
		$('#code_form').val($('#response_code').html());
		$('#bot_class_form').val($('#bot_class').html());
		$('#bot_category_form').val($('#bot_category').html());
		$('#bot_signature_form').val($('#bot_signature').html());
		$('#ip_form').val($('#ip').html());
		$('#kind').val(this.id);
		$('.mars').addClass("hidden");

		if(this.id == "attack_sig_global"){
			count = this.value; 
			$('#modal_title').html("Disable Signature on Policy");
			$('#signature_form').val($('#sig_id_'+count).html());
			$('.signature_form').removeClass("hidden");
		}		
		if(this.id == "attack_sig_url") {
			count = this.value; 
			$('#modal_title').html("Disable Signature on URL");
			$('#signature_form').val($('#sig_id_'+count).html());
			$('.signature_form').removeClass("hidden");
			$('.url_form').removeClass("hidden");
		}
		if(this.id == "attack_sig_parameter"){
			count = this.value;
			$('#modal_title').html("Disable Signature on Parameter");
			$('#signature_form').val($('#sig_id_'+count).html());
			$('#entity_form_label').html("Parameter");
			$('#entity_form').val($('#parameter_'+count).html());
			$('.signature_form').removeClass("hidden");
			$('.entity_form').removeClass("hidden");
		}
		if(this.id == "attack_sig_header"){
			count = this.value;
			$('#modal_title').html("Disable Signature on Header");
			$('#signature_form').val($('#sig_id_'+count).html());
			$('#entity_form').val($('#header_'+count).html());
			$('#entity_form_label').html("Header");
			$('.signature_form').removeClass("hidden");
			$('.entity_form').removeClass("hidden");
		}
		if(this.id == "attack_sig_cookie"){
			count = this.value;
			$('#modal_title').html("Disable Signature on Cookie");
			$('#signature_form').val($('#sig_id_'+count).html());
			$('#entity_form').val($('#cookie_'+count).html());
			$('#entity_form_label').html("Cookie");
			$('.signature_form').removeClass("hidden");
			$('.entity_form').removeClass("hidden");
		}			
		if(this.id == "attack_metachar_value"){
			count = this.value;
			$('#metachar_form').val($('#metachar_'+count).html());
			$('#parameter_form').val($('#parameter_'+count).html());
			$('.metachar_form').removeClass("hidden");
			$('.parameter_form').removeClass("hidden");
		}
		if(this.id == "modify_bot_class"){
			$('#modal_title').html("Modify Bot Class configuration");
			$('.bot_class_form').removeClass("hidden");
			$('.bot_class_action').removeClass("hidden");
		}
		if(this.id == "modify_bot_signature"){
			$('#modal_title').html("Modify Bot Signature configuration");
			$('.bot_signature_form').removeClass("hidden");
			$('.bot_signature_action').removeClass("hidden");
		}
		if(this.id == "cookie_length"){
			$('#modal_title').html("Modify Cookie Length");
			$('#current_length_form').val($('#cookie_configured_length_value').html());
			$('#new_length_form').val($('#cookie_length_value').html());
			$('#current_length_form_label').html("Current Cookie Length");
			$('#new_length_form_label').html("New Cookie Length");			
			$('.new_length_form').removeClass("hidden");
			$('.current_length_form').removeClass("hidden");
		}		
		if(this.id == "header_length"){
			$('#modal_title').html("Modify HTTP Header Length");
			$('#current_length_form').val($('#header_configured_length_value').html());
			$('#new_length_form').val($('#header_length_value').html());
			$('#current_length_form_label').html("Current Header Length");
			$('#new_length_form_label').html("New Header Length");					
			$('.new_length_form').removeClass("hidden");
			$('.current_length_form').removeClass("hidden");
		}
		if(this.id == "illegal_filetype"){
			$('#modal_title').html("Add FileType to AllowList");
			$('#filetype_form').val($('#filetype').html());
			$('.filetype_form').removeClass("hidden");
		}	
		if(this.id == "illegal_method"){
			$('#modal_title').html("Add Method to AllowList");
			$('#method_form').val($('#method').html());
			$('.method_form').removeClass("hidden");
		}		
		if(this.id == "illegal_url"){
			$('#modal_title').html("Add URL to AllowList");
			$('#url_form').val($('#url').html());
			$('.url_form').removeClass("hidden");
		}			
		if(this.id == "querystringlength"){
			$('#modal_title').html("Modify Query String Length");
			$('#current_length_form_label').html("Current Query String Length");
			$('#new_length_form_label').html("New Query String Length");				
			$('#current_length_form').val($('#querystring_configured_length').html());
			$('#new_length_form').val($('#querystring_detected_length').html());
			$('#entity_form').val($('#filetype_qs').html());
			$('#entity_form_label').html("FileType Extension");
			$('.entity_form').removeClass("hidden");
			$('.current_length_form').removeClass("hidden");
			$('.new_length_form').removeClass("hidden");
		}				
		if(this.id == "requestlength"){
			$('#modal_title').html("Modify Request Length");
			$('#current_length_form_label').html("Current Request Length");
			$('#new_length_form_label').html("New Request Length");				
			$('#current_length_form').val($('#request_configured_length').html());
			$('#new_length_form').val($('#request_detected_length').html());
			$('#entity_form').val($('#filetype_request').html());
			$('#entity_form_label').html("FileType Extension");
			$('.entity_form').removeClass("hidden");			
			$('.current_length_form').removeClass("hidden");
			$('.new_length_form').removeClass("hidden");
		}				
		if(this.id == "postdatalength"){
			$('#modal_title').html("Modify Post Data Length");
			$('#current_length_form_label').html("Current Post Data Length");
			$('#new_length_form_label').html("New Post Data Length");				
			$('#current_length_form').val($('#postdata_configured_length').html());
			$('#new_length_form').val($('#postdata_detected_length').html());
			$('#entity_form').val($('#filetype_url').html());
			$('#entity_form_label').html("FileType Extension");
			$('.entity_form').removeClass("hidden");			
			$('.current_length_form').removeClass("hidden");
			$('.new_length_form').removeClass("hidden");
		}				
		if(this.id == "urllength"){
			$('#modal_title').html("Modify URL Length");
			$('#current_length_form_label').html("Current URL Length");
			$('#new_length_form_label').html("New URL Length");				
			$('#current_length_form').val($('#url_configured_length').html());
			$('#new_length_form').val($('#url_detected_length').html());
			$('#entity_form').val($('#filetype_url').html());
			$('#entity_form_label').html("FileType Extension");
			$('.entity_form').removeClass("hidden");						
			$('.current_length_form').removeClass("hidden");
			$('.new_length_form').removeClass("hidden");
		}			
		if(this.id == "datatype"){
			$('#modal_title').html("Change Parameter data type");
			$('#entity_form').val($('#parameter_datatype_name').html());
			$('#entity_form_label').html("Parameter Name");
			$('.entity_form').removeClass("hidden");						
			$("#parameter_datatype_form").val($("#expected_data_type").html())		
			$('.parameter_datatype_form').removeClass("hidden");
		}		
		if(this.id == "repeat"){
			$('#modal_title').html("Allow repeated Parameter");
			$('#entity_form').val($('#parameter_repeat_name').html());
			$('#entity_form_label').html("Parameter Name");
			$('.entity_form').removeClass("hidden");						
		}		
		if(this.id == "multipart_null"){
			$('#modal_title').html("Modify Violation");
			$('#violation_form').val($('#violation_multipart_null').val());
			$('.violation_form').removeClass("hidden");						
			$('.violation_form_alarm').removeClass("hidden");						
			$('.violation_form_block').removeClass("hidden");
		}	
		if(this.id == "unknown_browser"){
			$('#modal_title').html("Modify Violation");
			$('#violation_form').val($('#unknown_browser_action').val());
			$('.violation_form').removeClass("hidden");						
			$('.violation_form_alarm').removeClass("hidden");						
			$('.violation_form_block').removeClass("hidden");
		}				
		if(this.id == "mandatory_parameter"){
			$('#modal_title').html("Disable Mandatory Parameter");
			$('#entity_form').val($('#manadatory_parameter_name').html());
			$('#entity_form_label').html("Parameter Name");
			$('.entity_form').removeClass("hidden");
		}		
		if(this.id == "illegal_location"){
			$('#modal_title').html("Change Parameter Location");
			$('#entity_form').val($('#illegal_location_name').html());
			$('#entity_form_label').html("Parameter Name");
			$('.parameter_location_form').removeClass("hidden");
			$('.entity_form').removeClass("hidden");
		}
		if(this.id == "parameter_empty"){
			$('#modal_title').html("Disable Parameter empty values");
			$('#entity_form').val($('#parameter_empty_name').html());
			$('#entity_form_label').html("Parameter Name");
			$('.entity_form').removeClass("hidden");
		}		
		if(this.id == "illegal_parameter"){
			$('#modal_title').html("Add Parameter to AllowList");
			$('#entity_form').val($('#illegal_parameter_name').html());
			$('#entity_form_label').html("Parameter Name");
			$('.entity_form').removeClass("hidden");
		}	
		if(this.id == "numeric_value_multipleof"){
			$('#modal_title').html("Change Parameter's MulipleOf");
			$('#entity_form').val($('#parameter_numeric_value_name').html());
			$('#entity_form_label').html("Parameter Name");
			$('#multipleof_form').val($('#expected_multiple_of_value').html());
			$('.multipleof_form_check').removeClass("hidden");
			$('.multipleof_form').removeClass("hidden");
			$('.entity_form').removeClass("hidden");
		}
		if(this.id == "numeric_value_min"){
			if ($('#value_exclusive').html()=="NO")
			{
				$('#numeric_value_check_exclusive').prop('checked', false);
			}
			$('#modal_title').html("Change Parameter's Numeric values");
			$('#entity_form').val($('#parameter_numeric_value_name').html());
			$('#entity_form_label').html("Parameter Name");
			$('#numeric_value_expected_label').html("Expected Min Value");
			$('#numeric_value_detected_label').html("Detected Min Value");
			$('#numeric_value_check_label').html("CheckMin");
			$('#numeric_valuecheck_exclusive_label').html("ExclusiveMin");
			$('#numeric_value_detected').val($("#parameter_numeric_value_detected").html());
			$('#numeric_value_expected').val($("#parameter_numeric_value_expected").html());
			$('.numeric_value_detected').removeClass("hidden");
			$('.numeric_value_expected').removeClass("hidden");
			$('.numeric_value_check_exclusive').removeClass("hidden");
			$('.numeric_value_check').removeClass("hidden");
			$('.entity_form').removeClass("hidden");
		}		
		if(this.id == "numeric_value_max"){
			if ($('#value_exclusive').html()=="NO")
			{
				$('#numeric_value_check_exclusive').prop('checked', false);
			}
			$('#modal_title').html("Change Parameter's Numeric values");
			$('#entity_form').val($('#parameter_numeric_value_name').html());
			$('#entity_form_label').html("Parameter Name");
			$('#numeric_value_expected_label').html("Expected Max Value");
			$('#numeric_value_detected_label').html("Detected Max Value");
			$('#numeric_value_check_label').html("CheckMax");
			$('#numeric_valuecheck_exclusive_label').html("ExclusiveMax");
			$('#numeric_value_detected').val($("#parameter_numeric_value_detected").html());
			$('#numeric_value_expected').val($("#parameter_numeric_value_expected").html());
			$('.numeric_value_detected').removeClass("hidden");
			$('.numeric_value_expected').removeClass("hidden");
			$('.numeric_value_check_exclusive').removeClass("hidden");
			$('.numeric_value_check').removeClass("hidden");
			$('.entity_form').removeClass("hidden");
		}		
		if(this.id == "parameter_illegal_base64"){
			$('#modal_title').html("Disable Parameter's Base64 config");
			$('#entity_form').val($('#parameter_illegal_base64_name').html());
			$('#entity_form_label').html("Parameter Name");
			$('.entity_form').removeClass("hidden");
		}		
		if(this.id == "illegal_value_length_min"){
			$('#modal_title').html("Change Parameter's Min Length values");
			$('#entity_form').val($('#parameter_length_value_name').html());
			$('#entity_form_label').html("Parameter Name");
			$('#numeric_value_expected_label').html("Expected Min Value");
			$('#numeric_value_detected_label').html("Detected Value");
			$('#numeric_value_check_label').html("checkMinValueLength");
			$('#numeric_value_detected').val($("#parameter_detected_length").html());
			$('#numeric_value_expected').val($("#parameter_expected_length").html());
			$('.numeric_value_detected').removeClass("hidden");
			$('.numeric_value_expected').removeClass("hidden");
			$('.numeric_value_check').removeClass("hidden");
			$('.entity_form').removeClass("hidden");
		}		
		if(this.id == "illegal_value_length_max"){
			$('#modal_title').html("Change Parameter's Max Length values");
			$('#entity_form').val($('#parameter_length_value_name').html());
			$('#entity_form_label').html("Parameter Name");
			$('#numeric_value_expected_label').html("Expected Max Value");
			$('#numeric_value_detected_label').html("Detected Value");
			$('#numeric_value_check_label').html("checkMaxValueLength");
			$('#numeric_value_detected').val($("#parameter_detected_length").html());
			$('#numeric_value_expected').val($("#parameter_expected_length").html());
			$('.numeric_value_detected').removeClass("hidden");
			$('.numeric_value_expected').removeClass("hidden");
			$('.numeric_value_check').removeClass("hidden");
			$('.entity_form').removeClass("hidden");
		}		
		if(this.id == "modified_asm_cookie"){
			$('#modal_title').html("Modify Violation");
			$('#violation_form').val($('#violation_modified_asm_cookie').val());
			$('.violation_form').removeClass("hidden");						
			$('.violation_form_alarm').removeClass("hidden");						
			$('.violation_form_block').removeClass("hidden");
		}	
		if(this.id == "illegal_static_value"){
			$('#modal_title').html("Change Parameter's static ValueType");
			$('#entity_form').val($('#parameter_static_value_name').html());
			$('#entity_form_label').html("Parameter Name");
			$('.parameter_valuetype_form').removeClass("hidden");			
			$('.entity_form').removeClass("hidden");
		}		
		if(this.id == "modify_static_value"){
			$('#modal_title').html("Add value to Static List");
			$('#entity_form').val($('#parameter_static_value_name').html());
			$('#entity_form_label').html("Parameter Name");
			$('#static_value_add').val($('#parameter_static_value').html());
			$('.static_value_add').removeClass("hidden");
			$('.entity_form').removeClass("hidden");
		}	
		if(this.id == "cookie_modified"){
			$('#modal_title').html("Change Enforcement to Allow");
			$('#entity_form').val($('#cookie_name').html());
			$('#entity_form_label').html("Cookie Name");
			$('.entity_form').removeClass("hidden");
		}	
		if(this.id == "malformed_cookie"){
			$('#modal_title').html("Modify Violation");
			$('#violation_form').val($('#violation_malformed_cookie').val());
			$('.violation_form').removeClass("hidden");						
			$('.violation_form_alarm').removeClass("hidden");						
			$('.violation_form_block').removeClass("hidden");
		}			
		if(this.id == "http_protocol_compliance"){
			$('#modal_title').html("Disable HTTP Protocol Compliance Sub-Violation");
			var id = this.value
			$('#violation_form').val($('#'+id).val());
			$('#violation_form_label').html("Sub-Violation");
			$('.violation_form').removeClass("hidden");						
		}	
		if(this.id == "evasion_technique"){
			$('#modal_title').html("Disable HTTP Protocol Compliance Sub-Violation");
			var id = this.value
			$('#violation_form').val($('#'+id).val());
			$('#violation_form_label').html("Sub-Violation");
			$('.violation_form').removeClass("hidden");						
		}	
		if(this.id == "encoding"){
			$('#modal_title').html("Modify Violation");
			$('#violation_form').val($('#violation_encoding').val());
			$('.violation_form').removeClass("hidden");						
			$('.violation_form_alarm').removeClass("hidden");						
			$('.violation_form_block').removeClass("hidden");
		}		
		if(this.id == "request_max_length"){
			$('#modal_title').html("Modify Violation");
			$('#violation_form').val($('#violation_request_max_length').val());
			$('.violation_form').removeClass("hidden");						
			$('.violation_form_alarm').removeClass("hidden");						
			$('.violation_form_block').removeClass("hidden");
		}		

		

		});
	});
</script>			

   <!-- Modal -->
<div class="modal fade" id="violation_modal" tabindex="-1" aria-labelledby="violation_modal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
    		<div class="modal-header">
				<h5 class="modal-title" id="modal_title">Disable Signature on URL</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
	  

      	<div class="modal-body" style="margin-top: -20px;">

				<form class="row g-3">
					<div class="col-md-8">
						<label class="form-label">Policy to Modify <i class="fa fa-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="The filename saved on Git must have the same name as the policy with either '.json' or '.yaml' extension. The extension will be added automatically depending on the Git selection."></i>  </label>
						<input type="text" class="form-control" id="policy_form">
					</div>
					<div class="col-md-4">
						<label class="form-label">SupportID</label>
						<input type="text" class="form-control" id="support_id_form">
					</div>
						
					<div class="col-md-3 method_form mars">
						<label class="form-label">Add Method</label>
						<input type="text" class="form-control" id="method_form">
					</div>
					<div class="col-md-3 code_form mars">
						<label class="form-label">Add Response Code</label>
						<input type="text" class="form-control" id="code_form">
					</div>
					<div class="col-md-3 bot_class_form mars">
						<label class="form-label">Bot Class </label>
						<input type="text" class="form-control" id="bot_class_form">
					</div>

					<div class="col-md-3 bot_class_action mars">
						<label class="form-label">Modify Action </label>
						<select class="form-select" id="bot_class_action">
							<option value="ignore" selected>Ignore</option>
							<option value="detect">Detect</option>
							<option value="alarm">Alarm</option>
							<option value="block">Block</option>
						</select>
					</div>

					<div class="col-md-6 bot_signature_form mars">
						<label class="form-label">Bot Signature</label>
						<input type="text" class="form-control" id="bot_signature_form">
					</div>

					<div class="col-md-3 bot_signature_action mars">
						<label class="form-label">Modify Action </label>
						<select class="form-select" id="bot_signature_action">
							<option value="ignore" selected>Ignore</option>
							<option value="detect">Detect</option>
							<option value="alarm">Alarm</option>
							<option value="block">Block</option>
						</select>
					</div>					
					<div class="col-md-6 violation_form mars">
						<label class="form-label" id="violation_form_label">Violation</label>
						<input type="text" class="form-control" id="violation_form" disabled>
					</div>
					<div class="col-md-2 violation_form_block mars form-check" style="text-align:center">
						<label class="form-label" for="violation_form_block" style="width:100%;">Block</label>
						<input class="" type="checkbox" id="violation_form_block" style="height:18px; width:18px">
					</div>
					<div class="col-md-2 violation_form_alarm mars form-check" style="text-align:center">
						<label class="form-label" for="violation_form_alarm" style="width:100%;">Alarm</label>
						<input class="" type="checkbox" id="violation_form_alarm" style="height:18px; width:18px">
					</div>											
					<div class="col-md-3 ip_form mars">
						<label class="form-label">Client IP</label>
						<input type="text" class="form-control" id="ip_form">
					</div>
					<div class="col-md-6 signature_form mars">
						<label class="form-label">Disable Signature</label>
						<input type="text" class="form-control" id="signature_form">
					</div>
					<div class="col-md-6 metachar_form mars">
						<label class="form-label">Disable MetaChar (Decimal Form)</label>
						<input type="text" class="form-control" id="metachar_form">
					</div>								
					<div class="col-md-4 entity_form mars">
						<label class="form-label" id="entity_form_label">Entity</label>
						<input type="text" class="form-control" id="entity_form">
					</div>
					<div class="col-md-6 url_form mars">
						<label class="form-label">URL</label>
						<input type="text" class="form-control" id="url_form">
					</div>
					<div class="col-md-6 header_form mars">
						<label class="form-label">Header</label>
						<input type="text" class="form-control" id="header_form">
					</div>
					<div class="col-md-6 cookie_form mars">
						<label class="form-label">Cookie</label>
						<input type="text" class="form-control" id="cookie_form">
					</div>
					<div class="col-md-4 current_length_form mars">
						<label class="form-label" id="current_length_form_label">Current Length</label>
						<input type="text" class="form-control" id="current_length_form" disabled>
					</div>
					<div class="col-md-4 new_length_form mars">
						<label class="form-label" id="new_length_form_label">New Length</label>
						<input type="text" class="form-control" id="new_length_form">
					</div>	

					<div class="col-md-3 filetype_form mars">
						<label class="form-label">Add FileType</label>
						<input type="text" class="form-control" id="filetype_form">
					</div>	
					<div class="col-md-3 parameter_datatype_form mars">
						<label class="form-label">Modify Data Type </label>
						<select class="form-select" id="parameter_datatype_form">
							<option value="alpha-numeric" selected>alpha-numeric</option>
							<option value="binary">binary</option>
							<option value="phone">phone</option>
							<option value="email">email</option>
							<option value="boolean">boolean</option>
							<option value="integer">integer</option>
							<option value="decimal">decimal</option>
						</select>
					</div>	
					<div class="col-md-3 parameter_location_form mars">
						<label class="form-label">Parameter Location </label>
						<select class="form-select" id="parameter_location_form">
							<option value="any" selected>any</option>
							<option value="cookie">cookie</option>
							<option value="form-data">form-data</option>
							<option value="header">header</option>
							<option value="path">path</option>
							<option value="query">query</option>
						</select>
					</div>

					<div class="col-md-3 parameter_valuetype_form mars">
						<label class="form-label">Modify ValueType </label>
						<select class="form-select" id="parameter_valuetype_form">
							<option value="auto-detect" selected>auto-detect</option>
							<option value="array">array</option>
							<option value="ignore">ignore</option>
							<option value="json">json</option>
							<option value="openapi-array">openapi-array</option>
							<option value="static-content">static-content</option>
							<option value="user-input">user-input</option>
							<option value="xml">xml</option>
						</select>
					</div>	
					<div class="col-md-3 static_value_add mars">
						<label class="form-label">Add Static Value</label>
						<input type="text" class="form-control" id="static_value_add">
					</div>	

					<div class="row" style="margin-top: 15px;margin-left: -15px;">
						<div class="col-md-3 multipleof_form mars">
							<label class="form-label">MultipleOf</label>
							<input type="text" class="form-control" id="multipleof_form">
						</div>
						<div class="col-md-2 multipleof_form_check mars form-check" style="text-align:center">
							<label class="form-label" for="multipleof_form_check" style="width:100%;">Enable</label>
							<input class="" type="checkbox" id="multipleof_form_check" style="height:18px; width:18px" checked>
						</div>
						<div class="col-md-3 numeric_value_detected mars">
							<label class="form-label" id="numeric_value_detected_label">Detected Value</label>
							<input type="text" class="form-control" id="numeric_value_detected" disabled>
						</div>
						<div class="col-md-3 numeric_value_expected mars">
							<label class="form-label" id="numeric_value_expected_label">Expected Value</label>
							<input type="text" class="form-control" id="numeric_value_expected">
						</div>					
						<div class="col-md-2 numeric_value_check mars form-check" style="text-align:center">
							<label class="form-label" id="numeric_value_check_label" style="width:100%;">Check</label>
							<input class="" type="checkbox" id="numeric_value_check" style="height:18px; width:18px" checked>
						</div>
						<div class="col-md-2 numeric_value_check_exclusive mars form-check" style="text-align:center">
							<label class="form-label" id="numeric_valuecheck_exclusive_label" style="width:100%;">Exclusive</label>
							<input class="" type="checkbox" id="numeric_value_check_exclusive" style="height:18px; width:18px" checked>
						</div>
					</div>			


					<div class="col-md-12">
						<label class="form-label">Git Comment</label>
						<input type="text" class="form-control" id="comment" aria-describedby="text">
					</div>		

					<div class="col-md-6">
						<label for="inputState" class="form-label">Repository <i class="fa fa-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Depending whether the repository is marked as (JSON) or (YAML), the equivalent extension will be added on the policy name."></i> </label>
						<select id="git" class="form-select">
						<option >Choose...</option>
						 	<?php
						 
							foreach ($git_data as $git)
							{
								echo '<option value="'.$git['uuid'].'">'.$git['fqdn'].'/'.$git['project'].'/'.$git['path'].' ('.$git['format'].')</option>';
							}
							
							?>
						 </select>
					</div>

					<div class="col-md-2 branch form-check" style="text-align:center;" hidden>
						<label class="form-label" for="branch" style="width:100%; ">Create Branch</label>
						<input class="" type="checkbox" id="branch" style="height:18px; width:18px; margin-top: 6px;" disabled>
					</div>					

					<div class="col-md-12 kind" hidden>
						<label class="form-label">Violation Kind</label>
						<input type="text" class="form-control" id="kind">
					</div>	

				</form>
				
 			</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="deploy" data-bs-dismiss="modal">Deploy</button>
      </div>
    </div>
  </div>
</div>



<script>
   var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
   var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
   })
</script>   




<script>
$(document).ready(function(){
  $("#deploy").click(function(){
		var data = {}
		var kind  = $("#kind").val();
		if ($("#comment").val()=="")
			var comment = btoa("-");
		else
			var comment = btoa($("#comment").val());

		var support_id =  $("#support_id_form").val();
		var policy =  $("#policy_form").val();
		var git =  $("#git option:selected").val();

		if (kind == 'modify_bot_class'){
			var bot_class = $('#bot_class').html().replace(" ", "-").toLowerCase();;
			var action = $('#bot_class_action option:selected').val();
			var policy_data = btoa('{"type":"'+kind+'","class_name":"'+bot_class+'","action":"'+action+'"}');
		}
		if (kind == 'modify_bot_signature'){
			var bot_signature = $('#bot_signature').html();
			var action = $('#bot_signature_action option:selected').val();
			var policy_data = btoa('{"type":"'+kind+'","signature_name":"'+bot_signature+'","action":"'+action+'"}');
		}
		if (kind == 'attack_sig_global'){
			var sig_id =  $("#signature_form").val();
			var policy_data = btoa('{"type":"'+kind+'","sig_id":'+sig_id+'}');
		}
		if ( kind == 'attack_sig_parameter' || kind == 'attack_sig_cookie' || kind == 'attack_sig_header'){
			var sig_id =  $("#signature_form").val();
			var entity = $("#entity_form").val();
			var policy_data = btoa('{"type":"'+kind+'","sig_id":'+sig_id+',"entity":"'+entity+'"}');
		}
		if ( kind == 'attack_sig_url'){
			var sig_id =  $("#signature_form").val();
			var entity = $("#url_form").val();
			var policy_data = btoa('{"type":"'+kind+'","sig_id":'+sig_id+',"entity":"'+entity+'"}');
		}		
		if ( kind == 'cookie_length'){
			var value = $("#new_length_form").val();
			var policy_data = btoa('{"type":"'+kind+'","value":'+value+'}');
		}		
		if ( kind == 'header_length'){
			var value = $("#new_length_form").val();
			var policy_data = btoa('{"type":"'+kind+'","value":'+value+'}');
		}
		if ( kind == 'illegal_filetype'){
			var filetype = $("#filetype_form").val();
			var policy_data = btoa('{"type":"'+kind+'","filetype":"'+filetype+'","enabled":true}');
		}
		if ( kind == 'illegal_method'){
			var method = $("#method_form").val();
			var policy_data = btoa('{"type":"'+kind+'","method":"'+method+'"}');
		}
		if ( kind == 'illegal_url'){
			var url = $("#url_form").val();
			var policy_data = btoa('{"type":"'+kind+'","url":"'+url+'","enabled":true}');
		}				
		if ( kind == 'querystringlength'){
			var entity = $("#entity_form").val();
			var value = $("#new_length_form").val();
			var policy_data = btoa('{"type":"'+kind+'","entity":"'+entity+'","value":'+value+'}');
		}	
		if ( kind == 'requestlength'){
			var entity = $("#entity_form").val();
			var value = $("#new_length_form").val();
			var policy_data = btoa('{"type":"'+kind+'","entity":"'+entity+'","value":'+value+'}');
		}			
		if ( kind == 'postdatalength'){
			var entity = $("#entity_form").val();
			var value = $("#new_length_form").val();
			var policy_data = btoa('{"type":"'+kind+'","entity":"'+entity+'","value":'+value+'}');
		}	
		if ( kind == 'urllength'){
			var entity = $("#entity_form").val();
			var value = $("#new_length_form").val();
			var policy_data = btoa('{"type":"'+kind+'","entity":"'+entity+'","value":'+value+'}');
		}	
		if ( kind == 'datatype'){
			var entity = $("#entity_form").val();
			var value = $('#parameter_datatype_form option:selected').val();
			var policy_data = btoa('{"type":"'+kind+'","entity":"'+entity+'","value":"'+value+'"}');
		}	
		if ( kind == 'repeat'){
			var entity = $("#entity_form").val();
			var policy_data = btoa('{"type":"'+kind+'","entity":"'+entity+'","value":true}');
		}	
		if ( kind == 'multipart_null'){
			var viol_name = $("#violation_form").val();
			var block = $("#violation_form_block").is(":checked");
			var alarm = $("#violation_form_alarm").is(":checked");
			var policy_data = btoa('{"type":"disable_violation","viol_name":"'+viol_name+'","block":'+block+',"alarm":'+block+'}');
		}	
		if ( kind == 'illegal_parameter'){
			var entity = $("#entity_form").val();
			var policy_data = btoa('{"type":"'+kind+'","entity":"'+entity+'","enabled":true}');
		}			

		if ( kind == 'mandatory_parameter'){
			var entity = $("#entity_form").val();
			var policy_data = btoa('{"type":"'+kind+'","entity":"'+entity+'","enabled":false}');
		}
		if ( kind == 'illegal_location'){
			var entity = $("#entity_form").val();
			var location = $("#parameter_location_form option:selected").val();
			var policy_data = btoa('{"type":"'+kind+'","entity":"'+entity+'","location":"'+location+'"}');
		}
		if ( kind == 'parameter_empty'){
			var entity = $("#entity_form").val();
			var policy_data = btoa('{"type":"'+kind+'","entity":"'+entity+'","value":true}');
		}	
		if ( kind == 'numeric_value_multipleof'){
			var entity = $("#entity_form").val();
			var multipleof = $("#multipleof_form").val();
			var check_multiple = $("#multipleof_form_check").is(":checked");
			var policy_data = btoa('{"type":"'+kind+'","entity":"'+entity+'","multipleof":'+multipleof+',"check_multiple":'+check_multiple+'}');
		}

		if ( kind == 'numeric_value_min' || kind == 'numeric_value_max'){
			var entity = $("#entity_form").val();
			var value = $("#numeric_value_expected").val();
			var check_value = $("#numeric_value_check").is(":checked");
			var exclusive = $("#numeric_value_check_exclusive").is(":checked");
			var policy_data = btoa('{"type":"'+kind+'","entity":"'+entity+'","value":'+value+',"check_value":'+check_value+',"exclusive":'+exclusive+'}');
		}
		if ( kind == 'illegal_value_length_max' || kind == 'illegal_value_length_min'){
			var entity = $("#entity_form").val();
			var value = $("#numeric_value_expected").val();
			var check_value = $("#numeric_value_check").is(":checked");
			var policy_data = btoa('{"type":"'+kind+'","entity":"'+entity+'","value":'+value+',"check_value":'+check_value+'}');
		}
		
		if ( kind == 'parameter_illegal_base64'){
			var entity = $("#entity_form").val();
			var policy_data = btoa('{"type":"'+kind+'","entity":"'+entity+'","enabled":false}');
		}


		if ( kind == 'illegal_static_value'){
			var entity = $("#entity_form").val();
			var value = $('#parameter_valuetype_form option:selected').val();
			var policy_data = btoa('{"type":"'+kind+'","entity":"'+entity+'","value":"'+value+'"}');
		}
		if ( kind == 'modify_static_value'){
			var entity = $("#entity_form").val();
			var value = $('#static_value_add').val();
			var policy_data = btoa('{"type":"'+kind+'","entity":"'+entity+'","value":"'+value+'"}');
		}
		if ( kind == 'cookie_modified'){
			var entity = $("#entity_form").val();
			var policy_data = btoa('{"type":"'+kind+'","entity":"'+entity+'","enforcementType":"allow"}');
		}

		if ( kind == "http_protocol_compliance"){
			var viol_name = $("#violation_form").val();
			var policy_data = btoa('{"type":"'+kind+'","name":"'+viol_name+'","enabled":false}');
		}	
		if ( kind == "evasion_technique"){
			var viol_name = $("#violation_form").val();
			var policy_data = btoa('{"type":"'+kind+'","name":"'+viol_name+'","enabled":false}');
		}	
		if ( kind == 'encoding' || kind == 'request_max_length' || kind == 'malformed_cookie' || kind == 'modified_asm_cookie' || kind == 'unknown_browser'){
			var viol_name = $("#violation_form").val();
			var block = $("#violation_form_block").is(":checked");
			var alarm = $("#violation_form_alarm").is(":checked");
			var policy_data = btoa('{"type":"disable_violation","viol_name":"'+viol_name+'","block":'+block+',"alarm":'+block+'}');
		}	
		if ( kind == "evasion_technique"){
			var viol_name = $("#violation_form").val();
			var policy_data = btoa('{"type":"'+kind+'","name":"'+viol_name+'","enabled":false}');
		}	
		

		$.ajax({
				method: "POST",
				url: "deploy.php",
				data:  {policy: policy, 
								policy_data:policy_data, 
								comment:comment, 
								support_id:support_id, 
								git:git
				}
			})
			.done(function( msg ) {
				$(".results").append(msg);
			})
			.fail(function( jqXHR, textStatus, Status  ) {
				$(".results").append("<h5><span style='color:red'> Something went wrong</span></h5>");
			})
			})
	});
</script>
