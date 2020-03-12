#!/bin/bash

# Possible workspaces
# declare -a WORKSPACES=(api)

# Validate user-supplied workspace.
# validate_workspace () {
#     local e match="$1"
#     shift
#     for e; do [[ "$e" == "$match" ]] && return 0; done
#     echo 1
# }

# if [[ $(validate_workspace "$1" "${WORKSPACES[@]}") == 1 ]]; then
#     echo "Invalid workspace provided. Available ones are: 'api'"
#     exit 1
# fi

# Build docker workspace container name.
# The pattern is luxstay-$1-workspace.
workspace="akinia-crawler-workspace"

# Execute commom commands inside workspace container.
if [ $# -gt 1 ]; then
    if [ "$2" == "artisan" ] || [ "$2" == "art" ]; then
        shift 2
        docker exec -it "$workspace" php artisan "$@"

    elif [ "$2" == "composer" ] || [ "$2" == "comp" ]; then
        shift 2
        docker exec -it "$workspace" composer "$@"

    elif [ "$2" == "npm" ]; then
        shift 2
        docker exec -it "$workspace" npm "$@"

    elif [ "$2" == "yarn" ]; then
        shift 2
        docker exec -it "$workspace" yarn "$@"

    elif [ "$2" == "test" ]; then
        shift 2
        docker exec -it "$workspace" ./vendor/bin/phpunit "$@"
    else
        docker exec -it "$workspace" bash
    fi
else
    docker exec -it "$workspace" bash
fi
