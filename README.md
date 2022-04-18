# NGINX App Protect WAF Dashboard (work in progress)

> This is a Grafana based dashboard for NGINX App Protect WAF (NAP). The overall solution uses Logstash to receive logs from NGINX App Protect WAF, process them and finally store them in Elasticsearch indexes. Grafana helps us visualize those logs.

<img src="images/attack-signatures-0.png"/>


## Table of Contents

- [Installation](#installation)
- [Dashboards](#features)
- [Support](#support)

---

## Installation

To run this Dashboard you will need to deploy following open source solutions. 
- Logstash
- Elasticsearch 
- Grafana
- Docker
- Docker Compose
- Python 3.7+

Steps will be provided on how to deploy all of the software in a Docker environment using Docker Compose. It is assumed that Docker and Python is already installed and configured on the system.

### Clone the repo

Clone this repo to your local machine using `https://github.com/skenderidis/nap-dashboard` and switch the working directory to be `nap-dashboard`

```shell
git clone https://github.com/skenderidis/nap-dashboard
cd nap-dashboard
```

### Install Elasticsearch-Logstash using docker-compose

```shell

TZ=Asia/Dubai && docker-compose up -d

```
NOTES:
>  - Change the timezone used in the docker containers by altering the inline environment variable in the command above accordingly to your location. A list of TZ Database Names can be found [here](https://en.wikipedia.org/wiki/List_of_tz_database_time_zones).
>  - The TCP port that Logstash is listening to is 515.


### Configure Elasticsearch
>  In you are not running the following commands from your docker host, please change `localhost` to the hostname or IP address of your docker host.

1. Create signature index on Elasticsearch
```shell
curl -X PUT 'http://localhost:9200/signatures/'
```
Expected Response: `{"acknowledged":true,"shards_acknowledged":true,"index":"signatures"}`

2. Create index mapping for signature index
```shell
curl -d "@elastic/signatures-mapping.json" -H 'Content-Type: application/json' -X PUT 'http://localhost:9200/signatures/_mapping/'
```
Expected Response: `{"acknowledged":true}`

3. Populate the signature index with the data extracted from NGINX signature report tool. You can repeat this process to update the signatures. 
In order to enrich the logs that Elasticsearch is receiving from NAP with information such as signature accuracy, risk, cve, systems affected, etc we need to extract the signatures from NAP (by using NGINX attack signature report tool) and import them into Elasticsearch. More info on NGINX signature report tool can be found <a href="https://docs.nginx.com/nginx-app-protect/configuration-guide/configuration/#attack-signature-report-tool" target="_blank">here</a>.
Otherwise you can use the `signature-report.json` file that can be found on the `signatures` folder and contains the latest signatures.

```shell
python3 signatures/upload-signatures.py signatures/signatures-report.json localhost
```
If successful it will take around 1 min to push all signatures to elastic. Expect to see multiple responses of the following: `{"_index":"signatures","_type":"_doc","_id":"200000001","_version":1,"result":"created","_shards":{"total":2,"successful":1,"failed":0},"_seq_no":7553,"_primary_term":1}`


4. Create template for NAP indexes Index Mapping
```shell
curl -d "@elastic/template-mapping.json" -H 'Content-Type: application/json' -X PUT 'http://localhost:9200/_template/waf_template?include_type_name'
```
Expected Response: `{"acknowledged":true}`


5. Create enrich policy for the NAP/Signatures Indices.
```shell
curl -d "@elastic/enrich-policy.json" -H 'Content-Type: application/json' -X PUT 'http://localhost:9200/_enrich/policy/signatures-policy'
```
Expected Response: `{"acknowledged":true}`

6. Deploy enrich policy.
```shell
curl -X POST 'http://localhost:9200/_enrich/policy/signatures-policy/_execute'
```
Expected Response: `{"status":{"phase":"COMPLETE"}}`

7. Create Ingest Pipeline.
```shell
curl -d "@elastic/sig-lookup.json" -H 'Content-Type: application/json' -X PUT 'http://localhost:9200/_ingest/pipeline/sig_lookup'
```
Expected Response: `{"acknowledged":true}`


### Configure Grafana
1. Setup Grafana source - Elastic WAF Index.
```shell
curl -d "@grafana/DS-waf-index.json" -H 'Content-Type: application/json' -u 'admin:admin' -X POST 'http://localhost:3000/api/datasources/'
```

2. Setup Grafana source - Elastic WAF Decoded Index.
```shell
curl -d "@grafana/DS-waf-decoded-index.json" -H 'Content-Type: application/json' -u 'admin:admin' -X POST 'http://localhost:3000/api/datasources/'
```

3. Deploy Grafana Dashboards.

To deploy the Grafana Dashboards goto `Import Dashboard` and input the Dashboard ID (as per the following table) on `Import via grafana.com` tab. 
<p align="center">
<img width="500" src="images/grafana-id.png"/>       
</p>

| Dashboard Name                        | Dashboard ID  |  Grafana Source        |   Grafana Website                                                  | 
| -------------                         | :---:         |-------------           |  :---:                                                             | 
| NGINX NAP Main Dashboard              | 15675         | WAF-Logs               | <a href="https://grafana.com/grafana/dashboards/15675"> Link </a>  |
| NGINX NAP Support-ID Dashboard        | 15676         | WAF-Logs , WAF-Decoded | <a href="https://grafana.com/grafana/dashboards/15676"> Link </a>  |
| NGINX NAP Attack Signatures Dashboard | 15677         | WAF-Decoded            | <a href="https://grafana.com/grafana/dashboards/15677"> Link </a>  |
| NGINX NAP BOT Dashboard               | 15678         | WAF-Logs               | <a href="https://grafana.com/grafana/dashboards/15678"> Link </a>  |

#### Note 1: Modifying the links for Support ID Dashboards.
The dashboards (NAP, Attack Signatures and Bot) have links so that you can click on the supportID from the logs and navigate to the SupportID dashboard having the supportID that you click as the selected filter. Currently the links point to the IP address `192.168.2.103` and this needs to change with the actual IP address of your Grafana deployment.
The easiest way to modify the links is the following.
 - Go to the dashboard settings and select the JSON Model.
 - Search for 192.168.2.103 and replace it with the actual IP

#### Note 2: Modifying the links for other Dashboards.
On the Main dashboard (NAP) there are links to the other 3 dashboards. Currently the links point to the demo URL for these dashboards. Please edit the `Violations table` and modify the links that have been highlighted on the image below with Red
<img width="800" src="images/datalinks.png"/>          |


---

## Dashboards

### Main Dashboard
This is the main dashboard that provides an overview of all the violations that have been logged by NGINX App Protect WAF. From this table you can navigate to the other dashboards like SupportID, by clicking on the links. Some of the graphs/tables included in this dashboard are:
- Attacks recorded and mitigated
- Violation categories
- Attacks over time
- Mitigated Bots 
- GeoMap
- Attacks per URL
- Attack Signature detected
- Bot activity per IP/Country
- Bot activity per Policy/Device
- CVEs and Threat Campaigns
- Logs

<p align="center">
<img width="960" src="images/nap1.png"/>
</p>

### Attack Signature Dashboard
The Attack Signature dashboard provides all details for the signatures that were triggered by NGINX App Protect WAF. Some of the graphs/tables included in this dashboard are:
- Signature Hits
- Signature Accuracy and Risk
- Signatures per Context 
- Signature details 
- Signatures per URL/IP/Policy
- Parameter Names and Values
- Header Names and Values
- Cookies Names and Values
- Logs

<p align="center">
<img width="960" src="images/attack-signatures-1.png"/>
</p>

### Bot Dashboard
The Bot Dashboard provides all details for the Bot activity that was logged by NGINX App Protect WAF. Some of the graphs/tables included in this dashboard are:
- Bot Types
- Bot Categories
- Bot Activity over time
- Mitigated Bots 
- Bot activity per URL
- Bot activity per IP/Country
- Bot activity per Policy/Device
- Logs

<p align="center">
<img width="960" src="images/bot-1.png"/>
</p>

### SupportID Dashboard
The SupportID Dashboard provides all details for a specific transaction that was logged by NGINX App Protect WAF. These include the following:
- Client/Server Information (Client IP/Port, Server IP/Port, X-Forwared-For, etc)
- Violation Details (Outcome, Request Status, Outcome Reson, etc)
- Bot Details (Bot Classm Bot Category, Bit Signature, etc)
- Device Details (NAP Device name, Vritual Server Name)
- Signatures Triggered
- Treat Campaign triggered
- Violation list
- Many more

It also includes both the original and decoded Elasticsearch indices for better troubleshooting.

<p align="center">
  <img width="960" src="images/support1.png">
</p>


## Support

Please open a GitHub issue for any problem or enhancement you need.

