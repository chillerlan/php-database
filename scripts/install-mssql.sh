#!/bin/bash

# https://docs.microsoft.com/en-us/sql/linux/sample-unattended-install-ubuntu
# Use the following variables to control your install:

# Password for the SA user (required)
MSSQL_SA_PASSWORD='DBtestytest42'

# Product ID of the version of SQL server you're installing
# Must be evaluation, developer, express, web, standard, enterprise, or your 25 digit product key
# Defaults to developer
MSSQL_PID='developer'

echo Adding Microsoft repositories...
sudo curl https://packages.microsoft.com/keys/microsoft.asc | sudo apt-key add -
sudo add-apt-repository "$(curl https://packages.microsoft.com/config/ubuntu/20.04/mssql-server-2019.list)"
sudo add-apt-repository "$(curl https://packages.microsoft.com/config/ubuntu/20.04/prod.list)"

echo Running apt-get update -y...
sudo apt-get update -y

echo Installing SQL Server...
sudo apt-get install -y mssql-server

echo Running mssql-conf setup...
sudo MSSQL_SA_PASSWORD=$MSSQL_SA_PASSWORD \
     MSSQL_PID=$MSSQL_PID \
     /opt/mssql/bin/mssql-conf -n setup accept-eula

echo Installing mssql-tools and unixODBC developer...
sudo ACCEPT_EULA=Y apt-get install -y mssql-tools unixodbc-dev

# Restart SQL Server after installing:
echo Restarting SQL Server...
sudo systemctl restart mssql-server

sudo /opt/mssql-tools/bin/sqlcmd -S localhost -U SA -P $MSSQL_SA_PASSWORD -Q "CREATE DATABASE dbtest;"

echo Done!
