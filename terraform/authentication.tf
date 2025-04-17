# Criação do fluxo de autenticação personalizado para navegador

resource "keycloak_authentication_flow" "custom_browser" {
  realm_id    = keycloak_realm.baita.id # Referência ao ID do realm que você está configurando
  alias       = "custom-browser"        # Nome único do fluxo de autenticação 
  description = "Custom browser flow"   # Descrição do fluxo
  provider_id = "basic-flow"            # Tipo do fluxo
}

# Execução de autenticação com formulário de nome de usuário e senha

resource "keycloak_authentication_execution" "username_password" {
  realm_id          = keycloak_realm.baita.id                           # Referência ao ID do realm
  parent_flow_alias = keycloak_authentication_flow.custom_browser.alias # Fluxo de autenticação associado
  authenticator     = "auth-username-password-form"                     # Tipo de autenticação
  requirement       = "REQUIRED"                                        # Requisito
}

# Execução de autenticação com formulário de TOTP

resource "keycloak_authentication_execution" "totp" {
  realm_id          = keycloak_realm.baita.id                           # Referência ao ID do realm
  parent_flow_alias = keycloak_authentication_flow.custom_browser.alias # Fluxo de autenticação associado
  authenticator     = "auth-otp-form"                                   # Tipo de autenticação
  requirement       = "ALTERNATIVE"                                     # Requisito
}

# Associando o fluxo de autenticação ao navegador para o realm

resource "keycloak_authentication_bindings" "baita_bindings" {
  realm_id     = keycloak_realm.baita.id                           # Referência ao ID do realm
  browser_flow = keycloak_authentication_flow.custom_browser.alias # Fluxo de autenticação do navegador
}
