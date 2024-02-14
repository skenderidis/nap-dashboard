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

   if ($type == "evasion-item")
   {
      $temp_data=json_decode(base64_decode($payload),true);
      $temp_array=[];
      $found=0;
      if(key_exists("evasions",$policy_data["policy"]["blocking-settings"]))
      {
         foreach($policy_data["policy"]["blocking-settings"]["evasions"] as $key)
         {
            if ($temp_data["description"]==$key["description"])
            {
               $found=1;
               $key["enabled"] = $temp_data["enabled"];
            }
            array_push($temp_array,$key); 
         }
         if ($found==0)
            array_push($policy_data["policy"]["blocking-settings"]["evasions"], json_decode(base64_decode($payload),true));
         else
            $policy_data["policy"]["blocking-settings"]["evasions"]=$temp_array;            
      }
      else
      {
         array_push($policy_data["policy"]["blocking-settings"]["evasions"], json_decode(base64_decode($payload),true));
      }
   }
   if ($type == "compliance-item")
   {
      $temp_data=json_decode(base64_decode($payload),true);
      $temp_array=[];
      $found=0;
      if(key_exists("http-protocols",$policy_data["policy"]["blocking-settings"]))
      {
         foreach($policy_data["policy"]["blocking-settings"]["http-protocols"] as $key)
         {
            if ($temp_data["description"]==$key["description"])
            {
               $found=1;
               $key["enabled"] = $temp_data["enabled"];
            }
            array_push($temp_array,$key); 
         }
         if ($found==0)
            array_push($policy_data["policy"]["blocking-settings"]["http-protocols"], json_decode(base64_decode($payload),true));
         else
            $policy_data["policy"]["blocking-settings"]["http-protocols"]=$temp_array;
      }
      else
      {
         array_push($policy_data["policy"]["blocking-settings"]["http-protocols"], json_decode(base64_decode($payload),true));
      }
   }

   if ($type == "violations-item")
   {
      $temp_data=json_decode(base64_decode($payload),true);
      $temp_array=[];
      $found=0;
      if(key_exists("violations",$policy_data["policy"]["blocking-settings"]))
      {
         foreach($policy_data["policy"]["blocking-settings"]["violations"] as $key)
         {
            if ($temp_data["name"]==$key["viol_name"])
            {
               $found=1;
               $key["alarm"] = $temp_data["alarm"];
               $key["block"] = $temp_data["block"];
            }
            array_push($temp_array,$key); 
         }
         if ($found==0)
            array_push($policy_data["policy"]["blocking-settings"]["violations"], json_decode(base64_decode($payload),true));
         else
            $policy_data["policy"]["blocking-settings"]["violations"]=$temp_array;            
      }
      else
      {
         array_push($policy_data["policy"]["blocking-settings"]["violations"], json_decode(base64_decode($payload),true));
      }
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

