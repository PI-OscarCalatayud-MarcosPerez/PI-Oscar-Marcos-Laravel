#!/bin/bash

# Define network name
NETWORK_NAME="pi-marcos-oscar-final_app-network"

echo "Attempting standard docker compose down..."
docker compose down

echo "Inspecting network $NETWORK_NAME for stuck containers..."
# Get list of container names attached to the network
CONTAINERS=$(docker network inspect $NETWORK_NAME -f '{{range .Containers}}{{.Name}} {{end}}' 2>/dev/null)

if [ -n "$CONTAINERS" ]; then
    echo "Found stuck containers: $CONTAINERS"
    echo "Force removing them..."
    docker rm -f $CONTAINERS
else
    echo "No stuck containers found attached to $NETWORK_NAME."
fi

echo "Removing network $NETWORK_NAME..."
docker network rm $NETWORK_NAME 2>/dev/null

if [ $? -eq 0 ]; then
    echo "Network removed successfully."
else
    echo "Failed to remove network (or it was already gone)."
fi

echo "------------------------------------------------"
echo "Cleanup finished. You can now run 'sudo make up'"
