


<a href="http://wwww.com"><img src="https://avatars1.githubusercontent.com/u/?v=3&s=200" title="FVCproductions" alt="FVCproductions"></a>



# NAP Dashboard

> This is Grafana based Dashboard for NGINX NAP Reporting. The overall solution uses Logstash to receive logs from NGINX App Protect, process them, decode them and finally store them in Elasticsearch indexes. Grafana help us visualize these logs.




## Table of Contents

- [Installation](#installation)
- [Features](#features)
- [Contributing](#contributing)
- [Team](#team)
- [FAQ](#faq)
- [Support](#support)
- [License](#license)

---

## Installation

To run this Dashboard you will need to deploy following opensource solutions. 
- Logstash
- Elasticsearch 
- Grafana
- Docker
- Python 3.7+
Steps will be provided on how to deploy all of the software in a docker environment. It is assumed that Docker and Python is already installed and configured on the system

### Clone the repo

Clone this repo to your local machine using `https://github.com/skenderidis/nap-dashboard`
Go to the directory of the repo

```shell
cd nap-dashboard
```

### Install Elasticsearch-Logstash
Use the docker network create command to create `elastic` bridge network.
```shell
docker network create elastic
docker run -d -e TZ=Asia/Dubai --name elasticsearch --net elastic -p 9200:9200 -p 9300:9300 -e "discovery.type=single-node" docker.elastic.co/elasticsearch/elasticsearch:7.16.2
docker run -e TZ=Asia/Dubai --name logstash --net elastic -p 515:515 -it --rm -v "$PWD":/config-dir docker.elastic.co/logstash/logstash:7.16.2 -f /config-dir/logstash.conf

```
>  - Change the timezone accordingly `TZ=Asia/Dubai`
>  - The port where Logstash will be listeting is 515.


### Configure Elasticsearch
In order to enrich the logs that elastic is receiving from NAP with signature details such as accuracy, risk, cve, systems affected, etc we need to extract the signatures from the NAP with the attack signature report tool. More info on the signature report tool can be found <a href="https://docs.nginx.com/nginx-app-protect/configuration-guide/configuration/#attack-signature-report-tool" target="_blank">here</a>.
Otherwise you can use the `signature-report.json` file that can be found on the `signatures` folder and contains the latest signatures.
>  192.168.2.103 is the Elasticsearch IP address that I am using. Please change it accordingly 

1. Create Signature Index
```shell
curl -X PUT 'http://192.168.2.103:9200/signatures/'
```

Expected Response: `{"acknowledged":true,"shards_acknowledged":true,"index":"signatures"}`

2. Create Signature Index Mapping
```shell
curl -d "@elastic/signature_mapping.json" -H 'Content-Type: application/json' -X PUT 'http://192.168.2.103:9200/signatures/_mapping/'
```
Expected Response: `{"acknowledged":true}`

3. Populate the Signature Index with the data extracted from the signature-report tool. You can repeat this process to update the signatures. 
```shell
cd signatures
python3 upload-signatures.py signature-report.json 192.168.2.103
```

If successfull it will take around 1 min to push all signatures to elastic. Expect to see multiple responses of the following: `{"_index":"signatures","_type":"_doc","_id":"200000001","_version":1,"result":"created","_shards":{"total":2,"successful":1,"failed":0},"_seq_no":7553,"_primary_term":1}`


4. Create template for NAP indexes Index Mapping
```shell
curl -d "@elastic/template-mapping.json" -H 'Content-Type: application/json' -X PUT 'http://192.168.2.103:9200/_template/waf_template?include_type_name'
```
Expected Response: `{"acknowledged":true}`


5. Create enrich policy for the NAP/Signatures Indices
```shell
curl -d "@elastic/enrich-policy.json" -H 'Content-Type: application/json' -X PUT 'http://192.168.2.103:9200/_enrich/policy/signatures-policy'
```
Expected Response: ``

6. Deploy enrich policy 
```shell
curl -X POST 'http://192.168.2.103:9200/_enrich/policy/signatures-policy/_execute'
```
Expected Response: ``

7. Create Ingest Pipeline
```shell
curl -d "@elastic/sig-lookup.json" -H 'Content-Type: application/json' -X PUT 'http://192.168.2.103:9200/_ingest/pipeline/sig_lookup'
```
Expected Response: `{"acknowledged":true}`


### Configure Grafana
The working directory should be `grafana`

1. Setup Grafana source - Elastic WAF Index
```shell
curl -d "@waf-index.json" -H 'Content-Type: application/json' -u 'admin:admin' -X POST 'http://192.168.2.103:3000/api/datasources/'
```

2. Setup Grafana source - Elastic WAF Decoded Index
```shell
curl -d "@waf-decoded-index.json" -H 'Content-Type: application/json' -u 'admin:admin' -X POST 'http://192.168.2.103:3000/api/datasources/'
```


2. Setup Grafana Dashboard - Main Dashboard
```shell
curl -d "@waf-decoded-index.json" -H 'Content-Type: application/json' -u 'admin:admin' -X POST 'http://192.168.2.103:3000/api/datasources/'
```

3. Populate the Signature Index with the data extracted from the signature-report tool. You can repeat this process to update the signatures. 
```shell
cd signatures
python3 upload-signatures.py signature-report.json 192.168.2.103
```



---

## Examples


## FAQ

- ** test *test* test?**
