import os
import json
import sys
import base64
import yaml


def is_json(myjson):
  try:
    json.load(myjson)
  except ValueError as e:
    return False
  return True

def key_exists(mod_json, key, value):
	x = 0
	exists = False
	for i in mod_json:
		if key in i:
			if i[key] == value:
				exists = True
				break;
		x = x + 1
	return (exists, x)

def value_exists(mod_json, value):
	x = 0
	exists = False
	for i in mod_json:
		if i == value:
			exists = True
			break;
		x = x + 1
	return (exists, x)

def check_value(mod_json, key, value):
	try:
		if mod_json[key] == value:
			return True
		else: 
			return False
	except (KeyError , TypeError):
		return False

def check_value_array(mod_json, key, value):
	try:
		if value.isnumeric():
			value = int(value)
		if value in mod_json[key]:
			return True
		else: 
			return False
	except (KeyError , TypeError):
		return False

def modify_http_evasion (policy_json, location, name, key_name, key_value):
	if "blocking-settings" in  policy_json["policy"] :
		if location in policy_json["policy"]["blocking-settings"]:
			exists, x = key_exists(policy_json["policy"]["blocking-settings"][location],"description",name)
			if exists :
				policy_json["policy"]["blocking-settings"][location][x][key_name] = key_value
			else:
				policy_json["policy"]["blocking-settings"][location].append(json.loads('{"description": "'+name+'","'+key_name+'": '+str(key_value)+'}'))
		else:
			policy_json["policy"]["blocking-settings"][location] = json.loads('[{"description": "'+name+'", "'+key_name+'": '+str(key_value)+'}]')
	else:
		policy_json["policy"]["blocking-settings"] = json.loads('{'+location+':[{"description": "'+name+'", "'+key_name+'": '+str(key_value)+'}]}') 
	
	return policy_json

def disable_violation(policy_json, viol_name, block, alarm):
	if "blocking-settings" in  policy_json["policy"] :
		if "violations" in policy_json["policy"]["blocking-settings"]:
			exists, x = key_exists(policy_json["policy"]["blocking-settings"]["violations"],"name",viol_name)
			if exists :
				policy_json["policy"]["blocking-settings"]["violations"][x]["block"] = block
				policy_json["policy"]["blocking-settings"]["violations"][x]["alarm"] = alarm
			else:
				policy_json["policy"]["blocking-settings"]["violations"].append(json.loads('{"name": "'+viol_name+'", "block":'+str(block).lower()+', "alarm":'+str(alarm).lower()+'}'))
		else:
			policy_json["policy"]["blocking-settings"]["violations"] = json.loads('[{"name": "'+viol_name+'", "block":'+str(block).lower()+', "alarm":'+str(alarm).lower()+'}]')
	else:
		policy_json["policy"]["blocking-settings"] = json.loads('{"violations":[{"name": "'+viol_name+'", "block":'+str(block).lower()+', "alarm":'+str(alarm).lower()+'}]}') 
	
	return policy_json, True, "<b>Success</b> Violation Modified"

def allow_reponse_code(policy_json, code):
	if "general" in  policy_json["policy"] :
		if "allowedResponseCodes" in  policy_json["policy"]["general"]:
			header_exists, x = value_exists(policy_json["policy"]["general"]["allowedResponseCodes"], code)
			if not header_exists :
				policy_json["policy"]["general"]["allowedResponseCodes"].append(json.loads('{'+code+'}'))
		else:
			policy_json["policy"]["general"]["allowedResponseCodes"] = json.loads('[{'+code+'}]')
	else:
		policy_json["policy"]["general"] = json.loads('{"allowedResponseCodes":[{'+code+'}]}') 

	return policy_json


#####   VIOL_BOT_CLIENT ######
def disable_bot_defense(policy_json):
	if "bot-defense" in  policy_json["policy"] :
		if "settings"	in policy_json["policy"]["bot-defense"]:
			if check_value(policy_json["policy"]["bot-defense"]["settings"], "isEnabled", False):
				return policy_json, False
			else:			
				policy_json["policy"]["bot-defense"]["settings"]["isEnabled"] = False
		else:
			policy_json["policy"]["bot-defense"]["settings"] = json.loads('{"isEnabled": false}')
	else:
		policy_json["policy"]["bot-defense"] = json.loads('{"settings":{"isEnabled": false}}')

	return policy_json, True
	
def modify_bot_class(policy_json, class_name, action):
	if "bot-defense" in policy_json["policy"] :
		if "mitigations" in policy_json["policy"]["bot-defense"]:
			if "classes" in policy_json["policy"]["bot-defense"]["mitigations"]:
				class_exists, x = key_exists(policy_json["policy"]["bot-defense"]["mitigations"]["classes"], "name", class_name)
				if class_exists:
					if check_value(policy_json["policy"]["bot-defense"]["mitigations"]["classes"][x], "action",action):
						return policy_json, False, "Bot Class is already set to "+action+"."
					else:
						policy_json["policy"]["bot-defense"]["mitigations"]["classes"][x]["action"] = action			
				else:
					policy_json["policy"]["bot-defense"]["mitigations"]["classes"].append(json.loads('{"action":"'+action+'","name":"'+class_name+'"}'))
			else:
				policy_json["policy"]["bot-defense"]["mitigations"]["classes"] = json.loads('[{"action":"'+action+'","name":"'+class_name+'"}]')
		else:
			policy_json["policy"]["bot-defense"]["mitigations"] = json.loads('{"classes":[{"action":"'+action+'","name":"'+class_name+'"}]}')
	else:
		policy_json["policy"]["bot-defense"] = json.loads('{"mitigations":{"classes":[{"action":"'+action+'","name":"'+class_name+'"}]}}') 
	
	return policy_json, True, "Success. Bot Class set to "+action+"."

def modify_bot_signature(policy_json, sinature_name, action):
	if "bot-defense" in policy_json["policy"] :
		if "mitigations" in policy_json["policy"]["bot-defense"]:
			if "signatures" in policy_json["policy"]["bot-defense"]["mitigations"]:
				class_exists, x = key_exists(policy_json["policy"]["bot-defense"]["mitigations"]["signatures"], "name", sinature_name)
				if class_exists:
					if check_value(policy_json["policy"]["bot-defense"]["mitigations"]["signatures"][x], "action", action):
						return policy_json, False,  "<b>Failed!</b>Bot Signature " + sinature_name +  "is already set to "+action+"."
					else:
						policy_json["policy"]["bot-defense"]["mitigations"]["signatures"][x]["action"] = action			
				else:
					policy_json["policy"]["bot-defense"]["mitigations"]["signatures"].append(json.loads('{"action":"'+action+'","name":"'+sinature_name+'"}'))
			else:
				policy_json["policy"]["bot-defense"]["mitigations"]["signatures"] = json.loads('[{"action":"'+action+'","name":"'+sinature_name+'"}]')
		else:
			policy_json["policy"]["bot-defense"]["mitigations"] = json.loads('{"signatures":[{"action":"'+action+'","name":"'+sinature_name+'"}]}')
	else:
		policy_json["policy"]["bot-defense"] = json.loads('{"mitigations":{"signatures":[{"action":"'+action+'","name":"'+sinature_name+'"}]}}') 
	
	return policy_json, True, "<b>Success!</b>. Bot Signature " + sinature_name +  " set to "+action+"."


#####  VIOL_METHOD ######

def illegal_method(policy_json, method):
	if "methods" in  policy_json["policy"] :
		method_exists, x = key_exists(policy_json["policy"]["methods"], "name", method)
		if method_exists:
			if check_value(policy_json["policy"]["methods"][x], "$action", "delete"):
				del policy_json["policy"]["methods"][x]["$action"]
				return policy_json, False, "<b>Success!</b> Method " + method + " allowed by removing $action=delete"
			else:
				return policy_json, False, "<b>Failed!</b>. Method " + method +  " already configured"
		else:
			policy_json["policy"]["methods"].append(json.loads('{"name": "'+method+'"}'))
	else:
		policy_json["policy"]["methods"] = json.loads('[{"name": "'+method+'"}]')
		
	return policy_json, True, "<b>Success!</b> Method " + method +  " added on the allowed list"

#####  VIOL_COOKIE_LENGTH ######

def cookie_length(policy_json, length):
	if length > 65536:
		length = 65536
	if length < 1:
		length = 1
	if "cookie-settings" in  policy_json["policy"] :
		if check_value(policy_json["policy"]["cookie-settings"], "maximumCookieHeaderLength", length):
			return policy_json, False, "<strong>Error!</strong>. Same Cookie length is already configured"
		else:		
			policy_json["policy"]["cookie-settings"]["maximumCookieHeaderLength"] = length
	else:
		policy_json["policy"]["cookie-settings"] = json.loads('{"maximumCookieHeaderLength": '+str(length)+'}')
		
	return policy_json, True, "<strong>Success!</strong>. Cookie length: "+ str(length)+" is configured"

#####  VIOL_HEADER_LENGTH ######

def header_length(policy_json, length):
	if length > 65536:
		length = 65536
	if length < 1:
		length = 1

	if "header-settings" in  policy_json["policy"] :
		if check_value(policy_json["policy"]["header-settings"], "maximumHttpHeaderLength",length):
			return policy_json, False, "<strong>Error!</strong>. Same Header length is already configured"
		else:		
			policy_json["policy"]["header-settings"]["maximumHttpHeaderLength"] = length
	else:
		policy_json["policy"]["header-settings"] = json.loads('{"maximumHttpHeaderLength": '+str(length)+'}')

	return policy_json, True, "<strong>Success!</strong>. Header length: "+ str(length)+" is configured"

##### VIOL_HEADER_METACHAR, VIOL_PARAMETER_NAME_METACHAR, VIOL_PARAMETER_VALUE_METACHAR, VIOL_URL_METACHAR

def override_metachar_on_url(policy_json, entity_name, metachar, enabled):
	if "urls" in  policy_json["policy"] :
		entity_exists, x = key_exists(policy_json["policy"]["urls"], "name", entity_name)
		if entity_exists :
			if "metacharOverrides" in policy_json["policy"]["urls"][x]:
				metachar_exists, y = key_exists(policy_json["policy"]["urls"][x]["metacharOverrides"], "metachar", metachar)
				if metachar_exists :
					if check_value(policy_json["urls"][x]["metacharOverrides"][y],"isAllowed", enabled):
						return policy_json, False
					else:							
						policy_json["policy"]["urls"][x]["metacharOverrides"][y]["isAllowed"] = enabled
				else:
					policy_json["policy"]["urls"][x]["metacharOverrides"].append(json.loads('{"metachar":"'+metachar+'","isAllowed": '+str(enabled).lower()+'}'))
			else:
				policy_json["policy"]["urls"][x]["metacharOverrides"] = json.loads('[{"metachar":"'+metachar+'","isAllowed": '+str(enabled).lower()+'}]')

		else:
			policy_json["policy"]["urls"].append(json.loads('{"name":"'+entity_name+'","metacharOverrides":[{"metachar":"'+metachar+'","isAllowed":'+str(enabled).lower()+'}]}'))
	else:
		policy_json["policy"]["urls"] = json.loads('{"name":"'+entity_name+'","metacharOverrides":[{"metachar":"'+metachar+'","isAllowed": '+str(enabled).lower()+'}]}') 
	
	return policy_json, True

def override_metacharname_on_parameter(policy_json, entity_name, metachar, enabled):
	if "parameters" in  policy_json["policy"] :
		entity_exists, x = key_exists(policy_json["policy"]["parameters"], "name", entity_name)
		if entity_exists :
			if "nameMetacharOverrides" in policy_json["policy"]["parameters"][x]:
				metachar_exists, y = key_exists(policy_json["policy"]["parameters"][x]["nameMetacharOverrides"], "metachar", metachar)
				if metachar_exists :
					if check_value(policy_json["parameters"][x]["nameMetacharOverrides"][y],"isAllowed", enabled):
						return policy_json, False
					else:							
						policy_json["policy"]["parameters"][x]["nameMetacharOverrides"][y]["isAllowed"] = enabled
				else:
					policy_json["policy"]["parameters"][x]["nameMetacharOverrides"].append(json.loads('{"metachar":"'+metachar+'","isAllowed": '+str(enabled).lower()+'}'))
			else:
				policy_json["policy"]["parameters"][x]["nameMetacharOverrides"] = json.loads('[{"metachar":"'+metachar+'","isAllowed": '+str(enabled).lower()+'}]')

		else:
			policy_json["policy"]["parameters"].append(json.loads('{"name":"'+entity_name+'","nameMetacharOverrides":[{"metachar":"'+metachar+'","isAllowed":'+str(enabled).lower()+'}]}'))
	else:
		policy_json["policy"]["parameters"] = json.loads('{"name":"'+entity_name+'","nameMetacharOverrides":[{"metachar":"'+metachar+'","isAllowed": '+str(enabled).lower()+'}]}') 
	
	return policy_json, True

def override_metacharvalue_on_parameter(policy_json, entity_name, metachar, enabled):
	if "parameters" in  policy_json["policy"] :
		entity_exists, x = key_exists(policy_json["policy"]["parameters"], "name", entity_name)
		if entity_exists :
			if "valueMetacharOverrides" in policy_json["policy"]["parameters"][x]:
				metachar_exists, y = key_exists(policy_json["policy"]["parameters"][x]["valueMetacharOverrides"], "metachar", metachar)
				if metachar_exists :
					if check_value(policy_json["parameters"][x]["valueMetacharOverrides"][y],"isAllowed", enabled):
						return policy_json, False
					else:							
						policy_json["policy"]["parameters"][x]["valueMetacharOverrides"][y]["isAllowed"] = enabled
				else:
					policy_json["policy"]["parameters"][x]["valueMetacharOverrides"].append(json.loads('{"metachar":"'+metachar+'","isAllowed": '+str(enabled).lower()+'}'))
			else:
				policy_json["policy"]["parameters"][x]["valueMetacharOverrides"] = json.loads('[{"metachar":"'+metachar+'","isAllowed": '+str(enabled).lower()+'}]')

		else:
			policy_json["policy"]["parameters"].append(json.loads('{"name":"'+entity_name+'","valueMetacharOverrides":[{"metachar":"'+metachar+'","isAllowed":'+str(enabled).lower()+'}]}'))
	else:
		policy_json["policy"]["parameters"] = json.loads('{"name":"'+entity_name+'","valueMetacharOverrides":[{"metachar":"'+metachar+'","isAllowed": '+str(enabled).lower()+'}]}') 
	
	return policy_json, True

def override_metachar_global(policy_json, metachar, enabled):
	##### THIS IS WRONG ######
	##### THIS IS WRONG ######
	##### THIS IS WRONG ######
	if "character-sets" in  policy_json["policy"] :
		header_exists, x = key_exists(policy_json["policy"]["character-sets"], "metachar", metachar)
		if header_exists :
			policy_json["policy"]["character-sets"][x]["isAllowed"] = enabled
		else:
			policy_json["policy"]["character-sets"].append(json.loads('{"metachar": "'+metachar+'","isAllowed": '+str(enabled).lower()+'}'))
	else:
		policy_json["policy"]["character-sets"] = json.loads('{"metachar": "'+metachar+'","isAllowed": '+str(enabled).lower()+'}')
	
	return policy_json


##### VIOL_ATTACK_SIGNATURE

def override_signature_on_entity(policy_json, location, entity_name, signatureId, enabled):
	if location in  policy_json["policy"] :
		header_exists, x = key_exists(policy_json["policy"][location], "name", entity_name)
		if header_exists :
			if "signatureOverrides" in policy_json["policy"][location][x]:
				signature_exists, y = key_exists(policy_json["policy"][location][x]["signatureOverrides"], "signatureId", signatureId)
				if signature_exists :
					if check_value(policy_json["policy"][location][x]["signatureOverrides"][y], "enabled", enabled):
						return policy_json, False, "<strong>Error!</strong>. SignatureID: "+ str(signatureId)+" on entity " + entity_name+ " is already configured"
					else:					
						policy_json["policy"][location][x]["signatureOverrides"][y]["enabled"] = enabled
				else:
					policy_json["policy"][location][x]["signatureOverrides"].append(json.loads('{"signatureId": '+str(signatureId)+',"enabled": '+str(enabled).lower()+'}'))
			else:
				policy_json["policy"][location][x]["signatureOverrides"] = json.loads('[{"signatureId": '+str(signatureId)+',"enabled": '+str(enabled).lower()+'}]')
		else:
			policy_json["policy"][location].append(json.loads('{"name": "'+entity_name+'","signatureOverrides":[{"signatureId":'+str(signatureId)+',"enabled":'+str(enabled).lower()+'}]}'))
	else:
		policy_json["policy"][location] = json.loads('[{"name": "'+entity_name+'","signatureOverrides": [{"signatureId": '+str(signatureId)+',"enabled": '+str(enabled).lower()+'}]}]') 
	
	return policy_json, True, "<strong>Success!</strong>. SignatureID: "+ str(signatureId)+" on entity " + entity_name+ " is configured"

def override_signature_global(policy_json, signatureId, enabled):
	if "signatures" in  policy_json["policy"] :
		signature_exists, x = key_exists(policy_json["policy"]["signatures"], "signatureId", signatureId)
		if signature_exists :
			if check_value(policy_json["policy"]["signatures"][x], "signatureId", signatureId):
				return policy_json, False, "SignatureID: " + str(signatureId) + " already configured"
			else:
				policy_json["policy"]["signatures"][x]["enabled"] = enabled
		else:
			policy_json["policy"]["signatures"].append(json.loads('{"signatureId": '+str(signatureId)+',"enabled": '+str(enabled).lower()+'}'))
	else:
		policy_json["policy"]["signatures"] = json.loads('[{"signatureId": '+str(signatureId)+',"enabled": '+str(enabled).lower()+'}]')
	
	return policy_json, True, "<strong>Success!</strong>. SignatureID: "+ str(signatureId)+" configured"


##### VIOL_FILETYPE_LENGTH
def urllength(policy_json, name, length):
	if "filetypes" in policy_json["policy"] :
		filetype_exists, x = key_exists(policy_json["policy"]["filetypes"], "name", name)
		if filetype_exists:
			if check_value(policy_json["policy"]["filetypes"][x], "urlLength", length):
				return policy_json, False, "<b>Error!</b> Same URL Length already configured"
			else:
				policy_json["policy"]["filetypes"][x]["urlLength"] = length
				if not "checkUrlLength" in policy_json["policy"]["filetypes"][x]:
					policy_json["policy"]["filetypes"][x]["checkUrlLength"] = True					
		else:
			policy_json["policy"]["filetypes"].append(json.loads('{"name":"'+name+'","urlLength":'+str(length)+', "checkUrlLength":true}'))
	else:
		policy_json["policy"]["filetypes"] = json.loads('[{"name":"'+name+'","urlLength":'+str(length)+', "checkUrlLength":true}]')
	
	return policy_json, True, "<b>Success!</b> URL Length adjusted to <b>" +str(length) +"</b>"

def postdatalength(policy_json, name, length):
	if "filetypes" in policy_json["policy"] :
		filetype_exists, x = key_exists(policy_json["policy"]["filetypes"], "name", name)
		if filetype_exists:
			if check_value(policy_json["policy"]["filetypes"][x], "postDataLength", length):
				return policy_json, False, "<b>Error!</b> Same PostData Length already configured"
			else:
				policy_json["policy"]["filetypes"][x]["postDataLength"] = length
				if not "checkPostDataLength" in policy_json["policy"]["filetypes"][x]:
					policy_json["policy"]["filetypes"][x]["checkPostDataLength"] = True				
		else:
			policy_json["policy"]["filetypes"].append(json.loads('{"name":"'+name+'","postDataLength":'+str(length)+', "checkPostDataLength":true}'))
	else:
		policy_json["policy"]["filetypes"] = json.loads('[{"name":"'+name+'","postDataLength":'+str(length)+', "checkPostDataLength":true}]')
	
	return policy_json, True, "<b>Success!</b> PostData Length adjusted to <b>" +str(length) +"</b>"

def querystringlength(policy_json, name, length):
	if "filetypes" in policy_json["policy"] :
		filetype_exists, x = key_exists(policy_json["policy"]["filetypes"], "name", name)
		if filetype_exists:
			if check_value(policy_json["policy"]["filetypes"][x], "queryStringLength", length):
				return policy_json, False, "<b>Error!</b> Same QueryString Length already configured"
			else:
				policy_json["policy"]["filetypes"][x]["queryStringLength"] = length
				if not "checkQueryStringLength" in policy_json["policy"]["filetypes"][x]:
					policy_json["policy"]["filetypes"][x]["checkQueryStringLength"] = True
		else:
			policy_json["policy"]["filetypes"].append(json.loads('{"name":"'+name+'","queryStringLength":'+str(length)+', "checkQueryStringLength":true}'))
	else:
		policy_json["policy"]["filetypes"] = json.loads('[{"name":"'+name+'","queryStringLength":'+str(length)+', "checkQueryStringLength":true}]')
	
	return policy_json, True, "<b>Success!</b> QueryString Length adjusted to <b>" +str(length) +"</b>"

def requestlength(policy_json, name, length):
	if "filetypes" in policy_json["policy"] :
		filetype_exists, x = key_exists(policy_json["policy"]["filetypes"], "name", name)
		if filetype_exists:
			if check_value(policy_json["policy"]["filetypes"][x], "requestLength", length):
				return policy_json, False, "<b>Error!</b> Same RequestLength Length already configured"
			else:
				policy_json["policy"]["filetypes"][x]["requestLength"] = length
				if not "checkRequestLength" in policy_json["policy"]["filetypes"][x]:
					policy_json["policy"]["filetypes"][x]["checkRequestLength"] = True									
		else:
			policy_json["policy"]["filetypes"].append(json.loads('{"name":"'+name+'","requestLength":'+str(length)+', "checkRequestLength":true}'))
	else:
		policy_json["policy"]["filetypes"] = json.loads('[{"name":"'+name+'","requestLength":'+str(length)+', "checkRequestLength":true}]')
	
	return policy_json, True, "<b>Success!</b> RequestLength Length adjusted to <b>" +str(length) +"</b>"


##### VIOL_FILETYPE
def illegal_filetype(policy_json, name, enabled):
	if "filetypes" in policy_json["policy"] :
		filetype_exists, x = key_exists(policy_json["policy"]["filetypes"], "name", name)
		if filetype_exists:
			if check_value(policy_json["policy"]["filetypes"][x], "$action", "delete"):
				del policy_json["policy"]["filetypes"][x]["$action"]
				policy_json["policy"]["filetypes"][x]["allowed"] = enabled		
				return policy_json, False, "FileType allowed and $action=delete removed"
			if check_value(policy_json["policy"]["filetypes"][x], "allowed", enabled):
				return policy_json, False, "<b>Failed!</b> FileType " + name + " already set to allowed"
			else:
				policy_json["policy"]["filetypes"][x]["allowed"] = enabled			
		else:
			policy_json["policy"]["filetypes"].append(json.loads('{"name":"'+name+'","allowed":'+str(enabled).lower()+'}'))
	else:
		policy_json["policy"]["filetypes"] = json.loads('[{"name":"'+name+'","allowed":'+str(enabled).lower()+'}]')
	
	return policy_json, True, "<b> Success!</b> FileType " + name + " set to allowed"

##### VIOL_URL
def illegal_url(policy_json, name, enabled):
	if "urls" in policy_json["policy"] :
		url_exists, x = key_exists(policy_json["policy"]["urls"], "name", name)
		if url_exists:
			if check_value(policy_json["policy"]["urls"][x], "$action", "delete"):
				del policy_json["policy"]["urls"][x]["$action"]
				policy_json["policy"]["urls"][x]["allowed"] = enabled		
				return policy_json, False, "<b>Success!</b>URL allowed by removing the $action=delete"
			if check_value(policy_json["policy"]["urls"][x], "allowed", enabled):
				return policy_json, False, "<b>Failed!</b>URL already allowed"
			else:
				policy_json["policy"]["urls"][x]["allowed"] = enabled			
		else:
			policy_json["policy"]["urls"].append(json.loads('{"name":"'+name+'","allowed":'+str(enabled).lower()+'}'))
	else:
		policy_json["policy"]["urls"] = json.loads('[{"name":"'+name+'","allowed":'+str(enabled).lower()+'}]')
	
	return policy_json, True, "<b>Success!</b> URL added to the allowed list"


#####  VIOL_THREAT
def threat_campaigns(policy_json, name, enabled):
	if "threat-campaigns" in  policy_json["policy"] :
		exists, x = key_exists(policy_json["policy"]["threat-campaigns"], "name", name)
		if exists :
			if check_value(policy_json["policy"]["threat-campaigns"][x], "isEnabled", enabled):
				return policy_json, False, "Threat Campaign already Disabled"
			else:
				policy_json["policy"]["threat-campaigns"][x]["isEnabled"] = enabled
		else:
			policy_json["policy"]["threat-campaigns"].append(json.loads('{"name": "'+name+'", "isEnabled":'+str(enabled).lower()+'}'))
	else:
		policy_json["policy"]["threat-campaigns"] = json.loads('[{"name": "'+name+'", "isEnabled":'+str(enabled).lower()+'}]')
	
	return policy_json, True, "Threat Campaign Disabled"




##### VIOL_PARAMETERS


##### VIOL_PARAMETER_DATA_TYPE
##### VIOL_PARAMETER_LOCATION
##### VIOL_PARAMETER_REPEATED
##### Sensitive Parameter####
##### VIOL_MANDATORY_PARAMETER

##### VIOL_PARAMETER_EMPTY_VALUE
##### VIOL_PARAMETER
def illegal_parameter(policy_json, name, enabled):
	if "parameters" in policy_json["policy"] :
		parameter_exists, x = key_exists(policy_json["policy"]["parameters"], "name", name)
		if parameter_exists:
			if check_value(policy_json["policy"]["parameters"][x], "$action", "delete"):
				del policy_json["policy"]["parameters"][x]["$action"]
				policy_json["policy"]["parameters"][x]["allowed"] = enabled		
				return policy_json, False, "Parameter "+name+" allowed and $action=delete removed"
			if check_value(policy_json["policy"]["parameters"][x], "allowed", enabled):
				return policy_json, False, "Parameter "+name+" already allowed"
			else:
				policy_json["policy"]["parameters"][x]["allowed"] = enabled			
		else:
			policy_json["policy"]["parameters"].append(json.loads('{"name":"'+name+'","allowed":'+str(enabled).lower()+'}'))
	else:
		policy_json["policy"]["parameters"] = json.loads('[{"name":"'+name+'","allowed":'+str(enabled).lower()+'}]')
	
	return policy_json, True, "<b>Success!</b> Parameter "+name+" allowed"

def change_parameter_datatype(policy_json, name, value):
	if "parameters" in policy_json["policy"] :
		parameter_exists, x = key_exists(policy_json["policy"]["parameters"], "name", name)
		if parameter_exists:
			if check_value(policy_json["policy"]["parameters"][x], "datatype", value):
				return policy_json, False, "<b> Failed</b> Parameter datatype already configured"
			else:
				policy_json["policy"]["parameters"][x]["datatype"] = value			
		else:
			policy_json["policy"]["parameters"].append(json.loads('{"name":"'+name+'","datatype":"'+value+'"}'))
	else:
		policy_json["policy"]["parameters"] = json.loads('[{"name":"'+name+'","datatype":"'+value+'"}]')
	
	return policy_json, True, "<b> Success!</b> Parameter datatype confgured"

def change_parameter_repeat(policy_json, name, enabled):
	if "parameters" in policy_json["policy"] :
		parameter_exists, x = key_exists(policy_json["policy"]["parameters"], "name", name)
		if parameter_exists:
			if check_value(policy_json["policy"]["parameters"][x], "allowRepeatedParameterName", enabled):
				return policy_json, False, "<b>Failed!</b> Parameter allow_repeat already enabled"
			else:
				policy_json["policy"]["parameters"][x]["allowRepeatedParameterName"] = enabled			
		else:
			policy_json["policy"]["parameters"].append(json.loads('{"name":"'+name+'","allowRepeatedParameterName":'+str(enabled).lower()+'}'))
	else:
		policy_json["policy"]["parameters"] = json.loads('[{"name":"'+name+'","allowRepeatedParameterName":'+str(enabled).lower()+'}]')
	
	return policy_json, True, "<b>Success!</b> Parameter allow_repeat enabled"

def change_parameter_empty(policy_json, name, enabled):
	if "parameters" in policy_json["policy"] :
		parameter_exists, x = key_exists(policy_json["policy"]["parameters"], "name", name)
		if parameter_exists:
			if check_value(policy_json["policy"]["parameters"][x], "allowEmptyValue", enabled):
				return policy_json, False, "<b>Failed!</b> Allow empty values for parameter already configured"
			else:
				policy_json["policy"]["parameters"][x]["allowEmptyValue"] = enabled			
		else:
			policy_json["policy"]["parameters"].append(json.loads('{"name":"'+name+'","allowEmptyValue":'+str(enabled).lower()+'}'))
	else:
		policy_json["policy"]["parameters"] = json.loads('[{"name":"'+name+'","allowEmptyValue":'+str(enabled).lower()+'}]')
	
	return policy_json, True, "<b>Success!</b> Allow empty values for parameter configured"

def change_parameter_sensitive (policy_json, name, enabled):
	if "parameters" in policy_json["policy"] :
		parameter_exists, x = key_exists(policy_json["policy"]["parameters"], "name", name)
		if parameter_exists:
			if check_value(policy_json["policy"]["parameters"][x], "sensitiveParameter", enabled):
				return policy_json, False, "Parameter sensitve already configued"
			else:
				policy_json["policy"]["parameters"][x]["sensitiveParameter"] = enabled			
		else:
			policy_json["policy"]["parameters"].append(json.loads('{"name":"'+name+'","sensitiveParameter":'+str(enabled).lower()+'}'))
	else:
		policy_json["policy"]["parameters"] = json.loads('[{"name":"'+name+'","sensitiveParameter":'+str(enabled).lower()+'}]')
	
	return policy_json, True, "Make parameter sensitve modified"

def change_parameter_mandatory (policy_json, name, enabled):
	if "parameters" in policy_json["policy"] :
		parameter_exists, x = key_exists(policy_json["policy"]["parameters"], "name", name)
		if parameter_exists:
			if check_value(policy_json["policy"]["parameters"][x], "mandatory", enabled):
				return policy_json, False, "<b>Failed!</b> Parameter mandatory settings already disabled"
			else:
				policy_json["policy"]["parameters"][x]["mandatory"] = enabled			
		else:
			policy_json["policy"]["parameters"].append(json.loads('{"name":"'+name+'","mandatory":'+str(enabled).lower()+'}'))
	else:
		policy_json["policy"]["parameters"] = json.loads('[{"name":"'+name+'","mandatory":'+str(enabled).lower()+'}]')
	
	return policy_json, True, "<b>Success!</b> Parameter mandatory settings disabled"

def change_parameter_static (policy_json, name, value):
	if "parameters" in policy_json["policy"] :
		parameter_exists, x = key_exists(policy_json["policy"]["parameters"], "name", name)
		if parameter_exists:
			if check_value(policy_json["policy"]["parameters"][x], "valuetype", value):
				return policy_json, False, "<b> Failed</b> Parameter valuetype already configured"
			else:
				policy_json["policy"]["parameters"][x]["valuetype"] = value			
		else:
			policy_json["policy"]["parameters"].append(json.loads('{"name":"'+name+'","valuetype":"'+value+'"}'))
	else:
		policy_json["policy"]["parameters"] = json.loads('[{"name":"'+name+'","valuetype":"'+value+'"}]')
	
	return policy_json, True, "<b> Success!</b> Parameter valuetype confgured"

def add_parameter_static (policy_json, name, value):
	if "parameters" in policy_json["policy"] :
		parameter_exists, x = key_exists(policy_json["policy"]["parameters"], "name", name)
		if parameter_exists:
			if check_value_array(policy_json["policy"]["parameters"][x], "staticValues", value):
				return policy_json, False, "<b> Failed!</b> Value aready exists on the list"
			else:
				if value.isnumeric():
					policy_json["policy"]["parameters"][x]["staticValues"].append(int(value))
				else:
					policy_json["policy"]["parameters"][x]["staticValues"].append(value)
		else:
			return policy_json, False, "<b> Failed!</b> Parameter doesn't exist on policy"
	else:
		return policy_json, False, "<b> Failed!</b> Parameter doesn't exist on policy"
	
	return policy_json, True, "<b> Success!</b> Value added to the Static List"


def change_parameter_location (policy_json, name, location):
	if "parameters" in policy_json["policy"] :
		parameter_exists, x = key_exists(policy_json["policy"]["parameters"], "name", name)
		if parameter_exists:
			if check_value(policy_json["policy"]["parameters"][x], "parameterLocation", location):
				return policy_json, False, "<b>Failed!</b> ParameterLocation settings already configured"
			else:
				policy_json["policy"]["parameters"][x]["parameterLocation"] = location			
		else:
			policy_json["policy"]["parameters"].append(json.loads('{"name":"'+name+'","parameterLocation":"'+location+'"}'))
	else:
		policy_json["policy"]["parameters"] = json.loads('[{"name":"'+name+'","parameterLocation":"'+location+'"}]')
	
	return policy_json, True, "<b>Success!</b> ParameterLocation settings modified"

def change_parameter_numeric_value_min (policy_json, name, value, check_value, exclusive):
	if "parameters" in policy_json["policy"] :
		parameter_exists, x = key_exists(policy_json["policy"]["parameters"], "name", name)
		if parameter_exists:
			policy_json["policy"]["parameters"][x]["minimumValue"] = value			
			policy_json["policy"]["parameters"][x]["checkMinValue"] = check_value			
			policy_json["policy"]["parameters"][x]["exclusiveMin"] = exclusive			
		else:
			policy_json["policy"]["parameters"].append(json.loads('{"name":"'+name+'","minimumValue":'+str(value)+',"checkMinValue":'+str(check_value).lower+',"exclusiveMin":'+str(exclusive).lower+'}'))
	else:
		policy_json["policy"]["parameters"] = json.loads('[{"name":"'+name+'","minimumValue":'+str(value)+',"checkMinValue":'+str(check_value).lower+',"exclusiveMin":'+str(exclusive).lower+'}]')
	
	return policy_json, True, "<b>Success!</b> Parameter "+name+" minimumValue modified."

def change_parameter_numeric_value_max (policy_json, name, value, check_value, exclusive):
	if "parameters" in policy_json["policy"] :
		parameter_exists, x = key_exists(policy_json["policy"]["parameters"], "name", name)
		if parameter_exists:
			policy_json["policy"]["parameters"][x]["maximumValue"] = value			
			policy_json["policy"]["parameters"][x]["checkMaxValue"] = check_value			
			policy_json["policy"]["parameters"][x]["exclusiveMax"] = exclusive			
		else:
			policy_json["policy"]["parameters"].append(json.loads('{"name":"'+name+'","maximumValue":'+str(value)+',"checkMaxValue":'+str(check_value).lower+',"exclusiveMax":'+str(exclusive).lower+'}'))
	else:
		policy_json["policy"]["parameters"] = json.loads('[{"name":"'+name+'","maximumValue":'+str(value)+',"checkMaxValue":'+str(check_value).lower+',"exclusiveMax":'+str(exclusive).lower+'}]')
	
	return policy_json, True, "<b>Success!</b> Parameter "+name+" maximumValue modified."

def change_parameter_numeric_value_multiple (policy_json, name, multipleof, check_multiple):
	if "parameters" in policy_json["policy"] :
		parameter_exists, x = key_exists(policy_json["policy"]["parameters"], "name", name)
		if parameter_exists:
			policy_json["policy"]["parameters"][x]["multipleOf"] = multipleof			
			policy_json["policy"]["parameters"][x]["checkMultipleOfValue"] = check_multiple			
		else:
			policy_json["policy"]["parameters"].append(json.loads('{"name":"'+name+'","multipleOf":'+str(multipleof)+',"checkMultipleOfValue":'+str(check_multiple).lower()+'}'))
	else:
		policy_json["policy"]["parameters"] = json.loads('[{"name":"'+name+'","multipleOf":'+str(multipleof)+',"checkMultipleOfValue":'+str(check_multiple).lower()+'}]')
	
	return policy_json, True, "<b>Success!</b> Parameter "+name+" multipleOf modified."

def change_parameter_length_value_min (policy_json, name, value, check_value):
	if "parameters" in policy_json["policy"] :
		parameter_exists, x = key_exists(policy_json["policy"]["parameters"], "name", name)
		if parameter_exists:
			policy_json["policy"]["parameters"][x]["minimumLength"] = value			
			policy_json["policy"]["parameters"][x]["checkMinValueLength"] = check_value			
		else:
			policy_json["policy"]["parameters"].append(json.loads('{"name":"'+name+'","minimumLength":'+str(value)+',"checkMinValueLength":'+str(check_value).lower()+'}'))
	else:
		policy_json["policy"]["parameters"] = json.loads('[{"name":"'+name+'","minimumLength":'+str(value)+',"checkMinValueLength":'+str(check_value).lower()+'}]')
	
	return policy_json, True, "<b>Success!</b> Parameter "+name+" minimumLength modified."

def change_parameter_length_value_max (policy_json, name, value, check_value):
	if "parameters" in policy_json["policy"] :
		parameter_exists, x = key_exists(policy_json["policy"]["parameters"], "name", name)
		if parameter_exists:
			policy_json["policy"]["parameters"][x]["maximumLength"] = value			
			policy_json["policy"]["parameters"][x]["checkMaxValueLength"] = check_value			
		else:
			policy_json["policy"]["parameters"].append(json.loads('{"name":"'+name+'","maximumLength":'+str(value)+',"checkMaxValueLength":'+str(check_value).lower()+'}'))
	else:
		policy_json["policy"]["parameters"] = json.loads('[{"name":"'+name+'","maximumLength":'+str(value)+',"checkMaxValueLength":'+str(check_value).lower()+'}]')
	
	return policy_json, True, "<b>Success!</b> Parameter "+name+" maximumLength modified."



##### VIOL_COOKIE_MODIFIED
def cookie_modified (policy_json, name, enforcementType):
	if "cookies" in policy_json["policy"] :
		cookie_exists, x = key_exists(policy_json["policy"]["cookies"], "name", name)
		if cookie_exists:
			if check_value(policy_json["policy"]["cookies"][x], "enforcementType", enforcementType):
				return policy_json, False, "<b>Failed!</b> Cookie enforcementType already allowed"
			else:
				policy_json["policy"]["cookies"][x]["enforcementType"] = enforcementType			
		else:
			policy_json["policy"]["cookies"].append(json.loads('{"name":"'+name+'","enforcementType":"'+enforcementType+'"}'))
	else:
		policy_json["policy"]["cookies"] = json.loads('[{"name":"'+name+'","enforcementType":"'+enforcementType+'"}]')
	
	return policy_json, True, "<b>Success!</b>  Cookie enforcementType changed to allowed"

##### VIOL_MANDATORY_HEADER
def change_header_mandatory (policy_json, name, enabled):
	if "headers" in policy_json["policy"] :
		header_exists, x = key_exists(policy_json["policy"]["headers"], "name", name)
		if header_exists:
			if check_value(policy_json["policy"]["headers"][x], "mandatory", enabled):
				return policy_json, False, "<b>Failed!</b> Header mandatory settings already disabled"
			else:
				policy_json["policy"]["headers"][x]["mandatory"] = enabled			
		else:
			policy_json["policy"]["headers"].append(json.loads('{"name":"'+name+'","mandatory":'+str(enabled).lower()+'}'))
	else:
		policy_json["policy"]["headers"] = json.loads('[{"name":"'+name+'","mandatory":'+str(enabled).lower()+'}]')
	
	return policy_json, True, "<b>Success!</b> Header mandatory settings disabled"

##### VIOL_PARAMETER_VALUE_BASE64
def change_parameter_base64 (policy_json, name, value):
	if "parameters" in policy_json["policy"] :
		parameter_exists, x = key_exists(policy_json["policy"]["parameters"], "name", name)
		if parameter_exists:
			if check_value(policy_json["policy"]["parameters"][x], "decodeValueAsBase64", value):
				return policy_json, False, "<b>Failed!</b> Parameter Base64 settings already configured"
			else:
				policy_json["policy"]["parameters"][x]["decodeValueAsBase64"] = value			
		else:
			policy_json["policy"]["parameters"].append(json.loads('{"name":"'+name+'","decodeValueAsBase64":'+str(value).lower()+'}'))
	else:
		policy_json["policy"]["parameters"] = json.loads('[{"name":"'+name+'","decodeValueAsBase64":'+str(value).lower()+'}]')
	
	return policy_json, True, "<b>Success!</b> Parameter Base64 settings modified"

def change_cookies_base64 (policy_json, name, value):
	if "cookies" in policy_json["policy"] :
		cookie_exists, x = key_exists(policy_json["policy"]["cookies"], "name", name)
		if cookie_exists:
			if check_value(policy_json["policy"]["cookies"][x], "decodeValueAsBase64", value):
				return policy_json, False, "<b>Failed!</b> Cookie Base64 settings already configured"
			else:
				policy_json["policy"]["cookies"][x]["decodeValueAsBase64"] = value			
		else:
			policy_json["policy"]["cookies"].append(json.loads('{"name":"'+name+'","decodeValueAsBase64":'+str(value).lower()+'}'))
	else:
		policy_json["policy"]["cookies"] = json.loads('[{"name":"'+name+'","decodeValueAsBase64":'+str(value).lower()+'}]')
	
	return policy_json, True, "<b>Success!</b> Cookie Base64 settings modified"

def change_header_base64 (policy_json, name, value):
	if "headers" in policy_json["policy"] :
		cookie_exists, x = key_exists(policy_json["policy"]["headers"], "name", name)
		if cookie_exists:
			if check_value(policy_json["policy"]["headers"][x], "decodeValueAsBase64", value):
				return policy_json, False, "<b>Failed!</b> Header Base64 settings already configured"
			else:
				policy_json["policy"]["headers"][x]["decodeValueAsBase64"] = value			
		else:
			policy_json["policy"]["headers"].append(json.loads('{"name":"'+name+'","decodeValueAsBase64":'+str(value).lower()+'}'))
	else:
		policy_json["policy"]["headers"] = json.loads('[{"name":"'+name+'","decodeValueAsBase64":'+str(value).lower()+'}]')
	
	return policy_json, True, "<b>Success!</b> Header Base64 settings modified"

##### VIOL_EVASION
def evasion_technique(policy_json, name, enabled):
	if "blocking-settings" in  policy_json["policy"] :
		if "evasions" in policy_json["policy"]["blocking-settings"]:
			exists, x = key_exists(policy_json["policy"]["blocking-settings"]["evasions"],"description",name)
			if exists :
				if check_value(policy_json["policy"]["blocking-settings"]["evasions"][x], "enabled", enabled):
					return policy_json, False, "<b>Failed!</b> Evasion technique sub-violation already disabled"
				else:
					policy_json["policy"]["blocking-settings"]["evasions"][x]["enabled"] = enabled
			else:
				policy_json["policy"]["blocking-settings"]["evasions"].append(json.loads('{"description": "'+name+'", "enabled":'+str(enabled).lower()+'}'))
		else:
			policy_json["policy"]["blocking-settings"]["evasions"] = json.loads('[{"description": "'+name+'", "enabled":'+str(enabled).lower()+'}]')
	else:
		policy_json["policy"]["blocking-settings"] = json.loads('{"evasions":[{"description": "'+name+'", "enabled":'+str(enabled).lower()+'}]}') 
	
	return policy_json, True, "<b>Success!</b> Evasion technique sub-violation disabled"
 
##### VIOL_HTTP_COMPLIANCE
def http_compliance(policy_json, name, enabled):
	if "blocking-settings" in  policy_json["policy"] :
		if "http-protocols" in policy_json["policy"]["blocking-settings"]:
			exists, x = key_exists(policy_json["policy"]["blocking-settings"]["http-protocols"],"description",name)
			if exists:
				if check_value(policy_json["policy"]["blocking-settings"]["http-protocols"][x], "enabled", enabled):
					return policy_json, False, "<b>Failed!</b> HTTP Protocol compliance sub-violation already disabled"
				else:
					policy_json["policy"]["blocking-settings"]["http-protocols"][x]["enabled"] = enabled
			else:
				policy_json["policy"]["blocking-settings"]["http-protocols"].append(json.loads('{"description": "'+name+'", "enabled":'+str(enabled).lower()+'}'))
		else:
			policy_json["policy"]["blocking-settings"]["http-protocols"] = json.loads('[{"description": "'+name+'", "enabled":'+str(enabled).lower()+'}]')
	else:
		policy_json["policy"]["blocking-settings"] = json.loads('{"http-protocols":[{"description": "'+name+'", "enabled":'+str(enabled).lower()+'}]}') 
	
	return policy_json, True, "<b>Success!</b> HTTP Protocol compliance sub-violation disabled"


format = sys.argv[1]
encoded_input = sys.argv[2]
input_variables_tmp = base64.b64decode(encoded_input).decode('utf-8')
input_variables = json.loads(input_variables_tmp)

# Open File
f = open('policy')

if (format == "yaml"):
	try:
		yData = yaml.safe_load(f)
		jData = yData["spec"]
	except:
		print ("Input file not YAML")		
		exit()
else:
	try:
		jData = json.load(f)
	except:
		print ("Input file not JSON")		
		exit()

f.close()

msg = "No match"

if input_variables["type"]=="attack_sig_global":
	jData, result, msg = override_signature_global(jData,input_variables["sig_id"],False)

if input_variables["type"]=="attack_sig_url":
	jData, result, msg  = override_signature_on_entity(jData,"urls",input_variables["entity"],input_variables["sig_id"],False)

if input_variables["type"]=="attack_sig_parameter":
	jData, result, msg  = override_signature_on_entity(jData,"parameters",input_variables["entity"],input_variables["sig_id"],False)

if input_variables["type"]=="attack_sig_cookie":
	jData, result, msg  = override_signature_on_entity(jData,"cookies",input_variables["entity"],input_variables["sig_id"],False)

if input_variables["type"]=="attack_sig_header":
	jData, result, msg  = override_signature_on_entity(jData,"headers",input_variables["entity"],input_variables["sig_id"],False)

if input_variables["type"]=="modify_bot_class":
	jData, result, msg = modify_bot_class(jData,input_variables["class_name"],input_variables["action"])

if input_variables["type"]=="modify_bot_signature":
	jData, result, msg = modify_bot_signature(jData,input_variables["signature_name"],input_variables["action"])

if input_variables["type"]=="cookie_length":
	jData, result, msg = cookie_length(jData,input_variables["value"])

if input_variables["type"]=="header_length":
	jData, result, msg = header_length(jData,input_variables["value"])

if input_variables["type"]=="illegal_filetype":
	jData, result, msg = illegal_filetype(jData,input_variables["filetype"],input_variables["enabled"])

if input_variables["type"]=="illegal_method":
	jData, result, msg = illegal_method(jData,input_variables["method"])

if input_variables["type"]=="illegal_url":
	jData, result, msg = illegal_url(jData,input_variables["url"],input_variables["enabled"])

if input_variables["type"]=="postdatalength":
	jData, result, msg = postdatalength(jData,input_variables["entity"],input_variables["value"])

if input_variables["type"]=="urllength":
	jData, result, msg = urllength(jData,input_variables["entity"],input_variables["value"])

if input_variables["type"]=="requestlength":
	jData, result, msg = requestlength(jData,input_variables["entity"],input_variables["value"])

if input_variables["type"]=="querystringlength":
	jData, result, msg = querystringlength(jData,input_variables["entity"],input_variables["value"])

if input_variables["type"]=="datatype":
	jData, result, msg = change_parameter_datatype(jData,input_variables["entity"],input_variables["value"])

if input_variables["type"]=="repeat":
	jData, result, msg = change_parameter_repeat(jData,input_variables["entity"],input_variables["value"])

if input_variables["type"]=="disable_violation":
	jData, result, msg = disable_violation(jData,input_variables["viol_name"],input_variables["block"],input_variables["alarm"])

if input_variables["type"]=="illegal_parameter":
	jData, result, msg = illegal_parameter(jData,input_variables["entity"],input_variables["enabled"])

if input_variables["type"]=="mandatory_parameter":
	jData, result, msg = change_parameter_mandatory(jData,input_variables["entity"],input_variables["enabled"])

if input_variables["type"]=="illegal_location":
	jData, result, msg = change_parameter_location(jData,input_variables["entity"],input_variables["location"])

if input_variables["type"]=="parameter_empty":
	jData, result, msg = change_parameter_empty(jData,input_variables["entity"],input_variables["value"])

if input_variables["type"]=="numeric_value_multipleof":
	jData, result, msg = change_parameter_numeric_value_multiple(jData,input_variables["entity"],input_variables["multipleof"],input_variables["check_multiple"])

if input_variables["type"]=="numeric_value_min":
	jData, result, msg = change_parameter_numeric_value_min(jData,input_variables["entity"],input_variables["value"],input_variables["check_value"],input_variables["exclusive"])

if input_variables["type"]=="numeric_value_max":
	jData, result, msg = change_parameter_numeric_value_max(jData,input_variables["entity"],input_variables["value"],input_variables["check_value"],input_variables["exclusive"])

if input_variables["type"]=="parameter_illegal_base64":
	jData, result, msg = change_parameter_base64(jData,input_variables["entity"],input_variables["enabled"])

if input_variables["type"]=="illegal_value_length_min":
	jData, result, msg = change_parameter_length_value_min(jData,input_variables["entity"],input_variables["value"],input_variables["check_value"])

if input_variables["type"]=="illegal_value_length_max":
	jData, result, msg = change_parameter_length_value_max(jData,input_variables["entity"],input_variables["value"],input_variables["check_value"])

if input_variables["type"]=="illegal_static_value":
	jData, result, msg = change_parameter_static(jData,input_variables["entity"],input_variables["value"])

if input_variables["type"]=="modify_static_value":
	jData, result, msg = add_parameter_static(jData,input_variables["entity"],input_variables["value"])

if input_variables["type"]=="cookie_modified":
	jData, result, msg = cookie_modified(jData,input_variables["entity"],input_variables["enforcementType"])


if input_variables["type"]=="evasion_technique":
	jData, result, msg = evasion_technique(jData,input_variables["name"],input_variables["enabled"])


if input_variables["type"]=="http_protocol_compliance":
	jData, result, msg = http_compliance(jData,input_variables["name"],input_variables["enabled"])


if (format == "yaml"):
	yData["spec"] = jData
	with open('policy_mod', 'w', encoding='utf-8') as f:
		yaml.dump(yData, f, indent=2)
else:
	with open('policy_mod', 'w', encoding='utf-8') as f:
		json.dump(jData, f, ensure_ascii=False, indent=4)

print (msg)
