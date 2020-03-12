#!/bin/bash

MODULES=''

while read -r MODULE; do
    [[ "$MODULE" =~ ^#.*$ ]] && continue
    MODULES+=" $MODULE"
done < modules

docker-compose -f docker-compose.yml up -d $@ $MODULES
