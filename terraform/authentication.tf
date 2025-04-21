# Criação de um fluxo de autenticação personalizado para navegadores
resource "keycloak_authentication_flow" "custom_browser" {
  realm_id    = keycloak_realm.baita.id
  alias       = "custom-browser"
  description = "Fluxo de autenticação personalizado para o navegador"
  provider_id = "basic-flow"
}

# Execução de autenticação com formulário de nome de usuário e senha
resource "keycloak_authentication_execution" "username_password" {
  realm_id          = keycloak_realm.baita.id
  parent_flow_alias = keycloak_authentication_flow.custom_browser.alias
  authenticator     = "auth-username-password-form"
  requirement       = "REQUIRED"
}

# Configurações adicionais da execução username_password, define nível de garantia
resource "keycloak_authentication_execution_config" "username_password" {
  realm_id     = keycloak_realm.baita.id
  alias        = "username-password-context"
  execution_id = keycloak_authentication_execution.username_password.id

  config = {
    "authnContextClassRef" = "1" # Nível de garantia baixo
  }
}

# Subfluxo ALTERNATIVE para TOTP
resource "keycloak_authentication_subflow" "totp_optional" {
  realm_id          = keycloak_realm.baita.id
  parent_flow_alias = keycloak_authentication_flow.custom_browser.alias
  alias             = "totp-subflow"
  provider_id       = "basic-flow"
  requirement       = "ALTERNATIVE" # pode ou não passar por esse subfluxo
}

# Verificação TOTP dentro do subfluxo
resource "keycloak_authentication_execution" "totp_in_subflow" {
  realm_id          = keycloak_realm.baita.id
  parent_flow_alias = keycloak_authentication_subflow.totp_optional.alias
  authenticator     = "auth-otp-form"
  requirement       = "REQUIRED" # Dentro do subfluxo vai requerir o uso
}

# Configuração do nível de garantia da execução TOTP
resource "keycloak_authentication_execution_config" "totp" {
  realm_id     = keycloak_realm.baita.id
  alias        = "totp-context"
  execution_id = keycloak_authentication_execution.totp_in_subflow.id

  config = {
    "authnContextClassRef" = "5"
  }
}
