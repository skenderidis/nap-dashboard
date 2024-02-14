import yaml
import json
import sys


input_file = sys.argv[1]
if 'yaml' in input_file:
    mod_file = input_file[:-5] + ".json"
else:
    print("failed matching the extension")
    exit()


with open(input_file, 'r') as file:
   try:
      configuration = yaml.safe_load(file)
      with open(mod_file, 'w') as json_file:
         json.dump(configuration["spec"], json_file, indent=3)
      print("success")
   except:
      print("parsing error") 

    
   
