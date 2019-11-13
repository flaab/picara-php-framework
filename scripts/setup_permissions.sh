#!/bin/bash
# Gives permissions to apache writing folders
chmod -R 777 app/cache
chmod -R 777 app/log
chmod -R 777 app/log/*
chmod -R 777 cache/
chmod -R 777 db/
chmod -R 777 db/*
chmod -R 777 tmp/
chmod -R 777 tmp/layout
chmod -R 777 tmp/view
chmod -R 777 htdocs/webroot/model_files
