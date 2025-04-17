# Provedores que serão utilizados no projeto

terraform {
  required_providers {
    docker = {
      source  = "kreuzwerker/docker" # Mantém a versão do Docker
      version = "~> 2.0"
    }

    keycloak = {
      source  = "mrparkers/keycloak"
      version = "~> 4.3.0"
    }
  }
}

# Configurações do provedor Keycloak

provider "keycloak" {
  client_id = "admin-cli"             # ID do cliente para autenticação no Keycloak
  username  = "admin"                 # Nome de usuário admin para autenticação
  password  = "admin"                 # Senha do usuário admin
  url       = "http://localhost:8080" # URL do servidor Keycloak
  realm     = "master"                # Realm no qual o provedor será autenticado
}

