# NAP Dashboard - Installation

To run this Dashboard you will need to deploy following open source solutions. 
- Logstash
- Elasticsearch 
- Grafana
- Docker
- Docker Compose
- Python 3.7+

Steps will be provided on how to deploy all of the software in a Docker environment using Docker Compose. It is assumed that Docker and Python 3.7+ is already installed and configured on the system.

### Clone the repo

Clone this repo to your local machine using `https://github.com/skenderidis/nap-dashboard` and switch the working directory to be `nap-policy-management/dashboard`

```shell
git clone https://github.com/skenderidis/nap-dashboard
cd nap-dashboard
```

### Install Elasticsearch-Kibana-Grafana using docker-compose

```shell

TZ=Asia/Dubai && docker-compose up -d

```
NOTES:
>  - Change the timezone used in the docker containers by altering the inline environment variable in the command above accordingly to your location. A list of TZ Database Names can be found [here](https://en.wikipedia.org/wiki/List_of_tz_database_time_zones).


### Install FluentD inside Kubernetes
Go to folder `fluentd` and install all the manifests

```shell
cd fluentd
kubectl apply -f ns-sa.yaml
kubectl apply -f configmap.yaml
kubectl apply -f deployment.yaml
kubectl apply -f service.yaml
```
In few seconds FluentD will be running and will be ready to get your 


### Configure Elasticsearch
>  In you are not running the following commands from your docker host, please change `localhost` to the hostname or IP address of your docker host.

2. Create index template for Access-Logs and WAF-Logs
```shell
curl -d "@elastic/index-template-access.json" -H 'Content-Type: application/json' -X PUT 'http://localhost:9200/_index_template/nginx-access-logs'
curl -d "@elastic/index-template-waf.json" -H 'Content-Type: application/json' -X PUT 'http://localhost:9200/_index_template/nginx-nap-logs'
```
Expected Response: `{"acknowledged":true}`


### Configure Grafana
1. Setup Grafana sources 
```shell
curl -d "@grafana/DS-waf-index.json" -H 'Content-Type: application/json' -u 'admin:admin' -X POST 'http://localhost:3000/api/datasources/'
curl -d "@grafana/DS-waf-decoded-index.json" -H 'Content-Type: application/json' -u 'admin:admin' -X POST 'http://localhost:3000/api/datasources/'
curl -d "@grafana/DS-nap-l7-dos.json" -H 'Content-Type: application/json' -u 'admin:admin' -X POST 'http://localhost:3000/api/datasources/'
curl -d "@grafana/DS-access-logs.json" -H 'Content-Type: application/json' -u 'admin:admin' -X POST 'http://localhost:3000/api/datasources/'
```

3. Deploy Grafana Dashboards.

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
| L7 DoS Overview                       | 20050         | WAF-Logs               | <a href="https://grafana.com/grafana/dashboards/20050"> Link </a>  |
| L7 DoS Details                        | 20051         | WAF-Logs               | <a href="https://grafana.com/grafana/dashboards/20051"> Link </a>  |
| Access Logs                           | 20047         | WAF-Logs               | <a href="https://grafana.com/grafana/dashboards/20047"> Link </a>  |



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
    format_string: "%json_log%"
    max_message_size: 20k
    max_request_size: "500"
    escaping_characters:
    - from: "%22%22"
      to: "%22"
  filter:
    request_type: illegal
```

## Support

Please open a GitHub issue for any problem or enhancement you need.

