#!/bin/bash

service apache2 restart

# Run cron in the foreground.
cron -f