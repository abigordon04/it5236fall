#!/usr/bin/env bash
git pull && docker build --no-cache -t doit-app . && docker stop -t 0 doit-app-container && docker rm doit-app-container && docker run -d --name doit-app-container -p 80:80 doit-app
