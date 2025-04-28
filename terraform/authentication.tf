# Criação de um fluxo de autenticação personalizado para navegadores
resource "keycloak_authentication_flow" "custom_browser" {
  realm_id    = keycloak_realm.baita.id
  alias       = "custom-browser"
  description = "Fluxo de autenticação personalizado para o navegador"
  provider_id = "basic-flow"
}

resource "keycloak_authentication_bindings" "browser_authentication_binding" {
  realm_id     = keycloak_realm.baita.id
  browser_flow = keycloak_authentication_flow.custom_browser.alias
}

# Execução de autenticação com formulário de nome de usuário e senha
resource "keycloak_authentication_execution" "username_password" {
  realm_id          = keycloak_realm.baita.id
  parent_flow_alias = keycloak_authentication_flow.custom_browser.alias
  authenticator     = "auth-username-password-form"
  requirement       = "REQUIRED" # Usuário/senha é obrigatório
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

# Subflow condicional para verificação de OTP
resource "keycloak_authentication_subflow" "otp_authentication_subflow" {
  realm_id          = keycloak_realm.baita.id
  parent_flow_alias = keycloak_authentication_flow.custom_browser.alias
  alias             = "OTP Authentication Subflow"
  description       = "Flow to check OTP if configured"
  provider_id       = "basic-flow"
  requirement       = "CONDITIONAL" # A condição depende da execução anterior
}

# Execução de condição: checar se o usuário tem OTP configurado
resource "keycloak_authentication_execution" "otp_condition" {
  realm_id          = keycloak_realm.baita.id
  parent_flow_alias = keycloak_authentication_subflow.otp_authentication_subflow.alias
  authenticator     = "conditional-user-configured"
  requirement       = "REQUIRED"
}

# Configuração da execução de condição para verificar o OTP configurado
resource "keycloak_authentication_execution_config" "otp_condition_config" {
  realm_id     = keycloak_realm.baita.id
  alias        = "check-otp-config"
  execution_id = keycloak_authentication_execution.otp_condition.id

  config = {
    "authenticators" = "[\"otp\"]" # Verifica se o OTP está configurado
  }
}

# Execução do prompt de autenticação TOTP
resource "keycloak_authentication_execution" "totp_authentication_prompt" {
  realm_id          = keycloak_realm.baita.id
  parent_flow_alias = keycloak_authentication_subflow.otp_authentication_subflow.alias
  authenticator     = "auth-otp-form"
  requirement       = "REQUIRED" # Somente se o OTP estiver configurado
}

# Configuração do nível de garantia para a execução TOTP
resource "keycloak_authentication_execution_config" "totp_authentication_config" {
  realm_id     = keycloak_realm.baita.id
  alias        = "totp-prompt-context"
  execution_id = keycloak_authentication_execution.totp_authentication_prompt.id

  config = {
    "authnContextClassRef" = "5" # Nível de garantia alto
  }
  depends_on = [keycloak_authentication_execution.totp_authentication_prompt]
}
