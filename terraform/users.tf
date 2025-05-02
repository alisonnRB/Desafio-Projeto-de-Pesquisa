# Criação do usuário "user1"

resource "keycloak_user" "user1" {
  realm_id       = keycloak_realm.baita.id # Referência ao realm onde o usuário será criado
  username       = "user1"                 # Nome de usuário
  first_name     = "User"                  # Primeiro nome do usuário
  last_name      = "Um"                    # Sobrenome do usuário
  email          = "user1@exemplo.com"     # E-mail do usuário
  enabled        = true                    # O usuário estará habilitado
  email_verified = true

  attributes = {
    departamento = "TI"
    cargo        = "Analista"
    senioridade  = "Pleno"
  }


  # Definição da senha inicial do usuário
  initial_password {
    value     = "password123" # Senha inicial do usuário
    temporary = false         # A senha não é temporária
  }

  depends_on = [
    keycloak_realm_user_profile.baita_user_profile
  ]
}

# Criação do usuário "user2"

resource "keycloak_user" "user2" {
  realm_id       = keycloak_realm.baita.id
  username       = "user2"
  first_name     = "User"
  last_name      = "Dois"
  email          = "user2@exemplo.com"
  enabled        = true
  email_verified = true

  attributes = {
    "departamento" = "contabilidade"
    "cargo"       = "Analista"
    "senioridade"  = "senior"
  }


  initial_password {
    value     = "password123"
    temporary = false
  }

  depends_on = [
    keycloak_realm_user_profile.baita_user_profile
  ]
}

resource "keycloak_openid_client_scope" "user_attrs_scope" {
  realm_id = keycloak_realm.baita.id
  name     = "user-attrs"
  description = "Atributos personalizados do usuário"
}

resource "keycloak_openid_user_attribute_protocol_mapper" "departamento_mapper" {
  name                = "departamento"
  realm_id            = keycloak_realm.baita.id
  client_scope_id     = keycloak_openid_client_scope.user_attrs_scope.id

  user_attribute  = "departamento"
  claim_name      = "departamento"
  claim_value_type = "String"

  add_to_id_token     = true
  add_to_access_token = true
  add_to_userinfo     = true
}

resource "keycloak_openid_user_attribute_protocol_mapper" "cargo_mapper" {
  name             = "cargo"
  realm_id         = keycloak_realm.baita.id
  client_scope_id  = keycloak_openid_client_scope.user_attrs_scope.id

  user_attribute   = "cargo"
  claim_name       = "cargo"
  claim_value_type = "String"

  add_to_id_token     = true
  add_to_access_token = true
  add_to_userinfo     = true
}

resource "keycloak_openid_user_attribute_protocol_mapper" "senioridade_mapper" {
  name             = "senioridade"
  realm_id         = keycloak_realm.baita.id
  client_scope_id  = keycloak_openid_client_scope.user_attrs_scope.id

  user_attribute   = "senioridade"
  claim_name       = "senioridade"
  claim_value_type = "String"

  add_to_id_token     = true
  add_to_access_token = true
  add_to_userinfo     = true
}

