<?php 
	session_start();

	if (!isset($_SESSION['auth']))
	{
		header("Location: login.php"); 
		exit();
	}
	if (!$_SESSION["auth"])
	{
		header("Location: login.php"); 
		exit();
	}

   if( !(isset($_POST['format']))) 
	{
		header("Location: policies.php"); 
		exit();
	}
   else
      $format = $_POST['format'];

   if( !(isset($_POST['uuid']))) 
      {
         header("Location: policies.php"); 
         exit();
      }
      else
         $uuid = $_POST['uuid'];

   if(!isset($_POST["policy"]))
   {
     Header("Location: policies.php");
     exit();
   }
   else
   {
      $file_location = "config_files/".$_POST["policy"]."/policy-full-export.json";
      if ( $format =="YAML")  ## Check if policy is YAML
         $file_location_original = "config_files/".$_POST["policy"]."/".str_replace(".yaml", ".json", $_POST["policy"]);
      else
         $file_location_original = "config_files/".$_POST["policy"]."/".$_POST["policy"];

   }

   // Read and decode the original policy file 
   $json = file_get_contents($file_location_original);
   $json_data_original = json_decode($json,true);

   // Read and decode the exported policy file 
   $json = file_get_contents($file_location);
   $json_data = json_decode($json,true);   




   # ----------  Violations   ---------------
   //Exported policy
   if(array_key_exists("violations", $json_data["policy"]["blocking-settings"]))
      $violations = "var violations = " . json_encode($json_data["policy"]["blocking-settings"]["violations"]) . " ;";
   else
      $violations = "var violations = [] ;";	

   //Original policy 
   if(array_key_exists("violations", $json_data_original["policy"]["blocking-settings"]))
      $violations_original = "var violations_original = " . json_encode($json_data_original["policy"]["blocking-settings"]["violations"])  . " ;";
   else
      $violations_original = "var violations_original = [] ;";	

   # ----------  HTTP Compliance   ---------------
   //Exported policy
   if(array_key_exists("http-protocols", $json_data["policy"]["blocking-settings"]))
      $compliance = "var compliance = " . json_encode($json_data["policy"]["blocking-settings"]["http-protocols"]) . " ;";
   else
      $compliance = "var compliance = [] ;";	

   //Original policy 
   if(array_key_exists("http-protocols", $json_data_original["policy"]["blocking-settings"]))
      $compliance_original = "var compliance_original = " . json_encode($json_data_original["policy"]["blocking-settings"]["http-protocols"])  . " ;";
   else
      $compliance_original = "var compliance_original = [] ;";	

   # ----------  Evasion   ---------------
   //Exported policy
   if(array_key_exists("evasions", $json_data["policy"]["blocking-settings"]))
   	$evasion = "var evasion = " .  json_encode($json_data["policy"]["blocking-settings"]["evasions"]) . " ;";
   else
      $evasion = "var evasion = [] ;";	

   //Original policy 
   if(array_key_exists("evasions", $json_data_original["policy"]["blocking-settings"]))
      $evasion_original = "var evasion_original = " . json_encode($json_data_original["policy"]["blocking-settings"]["evasions"])  . " ;";
   else
      $evasion_original = "var evasion_original = [] ;";	

   # ----------  Signature Sets   ---------------
   //Exported policy
	
   if(array_key_exists("signature-sets", $json_data["policy"]))
      $signature_sets = "var signature_sets = " . json_encode($json_data["policy"]["signature-sets"])  . " ;";
   else
      $signature_sets = "var signature_sets = [] ;";

   //Original policy
   if(array_key_exists("signature-sets", $json_data_original["policy"]))
      $signature_sets_original = "var signature_sets_original = " . json_encode($json_data_original["policy"]["signature-sets"])  . " ;";
   else
      $signature_sets_original = "var signature_sets_original = [] ;";


   # ----------  Signatures  ---------------
   //Check if exists on the exported policy
	if(array_key_exists("signatures", $json_data["policy"]))
		$signatures = "var signatures = " . json_encode($json_data["policy"]["signatures"])  . " ;";
	else
		$signatures = "var signatures = [] ;";
   //Check if exists on the original policy
   if(array_key_exists("signatures", $json_data_original["policy"]))
      $signatures_original = "var signatures_original = " . json_encode($json_data_original["policy"]["signatures"])  . " ;";
   else
      $signatures_original = "var signatures_original = [] ;";

   # ----------  Threat Campaigns  ---------------
   //Check if exists on the exported policy
   if(array_key_exists("threat-campaigns", $json_data["policy"]))
      $threat_campaigns =  "var threat_campaigns = " . json_encode($json_data["policy"]["threat-campaigns"])  . " ;";
   else
      $threat_campaigns = "var threat_campaigns = [] ;";
   //Check if exists on the original policy
   if(array_key_exists("threat-campaigns", $json_data_original["policy"]))
      $threat_campaigns_original =  "var threat_campaigns_original = " . json_encode($json_data_original["policy"]["threat-campaigns"])  . " ;";
   else
      $threat_campaigns_original = "var threat_campaigns_original = [] ;";


   # ----------  Server Technologies  ---------------
   //Check if exists on the exported policy
	if(array_key_exists("server-technologies", $json_data["policy"]))
		$server_technologies = "var server_technologies = " . json_encode($json_data["policy"]["server-technologies"])  . " ;";
   else
	   $server_technologies = "var server_technologies = [] ;";
   //Check if exists on the original policy
   if(array_key_exists("server-technologies", $json_data_original["policy"]))
      $server_technologies_original = "var server_technologies_original = " . json_encode($json_data_original["policy"]["server-technologies"])  . " ;";
   else
      $server_technologies_original = "var server_technologies_original = [] ;";

   # ----------  Signature Requirements  ---------------
   //Check if exists on the exported policy
   if(array_key_exists("signature-requirements", $json_data["policy"]))
   {
      $signature_requirements = "var signature_requirements = " . json_encode($json_data["policy"]["signature-requirements"])  . " ;";
      $signature_requirements_display = False;
   }	
   else
   {
      $signature_requirements = "var signature_requirements = [] ;";
      $signature_requirements_display = True;
   }
   //Check if exists on the original policy
   if(array_key_exists("signature-requirements", $json_data_original["policy"]))
      $signature_requirements_original = "var signature_requirements_original = " . json_encode($json_data_original["policy"]["signature-requirements"])  . " ;";
   else
      $signature_requirements_original = "var signature_requirements_original = [] ;";
   

   # ----------  File Types  ---------------
   //Check if exists on the exported policy
	if(array_key_exists("filetypes", $json_data["policy"]))
      $file_types = "var file_types = " . json_encode($json_data["policy"]["filetypes"])  . " ;";
   else
      $file_types = "var file_types = [] ;";
   //Check if exists on the original policy
	if(array_key_exists("filetypes", $json_data_original["policy"]))
      $file_types_original = "var file_types_original = " . json_encode($json_data_original["policy"]["filetypes"])  . " ;";
   else
      $file_types_original = "var file_types_original = [] ;";

      
     # ----------  Parameteres  ---------------
   //Check if exists on the exported policy
	if(array_key_exists("parameters", $json_data["policy"]))
      $parameters = "var parameters = " . json_encode($json_data["policy"]["parameters"]) . " ;";
   else
      $parameters = "var parameters = [] ;";
   //Check if exists on the original policy
	if(array_key_exists("parameters", $json_data_original["policy"]))
      $parameters_original = "var parameters_original = " . json_encode($json_data_original["policy"]["parameters"]) . " ;";
   else
      $parameters_original = "var parameters_original = [] ;";


     # ----------  Sensitive Parameteres  ---------------
   //Check if exists on the exported policy
	if(array_key_exists("sensitive-parameters", $json_data["policy"]))
      $sensitive_param = "var sensitive_param = " . json_encode($json_data["policy"]["sensitive-parameters"])  . " ;";
   else
      $sensitive_param = "var sensitive_param = [] ;";
   //Check if exists on the original policy
	if(array_key_exists("sensitive-parameters", $json_data_original["policy"]))
      $sensitive_param_original = "var sensitive_param_original = " . json_encode($json_data_original["policy"]["sensitive-parameters"])  . " ;";
   else
      $sensitive_param_original = "var sensitive_param_original = [] ;";


     # ----------  URLS  ---------------
   //Check if exists on the exported policy
	if(array_key_exists("urls", $json_data["policy"]))
      $url = "var url = " . json_encode($json_data["policy"]["urls"]). " ;";
   else
      $url = "var url = [] ;";
   //Check if exists on the original policy
	if(array_key_exists("urls", $json_data_original["policy"]))
      $url_original = "var url_original = " . json_encode($json_data_original["policy"]["urls"]). " ;";
   else
      $url_original = "var url_original = [] ;";


     # ----------  CSRF  ---------------
   //Check if exists on the exported policy
	if(array_key_exists("csrf-urls", $json_data["policy"]))
      $csrf = "var csrf = " . json_encode($json_data["policy"]["csrf-urls"])  . " ;";
   else
      $csrf = "var csrf = [] ;";
   //Check if exists on the original policy
	if(array_key_exists("csrf-urls", $json_data_original["policy"]))
      $csrf_original = "var csrf_original = " . json_encode($json_data_original["policy"]["csrf-urls"])  . " ;";
   else
      $csrf_original = "var csrf_original = [] ;";


     # ----------  Cookies  ---------------
   //Check if exists on the exported policy
	if(array_key_exists("cookies", $json_data["policy"]))
      $cookies = "var cookies = " . json_encode($json_data["policy"]["cookies"])  . " ;";
   else
      $cookies = "var cookies = [] ;";
   //Check if exists on the original policy
	if(array_key_exists("cookies", $json_data_original["policy"]))
      $cookies_original = "var cookies_original = " . json_encode($json_data_original["policy"]["cookies"])  . " ;";
   else
      $cookies_original = "var cookies_original = [] ;";


     # ----------  Headers  ---------------
   //Check if exists on the exported policy
	if(array_key_exists("headers", $json_data["policy"]))
      $headers = "var headers = " . json_encode($json_data["policy"]["headers"])  . " ;";
   else
      $headers = "var headers = [] ;";
   //Check if exists on the original policy
	if(array_key_exists("headers", $json_data_original["policy"]))
      $headers_original = "var headers_original = " . json_encode($json_data_original["policy"]["headers"])  . " ;";
   else
      $headers_original = "var headers_original = [] ;";


   # ----------  JSON Profiles  ---------------
   //Check if exists on the exported policy
	if(array_key_exists("json-profiles", $json_data["policy"]))
      $json_profiles = "var json_profiles = " . json_encode($json_data["policy"]["json-profiles"])  . " ;";
   else
      $json_profiles = "var json_profiles = [] ;";
   //Check if exists on the original policy
	if(array_key_exists("json-profiles", $json_data_original["policy"]))
      $json_profiles_original = "var json_profiles_original = " . json_encode($json_data_original["policy"]["json-profiles"])  . " ;";
   else
      $json_profiles_original = "var json_profiles_original = [] ;";


   # ----------  XML Profiles  ---------------
   //Check if exists on the exported policy
	if(array_key_exists("xml-profiles", $json_data["policy"]))
      $xml_profiles = "var xml_profiles = " . json_encode($json_data["policy"]["xml-profiles"])  . " ;";
   else
      $xml_profiles = "var xml_profiles = [] ;";
   //Check if exists on the original policy
	if(array_key_exists("json-profiles", $json_data_original["policy"]))
      $xml_profiles_original = "var xml_profiles_original = " . json_encode($json_data_original["policy"]["xml-profiles"])  . " ;";
   else
      $xml_profiles_original = "var xml_profiles_original = [] ;";


   # ----------  JSON Validation Files  ---------------
   //Check if exists on the exported policy
   if(array_key_exists("json-validation-files", $json_data["policy"]))
	   $json_validation_files = "var json_validation_files = " . json_encode($json_data["policy"]["json-validation-files"])  . " ;";
	else
		$json_validation_files = "var json_validation_files = [] ;";
   //Check if exists on the original policy
   if(array_key_exists("json-validation-files", $json_data_original["policy"]))
      $json_validation_files_original = "var json_validation_files_original = " . json_encode($json_data_original["policy"]["json-validation-files"])  . " ;";
   else
      $json_validation_files_original = "var json_validation_files_original = [] ;";


   # ----------  Bot Browsers  ---------------
   //Check if exists on the exported policy

   if(array_key_exists("browsers", $json_data["policy"]["bot-defense"]["mitigations"]))
      $bot_defense_browsers = "var bot_defense_browsers = " . json_encode($json_data["policy"]["bot-defense"]["mitigations"]["browsers"])  . " ;";
   else
      $bot_defense_browsers = "var bot_defense_browsers = [] ;";	
   //Check if exists on the original policy
   if(array_key_exists("browsers", $json_data_original["policy"]["bot-defense"]["mitigations"]))
      $bot_defense_browsers_original = "var bot_defense_browsers_original = " . json_encode($json_data_original["policy"]["bot-defense"]["mitigations"]["browsers"])  . " ;";
   else
      $bot_defense_browsers_original = "var bot_defense_browsers_original = [] ;";	

   # ----------  Bot Anomalies  ---------------
   //Check if exists on the exported policy
   if(array_key_exists("anomalies", $json_data["policy"]["bot-defense"]["mitigations"]))
      $bot_defense_anomalies = "var bot_defense_anomalies = " . json_encode($json_data["policy"]["bot-defense"]["mitigations"]["anomalies"])  . " ;";
   else
      $bot_defense_anomalies = "var bot_defense_anomalies = [] ;";	
   //Check if exists on the original policy
   if(array_key_exists("anomalies", $json_data_original["policy"]["bot-defense"]["mitigations"]))
      $bot_defense_anomalies_original = "var bot_defense_anomalies_original = " . json_encode($json_data_original["policy"]["bot-defense"]["mitigations"]["anomalies"])  . " ;";
   else
      $bot_defense_anomalies_original = "var bot_defense_anomalies_original = [] ;";


   # ----------  Bot Signatures  ---------------
   //Check if exists on the exported policy
   if(array_key_exists("signatures", $json_data["policy"]["bot-defense"]["mitigations"]))
      $bot_defense_signatures = "var bot_defense_signatures = " . json_encode($json_data["policy"]["bot-defense"]["mitigations"]["signatures"])  . " ;";
   else
      $bot_defense_signatures = "var bot_defense_signatures = [] ;";	  
   //Check if exists on the original policy
   if(array_key_exists("signatures", $json_data["policy"]["bot-defense"]["mitigations"]))
      $bot_defense_signatures_original = "var bot_defense_signatures_original = " . json_encode($json_data_original["policy"]["bot-defense"]["mitigations"]["signatures"])  . " ;";
   else
      $bot_defense_signatures_original = "var bot_defense_signatures_original = [] ;";	  


   # ----------  Bot Classes  ---------------
   //Check if exists on the exported policy
   if(array_key_exists("classes", $json_data["policy"]["bot-defense"]["mitigations"]))
      $bot_defense_classes = "var bot_defense_classes = " . json_encode($json_data["policy"]["bot-defense"]["mitigations"]["classes"])  . " ;";
   else
      $bot_defense_classes = "var bot_defense_classes = [] ;";	  
   //Check if exists on the original policy
   if(array_key_exists("classes", $json_data_original["policy"]["bot-defense"]["mitigations"]))
      $bot_defense_classes_original = "var bot_defense_classes_original = " . json_encode($json_data_original["policy"]["bot-defense"]["mitigations"]["classes"])  . " ;";
   else
      $bot_defense_classes_original = "var bot_defense_classes_original = [] ;";	  


   # ----------  Methods  ---------------
   //Check if exists on the exported policy
	if(array_key_exists("methods", $json_data["policy"]))
      $methods = "var methods = " . json_encode($json_data["policy"]["methods"])  . " ;";
   else
      $methods = "var methods = [] ;";
   //Check if exists on the original policy
	if(array_key_exists("methods", $json_data_original["policy"]))
      $methods_original = "var methods_original = " . json_encode($json_data_original["policy"]["methods"])  . " ;";
   else
      $methods_original = "var methods_original = [] ;";


   # ----------  Response Poges  ---------------
   //Check if exists on the exported policy
	if(array_key_exists("response-pages", $json_data["policy"]))
      $response_pages = "var response_pages = " . json_encode($json_data["policy"]["response-pages"])  . " ;";	
   else
      $response_pages = "var response_pages = [] ;";
   //Check if exists on the original policy
	if(array_key_exists("response-pages", $json_data_original["policy"]))
      $response_pages_original = "var response_pages_original = " . json_encode($json_data_original["policy"]["response-pages"])  . " ;";
   else
      $response_pages_original = "var response_pages_original = [] ;";


   # ----------  Whitelists  ---------------
   //Check if exists on the exported policy
   if(array_key_exists("whitelist-ips", $json_data["policy"]))
      $whitelist_ips = "var whitelist_ips = " . json_encode($json_data["policy"]["whitelist-ips"])  . " ;";
   else
      $whitelist_ips = "var whitelist_ips = [] ;";
   //Check if exists on the original policy
   if(array_key_exists("whitelist-ips", $json_data_original["policy"]))
      $whitelist_ips_original = "var whitelist_ips_original = " . json_encode($json_data_original["policy"]["whitelist-ips"])  . " ;";
   else
      $whitelist_ips_original = "var whitelist_ips_original = [] ;";	


   # ----------  Allowed ResponseCodes  ---------------
   //Check if exists on the exported policy
   if(array_key_exists("allowedResponseCodes", $json_data["policy"]["general"]))
      $allowedResponseCodes = "var allowedResponseCodes = " . json_encode($json_data["policy"]["general"]["allowedResponseCodes"])  . " ;";
   else
      $allowedResponseCodes = "var allowedResponseCodes = [] ;";
   //Check if exists on the original policy
   if(array_key_exists("allowedResponseCodes", $json_data_original["policy"]["general"]))
      $allowedResponseCodes_original = "var allowedResponseCodes_original = " . json_encode($json_data_original["policy"]["general"]["allowedResponseCodes"])  . " ;";
   else
      $allowedResponseCodes_original = "var allowedResponseCodes_original = [] ;";


   // Adding name Key so that it can be displayed with Datatables 
   $string = json_encode($json_data["policy"]["general"]["allowedResponseCodes"]); 
   $string = str_replace('[','[{"name":"',$string);
   $string = str_replace(',','"}, {"name":"',$string);
   $string = str_replace(']','"}]', $string);
   $allowed_response_codes_table = "var allowed_response_codes_table = " . $string . " ;";
   

   # ----------  DATA GUARD  ---------------
   //Check if exists on the exported policy
   if(array_key_exists("data-guard", $json_data["policy"]))
      $dataguard = "var dataguard = " . json_encode($json_data["policy"]["data-guard"])  . " ;";
   else
      $dataguard = "var dataguard = [] ;";
   //Check if exists on the original policy
   if(array_key_exists("data-guard", $json_data_original["policy"]))
      $dataguard_original = "var dataguard_original = " . json_encode($json_data_original["policy"]["data-guard"])  . " ;";
   else
      $dataguard_original = "var dataguard_original = [] ;";


     


   ###  Spefic Attributes


   //Dataguard Enabled
	if($json_data["policy"]["data-guard"]["enabled"])
      $dataguard_enabled = '<span class="green">Enabled</span>';
   else
      $dataguard_enabled = '<span class="red">Disabled</span>';


   //Dataguard maskCreditCardNumbersInRequest
   if(array_key_exists("maskCreditCardNumbersInRequest", $json_data["policy"]["general"]))
	{ 
      if($json_data["policy"]["general"]["maskCreditCardNumbersInRequest"])
      {
         $maskCreditCardNumbersInRequest_html = "<i class='fa fa-check-square-o fa-2x green'></i>";
         $maskCreditCardNumbersInRequest= "enabled";
      }
      else
      {
         $maskCreditCardNumbersInRequest_html = "<i class='fa fa-minus-square-o fa-2x red' ></i>";
         $maskCreditCardNumbersInRequest= "disabled";
      }
   }
   else
   {
      $maskCreditCardNumbersInRequest_html = "N/A";	
      $maskCreditCardNumbersInRequest = "disabled";	
   } 
	  
   //Dataguard maskData
	if(array_key_exists("maskData", $json_data["policy"]["data-guard"]))
	{
      if($json_data["policy"]["data-guard"]["maskData"])
			$maskData = "<i class='fa fa-check-square-o fa-2x green'></i>";
 		else
			$maskData = "<i class='fa fa-minus-square-o fa-2x red' ></i>";
   }
   else
    	$maskData = "N/A";	  
	
   //Dataguard usSocialSecurityNumbers
	if(array_key_exists("usSocialSecurityNumbers", $json_data["policy"]["data-guard"]))
	{
      if($json_data["policy"]["data-guard"]["usSocialSecurityNumbers"])
			$usSocialSecurityNumbers = "<i class='fa fa-check-square-o fa-2x green'></i>";
 		else
			$usSocialSecurityNumbers = "<i class='fa fa-minus-square-o fa-2x red' ></i>";
	}
   else
    	$usSocialSecurityNumbers = "N/A";	 	  

	//Dataguard creditCardNumbers
	if(array_key_exists("creditCardNumbers", $json_data["policy"]["data-guard"]))
   {
		if($json_data["policy"]["data-guard"]["creditCardNumbers"])
			$creditCardNumbers = "<i class='fa fa-check-square-o fa-2x green'></i>";
 		else
			$creditCardNumbers = "<i class='fa fa-minus-square-o fa-2x red' ></i>";
   }
   else
    	$creditCardNumbers = "N/A";	 	  

   //Dataguard enforcementUrls
	if(array_key_exists("enforcementUrls", $json_data["policy"]["data-guard"]))
		if( sizeof($json_data["policy"]["data-guard"]["enforcementUrls"])==0)
			$enforcementUrls = "List Empty";
		else
			$enforcementUrls = implode($json_data["policy"]["data-guard"]["enforcementUrls"], "<br>");
	else
		$enforcementUrls = "Not Configured";	



   //enforcementMode
   if($json_data["policy"]["enforcementMode"]=="blocking")
   {
      $enforcement_mode = "blocking";
      $enforcement_mode_html = "<span class='green'>Blocking</span>";
   }
   else
   {
      $enforcement_mode = "transparent";
      $enforcement_mode_html = "<span class='red'>Transparent</span>";
   }

   //csrf-protection
   if($json_data["policy"]["csrf-protection"]["enabled"])
   {
      $csrf_protection_html = "<i class='fa fa-check-square-o fa-2x green'></i>";
      $csrf_protection = "enabled";
   }
   else
   {
      $csrf_protection_html = "<i class='fa fa-minus-square-o fa-2x red' ></i>";
      $csrf_protection = "disabled";
   }


      
   //Bot Protection
   if($json_data["policy"]["bot-defense"]["settings"]["isEnabled"])
   {
      $bot_protection_html = "<span class='green'>Enabled</span>";
      $bot_protection = "enabled";
   }
   else
   {
      $bot_protection_html = "<span class='red'>Disabled</span>";
      $bot_protection = "disabled";
   }
   //caseSensitiveHttpHeaders
   if($json_data["policy"]["bot-defense"]["settings"]["caseSensitiveHttpHeaders"])
   {
      $botcasesensitive_html = "<i class='fa fa-check-square-o fa-2x green'></i>";
      $botcasesensitive = "enabled";      
   }
   else
   {
      $botcasesensitive_html = "<i class='fa fa-minus-square-o fa-2x red' ></i>";
      $botcasesensitive = "disabled";
   }
   
   //enforcer-settings/httpOnlyAttribute
   if($json_data["policy"]["enforcer-settings"]["enforcerStateCookies"]["httpOnlyAttribute"])
      $httpOnlyAttribute = "<i class='fa fa-check-square-o fa-2x green'></i>";
   else
      $httpOnlyAttribute = "<i class='fa fa-minus-square-o fa-2x red' ></i>";

   
   //minimumAccuracyForAutoAddedSignatures
	$minimumAccuracyForAutoAddedSignatures = $json_data["policy"]["signature-settings"]["minimumAccuracyForAutoAddedSignatures"];

   //caseInsensitive
   if($json_data["policy"]["caseInsensitive"])
   {
      $caseInsensitive = "<i class='fa fa-check-square-o fa-2x green'></i>";
      $caseInsensitive = "enabled";
   }
   else
   {
      $caseInsensitive = "disabled";
      $caseInsensitive_html = "<i class='fa fa-minus-square-o fa-2x red' ></i>";
   }

   //trustXff
   if($json_data["policy"]["general"]["trustXff"])
   {
      $trustXff_form = "<i class='fa fa-check-square-o fa-2x green'></i>";
      $trustXff = "enabled";
   }
   else
   {
      $trustXff_form = "<i class='fa fa-minus-square-o fa-2x red' ></i>";
      $trustXff = "disabled";
   }
   //customXffHeaders
	if(array_key_exists("customXffHeaders", $json_data["policy"]["general"]))
      $customXffHeaders = implode(",", $json_data["policy"]["general"]["customXffHeaders"]);
   else
      $customXffHeaders = "Not Configured";  

   $maximumCookieHeaderLength = $json_data["policy"]["cookie-settings"]["maximumCookieHeaderLength"];
   $maximumHttpHeaderLength = $json_data["policy"]["header-settings"]["maximumHttpHeaderLength"];
   $description = $json_data["policy"]["description"];

?>

<!doctype html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="description" content="">
      <meta name="author" content="Kostas Skenderidis - skenderidis@gmail.com">
      <title>NAP Policy Editor</title>
      <link href="css/dataTables.bootstrap5.min.css" rel="stylesheet">
      <link  href="css/font-awesome.min.css" rel="stylesheet">

      <!-- Bootstrap core CSS -->
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link href="css/flags16.css" rel="stylesheet">
      <link href="css/flags32.css" rel="stylesheet">

      <!-- Custom styles for this template -->
      <link href="dashboard.css" rel="stylesheet">

   </head>
   <body>
      <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
         <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#"><img src="images/app-protect.svg" width=32/> &nbsp; NGINX App Protect</a>
         <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
         <span class="navbar-toggler-icon"></span>
         </button>
         <div class="navbar-nav">
            <div class="nav-item text-nowrap">
               <a class="nav-link px-3" href="logout.php">Sign out</a>
            </div>
         </div>
      </header>
      <div class="container-fluid">
         <div class="row">
            <nav id="sidebarMenu" class="col-md-1 col-lg-1 d-md-block bg-light sidebar collapse">
               <div class="position-sticky pt-3">
                  <ul class="nav flex-column">
                     <li class="nav-item" >
                        <a class="nav-link" href="violation.php">
                        <span data-feather="file"></span>
                        Violations
                        </a>
                     </li>
							<li class="nav-item" style="background-color:#d2d8dc">
                        <a class="nav-link active" aria-current="page" href="policies.php">
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
               <div class="row align-items-center">
                  <div class="title"> NAP Policy: <b><?php echo $_POST['policy']; ?> </b>
                     
                     <button class="btn btn-primary btn-sync" data-bs-toggle="modal" data-bs-target="#syncModal" style="float:right" <?php if (!file_exists("config_files/".$_POST["policy"]."/sync")) echo "hidden"; ?> >Sync <i class="fa fa-refresh"></i> </button>
                  </div>
               </div>

               <div class="row">

                  <div class="d-flex align-items-start">
                    <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#taboverview" type="button" role="tab" aria-controls="taboverview" aria-selected="true">Overview</button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabevasion" type="button" role="tab" aria-controls="tabevasion" aria-selected="false">Settings</button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabsignatures" type="button" role="tab" aria-controls="tabsignatures" aria-selected="false">Signatures</button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabfiletypes" type="button" role="tab" aria-controls="tabfiletypes" aria-selected="false">File Types</button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabparameters" type="button" role="tab" aria-controls="tabparameters" aria-selected="false">Parameters</button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#taburls" type="button" role="tab" aria-controls="taburls" aria-selected="false">URLs</button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabcookies" type="button" role="tab" aria-controls="tabcookies" aria-selected="false">Headers</button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabprofiles" type="button" role="tab" aria-controls="tabprofiles" aria-selected="false">Profiles</button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabbotdefense" type="button" role="tab" aria-controls="tabbotdefense" aria-selected="false">Bot</button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabmethods" type="button" role="tab" aria-controls="tabmethods" aria-selected="false">Others</button>
                    </div>
                    <div class="tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade show active" id="taboverview" role="tabpanel" aria-labelledby="taboverview-tab">
                           
                        	<div class="row">

                           		<div class="col-5">
                                	<div class="panel">
                                    	<div class="title"> General Settings </div>
                                    	<div class="line"></div>
                                    	<div class="content">
                                          <table id="general" class="table table-striped table-bordered" style="width:100%">
                                             <thead>
                                                   <tr>
                                                   <th>Settings</th>
                                                   <th>Value</th>
                                                   <th style="width: 15px; text-align: center;"> Edit </th>
                                                   </tr>
                                             </thead>
                                             <tbody>
                                                <tr>
                                                   <td>Enforcement Mode</td>
                                                   <td> <b><?php echo $enforcement_mode_html; ?><b></td>
                                                   <td class="edit_button" id="enforcement_mode" value="<?php echo $enforcement_mode; ?>"><i class="fa fa-edit"></i></td>
                                                </tr>
                                                <tr>
                                                   <td>Bot Protection</td>
                                                   <td><b><?php echo $bot_protection_html ?></b></td>
                                                   <td class="edit_button" id="bot_protection" value="<?php echo $bot_protection; ?>"><i class="fa fa-edit"></i></td>
                                                </tr>
                                                <tr>
                                                   <td>CSRF Protection</td>
                                                   <td><b><?php echo $csrf_protection_html ?></b></td>
                                                   <td class="edit_button" id="csrf_protection" value="<?php echo $csrf_protection; ?>"><i class="fa fa-edit"></i></td>
                                                </tr>                                                                                        
                                                <tr>
                                                   <td>Template</td>
                                                   <td><?php echo $json_data["policy"]["template"]["name"]; ?></td>
                                                   <td class="disabled_button"><i class="fa fa-edit"></i></td>
                                                </tr>
                                                <tr>
                                                   <td>Description</td>
                                                   <td><?php echo $description; ?></td>
                                                   <td class="edit_button" id="description" value="<?php echo $description; ?>"><i class="fa fa-edit"></i></td>
                                                </tr>
                                                <tr>
                                                   <td>Max Cookie Length</td>
                                                   <td><?php echo $maximumCookieHeaderLength; ?></td>
                                                   <td class="edit_button" id="cookie_length" value="<?php echo $maximumCookieHeaderLength; ?>"><i class="fa fa-edit" ></i></td>
                                                </tr>
                                                <tr>
                                                   <td>Max Header Length</td>
                                                   <td><?php echo $maximumHttpHeaderLength; ?></td>
                                                   <td class="edit_button" id="header_length" value="<?php echo $maximumHttpHeaderLength; ?>"><i class="fa fa-edit"></i></td>
                                                </tr>
                                                <tr>
                                                   <td>Trust XFF</td>
                                                   <td><?php echo $trustXff_form; ?></td>
                                                   <td class="edit_button" id="xff" value="<?php echo $trustXff; ?>"><i class="fa fa-edit"></i></td>
                                                </tr>
                                                <tr>
                                                   <td>XFF Headers</td>
                                                   <td><?php echo $customXffHeaders; ?></td>
                                                   <td class="edit_button" id="xff_headers"  value="<?php echo $customXffHeaders; ?>"><i class="fa fa-edit"></i></td>
                                                </tr>
                                                <tr>
                                                   <td>Mask Credit Card</td>
                                                   <td><?php echo $maskCreditCardNumbersInRequest_html; ?></td>
                                                   <td class="edit_button" id="mask_credit_card"  value="<?php echo $maskCreditCardNumbersInRequest; ?>"><i class="fa fa-edit"></i></td>
                                                </tr>
                                                <tr>
                                                   <td>Case Insensitive</td>
                                                   <td><?php echo $caseInsensitive_html ?></td>
                                                   <td class="edit_button" id="case_insensitive"  value="<?php echo $caseInsensitive; ?>"><i class="fa fa-edit"></i></td>
                                                </tr>
                                                <tr>
                                                   <td>Bot Case Sensitive Headers</td>
                                                   <td><?php echo $botcasesensitive_html ?></td>
                                                   <td class="edit_button" id="botcasesensitive"  value="<?php echo $botcasesensitive; ?>"><i class="fa fa-edit"></i></td>
                                                </tr>                                                
                                             </tbody>
                                          </table>
                                    	</div>
                                 	</div>
                              	</div>

                            	<div class="col-7">
                                 	<div class="panel">
                                    	<div class="title"> Violations Settings
                                          <div class="btn-group" style="float:right">
                                             <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="blocking_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                          </div>
                                       </div>
                                    	<div class="line"></div>
                                    	<div class="content">
                                       		<table id="violations" class="table table-striped table-bordered" style="width:100%">
                                          		<thead>
                                                   <tr>
                                                      <th>Name</th>
                                                      <th>Decription</th>
                                                      <th style="width: 45px; text-align: center;">Alarm</th>
                                                      <th style="width: 45px; text-align: center;">Block</th>
                                                      <th style="width: 15px; text-align: center;">Edit</th>
                                                   </tr>
                                          		</thead>
                                       		</table>
                                    	</div>
                                 	</div>
                              	</div>

                            	<div class="col-3" hidden>
                                 	<div class="panel">
                                    	<div class="title"> Blocking Settings </div>
                                    	<div class="line"></div>
                                    	<div class="content">
		 												 <?php echo '<pre>' . json_encode($json_data["policy"]["urls"], JSON_PRETTY_PRINT) . '</pre>'; ?>
                                    	</div>
                                 	</div>
                              	</div>

								  
								

                              
                           </div>

                        </div>
                        <div class="tab-pane fade" id="tabevasion" role="tabpanel" aria-labelledby="tabevasion-tab">
                           
                           <div class="row">

                              <div class="col-5">
                                 <div class="panel">
                                    <div class="title"> HTTP Compliance
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="compliance_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="compliance" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                             <tr>
                                                <th>HTTP Protocol Compliance</th>
                                                <th style="width:60px; text-align: center;">Enabled</th>
                                                <th style="width:35px; text-align: center;">Edit</th>
                                             </tr>
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>

                              <div class="col-4">
                                 <div class="panel">
                                    <div class="title"> Evasions 
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="evasion_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="evasion" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                             <tr>
                                                <th>Evasion Technique Name</th>
                                                <th style="width:60px; text-align: center;">Enabled</th>
                                                <th style="width:35px; text-align: center;">Edit</th>
                                             </tr>
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>

                              <div class="col-3">
                                 <div class="panel">
                                       <div class="title"> Dataguard 
                                          <div class="btn-group" style="float:right">
                                             <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="dataguard_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                          </div>                                          
                                       </div>
                                       <div class="line"></div>
                                       <div class="content">
                                          <table id="dataguard" class="table table-striped table-bordered" style="width:100%">
                                             <thead>
                                                   <tr>
                                                   <th>Settings</th>
                                                   <th>Value</th>
                                                   </tr>
                                             </thead>
                                             <tbody>
                                                   <tr>
                                                   <td>Status</td>
                                                   <td> <b><?php echo $dataguard_enabled; ?><b></td>
                                                   </tr>
                                                   <tr>
                                                   <td>Enforcement Mode</td>
                                                   <td><?php echo $json_data["policy"]["data-guard"]["enforcementMode"]; ?></td>
                                                   </tr>
                                                   <tr>
                                                   <td>maskData</td>
                                                   <td><?php echo $maskData; ?></td>
                                                   </tr>                                            
                                                   <tr>
                                                   <td>usSocialSecurityNumbers</td>
                                                   <td><?php echo $usSocialSecurityNumbers; ?></td>
                                                   </tr>
                                                   <tr>
                                                   <td>creditCardNumbers</td>
                                                   <td><?php echo $creditCardNumbers; ?></td>
                                                   </tr>
                                                   <tr>
                                                   <td>enforcementUrls</td>
                                                   <td><?php echo $enforcementUrls; ?></td>
                                                   </tr>
                                             </tbody>
                                          </table>
                                       </div>
                                    </div>
                                 </div>                              
							  
                           </div>

                        </div>
                        <div class="tab-pane fade" id="tabsignatures" role="tabpanel" aria-labelledby="tabsignatures-tab">
   
                           <p class="title"> Accuracy for auto added signatures: <span class="green"><b><?php echo $minimumAccuracyForAutoAddedSignatures; ?></b></span></p>

                           <div class="row">
                              <div class="col-6">
                                 <div class="panel">
                                    <div class="title"> Signature Sets 
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="signature_sets_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="signature_sets" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                             <tr>
                                             <th style="width: 15px; text-align: center;"></th>
                                             <th>Signature Sets</th>
                                             <th style="width: 25px; text-align: center;">Alarm</th>
                                             <th style="width: 30px; text-align: center;">Block</th>
                                             <th style="width: 60px; text-align: center;">Type</th>
                                             <th colspan=2 style="width: 35px; text-align: center;">Edit</th>
                                             </tr>
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>


                              <div class="col-6">

                                 <div class="panel">
                                    <div class="title"> Threat Campaigns
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="tc_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                    <div class="alert alert-primary d-flex align-items-center" role="alert">
                                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                                          <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                       </svg>
                                       <div>
                                          By default <b>All Threat Campaigns</b> are enabled.
                                       </div>
                                       </div>
                                       <table id="threat_campaigns" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                             <tr>
                                                <th>Name</th>
                                                <th style="width:60px; text-align: center;">Enabled</th>
                                                <th style="width:35px; text-align: center;">Edit</th>
                                             </tr>
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              

                                 <div class="panel">
                                       <div class="title">Individual Signatures  
                                          <div class="btn-group" style="float:right">
                                             <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="signatures_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                          </div>
                                       </div>
                                       <div class="line"></div>
                                       <div class="content">
                                          <table id="signatures" class="table table-striped table-bordered" style="width:100%">
                                             <thead>
                                                <tr>
                                                   <th style="width: 45px; text-align: center;">Enabled</th>
                                                   <th>Signature ID</th>
                                                   <th>Signature Name</th>
                                                   <th>Tag</th>
                                                   <th style="width: 35px; text-align: center;">Edit</th>
                                                </tr>
                                             </thead>
                                          </table>
                                    </div>
                                 </div>

                                 <div class="panel">
                                       <div class="title">Server Technologies
                                          <div class="btn-group" style="float:right">
                                             <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="server_technologies_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                          </div>
                                       </div>
                                       <div class="line"></div>
                                       <div class="content">
                                          <table id="server_technologies" class="table table-striped table-bordered" style="width:100%">
                                             <thead>
                                                <tr>
                                                   <th>Server Technology Name</th>
                                                </tr>
                                             </thead>
                                          </table>
                                    </div>
                                 </div>


                                 <div class="panel <?php if($signature_requirements_display) echo 'display_none';  ?>">
                                    <div class="title"> Signature Requirements </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="signature_requirements" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                             <tr>
                                             <th>Tag</th>
                                             <th style="text-align: center;">Max Revision Date</th>
                                             <th style="text-align: center;">Min Revision Date</th>
                                             </tr>
                                          </thead>
                                       </table>
                                    </div>
                                 </div>


                              </div>
                        

                            </div>

                        </div>
                        <div class="tab-pane fade" id="tabfiletypes" role="tabpanel" aria-labelledby="tabfiletypes-tab">
                           
                           <div class="row">
                              <div class="col-12">
                                 <div class="panel">
                                    <div class="title"> File Types Allowed 
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="file_types_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                    	<table id="file_type" class="table table-striped table-bordered" style="width:100%">
                                        	<thead>
                                             <tr>
                                                <th rowspan="2" style="width:100%">File Type</th>
                                                <th rowspan="2" style="width:60px; text-align:center;">Type</th>
                                                <th rowspan="2" style="width:60px; text-align:center;">Allowed </th>
                                                <th colspan="4" style="width:70px; text-align:center;">Enable Check <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Allowed URI Length for each File Type"></i></th>
                                                <th colspan="4" style="width:70px; text-align:center;">Configure Length <i class="fa fa-info-circle" data-toggle="tooltip" data-original-title="Allowed URI Length for each File Type"></i></th>
                                                <th rowspan="2" style="width:110px; text-align:center;">Responses </th>
                                                <th rowspan="2"  colspan="2" style="width:15px; text-align: center;">Edit</th>
                                             </tr>
                                             <tr>
                                                <th style="width:50px; text-align:center;">URI</th>
                                                <th style="width:50px; text-align:center;">Query </th>
                                                <th style="width:50px; text-align:center;">Post </th>									
                                                <th style="width:50px; text-align:center;">Request </th>
                                                <th style="width:70px; text-align:center;">URI </th>
                                                <th style="width:70px; text-align:center;">Query </th>
                                                <th style="width:70px; text-align:center;">Post </th>
                                                <th style="width:70px; text-align:center;">Request </th>
                                             </tr>
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>
                 
                           </div>

                        </div>
                        <div class="tab-pane fade" id="tabparameters" role="tabpanel" aria-labelledby="tabparameters-tab">

                           <div class="row">
                              <div class="col-12">
                                 <div class="panel">
                                    <div class="title"> Parameters 
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="parameters_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>																
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="parameters" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                          <tr>
                                             <th rowspan=2 style="width: 15px; text-align: center;"></th>
                                             <th rowspan=2>Parameter Name</th>
                                             <th rowspan=2 style="width:55px; text-align:center;">Type</th>
                                             <th rowspan=2 style="width:70px; text-align:center;" data-toggle="tooltip" data-original-title="Is the parameter conifgured as Sensitive">Sensitive </th>
                                             <th colspan=3 style="width:70px; text-align:center;">Enable Check</th>
                                             <th colspan=3 style="width:70px; text-align:center;">Overrides</th>
                                             <th rowspan=2 colspan=2 style="text-align: center;">Edit</th>
                                          </tr>
                                          <tr>
                                             <th style="width:70px; text-align:center;" data-toggle="tooltip" data-original-title="If Attack Signatures have been enabled">Signatures</th>
                                             <th style="width:70px; text-align:center;" data-toggle="tooltip" data-original-title="If checking on Meta-characters has been enabled">MetaChar Value</th>
                                             <th style="width:70px; text-align:center;" data-toggle="tooltip" data-original-title="If checking on Meta-characters has been enabled">MetaChar Name</th>
                                             <th style="width:70px; text-align:center;" data-toggle="tooltip" data-original-title="How many Attack Signatures have been overriden">Signatures</th>
                                             <th style="width:70px; text-align:center;" data-toggle="tooltip" data-original-title="How many Meta-characters have been overriden">MetaChar Value</th>
                                             <th style="width:70px; text-align:center;" data-toggle="tooltip" data-original-title="How many Meta-characters have been overriden">MetaChar Name</th>
                                          </tr>                                          
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>
                                 
                              <div class="col-6">
                                 <div class="panel">
                                    <div class="title"> Sensitive Parameters 
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="sensitive_param_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="sensitive_param" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                             <tr>
                                                <th>Parameter Name</th>
                                                <th style="width:35px; text-align: center;">Edit</th>
                                             </tr>
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>

                           </div>

                        </div>
                        <div class="tab-pane fade" id="taburls" role="tabpanel" aria-labelledby="taburls-tab">
                           <div class="row">
                              <div class="col-12">
                                 <div class="panel">
                                    <div class="title"> URLs 
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="url_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="urls" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                             <tr>
                                                <th rowspan=2 style="width:10px;"></th>
                                                <th rowspan=2 style="width:50px; text-align:center;" data-toggle="tooltip" data-original-title="HTTP Protocol used (HTTP/HTTPS)">Proto</th>
                                                <th rowspan=2 style="width:50px; text-align:center;" data-toggle="tooltip" data-original-title="Allowed Methods">Method</th>
                                                <th rowspan=2>URL</th>
                                                <th rowspan=2 style="width:50px; text-align:center;" data-toggle="tooltip" data-original-title="Is the URL allowed?">Allowed</th>
                                                <th colspan=2 style="width:75px; text-align:center;" data-toggle="tooltip" data-original-title="If Attack Signatures have been enabled">Enable Check</th>
                                                <th colspan=2 style="width:90px; text-align:center;" data-toggle="tooltip" data-original-title="How many Attack Signatures have been overriden">Overrides</th>
                                                <th rowspan=2 colspan=2 style="text-align: center;">Edit</th>
                                             </tr>
                                             <tr>
                                                <th style="width:75px; text-align:center;" data-toggle="tooltip" data-original-title="If Attack Signatures have been enabled">Signatures</th>
                                                <th style="width:75px; text-align:center;" data-toggle="tooltip" data-original-title="If Meta-character Check has been enabled">Metachar</th>
                                                <th style="width:90px; text-align:center;" data-toggle="tooltip" data-original-title="How many Attack Signatures have been overriden">Signatures</th>
                                                <th style="width:90px; text-align:center;" data-toggle="tooltip" data-original-title="How many Meta-characters have been overriden">Metachar</th>
                                             </tr>                                             
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>


                              <div class="col-8">
                                 <div class="panel">
                                    <div class="title"> CSRF URLs 
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="csrf_url_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="csrf" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                             <tr>
                                                <th>URL</th>
                                                <th style="width:150px; text-align:center;">Enforcement Action</th>
                                                <th style="width:75px; text-align:center;">Method</th>
                                                <th style="width:130px; text-align:center;">Wildcard Order</th>
                                             </tr>                                             
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>
                            
                           </div>

                        </div>  
                        <div class="tab-pane fade" id="tabcookies" role="tabpanel" aria-labelledby="tabcookies-tab">

                           <div class="row">
                              <div class="col-6">
                                 <div class="panel">
                                    <div class="title"> Cookies
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="cookies_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>                                       
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="cookies" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                             <tr>
                                                <th style="width: 15px; text-align: center;"></th>
                                                <th> Name </th>
                                                <th style="width:45px; text-align:center;"> Type </th>
                                                <th style="width:90px; text-align: center;">Enforcement <i class="fa fa-info-circle" data-bs-toggle="tooltip" title="Whether the cookie is on Enforced or Allowed mode"></i></th>
                                                <th style="width:75px; text-align:center;">Signatures <i class="fa fa-info-circle" data-bs-toggle="tooltip" title="If Attack Signatures have been enabled"></i></th>
                                                <th style="width:90px; text-align:center;"> Overrides <i class="fa fa-info-circle" data-bs-toggle="tooltip" title="How many Attack Signatures have been overriden"></i> </th>
                                                <th colspan=2 style="text-align: center;"> Edit</th>
                                             </tr> 
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>

                              <div class="col-6">
                                 <div class="panel">
                                    <div class="title"> Headers
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="headers_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>                                       
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="headers" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                             <tr>
                                                <th style="width: 15px; text-align: center;"></th>
                                                <th>Name</th>
                                                <th style="width:45px; text-align:center;">Type</th>
                                                <th style="width:75px; text-align:center;" data-toggle="tooltip" data-original-title="If Attack Signatures have been enabled">Signatures</th>
                                                <th style="width:90px; text-align:center;">Overrides</th>
                                                <th colspan=2 style="text-align: center;"> Edit</th>
                                             </tr>
                                          </thead>
                                       </table>    
                                    </div>
                                 </div>
                              </div>

                              <div class="col-6">
                                 <div class="panel">
                                    <div class="title"> Enforcer Settings 
                                          <div class="btn-group" style="float:right" hidden>
                                             <button type="button" class="btn btn-sm btn-outline-secondary" id="blocking_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                          </div>                                   
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="enforcer_settings" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                                <tr>
                                                <th colspan=2>Settings</th>
                                                <th>Value</th>
                                                </tr>
                                          </thead>
                                          <tbody>
                                             <tr>
                                                <td rowspan=3 style="width:180px;vertical-align: middle;">Enforce state cookies</td>
                                                <td>httpOnlyAttribute</td>
                                                <td> <b><?php echo $httpOnlyAttribute; ?><b></td>
                                             </tr>
                                             <tr>
                                                <td>secureAttribute</td>
                                                <td> <b><?php echo $json_data["policy"]["enforcer-settings"]["enforcerStateCookies"]["secureAttribute"]; ?><b></td>
                                             </tr>
                                             <tr>
                                                <td>sameSiteAttribute</td>
                                                <td> <b><?php echo $json_data["policy"]["enforcer-settings"]["enforcerStateCookies"]["sameSiteAttribute"]; ?><b></td>
                                             </tr>
                                          </tbody>
                                       </table>
                                    </div>
                                 </div>
                              </div>                              
                              
                           </div>

                        </div>
                        <div class="tab-pane fade" id="tabmethods" role="tabpanel" aria-labelledby="tabmethods-tab">
                           
                           <div class="row">
                              <div class="col-3">
                                 <div class="panel">
                                    <div class="title"> HTTP Methods 
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="methods_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="methods" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                             <tr>
                                                <th>Allowed HTTP Methods</th>
                                                <th style="width:35px; text-align: center;">Edit</th>
                                             </tr>
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>

                              <div class="col-3">
                                 <div class="panel">
                                    <div class="title"> HTTP Response Codes 
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="response_codes_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="response_codes" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                          <tr>
                                             <th>HTTP Response Codes</th>
                                             <th style="width:35px; text-align: center;">Edit</th>
                                          </tr>
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>
                           
                              <div class="col-6">
                                 <div class="panel">
                                    <div class="title"> Response Pages 
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="response_pages_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="response_pages" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                             <tr>
                                                <th style="width: 15px; text-align: center;"></th>
                                                <th>Response Page Type</th>
                                             </tr>
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>
                                              
                           </div>

                           <div class="row">

                              <div class="col-6">
                                 <div class="panel">
                                    <div class="title">Whitelist IPs
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="whitelist_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>                                       
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="whitelist_ips" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                             <tr>
                                                <th style="width:100px;">IP</th>
                                                <th style="width:120px;">Mask</th>
                                                <th style="width:60px; text-align:center;" data-toggle="tooltip" data-original-title="Never Block this IP address">Block </th>
                                                <th style="width:60px; text-align:center;" data-toggle="tooltip" data-original-title="Never Log for this IP address">Log </th>
                                                <th style="text-align:center;">Description </th>
                                             </tr>
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>
                           </div>

                        </div>
                        <div class="tab-pane fade" id="tabbotdefense" role="tabpanel" aria-labelledby="tabbotdefense-tab">
                           
                           <div class="row">

                              <div class="col-4">
                                 <div class="panel">
                                    <div class="title">Bot Defense Classes
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="bot_classes_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>                                       
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="bot_defense_classes" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                             <tr>
                                                <th>Class Name</th>
                                                <th style="width:90px; text-align:center;">Action</th>
                                             </tr>
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>	

                              <div class="col-4">
                                 <div class="panel">
                                    <div class="title"> Bot Defense Browsers
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="bot_browsers_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="bot_defense_browsers" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                          <tr>
                                             <th rowspan=2>Name</th>
                                             <th rowspan=2>Action</th>
                                             <th colspan=2 style="text-align: center;">Version</th>
                                          </tr>
                                          <tr>
                                             <th style="width:55px; text-align: center;">Min</th>
                                             <th style="width:55px; text-align: center;">Max</th>
                                          </tr>                                          
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>


                              <div class="col-4">
                                 <div class="panel">
                                    <div class="title"> Bot Defense Signatures
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="bot_signatures_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="bot_defense_signatures" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                          <tr>
                                             <th>Signatures</th>
                                             <th style="width:90px; text-align: center;">Action</th>
                                          </tr>
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>



                              <div class="col-4">
                                 <div class="panel">
                                    <div class="title"> Bot Defense Anomalies
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="bot_anomalies_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="bot_defense_anomalies" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                          <tr>
                                             <th>Anomalies</th>
                                             <th style="width:90px; text-align: center;">Action</th>
                                             <th style="width:90px; text-align: center;">ScoreThreshold</th>
                                          </tr>
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>


                           </div>

                        </div>                        
                        <div class="tab-pane fade" id="tabprofiles" role="tabpanel" aria-labelledby="tabprofiles-tab">
                           
                           <div class="row">

                              <div class="col-8">
                                 <div class="panel">
                                    <div class="title">JSON Profiles
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="json_profile_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>                                       
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="json_profiles" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                             <tr>
                                                <th rowspan="2" style="width: 15px; text-align: center;"></th>
                                                <th rowspan="2">Name </th>
                                                <th rowspan="2">Description </th>
                                                <th colspan="2" style="width:70px; text-align:center;">Enable Check </th>
                                                <th colspan="2" style="width:70px; text-align:center;">Overrides </th>
                                                <th rowspan="2" colspan="2" style="width:35px; text-align: center;">Edit</th>
                                             </tr>
                                             <tr>
                                                <th style="width:70px; text-align:center;">Signatures </th>
                                                <th style="width:70px; text-align:center;">MetaChar </th>
                                                <th style="width:70px; text-align:center;">Signatures </th>
                                                <th style="width:70px; text-align:center;">MetaChar </th>
                                             </tr>
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>

                              <div class="col-4">
                                 <div class="panel">
                                    <div class="title">JSON Validation Files
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="json_file_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>   
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="json_validation_files" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                          <tr>
                                             <th>FileName</th>
                                             <th style="width:90px; text-align:center;">Content</th>
                                          </tr>
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>

                           </div>

                           <div class="row">

                              <div class="col-8">
                                 <div class="panel">
                                    <div class="title">XML Profiles
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="xml_profile_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>                                          
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="xml_profiles" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                             <tr>
                                                <th style="width: 15px; text-align: center;"></th>
                                                <th> Name </th>
                                                <th> Description</th>
                                                <th style="width:100px; text-align:center;">Signatures</th>
                                                <th style="width:100px; text-align:center;">Sig. Overrides </th>
                                                <th colspan="2" style="width:35px; text-align: center;">Edit</th>                                  
                                             </tr>
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>

                              <div class="col-4">
                                 <div class="panel">
                                    <div class="title">Open API Files
                                       <div class="btn-group" style="float:right">
                                          <button type="button" class="btn btn-sm btn-outline-secondary btn-json" id="open_api_json" data-bs-toggle="modal" data-bs-target="#jsonModal">Edit</button>
                                       </div>   
                                    </div>
                                    <div class="line"></div>
                                    <div class="content">
                                       <table id="open_api_files" class="table table-striped table-bordered" style="width:100%">
                                          <thead>
                                          <tr>
                                             <th>Link</th>
                                          </tr>
                                          </thead>
                                       </table>
                                    </div>
                                 </div>
                              </div>

                           </div>


                        </div>
                 
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



<!-- Scrollable modal -->
<!-- Button trigger modal -->

   <!-- Modal -->
   <div class="modal fade" id="jsonModal" tabindex="-1" aria-labelledby="jsonModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
         <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="jsonModalLabel">Configuration Settings</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <div class="alert alert-primary d-flex align-items-center" role="alert">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                     <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                  </svg>
                  <div>
                     The JSON configuration on the right includes the default values of the NAP Template. 
                  </div>
               </div>
               <div class="alert alert-warning hidden" id="change_results">
                     <i class="fa fa-spinner fa-pulse fa-3x" style="float:left; margin-right:10px "></i>
                     <h6 style="margin-top:10px"> Please wait.. It can take up to 30 seconds.</h6>  
               </div>               
               <input id="json_variable" type="text" hidden>
               <input id="policy_name" type="text" value="<?php echo $_POST['policy']; ?>" hidden>
               <div class="row">
                  <div class="col-md-6" >
                     <div id="json_title" style="font-size:16px">
               
            
                     </div>                     
                  </div>
                  <div class="col-md-6" >
                     <div id="json_title_original" style="font-size:16px">
                        <i>Inlcuding Template's Default values:</i>
                     </div>                         
                  </div>
               </div>

               <div class="row">
                  <div class="col-md-6" >
                     <textarea disabled id="original_json_text" style="width: 100%; height: 768px; border-color: #084298;border-width: 2px; padding: 5px 5px;" class="disabled textarea">

                     </textarea>
                  </div>               
                  <div class="col-md-6" >
                     <textarea disabled id="json_text" style="width: 100%; height: 768px; border-color: #084298;border-width: 2px; padding: 5px 5px;" class="disabled textarea">

                     </textarea>
                  </div> 
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-dark btn_bottom_form" data-bs-dismiss="modal">Close</button>
               <button type="button" class="btn btn-success btn_bottom_form" id="btn-edit">Edit</button>
               <button type="button" class="btn btn-secondary btn_bottom_form" id="btn-submit" disabled>Save changes</button>
            </div>
         </div>
      </div>
   </div>


   <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModal" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title">Edit </h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" >
               <div class="alert alert-warning hidden" id="change_results_general">
                  <i class="fa fa-spinner fa-pulse fa-3x" style="float:left; margin-right:10px "></i>
                  <h6 style="margin-top:10px"> Please wait.. It can take up to 30 seconds.</h6>  
               </div> 
               <form class="row g-3">
                   <!--  ##############  Global ############## -->

                   <input type="text" class="form-control" id="general_settings_key" hidden>

                  <div class="col-md-6 vars" hidden>
                     <input type="text" class="form-control row" id="table_row">
                  </div>

                   <!--  ##############  General Settings ############## -->
                  
                  <div class="col-md-6 form_enforcement_mode vars">
                     <label class="form-label">Enforcement Mode</label>
                  </div>
                  <div class="col-md-6 form_enforcement_mode vars">
                     <input class="checkbox_form" type="checkbox" id="form_enforcement_mode">    
                  </div>

                  <div class="col-md-6 form_bot_protection vars">
                     <label class="form-label">Bot Protection</label>
                  </div>

                  <div class="col-md-6 form_bot_protection vars">
                     <input class="checkbox_form" type="checkbox" id="form_bot_protection">    
                  </div>   

                  <div class="col-md-6 form_csrf_protection vars">
                     <label class="form-label">CSRF Protection</label>
                  </div>
                  <div class="col-md-6 form_csrf_protection vars">
                     <input class="checkbox_form" type="checkbox" id="form_csrf_protection">    
                  </div>                  

                  <div class="col-md-4 form_description vars">
                     <label class="form-label">Description</label>
                  </div>
                  <div class="col-md-8 form_description vars">
                     <input type="text" class="form-control" id="form_description">
                  </div>

                  <div class="col-md-6 form_cookie_length vars">
                     <label class="form-label">Max Cookie Length</label>
                  </div>
                  <div class="col-md-6 form_cookie_length vars">
                     <input type="text" class="form-control" id="form_cookie_length" >
                  </div>

                  <div class="col-md-6 form_header_length vars">
                     <label class="form-label">Max Header Length</label>
                  </div>

                  <div class="col-md-6 form_header_length vars">
                     <input type="text" class="form-control" id="form_header_length">
                  </div>

                  <div class="col-md-4 vars form_xff" >
                     <label class="form-label" style="width:100%;">Trust XFF</label>
                  </div>
                  <div class="col-md-8 vars form_xff" >
                     <input class="checkbox_form" type="checkbox" id="form_xff">
                  </div> 

                  <div class="col-md-4 vars form_xff" >
                     <label class="form-label">XFF Headers</label>
                  </div> 
                  <div class="col-md-8 vars form_xff" >
                     <input type="text" class="form-control" id="form_xff_headers">
                  </div> 
                  
                  
                  <div class="col-md-6 vars form_mask_credit_card">
                     <label class="form-label" style="width:100%;">Mask Credit Card</label>
                  </div>
                  <div class="col-md-6 vars form_mask_credit_card" style="text-align:center">
                     <input class="checkbox_form" type="checkbox" id="form_mask_credit_card">
                  </div>  


                  <div class="col-md-6 vars form_case_insensitive">
                     <label class="form-label" style="width:100%;">Policy Case Insensitive</label>
                  </div>
                  <div class="col-md-6 vars form_case_insensitive">
                     <input class="checkbox_form" type="checkbox" id="form_case_insensitive">
                  </div>                  

                  <div class="col-md-6 vars form_botcasesensitive">
                     <label class="form-label" >Bot Case Sensitive Headers</label>
                  </div>
                  <div class="col-md-6 vars form_botcasesensitive">
                     <input class="checkbox_form" type="checkbox" id="form_botcasesensitive">
                  </div>


                  <!--  ##############  Violations ############## -->
                           
                  <div class="col-md-8 vars form_violations">
                     <label class="form-label">Description</label>
                     <input type="text" class="form-control" id="violation_form_description" disabled>
                  </div>
                  <div class="col-md-8 vars">
                     <label class="form-label">Name</label>
                     <input type="text" class="form-control" id="violation_form_name" disabled>
                  </div>

                  <div class="col-md-2 vars form_violations" style="text-align:center" >
                     <label class="form-label" style="width:100%;">Alarm</label>
                     <input class="checkbox_form" type="checkbox" id="violation_form_alarm">
                  </div>
                  
                  <div class="col-md-2 vars form_violations" style="text-align:center" >
                     <label class="form-label" style="width:100%;">Block</label>
                     <input class="checkbox_form" type="checkbox" id="violation_form_block">
                  </div>


                  <!--  ##############  Evasions ############## -->
                           
                  <div class="col-md-10 vars form_evasion">
                     <label class="form-label">Description</label>
                     <input type="text" class="form-control" id="evasion_form_description" disabled>
                  </div>
      
                  <div class="col-md-2 vars form_evasion" style="text-align:center" >
                     <label class="form-label" style="width:100%;">Enabled</label>
                     <input class="checkbox_form" type="checkbox" id="evasion_form_enabled">
                  </div>

                  <!--  ##############  Compliance ############## -->
                           
                  <div class="col-md-10 vars form_compliance">
                     <label class="form-label">Description</label>
                     <input type="text" class="form-control" id="compliance_form_description" disabled>
                  </div>
      
                  <div class="col-md-2 vars form_compliance" style="text-align:center" >
                     <label class="form-label" style="width:100%;">Enabled</label>
                     <input class="checkbox_form" type="checkbox" id="compliance_form_enabled">
                  </div>


               </form>
               <br>

            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-dark btn_bottom_form" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-secondary btn_bottom_form" id="btn-submit-general" >Save changes</button>
            </div>
         </div>
      </div>
   </div>

   <!-- Modal -->
   <div class="modal fade" id="syncModal" tabindex="-1" aria-labelledby="syncModal" aria-hidden="true">
	   <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modal_title">Sync Policy with Git</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
      
            <div class="modal-body">

                  <div class="col-md-12" id="change_results_sync" style="display: none;">
                     <div class="alert alert-warning ">
                        <i class="fa fa-spinner fa-pulse fa-3x" style="float:left; margin-right:10px "></i>
                        <h6 style="margin-top:10px"> Please wait.. It can take up to 10 seconds.</h6>  
                     </div>
                  </div>
                  <div class="col-md-12">
                     <label class="form-label">Git Comment</label>
                     <input type="text" class="form-control" id="git_comment" aria-describedby="text" value="Changes made by NAP Policy Management Tool">
                  </div>
                  <br><br>


                  <div class="col-md-2 branch form-check" style="text-align:center;" hidden>
                     <label class="form-label" for="branch" style="width:100%; ">Create Branch</label>
                     <input class="" type="checkbox" id="branch" style="height:18px; width:18px; margin-top: 6px;" disabled>
                  </div>					


               </form>
               
            </div>
         <div class="modal-footer">
         <button type="button" class="btn btn-secondary btn_bottom_form" data-bs-dismiss="modal">Close</button>
         <button type="button" class="btn btn-primary btn_bottom_form" id="deploy">Deploy</button>
         </div>
      </div>
  </div>
  
</div>

</html>



<script>
   $( "#btn-edit" ).click(function() {
      $("#original_json_text").removeAttr("disabled");
      $("#btn-submit").removeAttr("disabled");
   });
</script>




 <!-- Enable tooltips -->
  
<script>
   var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
   var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
   })
</script>   


 <!-- Initialize javascript variables -->
<script> 
   <?php 
      echo $compliance_original . "\n"; 
      echo $compliance. "\n";
      echo $violations_original. "\n";
      echo $violations. "\n";
      echo $evasion_original. "\n";
      echo $evasion. "\n";
      echo $signature_sets_original. "\n";
      echo $signature_sets. "\n";
      echo $signatures_original. "\n";
      echo $signatures. "\n";
      echo $threat_campaigns_original. "\n";
      echo $threat_campaigns. "\n";
      echo $server_technologies_original. "\n";
      echo $server_technologies. "\n";
      echo $signature_requirements_original. "\n";
      echo $signature_requirements. "\n";
      echo $file_types_original. "\n";
      echo $file_types. "\n";
      echo $parameters_original. "\n";
      echo $parameters. "\n";
      echo $sensitive_param_original. "\n";
      echo $sensitive_param. "\n";
      echo $url_original. "\n";
      echo $url. "\n";
      echo $cookies_original. "\n";
      echo $cookies. "\n";
      echo $csrf_original. "\n";
      echo $csrf. "\n";
      echo $headers_original. "\n";
      echo $headers. "\n";
      echo $json_validation_files_original. "\n";
      echo $json_validation_files. "\n";
      echo $response_pages_original. "\n";
      echo $response_pages. "\n";
      echo $methods_original. "\n";
      echo $methods. "\n";
      echo $bot_defense_classes_original. "\n";
      echo $bot_defense_classes. "\n";
      echo $bot_defense_signatures_original. "\n";
      echo $bot_defense_signatures. "\n";
      echo $bot_defense_anomalies_original. "\n";
      echo $bot_defense_anomalies. "\n";
      echo $bot_defense_browsers_original. "\n";
      echo $bot_defense_browsers. "\n";
      echo $xml_profiles_original. "\n";
      echo $xml_profiles. "\n";
      echo $json_profiles_original. "\n";
      echo $json_profiles. "\n";
      echo $allowed_response_codes_table. "\n";
      echo $allowedResponseCodes_original. "\n";
      echo $allowedResponseCodes. "\n";
      echo $dataguard_original. "\n";
      echo $dataguard. "\n";
      echo $whitelist_ips_original. "\n";
      echo $whitelist_ips. "\n";
      
   ?>

</script>
  

<!-- General Table -->

<script>
   $(document).ready(function () {
      $('#general').DataTable(
      {
         "searching": false,
         "info": false,
         "paging":true,
         "ordering":false,
         "order": [],
         "pageLength": 25
      }
   );
   $('#general tbody').on( 'click', 'td.edit_button', function () {

         $(".vars").hide();
         var myModal = new bootstrap.Modal(document.getElementById('editModal'))
         myModal.show()
         var form_id = "#form_"+this.id;
         var form_class = ".form_"+this.id;
         var value = $(this).attr('value');

         if (this.id=="bot_protection" || this.id=="mask_credit_card" || this.id=="case_insensitive" || this.id=="botcasesensitive" || this.id=="csrf_protection" )
         {            
            if ($("#"+this.id).attr('value')=="enabled")
               $(form_id).attr('checked', 'checked');
            else
               $(form_id).removeAttr('checked');
         }
         if (this.id=="enforcement_mode")
         {            
            if ($("#"+this.id).attr('value')=="blocking")
               $(form_id).attr('checked', 'checked');
            else
               $(form_id).removeAttr('checked');
         }         
         if (this.id=="description" || this.id=="cookie_length" || this.id=="header_length")
            $(form_id).val(value);

         if (this.id=="xff" || this.id=="xff_headers")
         {
            $(".form_xff").show();
            if ($("#xff").attr('value')=="enabled")
               $(form_id).attr('checked', 'checked');
            else
               $(form_id).removeAttr('checked');

               $("#form_xff_headers").val($("#xff_headers").attr('value'));
         }              
  
         $(form_class).show();
         $("#general_settings_key").val(this.id);
      });
   });
      
</script>


<!-- Blocking settings -->
<script>
	$(document).ready(function() {
		var table = $('#violations').DataTable( {
			"data": violations,
         "pageLength": 25,
			"createdRow": function( row, data, dataIndex ) {
  
				if ( data['alarm'] == true )
				  $('td', row).eq(2).html("<i class='fa fa-check-square-o fa-2x green'></i>");
				else 
				  $('td', row).eq(2).html("<i class='fa fa-minus-square fa-2x red' ></i>");
				if ( data['block'] == true )
				  $('td', row).eq(3).html("<i class='fa fa-check-square-o fa-2x green'></i>");
				else 
				  $('td', row).eq(3).html("<i class='fa fa-minus-square fa-2x red' ></i>");
				
            $('td', row).eq(4).html("<i class='fa fa-edit'></i>");  

			  },
         "columnDefs": [
            {target: 4,visible: true,searchable: false}
         ],	            
         "columns": [
				{ "className":'bold', "data":"name" },
				{ "className":'bold', "data":"description" },
				{ "className":'attacks', "data":"alarm"},
				{ "className":'attacks', "data":"block"},
				{ "className":'edit_button',"data": null, "orderable":false}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " },
				"order": [[2, 'desc']]
		} );	
         $('#violations tbody').on( 'click', '.edit_button', function () {

            var tr = $(this).closest('tr');
            var idx = table.row(tr).index();

            var viol_name = table.cell( idx, 0).data();
            var description = table.cell( idx, 1).data();
            var alarm = table.cell( idx, 2).data();
            var block = table.cell( idx, 3).data();

            $(".vars").hide();
            $(".form_violations").show();

            var myModal = new bootstrap.Modal(document.getElementById('editModal'))
            myModal.show()
            $("#violation_form_description").val(description);
            $("#violation_form_name").val(viol_name);

            $("#table_row").val(idx);
            $("#general_settings_key").val("violations-item");
            if (alarm == true)
               $("#violation_form_alarm").attr("checked", "checked");
            else
               $("#violation_form_alarm").removeAttr( "checked");

            if (block == true)
               $("#violation_form_block").attr("checked", "checked");
            else
               $("#violation_form_block").removeAttr( "checked"); 
      });

	} );
</script>	

<!-- Evasion -->
<script>
	$(document).ready(function() {
		var table = $('#evasion').DataTable( {
			"data": evasion,
			"searching": false,
			"info": false,
         "columnDefs": [
            {target: 2,visible: true,searchable: false}
         ],	           
			"createdRow": function( row, data, dataIndex ) {
				if ( data['enabled'] == true )
				  $('td', row).eq(1).html("<i class='fa fa-check-square-o fa-2x green'></i>");
				else 
				  $('td', row).eq(1).html("<i class='fa fa-minus-square-o fa-2x red' ></i>");

            $('td', row).eq(2).html("<i class='fa fa-edit'></i>");  

			},
			  "columns": [
				{"className": 'bold',"data": "description" },
				{"className": 'attacks', "data": "enabled"},
				{"className": 'edit_button',"data": null, "orderable":false}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " },
				"order": [[0, 'desc']]
		} );	
      $('#evasion tbody').on( 'click', '.edit_button', function () {

         var tr = $(this).closest('tr');
         var idx = table.row(tr).index();

         var description = table.cell( idx, 0).data();
         var enabled = table.cell( idx, 1).data();

         $(".vars").hide();

         var myModal = new bootstrap.Modal(document.getElementById('editModal'))
         myModal.show()
         $("#evasion_form_description").val(description);
         $("#table_row").val(idx);
         $("#general_settings_key").val("evasion-item");
         if (enabled == true)
            $("#evasion_form_enabled").attr("checked", "checked");
         else
            $("#evasion_form_enabled").removeAttr( "checked");            

         $(".form_evasion").show();

      });      

	} );
</script>

<!-- Compliance -->
<script>
	$(document).ready(function() {
		var table = $('#compliance').DataTable( {
			"data": compliance,
         "pageLength": 25,         
			"searching": false,
			"info": false,
			"createdRow": function( row, data, dataIndex ) {
				if ( data['enabled'] == true )
				  $('td', row).eq(1).html("<i class='fa fa-check-square-o fa-2x green'></i>");
				else 
				  $('td', row).eq(1).html("<i class='fa fa-minus-square-o fa-2x red' ></i>");
            
           $('td', row).eq(2).html("<i class='fa fa-edit'></i>");                
			},
         "columnDefs": [
            {target: 2,visible: true,searchable: false}
         ],	           
			"columns": [
				{"className": 'bold',"data": "description" },
				{"className": 'attacks', "data": "enabled"},
				{"className": 'edit_button',"data": null, "orderable":false}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " },
				"order": [[0, 'desc']]
		} );	
      $('#compliance tbody').on( 'click', '.edit_button', function () {
         var tr = $(this).closest('tr');
         var idx = table.row(tr).index();

         var description = table.cell( idx, 0).data();
         var enabled = table.cell( idx, 1).data();

         $(".vars").hide();

         var myModal = new bootstrap.Modal(document.getElementById('editModal'))
         myModal.show()
         $("#compliance_form_description").val(description);
         $("#table_row").val(idx);
         $("#general_settings_key").val("compliance-item");

         if (enabled == true)
            $("#compliance_form_enabled").attr("checked", "checked");
         else
            $("#compliance_form_enabled").removeAttr( "checked");
         $(".form_compliance").show();

         });   
	} );
</script>

<!-- Threat campaigns -->
<script>
	$(document).ready(function() {
		var table = $('#threat_campaigns').DataTable( {
			"data": threat_campaigns,
			"searching": false,
         "order": [[0, 'asc']],
			"info": false,
         "columnDefs": [
            {target: 2,visible: false,searchable: false}
         ],	           
			"createdRow": function( row, data, dataIndex ) {
				if ( data['enabled'] == true )
				  $('td', row).eq(1).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(1).html("<i class='fa fa-minus-circle fa-2x red' ></i>");

            $('td', row).eq(2).html("<i class='fa fa-trash'></i> ");  
			},			
			"columns": [
				{"className": 'bold',"data": "name", "defaultContent":"N/A" },
				{"className": 'attacks',"data": "enabled" },
				{"className": 'delete_button',"data": null, "orderable":false}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );	
      $('#threat_campaigns tbody').on( 'click', 'td.delete_button', function () {
         //var idx = table.row(this).index();
         //var data = table.cell( idx, 1).data();
         table.row(this).remove().draw( false );
         $('#threat_campaigns_save').removeAttr("disabled");
      });
	} );
</script>

<!-- signature-requirements -->
<script>
	$(document).ready(function() {
		var table = $('#signature_requirements').DataTable( {
			"data": signature_requirements,
			"searching": false,
			"paging":false,
			"info": false,
			"columns": [
			{ "className": 'bold',"data": "tag" },
			{ "className": 'attacks', "data": "maxRevisionDatetime", "defaultContent": "None"},
			{ "className": 'attacks', "data": "minRevisionDatetime", "defaultContent": "None"},
			],
			"autoWidth": false,
			"processing": true,
			"language": {"processing": "Waiting.... " },
			"order": [[1, 'desc']]
		} );	

	} );
</script>

<!-- Signatures -->
<script>
	$(document).ready(function() {
		var table = $('#signatures').DataTable( {
			"data": signatures,
			"searching": false,
         "order": [[1, 'desc']],
			"info": false,
			"createdRow": function( row, data, dataIndex ) {
				if ( data['enabled'] == true )
					$('td', row).eq(0).html("<i class='fa fa-check-square-o fa-2x green'></i>");
				else 
					$('td', row).eq(0).html("<i class='fa fa-minus-square fa-2x red' ></i>");
   
            $('td', row).eq(4).html("<i class='fa fa-trash' ></i> ");  
			},
         "columnDefs": [
            {target: 4,visible: false,searchable: false}
         ],	         
			"columns": [
				{"className": 'attacks', "data": "enabled","defaultContent": false},
				{"className": 'bold', "data": "signatureId","defaultContent": "N/A"},
				{"className": 'bold', "data": "name","defaultContent": "N/A"},
				{"className": 'bold', "data": "tag","defaultContent": "N/A"},
				{"className": 'delete_button',"data": null, "orderable":false}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );

      $('#signatures tbody').on( 'click', 'td.delete_button', function () {
         //var idx = table.row(this).index();
         //var data = table.cell( idx, 1).data();
         table.row(this).remove().draw( false );
         $('#signatures_save').removeAttr("disabled");

      });	
	} );
</script>	

<!-- Signature Sets -->
<script>
	function format_signature_sets ( d ) {
		var filter = "N/A";
		var systems = "N/A";
		var signatures = "N/A";
		var table_add = "";
		var line_add = "";
		if ("signatureSet" in d)
		{
			if ("filter" in d.signatureSet)
			{
				var filter = "";
				for(var j in d.signatureSet.filter){
					var sub_key = j;
					var sub_val = d.signatureSet.filter[j];
					if (sub_key == "attackType")
						filter = filter + sub_key+': <b> ' + sub_val.name + '</b><br>';
					else
						filter = filter + sub_key+': <b> ' + sub_val + '</b><br>';
				}				
			}
			if ("signatures" in d.signatureSet)
			{
				var signatures = "";
				for(var j in d.signatureSet.signatures){
					var sub_key = j;
					var sub_val = d.signatureSet.signatures[j];
					signatures = signatures + 'signatureId: <b> ' + sub_val.signatureId + '</b><br>';
				}				
			}
			if ("systems" in d.signatureSet)
			{
				var systems = "";
				for(var j in d.signatureSet.systems){
					var sub_key = j;
					var sub_val = d.signatureSet.systems[j];
					systems = systems + 'name: <b> ' + sub_val.name + '</b><br>';
				}				
			}
		}

		return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered subtable">'+
			'<tr>'+
				'<td style="width:150px; background-color:#eaecf0"><b>Filter:</b></td>'+
				'<td >'+filter+'</td>'+
			'</tr>'+ 
			'<tr>'+
				'<td style="width:150px; background-color:#eaecf0"><b>Systems:</b></td>'+
				'<td >'+systems+'</td>'+
			'</tr>'+
			'<tr>'+
				'<td style="width:150px; background-color:#eaecf0"><b>Individual Signatures:</b></td>'+
				'<td >'+signatures+'</td>'+
			'</tr>'+
			table_add +
			'</table>';
	}

   $(document).ready(function() {
		var table = $('#signature_sets').DataTable( {
			"data": signature_sets,
         "pageLength": 25,
         "order": [[3, 'desc']],
			"createdRow": function( row, data, dataIndex ) {
				if ( data['alarm'] == true )
				  $('td', row).eq(2).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(2).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
				if ( data['block'] == true )
				  $('td', row).eq(3).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(3).html("<i class='fa fa-minus-circle fa-2x red' ></i>");

            $('td', row).eq(5).html("<i class='fa fa-edit'></i> ");  
            $('td', row).eq(6).html("<i class='fa fa-trash' ></i> ");  
			},
         "columnDefs": [
            {target: 5,visible: false,searchable: false,},
            {target: 6,visible: false,searchable: false,}
         ],	           
			"columns": [
				{"className":'details-control',"orderable":false,"data":null,"defaultContent": ''},		
				{"className":'bold',"data": "name" },
				{"className":'attacks',"data": "alarm"},
				{"className":'attacks',"data": "block"},
				{"className":'attacks',"data": "signatureSet.type", "defaultContent": "default"},
				{"className":'edit_button',"orderable":false ,"data": null},
				{"className":'delete_button',"orderable":false ,"data": null}
				],

				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );
		$('#signature_sets tbody').on('click', 'td.details-control', function () {
			var tr = $(this).closest('tr');
			var row = table.row( tr );
	
			if ( row.child.isShown() ) {
				// This row is already open - close it
				row.child.hide();
				tr.removeClass('shown');
			}
			else {
				// Open this row
				row.child( format_signature_sets(row.data()) ).show();
				tr.addClass('shown');
			}
		} );

      $('#signature_sets tbody').on( 'click', 'td.delete_button', function () {
         //var idx = table.row(this).index();
         //var data = table.cell( idx, 1).data();
         table.row(this).remove().draw( false );
         $('#signature_sets_save').removeAttr("disabled");
      });	

	} );
</script>

<!-- File Types -->
<script>
	$(document).ready(function() {
		var table = $('#file_type').DataTable( {
			"data": file_types,
         "order": [[0, 'desc']],
         "columnDefs": [
            {target: 12,visible: false,searchable: false},
            {target: 13,visible: false,searchable: false}
         ],	         
			"createdRow": function( row, data, dataIndex ) {
            if ( data['allowed'] == true )
				{
               $('td', row).eq(2).html("<i class='fa fa-check-circle fa-2x green'></i>");
               if ( data['checkUrlLength'] == true )
                  $('td', row).eq(3).html("<i class='fa fa-check-square-o fa-2x green'></i>");
               else 
                  $('td', row).eq(3).html("<i class='fa fa-minus-square-o  fa-2x black' ></i>");
               if ( data['checkQueryStringLength'] == true )
                  $('td', row).eq(4).html("<i class='fa fa-check-square-o fa-2x green'></i>");
               else 
                  $('td', row).eq(4).html("<i class='fa fa-minus-square-o  fa-2x black' ></i>");
               if ( data['checkPostDataLength'] == true )
                  $('td', row).eq(5).html("<i class='fa fa-check-square-o fa-2x green'></i>");
               else 
                  $('td', row).eq(5).html("<i class='fa fa-minus-square-o  fa-2x black' ></i>");
               if ( data['checkRequestLength'] == true )
                  $('td', row).eq(6).html("<i class='fa fa-check-square-o fa-2x green'></i>");
               else 
                  $('td', row).eq(6).html("<i class='fa  fa-minus-square-o fa-2x black' ></i>");
               if ( data['responseCheck'] == true )
                  $('td', row).eq(11).html("<i class='fa fa-check-square-o fa-2x green'></i>");
               else 
                  $('td', row).eq(11).html("<i class='fa fa-minus-square-o  fa-2x black' ></i>");	  
            }
            else 
				{
               $('td', row).eq(2).html("<i class='fa fa-minus-circle fa-2x red' ></i>");         
            }
              $('td', row).eq(12).html("<i class='fa fa-edit'></i> ");  
              $('td', row).eq(13).html("<i class='fa fa-trash'></i> ");  

			  },
			  "columns": [
				{ "className": 'bold',"data": "name" },
				{ "className": 'attacks', "data": "type", "defaultContent": "explicit"},
				{ "className": 'attacks', "data": "allowed"},
				{ "className": 'attacks',"data": "checkUrlLength", "defaultContent": "N/A"},
				{ "className": 'attacks',"data": "checkQueryStringLength", "defaultContent": "N/A"},
				{ "className": 'attacks',"data": "checkPostDataLength", "defaultContent": "N/A"},
				{ "className": 'attacks',"data": "checkRequestLength", "defaultContent": "N/A"},
				{ "className": 'attacks',"data": "urlLength", "defaultContent": "N/A"},
				{ "className": 'attacks',"data": "queryStringLength", "defaultContent": "N/A"},
				{ "className": 'attacks',"data": "postDataLength", "defaultContent": "N/A"},
				{ "className": 'attacks',"data": "requestLength", "defaultContent": "N/A"},
				{ "className": 'attacks',"data": "responseCheck", "defaultContent": "N/A"},
            {"className": 'edit_button',"data": null, "orderable":false},
            {"className": 'delete_button',"data": null, "orderable":false}
				],
				"autoWidth": false,
				"processing": true,
				"order": [[0, 'asc']]
		} );	

      $('#file_type tbody').on( 'click', 'td.delete_button', function () {
         //var idx = table.row(this).index();
         //var data = table.cell( idx, 1).data();
         table.row(this).remove().draw( false );
         $('#file_type_save').removeAttr("disabled");
      });	
	} );
</script>

<!-- Parameters -->
<script>
   function format_parameter ( d ) {

      if ("contentProfile" in d)
         contentProfile = d.contentProfile.contentProfile.name
      
      var signatures = "None";
         if ("signatureOverrides" in d)
         {
            var signatures = "";
            for(var j in d.signatureOverrides){
               var sub_key = j;
               var sub_val = d.signatureOverrides[j];
               if (sub_key == "tag")
                  signatures = signatures + '"name" : <b> "' + sub_val.name + '" </b> - ' + '"Tag" : <b> "' + sub_val.tag + '"</b><br>';
               else
                  signatures = signatures + '"name" : <b> "' + sub_val.name + '" </b> - ' + '"SignatureID" : <b> "' + sub_val.signatureId + '"</b><br>';
            }				
         }
         var valueMetacharOverrides = "None";

         if ("valueMetacharOverrides" in d)
         {
            var valueMetacharOverrides = "";
            for(var j in d.valueMetacharOverrides){
               var sub_key = j;
               var sub_val = d.valueMetacharOverrides[j];
               valueMetacharOverrides = valueMetacharOverrides + '"MetaChar" : <b> "' + sub_val.metachar + '" </b> - ' + '"isAllowed" : <b> "' + sub_val.isAllowed + '"</b><br>';
            }				
         }

         var nameMetacharOverrides = "None";

         if ("nameMetacharOverrides" in d)
         {
            var nameMetacharOverrides = "";
            for(var j in d.nameMetacharOverrides){
               var sub_key = j;
               var sub_val = d.nameMetacharOverrides[j];
               nameMetacharOverrides = nameMetacharOverrides + '"MetaChar" : <b> "' + sub_val.metachar + '" </b> - ' + '"isAllowed" : <b> "' + sub_val.isAllowed + '"</b><br>';
            }				
         }

      skip =  ["checkMetachars", "attackSignaturesCheck", "name", "valueType", "contentProfile", "valueMetacharOverrides", "url", "signatureOverrides", "parameterEnumValues", "nameMetacharOverrides"];
		var table= "";
      for(var i in d){
			var key = i;
			var val = d[i];
         if(val === false)
            val = '<i class="fa fa-minus-square-o fa-2x red" ></i>';
         if(val === true)
            val = '<i class="fa fa-check-square-o fa-2x green"></i>';

         if (!skip.includes(key))
         {
            table = table + '<tr>'+
                        '<td style="width:250px; background-color:#eaecf0"><b>'+key+':</b></td>'+
                        '<td colspan=5>'+val+'</td>'+
                     '</tr>';

         }
      }

         return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered subtable">'+
                     '<tr>'+
                        '<td style="width:250px; background-color:#eaecf0"><b>Signature Overrides:</b></td>'+
                        '<td colspan=5>'+signatures+'</td>'+
                     '</tr>'+
                     '<tr>'+
                        '<td style="width:250px; background-color:#eaecf0"><b>Value MetaChr Overrides:</b></td>'+
                        '<td colspan=5>'+valueMetacharOverrides+'</td>'+
                     '</tr>'+
                     '<tr>'+
                        '<td style="width:250px; background-color:#eaecf0"><b>Name MetaChr Overrides:</b></td>'+
                        '<td colspan=5>'+nameMetacharOverrides+'</td>'+
                     '</tr>'+                     
                     table +
                  '</table>';
   }

	$(document).ready(function() {
		var table = $('#parameters').DataTable( {
			"data": parameters,
			"createdRow": function( row, data, dataIndex ) {
				if ("nameMetacharOverrides" in data)
					$('td', row).eq(9).html(data.nameMetacharOverrides.length);
				else
					$('td', row).eq(9).html("0");
            if ("valueMetacharOverrides" in data)
					$('td', row).eq(8).html(data.valueMetacharOverrides.length);
				else
					$('td', row).eq(8).html("0");

				if ("signatureOverrides" in data)
					$('td', row).eq(7).html(data.signatureOverrides.length);
				else
					$('td', row).eq(7).html("0");			
            if ( data['checkMetachars'] == true )
				  $('td', row).eq(6).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(6).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
				if ( data['attackSignaturesCheck'] == true )
				  $('td', row).eq(4).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(4).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
				if ( data['metacharsOnParameterValueCheck'] == true)
				  $('td', row).eq(5).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(5).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
				if ( data['sensitiveParameter'] == true )
				  $('td', row).eq(3).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(3).html("<i class='fa fa-minus-circle fa-2x red' ></i>");

            $('td', row).eq(10).html("<i class='fa fa-edit'></i> ");  
            $('td', row).eq(11).html("<i class='fa fa-trash'></i> ");  
			  },
           "columnDefs": [
            {target: 10,visible: false,searchable: false},
            {target: 11,visible: false,searchable: false}
            ],        
			  "columns": [
				{"className":'details-control',"orderable":false,"data":null,"defaultContent": ''},
				{ "className": 'bold',"data": "name","defaultContent": '' },
				{ "className": 'attacks',"data": "valueType","defaultContent": ''},
				{ "className": 'attacks',"data": "sensitiveParameter","defaultContent": ''},
				{ "className": 'attacks',"data": "attackSignaturesCheck","defaultContent": ''},
				{ "className": 'attacks',"data": "metacharsOnParameterValueCheck","defaultContent": ''},
				{ "className": 'attacks',"data": "checkMetachars","defaultContent": ''},
				{ "className": 'attacks',"data": null,"defaultContent": 0},
				{ "className": 'attacks',"data": null,"defaultContent": 0},
				{ "className": 'attacks',"data": null,"defaultContent": 0},
            {"className": 'edit_button',"data": null, "orderable":false},
            {"className": 'delete_button',"data": null, "orderable":false}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " },
				"order": [[1, 'asc']]
		} );	

    $('#parameters tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format_parameter(row.data()) ).show();
            tr.addClass('shown');
        }
    } );

    $('#parameters tbody').on( 'click', 'td.delete_button', function () {
         //var idx = table.row(this).index();
         //var data = table.cell( idx, 1).data();
         table.row(this).remove().draw( false );
         $('#parameters_save').removeAttr("disabled");
      });	

	} );
</script>

<!-- SENSITIVE PARAMS -->
<script>
	$(document).ready(function() {
		var table = $('#sensitive_param').DataTable( {
			"searching": true,
			"info": true,
         "order": [[0, 'desc']],
			"data": sensitive_param,
			"createdRow": function( row, data, dataIndex ) {
            $('td', row).eq(1).html("<i class='fa fa-trash' ></i> "); 

			},
         "columnDefs": [
            {target: 1,visible: false,searchable: false}
         ],               
			"columns": [
				{ "className": 'bold',"data": "name" },
				{"className": 'delete_button',"data": null, "orderable":false}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );
      $('#sensitive_param tbody').on( 'click', 'td.delete_button', function () {
         //var idx = table.row(this).index();
         //var data = table.cell( idx, 1).data();
         table.row(this).remove().draw( false );
         $('#sensitive_param_save').removeAttr("disabled");
      });	      
	} );
</script>

<!-- Urls -->
<script>

	function format_url ( d ) {
		var contentprofiles = "N/A";
		var clickjackingProtection ="<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var disallowFileUploadOfExecutables ="<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var wildcardOrder = "N/A";
		var methodsOverrideOnUrlCheck = "<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var type = "N/A";
		var mandatoryBody = "<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var metacharacters = "N/A";
		var signatures = "N/A";

		if ("urlContentProfiles" in d)
		{
			var urlContentProfiles = "";
			for(var i in d.urlContentProfiles){
				var key = i;
				var contentprofile = "N/A";
				var val = d.urlContentProfiles[i];
				if ("ContentProfiles" in val)
					contentprofile = val.ContentProfiles;
				
				urlContentProfiles = urlContentProfiles + 'HeaderName: <b> ' +val.headerName + '</b>, ' + 'HeaderValue: <b> ' + val.headerValue + '</b>, '+ 'HeaderValue: <b> ' +val.headerValue + '</b>, '+ 'HeaderOrder: <b> ' +val.headerOrder + '</b>, '+ 'Type: <b> ' +val.type + '</b>, ContentProfile : <b> ' + contentprofile +', </b> <br>';
			}
		}

		if ("clickjackingProtection" in d)
			if (d.clickjackingProtection == true)
				clickjackingProtection = "<i class='fa fa-check-square-o fa-2x green'></i>";

			
		if ("disallowFileUploadOfExecutables" in d)
			if (d.disallowFileUploadOfExecutables == true)
				disallowFileUploadOfExecutables = "<i class='fa fa-check-square-o fa-2x green'></i>";

		if ("methodsOverrideOnUrlCheck" in d)
			if (d.methodsOverrideOnUrlCheck == true)
				methodsOverrideOnUrlCheck = "<i class='fa fa-check-square-o fa-2x green'></i>";
					
		if ("mandatoryBody" in d)
			if (d.mandatoryBody == true)
				mandatoryBody = "<i class='fa fa-check-square-o fa-2x green'></i>";
					
		
				
		if ("wildcardOrder" in d)
			wildcardOrder = d.wildcardOrder



		if ("signatureOverrides" in d)
		{
			var signatures = "";
			for(var j in d.signatureOverrides){
				var sub_key = j;
				var sub_val = d.signatureOverrides[j];
			// Chack about the tag
         //	if (sub_key == "tag")
			//		signatures = signatures + '"name" : <b> "' + sub_val.name + '" </b> - ' + '"Tag" : <b> "' + sub_val.tag + '"</b><br>';
			//	else
			//		signatures = signatures + '"name" : <b> "' + sub_val.name + '" </b> - ' + '"SignatureID" : <b> "' + sub_val.signatureId + '"</b><br>';
					signatures = signatures + 'SignatureID: <b> ' + sub_val.signatureId + '</b> - ' + 'Enabled: <b> ' + sub_val.enabled + '</b><br>';
			}				
		}

		if ("metacharOverrides" in d)
		{
			var metacharacters = "";
			for(var j in d.metacharOverrides){
				var sub_key = j;
				var sub_val = d.metacharOverrides[j];
				metacharacters = metacharacters + 'MetaChar: <b> ' + sub_val.metachar + '</b> - ' + 'isAllowed:<b> ' + sub_val.isAllowed + '</b><br>';
			}				
		}		


		return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered subtable">'+
			'<tr>'+
				'<td style="width:250px; background-color:#eaecf0;"><b>URL Content Profiles:</b></td>'+
				'<td >'+urlContentProfiles+'</td>'+
			'</tr>'+ 
			'<tr>'+
				'<td style="width:250px; background-color:#eaecf0;"><b>Type:</b></td>'+
				'<td >'+type+'</td>'+
			'</tr>'+
			'<tr>'+
				'<td style="width:250px; background-color:#eaecf0;"><b>Clickjacking Protection:</b></td>'+
				'<td >'+clickjackingProtection+'</td>'+
			'</tr>'+
			'<tr>'+
				'<td style="width:250px; background-color:#eaecf0;"><b>Disallow FileUpload Of Executables:</b></td>'+
				'<td >'+disallowFileUploadOfExecutables+'</td>'+
			'</tr>'+
			'<tr>'+
				'<td style="width:250px; background-color:#eaecf0;"><b>Mandatory Body:</b></td>'+
				'<td >'+mandatoryBody+'</td>'+
			'</tr>'+
			'<tr>'+
				'<td style="width:250px; background-color:#eaecf0;"><b>Signature Overrides:</b></td>'+
				'<td >'+signatures+'</td>'+
			'</tr>'+
			'<tr>'+
				'<td style="width:250px; background-color:#eaecf0;"><b>Metachar Overrides:</b></td>'+
				'<td >'+metacharacters+'</td>'+
			'</tr></table>';
	}

	$(document).ready(function() {
		var table = $('#urls').DataTable( {
			"data": url,
			"createdRow": function( row, data, dataIndex ) {
				if ("metacharOverrides" in data)
					$('td', row).eq(8).html(data.metacharOverrides.length);
				else
					$('td', row).eq(8).html("0");
				if ("signatureOverrides" in data)
					$('td', row).eq(7).html(data.signatureOverrides.length);
				else
					$('td', row).eq(7).html("0");
				if ( data['attackSignaturesCheck'] == true )
					$('td', row).eq(5).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
					$('td', row).eq(5).html("<i class='fa fa-times fa-2x black' ></i>");
				if ( data['metacharsOnUrlCheck'] == true )
				  $('td', row).eq(6).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(6).html("<i class='fa fa-times fa-2x black' ></i>");
				if ( data['isAllowed'] == true )
				  $('td', row).eq(4).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(4).html("<i class='fa fa fa-times fa-2x black' ></i>");

            $('td', row).eq(9).html("<i class='fa fa-edit'></i> ");  
            $('td', row).eq(10).html("<i class='fa fa-trash' ></i> ");  
			},
         "columnDefs": [
            {target: 9,visible: false,searchable: false},
            {target: 10,visible: false,searchable: false}
            ],           
			"columns": [
				{ "className":'details-control',"orderable": false,"data": null,"defaultContent": ''},
				{ "className": 'attacks',"data": "protocol"},
				{ "className": 'attacks',"data": "method"},
				{ "className": 'bold',"data": "name" },
				{ "className": 'attacks',"data": "isAllowed"},
				{ "className": 'attacks',"data": "attackSignaturesCheck"},
				{ "className": 'attacks',"data": "metacharsOnUrlCheck"},
				{ "className": 'attacks',"data": null,"defaultContent": ''},
				{ "className": 'attacks',"data": null,"defaultContent": ''},
				{"className": 'edit_button',"data": null, "orderable":false},
				{"className": 'delete_button',"data": null, "orderable":false}
				],
				"autoWidth": false,
				"processing": true,
				"order": [[1, 'asc']]
		} );	
		
		$('#urls tbody').on('click', 'td.details-control', function () {
			var tr = $(this).closest('tr');
			var row = table.row( tr );
	
			if ( row.child.isShown() ) {
				// This row is already open - close it
				row.child.hide();
				tr.removeClass('shown');
			}
			else {
				// Open this row
				row.child( format_url(row.data()) ).show();
				tr.addClass('shown');
			}
		} );
      $('#urls tbody').on( 'click', 'td.delete_button', function () {
         //var idx = table.row(this).index();
         //var data = table.cell( idx, 1).data();
         table.row(this).remove().draw( false );
         $('#urls_save').removeAttr("disabled");
      });    
	} );
</script>

<!--Cookies -->
<script>

	function format_cookie ( d ) {
		var wildcardOrder = "N/A";
		var accessibleOnlyThroughTheHttpProtocol ="<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var decodeValueAsBase64 ="<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var insertSameSiteAttribute = "<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var securedOverHttpsConnection = "<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var signatures = "None";

		if ("securedOverHttpsConnection" in d)
			if (d.securedOverHttpsConnection == true)
				securedOverHttpsConnection = "<i class='fa fa-check-circle fa-2x green'></i>";

			
		if ("insertSameSiteAttribute" in d)
			if (d.insertSameSiteAttribute == true)
				insertSameSiteAttribute = "<i class='fa fa-check-circle fa-2x green'></i>";

		if ("decodeValueAsBase64" in d)
			if (d.decodeValueAsBase64 == true)
				decodeValueAsBase64 = "<i class='fa fa-check-circle fa-2x green'></i>";
					
		if ("accessibleOnlyThroughTheHttpProtocol" in d)
			if (d.accessibleOnlyThroughTheHttpProtocol == true)
				accessibleOnlyThroughTheHttpProtocol = "<i class='fa fa-check-circle fa-2x green'></i>";
					
						
		if ("wildcardOrder" in d)
			wildcardOrder = d.wildcardOrder



		if ("signatureOverrides" in d)
		{
			var signatures = "";
			for(var j in d.signatureOverrides){
				var sub_key = j;
				var sub_val = d.signatureOverrides[j];
				// I need to check with TAGS if it can work
            //if (sub_key == "tag")
				//	signatures = signatures + '"name" : <b> "' + sub_val.name + '" </b> - ' + '"Tag" : <b> "' + sub_val.tag + '"</b><br>';
				//else
					signatures = signatures + 'SignatureID : <b> ' + sub_val.signatureId + ' </b> - ' + 'Enabled: <b> ' + sub_val.enabled + '</b><br>';
			}				
		}

		return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered subtable">'+
			'<tr>'+
				'<td style="width:250px; background-color:#eaecf0; "><b>HTTPOnly:</b></td>'+
				'<td >'+accessibleOnlyThroughTheHttpProtocol+'</td>'+
			'</tr>'+
			'<tr>'+
				'<td style="width:250px; background-color:#eaecf0; "><b>DecodeValue As Base64:</b></td>'+
				'<td >'+decodeValueAsBase64+'</td>'+
			'</tr>'+
			'<tr>'+
				'<td style="width:250px; background-color:#eaecf0; "><b>Insert SameSite:</b></td>'+
				'<td >'+insertSameSiteAttribute+'</td>'+
			'</tr>'+
			'<tr>'+
				'<td style="width:250px; background-color:#eaecf0; "><b>Secured over HTTPS:</b></td>'+
				'<td >'+securedOverHttpsConnection+'</td>'+
			'</tr>'+
			'<tr>'+
				'<td style="width:250px; background-color:#eaecf0; "><b>Signature Overrides:</b></td>'+
				'<td >'+signatures+'</td>'+
			'</tr>'+
			'<tr>'+
				'<td style="width:250px; background-color:#eaecf0; "><b>wildcardOrder:</b></td>'+
				'<td >'+wildcardOrder+'</td>'+
			'</tr></table>';

		}


 
	$(document).ready(function() {
		var table = $('#cookies').DataTable( {
		"data": cookies,
      "order": [[1, 'desc']],
		"createdRow": function( row, data, dataIndex ) {
			if ("signatureOverrides" in data)
					$('td', row).eq(5).html(data.signatureOverrides.length);
				else
					$('td', row).eq(5).html("0");
			if ( data['attackSignaturesCheck'] == true )
			  $('td', row).eq(4).html("<i class='fa fa-check-circle fa-2x green'></i>");
			else 
				$('td', row).eq(4).html("<i class='fa fa-times fa-2x' ></i>");

         $('td', row).eq(6).html("<i class='fa fa-edit'></i>");  
         $('td', row).eq(7).html("<i class='fa fa-trash' ></i> ");  
		  },
        "columnDefs": [
            {target: 6,visible: false,searchable: false},
            {target: 7,visible: false,searchable: false}
            ],        
		  "columns": [
            { "className":'details-control',"orderable":false,"data":null,"defaultContent": ''},
            { "className": 'bold',"data": "name" },
            { "className": 'attacks',"data": "type", "defaultContent": "explicit"},
            { "className": 'attacks',"data": "enforcementType", "defaultContent": "allow"},
            { "className": 'attacks',"data": "attackSignaturesCheck", "defaultContent": true},
            { "className": 'attacks',"data": "num_of_sign_overides", "defaultContent": 0},
				{ "className": 'edit_button',"data": null, "orderable":false},
				{ "className": 'delete_button',"data": null, "orderable":false}
			],
			"autoWidth": false,
			"processing": true,
			"language": {"processing": "Waiting.... " },
			"order": [[1, 'asc']]
		} );	

		$('#cookies tbody').on('click', 'td.details-control', function () {
			var tr = $(this).closest('tr');
			var row = table.row( tr );
	
			if ( row.child.isShown() ) {
				// This row is already open - close it
				row.child.hide();
				tr.removeClass('shown');
			}
			else {
				// Open this row
				row.child( format_cookie(row.data()) ).show();
				tr.addClass('shown');
			}
		} );
      $('#cookies tbody').on( 'click', 'td.delete_button', function () {
         //var idx = table.row(this).index();
         //var data = table.cell( idx, 1).data();
         table.row(this).remove().draw( false );
         $('#cookies_save').removeAttr("disabled");
      });     

	} );
</script>

<!-- HEADERS -->
<script>

	function format_header ( d ) {
		var wildcardOrder = "N/A";
		var urlNormalization ="<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var percentDecoding ="<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var normalizationViolations = "<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var htmlNormalization = "<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var decodeValueAsBase64 = "<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var mandatory = "<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var signatures = "N/A";
		

		if ("urlNormalization" in d)
			if (d.urlNormalization == true)
				urlNormalization = "<i class='fa fa-check-square-o fa-2x green'></i>";

			
		if ("percentDecoding" in d)
			if (d.percentDecoding == true)
				percentDecoding = "<i class='fa fa-check-square-o fa-2x green'></i>";

		if ("normalizationViolations" in d)
			if (d.normalizationViolations == true)
				normalizationViolations = "<i class='fa fa-check-square-o fa-2x green'></i>";
					
		if ("htmlNormalization" in d)
			if (d.htmlNormalization == true)
				htmlNormalization = "<i class='fa fa-check-square-o fa-2x green'></i>";

		if ("mandatory" in d)
			if (d.htmlNormalization == true)
				htmlNormalization = "<i class='fa fa-check-square-o fa-2x green'></i>";				
						
		if ("wildcardOrder" in d)
			wildcardOrder = d.wildcardOrder



		if ("signatureOverrides" in d)
		{
			var signatures = "";
			for(var j in d.signatureOverrides){
				var sub_key = j;
				var sub_val = d.signatureOverrides[j];
			   
            // I need to check with TAGS if it can work
            //if (sub_key == "tag")
				//	signatures = signatures + '"name" : <b> "' + sub_val.name + '" </b> - ' + '"Tag" : <b> "' + sub_val.tag + '"</b><br>';
				//else
            signatures = signatures + 'SignatureID : <b> ' + sub_val.signatureId + ' </b> - ' + 'Enabled: <b> ' + sub_val.enabled + '</b><br>';
				}				
		}

		return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered subtable">'+
		'<tr>'+
				'<td style="width:250px; background-color:#eaecf0"><b>Signature Overrides:</b></td>'+
				'<td >'+signatures+'</td>'+
			'</tr>'+
			'<tr>'+
				'<td style="width:250px; background-color:#eaecf0"><b>mandatory:</b></td>'+
				'<td >'+mandatory+'</td>'+
			'</tr>'+
			'<tr>'+
				'<td style="width:250px; background-color:#eaecf0"><b>htmlNormalization:</b></td>'+
				'<td >'+htmlNormalization+'</td>'+
			'</tr>'+
			'<tr>'+
				'<td style="width:250px; background-color:#eaecf0"><b>normalizationViolations:</b></td>'+
				'<td >'+normalizationViolations+'</td>'+
			'</tr>'+
			'<tr>'+
				'<td style="width:250px; background-color:#eaecf0"><b>percentDecoding:</b></td>'+
				'<td >'+percentDecoding+'</td>'+
			'</tr>'+
			'<tr>'+
				'<td style="width:250px; background-color:#eaecf0"><b>urlNormalization:</b></td>'+
				'<td >'+urlNormalization+'</td>'+
			'</tr>'+			
			'<tr>'+
				'<td style="width:250px; background-color:#eaecf0"><b>wildcardOrder:</b></td>'+
				'<td >'+wildcardOrder+'</td>'+
			'</tr></table>';

		}	
 

 
	$(document).ready(function() {
		var table = $('#headers').DataTable( {
		"data": headers,
      "order": [[2, 'desc']],
		"createdRow": function( row, data, dataIndex ) {
			if ("signatureOverrides" in data)
					$('td', row).eq(4).html(data.signatureOverrides.length);
				else
					$('td', row).eq(4).html("0");         
			if ( data['checkSignatures'] == true )
			  $('td', row).eq(3).html("<i class='fa fa-check-circle fa-2x green'></i>");
			else 
			  $('td', row).eq(3).html("<i class='fa fa-minus-circle fa-2x red' ></i>");

         $('td', row).eq(5).html("<i class='fa fa-edit'></i>");  
         $('td', row).eq(6).html("<i class='fa fa-trash'></i> ");  
	  
		  },
        "columnDefs": [
            {target: 5,visible: false,searchable: false},
            {target: 6,visible: false,searchable: false}
            ],        
		  "columns": [
		    { "className": 'details-control', "orderable": false, "data": null, "defaultContent": ''},
			{ "className": 'bold',"data": "name", "defaultContent": '' },
			{ "className": 'attacks',"data": "type", "defaultContent": ''},
			{ "className": 'attacks',"data": "checkSignatures", "defaultContent": ''},
			{ "className": 'attacks',"data": "num_of_sign_overides", "defaultContent": 0},
			{"className": 'edit_button',"data": null, "orderable":false},
			{"className": 'delete_button',"data": null, "orderable":false}
			],
			"autoWidth": false,
			"processing": true,
			"language": {"processing": "Waiting.... " },
			"order": [[1, 'asc']]
		} );	

		$('#headers tbody').on('click', 'td.details-control', function () {
			var tr = $(this).closest('tr');
			var row = table.row( tr );
	
			if ( row.child.isShown() ) {
				// This row is already open - close it
				row.child.hide();
				tr.removeClass('shown');
			}
			else {
				// Open this row
				row.child( format_header(row.data()) ).show();
				tr.addClass('shown');
			}
		} );
      $('#headers tbody').on( 'click', 'td.delete_button', function () {
         //var idx = table.row(this).index();
         //var data = table.cell( idx, 1).data();
         table.row(this).remove().draw( false );
         $('#headers_save').removeAttr("disabled");
      });   
	} );
</script>

<!-- JSON Profiles -->
<script>

	function format_json_profiles ( d ) {
		var handleJsonValuesAsParameters ="<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var hasValidationFiles = "<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var validationFiles = "N/A";
		var maximumArrayLength ="<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var maximumStructureDepth ="<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var maximumTotalLengthOfJSONData ="<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var maximumValueLength ="<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var tolerateJSONParsingWarnings ="<i class='fa fa-minus-square-o fa-2x red' ></i>";


		if ("handleJsonValuesAsParameters" in d)
			if (d.handleJsonValuesAsParameters == true)
				handleJsonValuesAsParameters = "<i class='fa fa-check-square-o fa-2x green'></i>";
		if ("hasValidationFiles" in d)
			if (d.hasValidationFiles == true)
				hasValidationFiles = "<i class='fa fa-check-square-o fa-2x green'></i>";
      if ("tolerateJSONParsingWarnings" in d.defenseAttributes)
			if (d.tolerateJSONParsingWarnings == true)
            tolerateJSONParsingWarnings = "<i class='fa fa-check-square-o fa-2x green'></i>";	

		if (d.validationFiles.length >11110)
		{

		}	

	
		return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered subtable">'+

         '<tr>'+
				'<td style="width:250px; background-color:#eaecf0; font-weight:bold"><b>Maximum Array Length:</b></td>'+
				'<td >'+d.defenseAttributes.maximumArrayLength+'</td>'+
				'<td style="width:250px; background-color:#eaecf0; font-weight:bold"><b>Tolerate JSON Parsing Warnings:</b></td>'+
				'<td >'+tolerateJSONParsingWarnings+'</td>'+            
			'</tr>'+ 
			'<tr>'+
				'<td style="width:250px; background-color:#eaecf0; font-weight:bold"><b>Maximum Structure Depth:</b></td>'+
				'<td >'+d.defenseAttributes.maximumStructureDepth+'</td>'+
				'<td style="width:250px; background-color:#eaecf0; font-weight:bold"><b>Handle JsonValues As Parameters:</b></td>'+
				'<td >'+handleJsonValuesAsParameters+'</td>'+            
			'</tr>'+ 
         '<tr>'+
				'<td style="width:250px; background-color:#eaecf0; font-weight:bold"><b>Maximum Total Length Of JSON Data:</b></td>'+
				'<td >'+d.defenseAttributes.maximumTotalLengthOfJSONData+'</td>'+
				'<td style="width:250px; background-color:#eaecf0; font-weight:bold"><b>Has Validation Files:</b></td>'+
				'<td >'+hasValidationFiles+'</td>'+            
			'</tr>'+ 
         '<tr>'+
				'<td style="width:250px; background-color:#eaecf0; font-weight:bold"><b>Maximum Value Length:</b></td>'+
				'<td >'+d.defenseAttributes.maximumValueLength+'</td>'+
			'</tr>'+ 
			'</table>';
	}

	$(document).ready(function() {
		var table = $('#json_profiles').DataTable( {
			"data": json_profiles,
         "order": [[1, 'desc']],
			"createdRow": function( row, data, dataIndex ) {
				if ( data['attackSignaturesCheck'] == true )
					$('td', row).eq(3).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				 	$('td', row).eq(3).html("<i class='fa fa-minus-circle fa-2x red' ></i>");

				if ( data['metacharElementCheck'] == true )
					$('td', row).eq(4).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
					$('td', row).eq(4).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
				if ("signatureOverrides" in data)
					$('td', row).eq(5).html(data.signatureOverrides.length);
				else
					$('td', row).eq(5).html("0");
				if ("metacharOverrides" in data)
					$('td', row).eq(6).html(data.metacharOverrides.length);
				else
					$('td', row).eq(6).html("0");
            $('td', row).eq(7).html("<i class='fa fa-edit'></i> ");  
            $('td', row).eq(8).html("<i class='fa fa-trash'></i> ");  
			  },
           "columnDefs": [
            {target: 7,visible: false,searchable: false},
            {target: 8,visible: false,searchable: false}
            ],           
			  "columns": [
				{"className":'details-control',"orderable":false,"data":null,"defaultContent": ''},		
				{"className":'bold',"data": "name"},
				{"className":'bold',"data": "description"},
				{"className":'attacks',"data": "attackSignaturesCheck", "defaultContent": true },
				{"className":'attacks',"data": "metacharElementCheck", "defaultContent": true },
				{"className":'attacks',"data": "num_of_sig_overrides", "defaultContent": 0 },
				{"className":'attacks',"data": "num_of_meta_overrides", "defaultContent": 0 },
				{"className": 'edit_button',"data": null, "orderable":false},
				{"className": 'delete_button',"data": null, "orderable":false}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );
		$('#json_profiles tbody').on('click', 'td.details-control', function () {
			var tr = $(this).closest('tr');
			var row = table.row( tr );
	
			if ( row.child.isShown() ) {
				// This row is already open - close it
				row.child.hide();
				tr.removeClass('shown');
			}
			else {
				// Open this row
				row.child( format_json_profiles(row.data()) ).show();
				tr.addClass('shown');
			}
		} );
      $('#json_profiles tbody').on( 'click', 'td.delete_button', function () {
         //var idx = table.row(this).index();
         //var data = table.cell( idx, 1).data();
         table.row(this).remove().draw( false );
         $('#json_profiles_save').removeAttr("disabled");
      });   
	} );
</script>

<!--JSON validation files -->
<script>

	$(document).ready(function() {
		var table = $('#json_validation_files').DataTable( {
			"data": json_validation_files,	
			"columns": [
				{"className": 'bold',"data": "fileName" },
				{"className": 'attacks',"data": "allowed", "defaultContent": "<a href='#'><i class='fa fa-search'></i></a>"},
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );	

	} );
</script>

<!-- XML Profiles -->
<script>

	function format_xml_profiles ( d ) {
		var allowCDATA ="<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var allowDTDs = "<i class='fa fa-minus-square-o fa-2x red' ></i>";
		var allowExternalReferences ="<i class='fa fa-minus-square-o  fa-2x red' ></i>";
      var allowProcessingInstructions ="<i class='fa fa-minus-square-o  fa-2x red' ></i>";
		var tolerateCloseTagShorthand = "<i class='fa fa-minus-square-o  fa-2x red' ></i>";
		var tolerateLeadingWhiteSpace = "<i class='fa fa-minus-square-o  fa-2x red' ></i>";
		var tolerateNumericNames = "<i class='fa fa-minus-square-o  fa-2x red' ></i>";

      if ("allowCDATA" in d.defenseAttributes)
			if (d.defenseAttributes.allowCDATA == true)
            allowCDATA = "<i class='fa fa-check-square-o fa-2x green'></i>";
      if ("allowDTDs" in d.defenseAttributes)
			if (d.defenseAttributes.allowDTDs == true)
            allowDTDs = "<i class='fa fa-check-square-o fa-2x green'></i>";
      if ("allowExternalReferences" in d.defenseAttributes)
			if (d.defenseAttributes.allowExternalReferences == true)
            allowExternalReferences = "<i class='fa fa-check-square-o fa-2x green'></i>";
		if ("allowProcessingInstructions" in d.defenseAttributes)
			if (d.defenseAttributes.allowProcessingInstructions == true)
            allowProcessingInstructions = "<i class='fa fa-check-square-o fa-2x green'></i>";
      if ("tolerateCloseTagShorthand" in d.defenseAttributes)
			if (d.defenseAttributes.tolerateCloseTagShorthand == true)
            tolerateCloseTagShorthand = "<i class='fa fa-check-square-o fa-2x green'></i>";
      if ("tolerateLeadingWhiteSpace" in d.defenseAttributes)
			if (d.defenseAttributes.tolerateLeadingWhiteSpace == true)
            tolerateLeadingWhiteSpace = "<i class='fa fa-check-square-o fa-2x green'></i>";
      if ("tolerateNumericNames" in d.defenseAttributes)
			if (d.defenseAttributes.tolerateNumericNames == true)
            tolerateNumericNames = "<i class='fa fa-check-square-o fa-2x green'></i>";
	
		return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered subtable">'+

			'<tr>'+
            '<td style="width:250px; background-color:#eaecf0; font-weight:bold">Maximum Attribute Value Length:</td>'+
				'<td >'+d.defenseAttributes.maximumAttributeValueLength+'</td>'+
				'<td style="width:250px; background-color:#eaecf0; font-weight:bold">Allow CDATA:</td>'+
				'<td >'+allowCDATA+'</td>'+
         '</tr>'+
			'<tr>'+
            '<td style="width:250px; background-color:#eaecf0; font-weight:bold">Maximum Attributes Per Element:</td>'+
				'<td >'+d.defenseAttributes.maximumAttributesPerElement+'</td>'+
				'<td style="width:250px; background-color:#eaecf0; font-weight:bold">Allow DTDs:</td>'+
				'<td >'+allowDTDs+'</td>'+            
			'</tr>'+         
			'<tr>'+
            '<td style="width:250px; background-color:#eaecf0; font-weight:bold">Maximum Children Per Element:</td>'+
				'<td >'+d.defenseAttributes.maximumChildrenPerElement+'</td>'+
				'<td style="width:250px; background-color:#eaecf0; font-weight:bold">Allow External References:</td>'+
				'<td >'+allowExternalReferences+'</td>'+
			'</tr>'+
			'<tr>'+
            '<td style="width:250px; background-color:#eaecf0; font-weight:bold">Maximum Document Depth:</td>'+
				'<td >'+d.defenseAttributes.maximumDocumentDepth+'</td>'+
				'<td style="width:250px; background-color:#eaecf0; font-weight:bold">Allow Processing Instructions:</td>'+
				'<td >'+allowProcessingInstructions+'</td>'+            
			'</tr>'+  
			'<tr>'+
            '<td style="width:250px; background-color:#eaecf0; font-weight:bold">Maximum Document Size:</td>'+
				'<td >'+d.defenseAttributes.maximumDocumentSize+'</td>'+
            '<td style="width:250px; background-color:#eaecf0; font-weight:bold">Tolerate Close Tag Shorthand:</td>'+
				'<td >'+tolerateCloseTagShorthand+'</td>'+            
			'</tr>'+
			'<tr>'+
            '<td style="width:250px; background-color:#eaecf0; font-weight:bold">Maximum Elements:</td>'+
				'<td >'+d.defenseAttributes.maximumElements+'</td>'+
            '<td style="width:250px; background-color:#eaecf0; font-weight:bold">Tolerate Leading WhiteSpace:</td>'+
				'<td >'+tolerateLeadingWhiteSpace+'</td>'+            
			'</tr>'+  
			'<tr>'+
            '<td style="width:250px; background-color:#eaecf0; font-weight:bold">Maximum NS Declarations:</td>'+
				'<td >'+d.defenseAttributes.maximumNSDeclarations+'</td>'+
            '<td style="width:250px; background-color:#eaecf0; font-weight:bold">Tolerate Numeric Names:</td>'+
				'<td >'+tolerateNumericNames+'</td>'+            
			'</tr>'+
			'<tr>'+
            '<td style="width:250px; background-color:#eaecf0; font-weight:bold">Maximum Name Length:</td>'+
				'<td >'+d.defenseAttributes.maximumNameLength+'</td>'+
			'</tr>'+
			'<tr>'+
            '<td style="width:250px; background-color:#eaecf0; font-weight:bold">Maximum Namespace Length:</td>'+
				'<td >'+d.defenseAttributes.maximumNamespaceLength+'</td>'+
			'</tr>'+
			'</table>';
	}

	$(document).ready(function() {
		var table = $('#xml_profiles').DataTable( {
			"data": xml_profiles,
         "order": [[1, 'desc']],
			"createdRow": function( row, data, dataIndex ) {
				if ( data['attackSignaturesCheck'] == true )
					$('td', row).eq(3).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				 	$('td', row).eq(3).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
				if ("signatureOverrides" in data)
					$('td', row).eq(4).html(data.signatureOverrides.length);
				else
					$('td', row).eq(4).html("0");

            $('td', row).eq(5).html("<i class='fa fa-edit'>"); 
            $('td', row).eq(6).html("<i class='fa fa-trash' ></i> "); 
         },
			  "columns": [
				{"className":'details-control',"orderable":false,"data":null,"defaultContent": ''},		
				{"className":'bold',"data": "name"},
				{"className":'bold',"data": "description", "defaultContent": '-' },
				{"className":'attacks',"data": "attackSignaturesCheck", "defaultContent": true },
				{"className":'attacks',"data": "num_of_sig_overrides", "defaultContent": 0 },
				{"className": 'edit_button',"data": null, "orderable":false},
				{"className": 'delete_button',"data": null, "orderable":false}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );
		$('#xml_profiles tbody').on('click', 'td.details-control', function () {
			var tr = $(this).closest('tr');
			var row = table.row( tr );
	
			if ( row.child.isShown() ) {
				// This row is already open - close it
				row.child.hide();
				tr.removeClass('shown');
			}
			else {
				// Open this row
				row.child( format_xml_profiles(row.data()) ).show();
				tr.addClass('shown');
			}
		} );
      $('#xml_profiles tbody').on( 'click', 'td.delete_button', function () {
         //var idx = table.row(this).index();
         //var data = table.cell( idx, 1).data();
         table.row(this).remove().draw( false );
         $('#xml_profiles_save').removeAttr("disabled");
      });   
	} );
</script>


<!-- METHODS-->
<script>
	$(document).ready(function() {
		var table = $('#methods').DataTable( {
			"data": methods,
			"searching": false,
         "order": [[0, 'desc']],
			"info": false,
			"createdRow": function( row, data, dataIndex ) {
            $('td', row).eq(1).html("<i class='fa fa-trash' ></i> ");  
			},
         "columnDefs": [
            {target: 1,visible: false,searchable: false}
         ],         
			"columns": [
				{ "className": 'bold',"data": "name" },
				{"className": 'delete_button',"data": null, "orderable":false}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );	
      $('#methods tbody').on( 'click', 'td.delete_button', function () {
         //var idx = table.row(this).index();
         //var data = table.cell( idx, 1).data();
         table.row(this).remove().draw( false );
         $('#methods_save').removeAttr("disabled");
      });       
	} );
</script>


<!-- Allowed Response codes-->
<script>
	$(document).ready(function() {
		var table = $('#response_codes').DataTable( {
			"data": allowed_response_codes_table,
			"searching": false,
         "order": [[0, 'desc']],
			"info": false,
			"createdRow": function( row, data, dataIndex ) {
            $('td', row).eq(1).html("<i class='fa fa-trash' ></i> ");  
			},
         "columnDefs": [
               {target: 1,visible: false,searchable: false,},
         ],	                  
			"columns": [
				{"className": 'bold', "data": "name"},
            {"className": 'delete_button',"data": null, "orderable":false}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );
      $('#response_codes tbody').on( 'click', 'td.delete_button', function () {
         //var idx = table.row(this).index();
         //var data = table.cell( idx, 1).data();
         table.row(this).remove().draw( false );
         $('#response_save').removeAttr("disabled");
      });          
	} );
</script>	

<!-- CSRF -->
<script>

	$(document).ready(function() {
		var table = $('#csrf').DataTable( {
			"data": csrf,
			"searching": false,
			"info": false,
			"columns": [
				{ "className":'bold',"data": "url" },
				{ "className":'attacks',"data": "method", "defaultContent": "N/A" },
				{ "className":'attacks',"data": "enforcementAction", "defaultContent": "N/A"},
				{ "className":'bold',"attacks": "wildcardOrder", "defaultContent": "N/A" }
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );	

	} );
</script>


<!-- RESPONSE PAGES -->
<script>

   function format_response_pages ( d ) {

      skip =  ["responsePageType"];
      var table= "";
      for(var i in d){
         var key = i;
         var val = d[i];
         if(val === false)
            val = '<i class="fa fa-minus-square-o fa-2x red" ></i>';
         if(val === true)
            val = '<i class="fa fa-check-square-o fa-2x green"></i>';

         if (!skip.includes(key))
         {
            table = table + '<tr>'+
                        '<td style="width:250px; background-color:#eaecf0"><b>'+key+':</b></td>'+
                        '<td colspan=5>'+val+'</td>'+
                     '</tr>';

         }
      }

         return '<table cellpadding="5" cellspacing="0" border="0" class="table table-bordered subtable">'+
                     table +
                  '</table>';
      }

	$(document).ready(function() {
		var table = $('#response_pages').DataTable( {
			"data": response_pages,
         "order": [[1, 'desc']],
			"createdRow": function( row, data, dataIndex ) {
				if ( data['attackSignaturesCheck'] == true )
					$('td', row).eq(3).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				 	$('td', row).eq(3).html("<i class='fa fa-minus-circle fa-2x red' ></i>");
				if ("signatureOverrides" in data)
					$('td', row).eq(4).html(data.signatureOverrides.length);
				else
					$('td', row).eq(4).html("0");

            $('td', row).eq(5).html("<i class='fa fa-edit'>"); 
            $('td', row).eq(6).html("<i class='fa fa-trash' ></i> "); 
         },
         "columns": [
         {"className":'details-control',"orderable":false,"data":null,"defaultContent": ''},		
         {"className":'bold',"data": "responsePageType"}
         ],
         "autoWidth": false,
         "processing": true,
         "language": {"processing": "Waiting.... " }
		} );
		$('#response_pages tbody').on('click', 'td.details-control', function () {
			var tr = $(this).closest('tr');
			var row = table.row( tr );
	
			if ( row.child.isShown() ) {
				// This row is already open - close it
				row.child.hide();
				tr.removeClass('shown');
			}
			else {
				// Open this row
				row.child( format_response_pages(row.data()) ).show();
				tr.addClass('shown');
			}
		} );
      $('#response_pages tbody').on( 'click', 'td.delete_button', function () {
         //var idx = table.row(this).index();
         //var data = table.cell( idx, 1).data();
         table.row(this).remove().draw( false );
      });   
	} );

</script>


<!-- Bot Defense -->
<script>

	$(document).ready(function() {
		var table = $('#bot_defense_classes').DataTable( {
			"data": bot_defense_classes,
			"searching": false,
			"info": false,
			"createdRow": function( row, data, dataIndex ) {
  
			if ( data['action'] == "block" )
				$('td', row).eq(1).html("<span class='red'><b>Block</span>");
			if ( data['action'] == "alarm" ) 
				$('td', row).eq(1).html("<span class='orange'><b>Alarm</b></span>");
			if ( data['action'] == "detect" ) 
				$('td', row).eq(1).html("<span class='green'><b>Detect Only</b></span>");
			if ( data['action'] == "ignore" ) 
				$('td', row).eq(1).html("<span class='blue'><b>Ignore</b></span>");
			},			
			"columns": [
				{ "className": 'bold',"data": "name" },
				{ "className": 'bold',"data": "action" }
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );	

	} );
</script>

<!-- Whitelist IPs -->
<script>

	$(document).ready(function() {
		var table = $('#whitelist_ips').DataTable( {
			"data": whitelist_ips,
			//"searching": false,
			"info": false,
			"createdRow": function( row, data, dataIndex ) {
				if ( data['blockRequests'] == "always" )
				  $('td', row).eq(2).html("<span class='red'>Always</span>");
				if ( data['neverLogRequests'] == true )
					$('td', row).eq(3).html("<i class='fa fa-check-circle fa-2x green'></i>");
				else 
				  $('td', row).eq(3).html("<i class='fa fa-minus-circle fa-2x red ' ></i>");	
				if ( data['description'] == "" )
					$('td', row).eq(4).html("-");

			},
			"columns": [
				{ "className": 'bold',"data": "ipAddress" },
				{ "className": 'bold',"data": "ipMask" },
				{ "className": 'attacks',"data": "blockRequests" },
				{ "className": 'attacks',"data": "neverLogRequests", "defaultContent":false},
				{ "className": 'attacks',"data": "description", "defaultContent":"None" }
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );	

	} );
</script>

<!-- ServerTechnologies -->
<script>

	$(document).ready(function() {
		var table = $('#server_technologies').DataTable( {
			"data": server_technologies,
			//"searching": false,
			"info": false,
			"columns": [
				{ "className": 'bold',"data": "serverTechnologyName" }
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );	

	} );
</script>

<!-- Bot Defense Browser-->
<script>

	$(document).ready(function() {
		var table = $('#bot_defense_browsers').DataTable( {
			"data": bot_defense_browsers,
			"searching": false,
			"info": false,
			"createdRow": function( row, data, dataIndex ) {
  
			if ( data['action'] == "block" )
				$('td', row).eq(1).html("<span class='red'><b>Block</span>");
			if ( data['action'] == "alarm" ) 
				$('td', row).eq(1).html("<span class='orange'><b>Alarm</b></span>");
			if ( data['action'] == "detect" ) 
				$('td', row).eq(1).html("<span class='green'><b>Detect Only</b></span>");
			if ( data['action'] == "ignore" ) 
				$('td', row).eq(1).html("<span class='blue'><b>Ignore</b></span>");
			},			
			"columns": [
				{ "className": 'bold',"data": "name" },
				{ "className": 'attacks',"data": "action" },
				{ "className": 'attacks',"data": "minVersion", "defaultContent":"-"},
				{ "className": 'attacks',"data": "maxVersion", "defaultContent":"-"}
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );	

	} );
</script>

<!-- Bot Defense Signatures-->
<script>

	$(document).ready(function() {
		var table = $('#bot_defense_signatures').DataTable( {
			"data": bot_defense_signatures,
			"searching": false,
			"info": false,
			"createdRow": function( row, data, dataIndex ) {
  
			if ( data['action'] == "block" )
				$('td', row).eq(1).html("<span class='red'><b>Block</span>");
			if ( data['action'] == "alarm" ) 
				$('td', row).eq(1).html("<span class='orange'><b>Alarm</b></span>");
			if ( data['action'] == "detect" ) 
				$('td', row).eq(1).html("<span class='green'><b>Detect Only</b></span>");
			if ( data['action'] == "ignore" ) 
				$('td', row).eq(1).html("<span class='blue'><b>Ignore</b></span>");
			},			
			"columns": [
				{ "className": 'bold',"data": "name" },
				{ "className": 'attacks',"data": "action" }
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );	

	} );
</script>

<!-- Bot Defense Anomalies-->
<script>

	$(document).ready(function() {
		var table = $('#bot_defense_anomalies').DataTable( {
			"data": bot_defense_anomalies,
			"searching": false,
			"info": false,
			"createdRow": function( row, data, dataIndex ) {
  
			if ( data['action'] == "block" )
				$('td', row).eq(1).html("<span class='red'><b>Block</span>");
			if ( data['action'] == "alarm" ) 
				$('td', row).eq(1).html("<span class='orange'><b>Alarm</b></span>");
			if ( data['action'] == "detect" ) 
				$('td', row).eq(1).html("<span class='green'><b>Detect Only</b></span>");
			if ( data['action'] == "ignore" ) 
				$('td', row).eq(1).html("<span class='blue'><b>Ignore</b></span>");
			},			
			"columns": [
				{ "className": 'bold',"data": "name" },
				{ "className": 'attacks',"data": "action" },
				{ "className": 'attacks',"data": "scoreThreshold", "defaultContent":"-" }
				],
				"autoWidth": false,
				"processing": true,
				"language": {"processing": "Waiting.... " }
		} );	

	} );
</script>



<!-- -------------  Modals/Offcanvas   ------------------- -->

<script>
   $( ".btn-json" ).click(function() {
     
      $("#original_json_text").val("");
      $("#json_text").val("");
      $("#original_json_text").attr("disabled","disabled");
      $("#btn-submit").attr("disabled","disabled");
      var str_original = "[]";
      var entity = $(this).attr('id');
      if (entity == "file_types_json")
      {
         var title = "FileTypes";         
         var str = JSON.stringify(file_types, null, 3);
         var str_original = JSON.stringify(file_types_original, null, 3);
      } 
      if (entity == "evasion_json")
      {
         var title = "Evasion-Techniques";       
         var str = JSON.stringify(evasion, null, 3);
         var str_original = JSON.stringify(evasion_original, null, 3);           
      }      
      if (entity == "compliance_json")
      {
         var title = "HTTP-Compliance";         
         var str = JSON.stringify(compliance, null, 3);
         var str_original = JSON.stringify(compliance_original, null, 3);           
      }
      if (entity == "json_profile_json")
      {
         var title = "JSON-Profiles";
         var str = JSON.stringify(json_profiles, null, 3);
         var str_original = JSON.stringify(json_profiles_original, null, 3);           
      }
      if (entity == "xml_profile_json")
      {
         var title = "XML-Profiles";
         var str = JSON.stringify(xml_profiles, null, 3);
         var str_original = JSON.stringify(xml_profiles_original, null, 3);           
      }
      if (entity == "url_json")
      {
         var title = "URLs";
         var str = JSON.stringify(url, null, 3);
         var str_original = JSON.stringify(url_original, null, 3);           
      }
      if (entity == "csrf_url_json")
      {
         var title = "CSRF URLs";
         var str = JSON.stringify(csrf, null, 3);
         var str_original = JSON.stringify(csrf_original, null, 3);           
      }
      if (entity == "tc_json")
      {
         var title = "Threat-Campaigns";
         var str = JSON.stringify(threat_campaigns, null, 3);
         var str_original = JSON.stringify(threat_campaigns_original, null, 3);           
      }

      if (entity == "signature_sets_json")
      {
         var title = "Signature-Sets";
         var str = JSON.stringify(signature_sets, null, 3);
         var str_original = JSON.stringify(signature_sets_original, null, 3);
      }
      
      if (entity == "signatures_json")
      {
         var title = "Signatures";
         var str = JSON.stringify(signatures, null, 3);
         var str_original = JSON.stringify(signatures_original, null, 3);           
      }     

      if (entity == "response_codes_json")
      {
         var title = "Response-Codes";
         var str = JSON.stringify(allowedResponseCodes, null, 3);
         var str_original = JSON.stringify(allowedResponseCodes_original, null, 3);           
      }   
      if (entity == "methods_json")
      {
         var title = "Methods";
         var str = JSON.stringify(methods, null, 3);
         var str_original = JSON.stringify(methods_original, null, 3);           
      }   
      if (entity == "parameters_json")
      {
         var title = "Parameters";
         var str = JSON.stringify(parameters, null, 3);
         var str_original = JSON.stringify(parameters_original, null, 3);           
      }   
      if (entity == "sensitive_param_json")
      {
         var title = "Sensitive Parameters";
         var str = JSON.stringify(sensitive_param, null, 3);
         var str_original = JSON.stringify(sensitive_param_original, null, 3);           
      }  
      if (entity == "cookies_json")
      {
         var title = "Cookies";
         var str = JSON.stringify(cookies, null, 3);
         var str_original = JSON.stringify(cookies_original, null, 3);           
      }
      if (entity == "headers_json")
      {
         var title = "Headers";
         var str = JSON.stringify(headers, null, 3);
         var str_original = JSON.stringify(headers_original, null, 3);           
      }  
      if (entity == "blocking_json")
      {
         var title = "Violations";
         var str = JSON.stringify(violations, null, 3);
         var str_original = JSON.stringify(violations_original, null, 3);           
      }
      if (entity == "whitelist_json")
      {
         var title = "Whitelist-IPs";
         var str = JSON.stringify(whitelist_ips, null, 3);
         var str_original = JSON.stringify(whitelist_ips_original, null, 3);           
      }
      if (entity == "response_pages_json")
      {
         var title = "Response-Pages";
         var str = JSON.stringify(response_pages, null, 3);
         var str_original = JSON.stringify(response_pages_original, null, 3);           
      }
      if (entity == "bot_classes_json")
      {
         var title = "Bot-Defense-Classes";
         var str = JSON.stringify(bot_defense_classes, null, 3);
         var str_original = JSON.stringify(bot_defense_classes_original, null, 3);           
      }
      if (entity == "bot_browsers_json")
      {
         var title = "Bot-Defense-Browsers";
         var str = JSON.stringify(bot_defense_browsers, null, 3);
         var str_original = JSON.stringify(bot_defense_browsers_original, null, 3);           
      } 
      if (entity == "bot_signatures_json")
      {
         var title = "Bot-Defense-Signatures";
         var str = JSON.stringify(bot_defense_signatures, null, 3);
         var str_original = JSON.stringify(bot_defense_signatures_original, null, 3);           
      } 
      if (entity == "bot_anomalies_json")
      {
         var title = "Bot-Defense-Anomalies";
         var str = JSON.stringify(bot_defense_anomalies, null, 3);
         var str_original = JSON.stringify(bot_defense_anomalies_original, null, 3);           
      }             
      if (entity == "json_file_json")
      {
         var title = "JSON-Validation-Files";
         var str = JSON.stringify(json_validation_files, null, 3);
         var str_original = JSON.stringify(json_validation_files_original, null, 3);           
      }           
      if (entity == "server_technologies_json")
      {
         var title = "Server-Technologies";
         var str = JSON.stringify(server_technologies, null, 3);
         var str_original = JSON.stringify(server_technologies_original, null, 3);           
      }
      if (entity == "dataguard_json")
      {
         var title = "Dataguard";
         var str = JSON.stringify(dataguard, null, 3);
         var str_original = JSON.stringify(dataguard_original, null, 3);           
      }         

      $("#json_variable").val(title);
      $("#json_title").html("The configuration for <b><u>" + title + "</b></u> is:");
      $("#json_text").val(str);
      $("#original_json_text").val(str_original);      
   
   });
</script>

<!-- Update config -->

<script>
   $("#btn-submit").click(function() {

      var json_policy = $("#original_json_text").val();
      if (json_policy =="")
         json_policy ="[]";
      var payload = btoa(json_policy);
      var type = $("#json_variable").val();
      var policy = $("#policy_name").val();
      var format = "<?php echo $format; ?>";



      $("#change_results").show();
      $.ajax({
         method: "POST",
         url: "save-config-json.php",
         data: {
            type:type,
            policy: policy,
            config: payload,
            format: format
         }
      })
         .done(function(msg) {
            if(msg.completed_successfully)
            {
               $("#change_results").html(" <h5> Parsing completed <span style='color:green'>successfully</span>.</h5> ");
               if(msg.warnings.length>0)
               {
                  $("#change_results").append("<h7 style='color:red'> <b>Warnings:</b> <span style='color:red'>"+JSON.stringify(msg.warnings, null, 2)+"</span>. </h7><br>");
                  $("#change_results").append("<h7 style='color:orange'>The window will reload in 2 seconds</h7><br>");
                  
                  setTimeout(function() {
                     location.reload();
                  }, 2000); 
               }
               else
               {
                  $("#change_results").append("<h7 style='color:green'> <b>No Warnings</b></h7><br>");
                  $("#change_results").append("<h7 style='color:orange'>The window will reload in 2 seconds</h7><br>");
                  
                  setTimeout(function() {
                     location.reload();
                  }, 2000); 
               }               
            }
            else
            {
               if("error_message" in msg )
               {
                  $("#change_results").append("<h6> Parsing error:<span style='color:red'> "+msg.error_message+"</span>. </h6>");
               }
            }           
         })
         .fail(function(jqXHR, textStatus, Status) {
            $("#change_results").html("<h6>Error. Got <span style='color:red'> "+Status+"</span></h6>");            
         });
      });
</script>


<script>
   $("#btn-submit-general").click(function() {


      var type = $("#general_settings_key").val();
      var policy = $("#policy_name").val();
      var format = "<?php echo $format; ?>";

 
      if (type=="mask_credit_card" || type=="case_insensitive" || type=="botcasesensitive" || type=="csrf_protection" || type=="bot_protection" )
      {
         if($("#form_"+type).is(":checked"))
            payload = btoa("true");
         else
            payload = btoa("false");
      }
      if (type=="enforcement_mode")
      {
         if($("#form_"+type).is(":checked"))
            payload = btoa("blocking");
         else
            payload = btoa("transparent");
      }        
 
      if (type=="description" || type=="cookie_length" || type=="header_length")
         payload = btoa($("#form_"+type).val());


      if (type=="xff" || type=="xff_headers")
      {
         if($("#form_xff").is(":checked"))
         {   
            if($("#form_xff_headers").val()!="Not Configured")
               payload = btoa('{"xff":true, "xff_headers":"'+$("#form_xff_headers").val()+'"}');
            else
               payload = btoa('{"xff":true, "xff_headers":"none"}');
         }
         else
            payload = btoa('{"xff":false}');
      }

      if (type=="violations-item")
      {
         var description=$("#violation_form_description").val();
         var viol_name=$("#violation_form_name").val();
         
         if($("#violation_form_alarm").is(":checked"))
            var alarm=true;
         else
            var alarm=false;

         if($("#violation_form_block").is(":checked"))
            var block=true;
         else
            var block=false;  
         
         payload = btoa('{"name":"'+viol_name+'", "description":"'+description+'", "alarm":'+alarm+', "block":'+block+'}');
      }  
      if (type=="evasion-item")
      {
         var description=$("#evasion_form_description").val();
         
         if($("#evasion_form_enabled").is(":checked"))
            var enabled=true;
         else
            var enabled=false;

        
         payload = btoa('{"description":"'+description+'", "enabled":'+enabled+'}');
      }
      if (type=="compliance-item")
      {
         var description=$("#compliance_form_description").val();
         
         if($("#compliance_form_enabled").is(":checked"))
            var enabled=true;
         else
            var enabled=false;

        
         payload = btoa('{"description":"'+description+'", "enabled":'+enabled+'}');
      }        

      $("#change_results_general").show();
      $.ajax({
         method: "POST",
         url: "save-config.php",
         data: {
            type:type,
            policy: policy,
            config: payload,
            format: format
         }
      })
         .done(function(msg) {
            if(msg.completed_successfully)
            {
               $("#change_results_general").html(" <h5> Parsing completed <span style='color:green'>successfully</span>.</h5> ");
               if(msg.warnings.length>0)
               {
                  $("#change_results_general").append("<h7 style='color:red'> <b>Warnings:</b> <span style='color:red'>"+JSON.stringify(msg.warnings, null, 2)+"</span>. </h7><br>");
                  $("#change_results_general").append("<h7 style='color:orange'>The window will reload in 5 second</h7><br>");
                  
                  setTimeout(function() {
                     location.reload();
                  }, 5000); 
               }
               else
               {
                  $("#change_results_general").append("<h7 style='color:green'> <b>No Warnings</b></h7><br>");
                  $("#change_results_general").append("<h7 style='color:orange'>The window will reload in 1 second</h7><br>");
                  
                  setTimeout(function() {
                     location.reload();
                  }, 1000); 
               }               
            }
            else
            {
               if("error_message" in msg )
               {
                  $("#change_results_general").html("<h6> Parsing error:<span style='color:red'> "+msg.error_message+"</span>. </h6>");
               }
            }           
         })
         .fail(function(jqXHR, textStatus, Status) {
               $("#change_results_general").html("<h6>Error. Got <span style='color:red'> "+Status+"</span></h6>");            

         });
      });
</script>



<script>
   $("#deploy").click(function() {

      $("#change_results_sync").html(' <div class="alert alert-warning "><i class="fa fa-spinner fa-pulse fa-3x" style="float:left; margin-right:10px "></i><h6 style="margin-top:10px"> Please wait.. It can take up to 10 seconds.</h6></div>');
      var policy = $("#policy_name").val()
      var uuid = "<?php echo $uuid; ?>";
      var comment = btoa($("#git_comment").val());
      $("#change_results_sync").show();

      $.ajax({
         method: "POST",
         url: "sync-policy.php",
         data: {
            policy: policy,
            uuid: uuid,
            comment: comment
         }
      })
         .done(function(msg) {
            $("#change_results_sync").html(msg);
            $(".btn-sync").hide();
         })
         .fail(function(jqXHR, textStatus, Status) {
            $("#change_results_sync").html("<h6> Parsing error:<span style='color:red'> Undetermined Error </span>. </h6>");
         });
      });
</script>






