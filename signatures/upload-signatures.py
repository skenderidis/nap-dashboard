#from email import feedparser
import requests
import json
import sys

file_name = sys.argv[1]
elastic_ip = sys.argv[2]

# Opening JSON file
#f = open('signature-report.json')
f = open(file_name)


# returns JSON object as
# a dictionary
data = json.load(f)
 


# the result is a Python dictionary:
#print(data[0])
#for i in data:
#  print(json.dumps(i))


for i in data["signatures"]:
    print(i["signatureId"])
    i["sig_id"]=i["signatureId"]
    del i["signatureId"]
    if 'hasCve' in i:
        i["cve"]=[]
        if i["hasCve"]:
            for x in i["references"]:
                if x["type"] == "cve":
                    i["cve"].append(x["value"])
        else:
            i["cve"]="N/A"
    else:
        i["cve"]="N/A"            
    url = 'http://'+elastic_ip+':9200/signatures/_doc/'+str(i["sig_id"])
    headers = {'content-type': 'application/json'}
    print(i)
    x = requests.post(url, data = json.dumps(i), headers=headers)
    print(x.text)

# Closing file
f.close()