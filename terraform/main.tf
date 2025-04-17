# Provedores que serão utilizados no projeto

terraform {
  required_providers {
    docker = {
      source  = "kreuzwerker/docker" # Definindo o provedor Docker
      version = "~> 2.0"             # Especificando a versão do provedor Docker
    }

    keycloak = {
      source  = "mrparkers/keycloak" # Definindo o provedor Keycloak
      version = "~> 4.1.0"           # Especificando a versão do provedor Keycloak
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

