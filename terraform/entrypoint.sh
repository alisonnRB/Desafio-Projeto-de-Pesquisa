#!/bin/bash

# Espera o Keycloak estar disponível
echo "🔄 Esperando o Keycloak estar pronto..."
until curl -s http://keycloak:8080/realms/master/.well-known/openid-configuration; do
  sleep 5
done

echo "✅ Keycloak está pronto! Inicializando Terraform..."

cd /workspace
terraform init
terraform apply -auto-approve