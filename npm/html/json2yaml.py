import yaml
import json
import sys


input_file = sys.argv[1]
if 'json' in input_file:
    mod_file = input_file[:-5] + ".yaml"
else:
    print("failed matching the extension")
    exit()

# Open the YAML file and get the Kubernetes confif (first few lines). We will add the new configuration under "spec" attribute    
with open(mod_file, 'r') as yaml_file:
   try:
      yaml_policy_json = yaml.safe_load(yaml_file)
      with open(input_file, 'r') as file:
         try:
            configuration = json.load(file)
            yaml_policy_json["spec"] = configuration
            with open(mod_file, 'w') as json_file:
               yaml.dump(yaml_policy_json, json_file, indent=2)
            print("success")
         except:
            print("parsing error") 
   except:
      print("Can't read/parse exising YAML policy error") 


#with open(input_file, 'r') as file:
#   try:
#      configuration = json.load(file)
#      with open(mod_file, 'w') as json_file:
#         yaml.dump(configuration, json_file, indent=2)
#
#      print("success")
#   except:
#      print("parsing error") 

    
   
