# How to use the dashboard system

### Requirements
#### Docker
Docker Desktop on Windows or installation package for docker + docker compose from the official site

#### SQL Dump with Data
Since the data inherits information about the adresses of users it is recommended to not push the sql dump into github

### Steps to Start the Deployment
1. build the Docker Image for the PHP Service
```
docker build .
```

2. start the containers
```
docker-compose up -d
```
or
```
docker compose up -d
```
The -d is optional but it prevents the instant logging of the containers when they are started

3. Import the mysql dump
Windows
```
cmd /c "docker exec -i user-query-dashboard-db-1 mysql -u root -ppassword userdata < streetsearch_log.sql"
```
Linux
```
docker exec -i user-query-dashboard-db-1 mysql -u root -ppassword userdata < streetsearch_log.sql
```

4. Open localhost:8080 in a webbrowser of your choice on your machine