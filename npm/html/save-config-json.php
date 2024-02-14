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
   
	if( !(isset($_POST['policy'])) )
	{
         header("HTTP/1.1 500 Policy Not Defined");					
			exit();
	}
	else
	{
			$policy_name = $_POST['policy'];
	}
   
	if( !(isset($_POST['type'])) )
	{
         header("HTTP/1.1 500 Type not Defined");					
			exit();
	}
	else
	{
			$type = $_POST['type'];
	}
	if( !(isset($_POST['config'])) )
	{
         header("HTTP/1.1 500 Payload not Defined");					
			exit();
	}
	else
	{
			$payload = $_POST['config'];
	}
	if( !(isset($_POST['format'])) )
	{
         header("HTTP/1.1 500 Format not Defined");					
			exit();
	}
	else
	{
			$format = $_POST['format'];
	}   


   if ($format == "YAML" )  
      $file = "config_files/".$policy_name."/".str_replace(".yaml", ".json", $policy_name);
   else
      $file = "config_files/".$policy_name."/".$policy_name;

   $policy= file_get_contents($file);

   $policy_data = json_decode($policy, true);
   $new_data = json_decode(base64_decode($payload), true);


   if ($type == "Evasion-Techniques")
   {
      $policy_data["policy"]["blocking-settings"]["evasions"] = json_decode(base64_decode($payload),true);
   }
   if ($type == "HTTP-Compliance")
   {
      $policy_data["policy"]["blocking-settings"]["http-protocols"] = json_decode(base64_decode($payload),true);
   }
   if ($type == "Violations")
   {
      $policy_data["policy"]["blocking-settings"]["violations"] = json_decode(base64_decode($payload),true);
   }
   if ($type == "Headers")
   {
      $policy_data["policy"]["headers"] = json_decode(base64_decode($payload),true);
   }   
   if ($type == "Cookies")
   {
      $policy_data["policy"]["cookies"] = json_decode(base64_decode($payload),true);
   }      
   if ($type == "Sensitive Parameters")
   {
      $policy_data["policy"]["sensitive-parameters"] = json_decode(base64_decode($payload),true);
   }
   if ($type == "Parameters")
   {
      $policy_data["policy"]["parameters"] = json_decode(base64_decode($payload),true);
   }
   if ($type == "Methods")
   {
      $policy_data["policy"]["methods"] = json_decode(base64_decode($payload),true);
   }
   if ($type == "Signatures")
   {
      $policy_data["policy"]["signatures"] = json_decode(base64_decode($payload),true);
   }      
   if ($type == "Signature-Sets")
   {
      $policy_data["policy"]["signature-sets"] = json_decode(base64_decode($payload),true);
   }  
   if ($type == "Threat-Campaigns")
   {
      $policy_data["policy"]["threat-campaigns"] = json_decode(base64_decode($payload),true);
   }
   if ($type == "XML-Profiles")
   {
      $policy_data["policy"]["xml-profiles"] = json_decode(base64_decode($payload),true);
   }
   if ($type == "JSON-Profiles")
   {
      $policy_data["policy"]["json-profiles"] = json_decode(base64_decode($payload),true);
   }
   if ($type == "URLs")
   {
      $policy_data["policy"]["urls"] = json_decode(base64_decode($payload),true);
   }
   if ($type == "FileTypes")
   {
      $policy_data["policy"]["filetypes"] = json_decode(base64_decode($payload),true);
   }
   if ($type == "Response-Codes")
   {
      $policy_data["policy"]["general"]["allowedResponseCodes"] = json_decode(base64_decode($payload),true);
   }
   if ($type == "Whitelist-IPs")
   {
      $policy_data["policy"]["whitelist-ips"] = json_decode(base64_decode($payload),true);
   }
   if ($type == "Response-Pages")
   {
      $policy_data["policy"]["response-pages"] = json_decode(base64_decode($payload),true);
   }
   if ($type == "Bot-Defense-Browsers")
   {
      $policy_data["policy"]["bot-defense"]["mitigations"]["browsers"] = json_decode(base64_decode($payload),true);
   }
   if ($type == "Bot-Defense-Classes")
   {
      $policy_data["policy"]["bot-defense"]["mitigations"]["classes"] = json_decode(base64_decode($payload),true);
   }
   if ($type == "Bot-Defense-Signatures")
   {
      $policy_data["policy"]["bot-defense"]["mitigations"]["signatures"] = json_decode(base64_decode($payload),true);
   }
   if ($type == "Bot-Defense-Anomalies")
   {
      $policy_data["policy"]["bot-defense"]["mitigations"]["anomalies"] = json_decode(base64_decode($payload),true);
   }      
   if ($type == "JSON-Validation-Files")
   {
      $policy_data["policy"]["json-validation-files"]= json_decode(base64_decode($payload),true);
   }      
   if ($type == "Server-Technologies")
   {
      $policy_data["policy"]["server-technologies"]= json_decode(base64_decode($payload),true);
   }    
   if ($type == "Dataguard")
   {
      $policy_data["policy"]["data-guard"]= json_decode(base64_decode($payload),true);
   }  

   if ($type == "case_insensitive")
   {
      $policy_data["policy"]["caseInsensitive"]= json_decode(base64_decode($payload),true);
   }  
   if ($type == "botcasesensitive")
   {
      $policy_data["policy"]["bot-defense"]["settings"]["caseSensitiveHttpHeaders"]= json_decode(base64_decode($payload),true);
   }  
   if ($type == "mask_credit_card")
   {
      $policy_data["policy"]["general"]["maskCreditCardNumbersInRequest"]= json_decode(base64_decode($payload),true);
   }  

   if ($type == "csrf_protection")
   {
      $policy_data["policy"]["csrf-protection"]["enabled"] = json_decode(base64_decode($payload),true);
   }
   if ($type == "bot_protection")
   {
      $policy_data["policy"]["bot-defense"]["settings"]["isEnabled"] = json_decode(base64_decode($payload),true);
   }     
   if ($type == "enforcement_mode")
   {
      $policy_data["policy"]["enforcementMode"] = base64_decode($payload);
      $info_file= ("config_files/".$policy_name."/info.json");
      $info_content= file_get_contents($info_file);
      $new_info = json_decode($info_content, true);
      $new_info["enforcement"] = base64_decode($payload);
      file_put_contents($info_file,json_encode($new_info,JSON_PRETTY_PRINT));
   }
  
   if ($type == "description")
   {
      $policy_data["policy"]["description"] = base64_decode($payload);
   }  

   if ($type == "cookie_length")
   {
      $policy_data["policy"]["cookie-settings"]["maximumCookieHeaderLength"] = base64_decode($payload);
   }  

   if ($type == "header_length")
   {
      $policy_data["policy"]["header-settings"]["maximumHttpHeaderLength"] = base64_decode($payload);
   }  
 
   if ($type=="xff" || $type=="xff_headers")
   {
      $response = json_decode(base64_decode($payload),true);
      $policy_data["policy"]["general"]["trustXff"] = $response["xff"];

      if ($response["xff"])
      {
         if ($response["xff_headers"]!="none")
         {
            $policy_data["policy"]["general"]["customXffHeaders"] = explode(",",$response["xff_headers"]);
         }

      }
   }          

   $file_sync= ("config_files/".$policy_name."/sync");
   file_put_contents($file,json_encode($policy_data,JSON_PRETTY_PRINT));
   file_put_contents($file_sync,"yes");

   if ($format == "YAML" )  
   {
      $convert_2_yaml = 'python3 json2yaml.py '.$file;
      $command = escapeshellcmd($convert_2_yaml);
      $output = shell_exec($command);
   }


   $policy_location = getcwd()."/". $file;
   $policy_output = getcwd()."/config_files/".$policy_name."/policy-full-export.json";
	$convert_command = '/opt/app_protect/bin/convert-policy -i '.$policy_location.' -o '.$policy_output.' --full-export';
	
   $command = escapeshellcmd($convert_command);
	$output = shell_exec($command);

   header("Content-Type: application/json");
	echo $output;

?>

