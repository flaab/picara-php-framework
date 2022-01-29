#!/bin/bash
# Gives permissions to apache writing folders
chmod -R 777 app/html/cache
chmod -R 777 app/log
chmod -R 777 app/log/*
chmod -R 777 resources/cache/
chmod -R 777 resources/db/
chmod -R 777 resources/db/*
chmod -R 777 resources/tmp/
chmod -R 777 resources/tmp/layout
chmod -R 777 resources/tmp/view
chmod -R 777 htdocs/assets/model_files
