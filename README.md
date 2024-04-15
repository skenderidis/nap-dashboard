# NAP Dashboard
This project makes it easier for SecOps teams to review and analyze NGINX APP Protect (NAP) violations with the use  of Grafana Dashboards.

<p align="center">
<img width="720" src="images/grafana.gif"/>
</p>

The solution uses Logstash/FluentD to ingest the logs from NAP instances, transforms them and finally store them in Elasticsearch indexes. Grafana is used as the analytics platform that connects to the datasource (Elasticsearch) and provides interactive dashboards.

> **Note**: *We have provided 2 different log ingestion/transformation solutions (**FluentD** or **Logstash**) that you can choose from and we will provide configuration examples for both options.*

In the following sections we will cover the following:

 - Logging transformation 
 - Dashboards created
 - Installation

## Logging transformation
Starting with NAP release 4.3, the **json_log** field includes the Violation details formatted in JSON format. While json_log doesnt provide yet the violation details for all violation types, it makes it significant easier and faster to parse NAP logs with FluentD and Logstash compared to the key/value pairs and the XML violation details that we had earlier.
Also with each release more violations are getting added to the **json_log** field, which makes it the logical choice for logging the violation details. 

To check which violations are currently supported with **json_log** field please click "here" 

Below you can see the information of the **json_log** field. While going through the details provided by the **json_log** field, take a closer look on the following variables; `url`, `rawRequest.httpRequest` and the `violations`.

<p align="center">
<img width="720" src="images/json_log.png"/>
</p>

The `url` and `rawRequest.httpRequest` have their Base64 encoded while the `violations` provides an array with each violation recorded on that specific transaction.

For the first two we will have FluentD/Logstash Base64 decode them, while for the violations array we will create a separate log for each violation, Base64 decode any field that is encoded and save it to a separate index. In addition to that we will enrich the log with the GeoIP point of the SourceIP so that we can represent to a map the location of the attackers. 


> **Note**: Until Bot logs are incorporated into **json_log**, we have manually included them as additional key/value pairs and we are extracting them with logstash with additional configuration. 

## NAP Dashboards
The Dashboards that we have created so far are:
- [Main](#overview)
- [Attack Signatures](#attack-signatures)
- [Parameter Violations](#parameters-violations)
- [File Types Violations](#filetype-violations)
- [Protocol Violations ](#protocol-violations)
- [Meta Character Violations](#meta-character-violations)
- [Header Violations](#header-violations)
- [Cookie Violations](#cookie-violations)
- [SupportID](#supportid)


#### Overview
This is the main dashboard that provides an overview of all the violations that have been logged by NGINX App Protect WAF. From this table you can navigate to the other dashboards like SupportID, Attack-Signatures, Parameter Violations, by clicking on the links at the top right corner. Some of the graphs/tables included in this dashboard are:
- Attacks recorded and mitigated
- Violation categories
- Attacks over time
- Mitigated Bots 
- GeoMap
- Attacks per URL/IP
- Attack Signature detected
- Attacks per VirtualServer and Policy
- Bot Categories and Signatures
- Bot Class and Anomalies
- CVEs and Threat Campaigns
- Logs

<p align="center">
<img width="720" src="images/main-1.png"/>
</p>

For more screenshots go of the **Main Dashboard** click [Here](dashboards/main.md)

#### Attack Signatures
The Attack Signature dashboard provides details for all the signatures that were triggered by NGINX App Protect WAF. This dashboard gets the information from the decoded index and matches the violation name with `VIOL_ATTACK_SIGNATURE`. Some of the graphs/tables included in this dashboard are:
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
<img width="720" src="images/sig-1.png"/>
</p>

For more screenshots go of the **Attack Signature Dashboard** click [Here](dashboards/attack_signature.md)


#### Parameter Violations
The Parameter Dashboard provides details for all **parameter** related violations including `VIOL_PARAMETER`, `VIOL_PARAMETER_ARRAY_VALUE`,`VIOL_PARAMETER_DATA_TYPE`,`VIOL_PARAMETER_EMPTY_VALUE`,`VIOL_PARAMETER_LOCATION`,`VIOL_PARAMETER_NUMERIC_VALUE`,`VIOL_PARAMETER_STATIC_VALUE`,`VIOL_PARAMETER_VALUE_LENGTH`

<p align="center">
<img width="720" src="images/param-1.png"/>
</p>

For more screenshots go of the **Attack Signature Dashboard** click [Here](dashboards/parameter_violations.md)

#### FileType Violations
The FileType Dashboard provides details for all the file type related violations including `VIOL_FILETYPE`, `VIOL_POST_DATA_LENGTH`,`VIOL_QUERY_STRING_LENGTH`,`VIOL_REQUEST_LENGTH`,`VIOL_URL_LENGTH`. 

<p align="center">
<img width="720" src="images/ft-1.png"/>
</p>

For more screenshots go of the **File Type Violations Dashboard** click [Here](dashboards/filetype_violations.md)


#### Meta-Character Violations
The Meta-Character Violations Dashboard provides details for all the meta-character related violations including `VIOL_PARAMETER_VALUE_METACHAR`, `VIOL_PARAMETER_NAME_METACHAR`, `VIOL_PARAMETER_URL_METACHAR`.

<p align="center">
<img width="720" src="images/mt-1.png"/>
</p>

For more screenshots go of the **Protocol Violations Dashboard** click [Here](dashboards/metacharacter_violations.md)


#### Protocol Violations
The Protocol Dashboard provides details for all the protocol related violations including `VIOL_HTTP_PROTOCOL`, `VIOL_EVASION`.

<p align="center">
<img width="720" src="images/protocol-1.png"/>
</p>

For more screenshots go of the **Protocol Violations Dashboard** click [Here](dashboards/protocol_violations.md)


#### SupportID Dashboard
The SupportID Dashboard provides details for a specific transaction (SupportID) that was logged by NGINX App Protect WAF. These include the following:
- Client/Server Information (Client IP/Port, Server IP/Port, X-Forwared-For, etc)
- Violation Details (Outcome, Request Status, Outcome Reson, etc)
- Bot Details (Bot Classm Bot Category, Bit Signature, etc)
- Device Details (NAP Device name, Vritual Server Name)
- Signatures Triggered
- Treat Campaign triggered
- Violation list
- Many more

<p align="center">
  <img width="720" src="images/support1.png">
</p>

For more screenshots go of the **SupportID Violations Dashboard** click [Here](dashboards/supprt_id.md)


## Installation

To run this Dashboard you will need to deploy following open source solutions. 
- Logstash/FluentD
- Elasticsearch 
- Grafana
- Docker
- Docker Compose

Steps will be provided on how to deploy all of the software in a Docker environment using Docker Compose. It is assumed that Docker and Docker Compose are already installed and configured on the system.
While we will provide the installation details for Logstash by default, at the end of the section we will provide details on how to use FluentD instead of Logstash.

### Clone the repo

Clone this repo to your local machine using `https://github.com/skenderidis/nap-dashboard` and switch the working directory to be `nap-policy-management/dashboard`

```shell
git clone https://github.com/skenderidis/nap-dashboard
cd nap-dashboard
```


### Install Logstash-Elasticsearch-Kibana-Grafana using docker-compose

```shell
TZ=Asia/Dubai && docker-compose up -d
```

***NOTES:***
>  - Logstash is configured on port 8515. Please use this port to send the logs from NGINX App Protect.
>  - Change the timezone used in the docker containers by altering the inline environment variable in the command above accordingly to your location. A list of TZ Database Names can be found [here](https://en.wikipedia.org/wiki/List_of_tz_database_time_zones).


### Configure Elasticsearch
>  In you are not running the following commands from your docker host, please change `localhost` to the hostname or IP address of your docker host.

1. Create index template for the NAP Logs
```shell
curl -d "@elastic/index-template-waf.json" -H 'Content-Type: application/json' -X PUT 'http://localhost:9200/_index_template/nginx-nap-logs'
```
Expected Response: `{"acknowledged":true}`


### Configure Grafana
1. Setup Grafana sources 
```shell
curl -d "@grafana/DS-waf-index.json" -H 'Content-Type: application/json' -u 'admin:admin' -X POST 'http://localhost:3000/api/datasources/'
curl -d "@grafana/DS-waf-decoded-index.json" -H 'Content-Type: application/json' -u 'admin:admin' -X POST 'http://localhost:3000/api/datasources/'
```

2. Deploy Grafana Dashboards.

To deploy the Grafana Dashboards goto `Import Dashboard` and input the Dashboard ID (as per the following table) on `Import via grafana.com` tab. 
<p align="center">
<img width="500" src="../images/grafana-id.png"/>       
</p>

| Dashboard Name                        | Dashboard ID  |  Grafana Source        |   Grafana Website                                                  | 
| -------------                         | :---:         |-------------           |  :---:                                                             | 
| Main Dashboard                        | 20052         | WAF-Logs               | <a href="https://grafana.com/grafana/dashboards/20052"> Link </a>  |
| SupportIDs                            | 20055         | WAF-Logs , WAF-Decoded | <a href="https://grafana.com/grafana/dashboards/20055"> Link </a>  |
| Attack Signatures                     | 20048         | WAF-Decoded            | <a href="https://grafana.com/grafana/dashboards/20048"> Link </a>  |
| File Types                            | 20049         | WAF-Logs               | <a href="https://grafana.com/grafana/dashboards/20049"> Link </a>  |
| Meta Characters                       | 20053         | WAF-Logs               | <a href="https://grafana.com/grafana/dashboards/20053"> Link </a>  |
| Parameters                            | 20054         | WAF-Logs               | <a href="https://grafana.com/grafana/dashboards/20054"> Link </a>  |
| Protocol                              | 20892         | WAF-Logs               | <a href="https://grafana.com/grafana/dashboards/20892"> Link </a>  |


### Configure Logging profile for NAP

For NAP working on an Ingress please configure the following logging format 
```yaml
apiVersion: appprotect.f5.com/v1beta1
kind: APLogConf
metadata:
  name: logconf
spec:
  content:
    format: user-defined
    format_string: "{\"campaign_names\":\"%threat_campaign_names%\",\"bot_signature_name\":\"%bot_signature_name%\",\"bot_category\":\"%bot_category%\",\"bot_anomalies\":\"%bot_anomalies%\",\"enforced_bot_anomalies\":\"%enforced_bot_anomalies%\",\"client_class\":\"%client_class%\",\"client_application\":\"%client_application%\",\"json_log\":%json_log%}"
    max_message_size: 30k
    max_request_size: "500"
    escaping_characters:
    - from: "%22%22"
      to: "%22"
  filter:
    request_type: illegal
```

For NAP working on a Docker or on a VM implementation, please configure the following logging format

```json
{
    "filter": {
        "request_type": "illegal"
    },
  
    "content": {
        "format": "user-defined",
        "format_string": "{\"campaign_names\":\"%threat_campaign_names%\",\"bot_signature_name\":\"%bot_signature_name%\",\"bot_category\":\"%bot_category%\",\"bot_anomalies\":\"%bot_anomalies%\",\"enforced_bot_anomalies\":\"%enforced_bot_anomalies%\",\"client_class\":\"%client_class%\",\"client_application\":\"%client_application%\",\"json_log\":%json_log%}", 
        "max_request_size": "500",
        "max_message_size": "30k",
        "escaping_characters": [
         {
            "from": "%22%22",
            "to": "%22"
         }  
      ]      
    }
  }
  ```