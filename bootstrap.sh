#!/bin/bash

chmod -R 777 bootstrap storage

awslocal s3api create-bucket --bucket localstack --acl public-read-write
awslocal sqs create-queue --queue-name localstack
awslocal ses verify-email-identity --email teste@teste.com
