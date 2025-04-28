# Criação de um cliente confidencial que atuará como registrador de outros clientes

resource "keycloak_openid_client" "client_registrador" {
  realm_id                     = keycloak_realm.baita.id
  client_id                    = "registrador"
  name                         = "Client Registrador"
  access_type                  = "CONFIDENTIAL" # Cliente confidencial (com uso de secret)
  enabled                      = true
  service_accounts_enabled     = true        # Ativa a conta de serviço para esse cliente
  standard_flow_enabled        = false       # Desativa o Authorization Code Flow
  direct_access_grants_enabled = false       # Desativa login direto com usuário e senha
  client_secret                = "secretKey" # secret usado para autenticação do cliente
}

# Obtém informações do cliente interno do Keycloak responsável por gerenciamento do realm

data "keycloak_openid_client" "realm_management" {
  realm_id  = keycloak_realm.baita.id # Realm onde buscar o cliente
  client_id = "realm-management"      # Nome padrão do cliente de gerenciamento interno do Keycloak
}

# Concede à conta de serviço do cliente "registrador" a permissão para gerenciar outros clientes no realm
resource "keycloak_openid_client_service_account_role" "registrador_manage_clients" {
  realm_id                = keycloak_realm.baita.id                                           # Realm onde a permissão será aplicada
  service_account_user_id = keycloak_openid_client.client_registrador.service_account_user_id # ID da conta de serviço do cliente
  client_id               = data.keycloak_openid_client.realm_management.id                   # ID do cliente
  role                    = "manage-clients"                                                  # Nome da role que permite gerenciar clientes
}


