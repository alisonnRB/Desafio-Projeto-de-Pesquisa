# Criação de um cliente OpenID público que será usado para registrar outros clientes dinamicamente

resource "keycloak_openid_client" "dynamic_client_registration" {
  realm_id                     = keycloak_realm.baita.id            # ID do realm onde o cliente será criado
  client_id                    = "dynamic-client-registration"      # ID único do cliente
  name                         = "Dynamic Client Registration"      # Nome descritivo do cliente
  enabled                      = true                               # Habilita o cliente
  access_type                  = "PUBLIC"                           # Tipo de acesso (sem uso de secret)
  standard_flow_enabled        = true                               # Permite o uso do Authorization Code Flow
  direct_access_grants_enabled = true                               # Permite login direto
  valid_redirect_uris          = ["http://localhost:8081/callback"] # URIs válidas para redirecionamento após login
}

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
