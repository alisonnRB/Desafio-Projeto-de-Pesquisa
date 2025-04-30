# Criação do fluxo personalizado
resource "keycloak_authentication_flow" "custom_browser" {
  realm_id    = keycloak_realm.baita.id
  alias       = "custom-browser"
  description = "Fluxo de autenticação personalizado para o navegador"
  provider_id = "basic-flow"
}

# Ativação do fluxo como padrão de navegador
resource "keycloak_authentication_bindings" "browser_authentication_binding" {
  realm_id     = keycloak_realm.baita.id
  browser_flow = keycloak_authentication_flow.custom_browser.alias
}

# Cookie (login já existente)
resource "keycloak_authentication_execution" "cookie" {
  realm_id          = keycloak_realm.baita.id
  parent_flow_alias = keycloak_authentication_flow.custom_browser.alias
  authenticator     = "auth-cookie"
  requirement       = "DISABLED"
  priority          = 10
}

# Subfluxo principal
resource "keycloak_authentication_subflow" "step_up" {
  realm_id          = keycloak_realm.baita.id
  parent_flow_alias = keycloak_authentication_flow.custom_browser.alias
  alias             = "forms-step-up"
  description       = "username_password, conditional otp"
  provider_id       = "basic-flow"
  requirement       = "REQUIRED"
  priority          = 20
}

resource "keycloak_authentication_execution" "username_password" {
  realm_id          = keycloak_realm.baita.id
  parent_flow_alias = keycloak_authentication_subflow.step_up.alias
  authenticator     = "auth-username-password-form"
  requirement       = "REQUIRED"
  priority          = 40
}

resource "keycloak_authentication_subflow" "totp_condition_flow" {
  realm_id          = keycloak_realm.baita.id
  parent_flow_alias = keycloak_authentication_subflow.step_up.alias
  alias             = "condition-totp-flow"
  description       = "LoA"
  provider_id       = "basic-flow"
  requirement       = "CONDITIONAL"
  priority          = 50
}

resource "keycloak_authentication_execution" "otp_condition" {
  realm_id          = keycloak_realm.baita.id
  parent_flow_alias = keycloak_authentication_subflow.totp_condition_flow.alias
  authenticator     = "conditional-user-configured"
  requirement       = "REQUIRED"
  priority          = 50
}

resource "keycloak_authentication_execution_config" "otp_condition_config" {
  realm_id     = keycloak_realm.baita.id
  alias        = "check-otp-config"
  execution_id = keycloak_authentication_execution.otp_condition.id

  config = {
    authenticators = "[\"otp\"]"
  }
}

resource "keycloak_authentication_execution" "loa_2_condition" {
  realm_id          = keycloak_realm.baita.id
  parent_flow_alias = keycloak_authentication_subflow.totp_condition_flow.alias
  authenticator     = "conditional-level-of-authentication"
  requirement       = "REQUIRED"
  priority          = 60
}

resource "keycloak_authentication_execution_config" "loa_2_condition_config" {
  realm_id     = keycloak_realm.baita.id
  alias        = "loa-2-config"
  execution_id = keycloak_authentication_execution.loa_2_condition.id

  config = {
    "loa-condition-level" = "2"
    "loa-max-age"         = 35000
  }
}

resource "keycloak_authentication_execution" "totp_authentication_prompt" {
  realm_id          = keycloak_realm.baita.id
  parent_flow_alias = keycloak_authentication_subflow.totp_condition_flow.alias
  authenticator     = "auth-otp-form"
  requirement       = "REQUIRED"
  priority          = 70
}

