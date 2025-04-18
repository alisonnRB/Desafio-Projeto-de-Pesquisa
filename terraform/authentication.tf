# Criação de um fluxo de autenticação personalizado para navegadores

resource "keycloak_authentication_flow" "custom_browser" {
  realm_id    = keycloak_realm.baita.id                                # ID do realm onde o fluxo será criado
  alias       = "custom-browser"                                       # Nome identificador único para o fluxo
  description = "Fluxo de autenticação personalizado para o navegador" # Descrição do fluxo
  provider_id = "basic-flow"                                           # Tipo de fluxo
}

# Execução de autenticação com formulário de nome de usuário e senha

resource "keycloak_authentication_execution" "username_password" {
  realm_id          = keycloak_realm.baita.id                           # Realm onde a execução será usada
  parent_flow_alias = keycloak_authentication_flow.custom_browser.alias # Nome do fluxo ao qual essa execução pertence
  authenticator     = "auth-username-password-form"                     # Tipo de autenticação usada
  requirement       = "REQUIRED"                                        # Essa etapa é obrigatória
  priority          = 0                                                 # Prioridade de execução
}

# Execução de autenticação com verificação TOTP (como app de autenticação de dois fatores)
resource "keycloak_authentication_execution" "totp" {
  realm_id          = keycloak_realm.baita.id
  parent_flow_alias = keycloak_authentication_flow.custom_browser.alias
  authenticator     = "auth-otp-form"
  requirement       = "ALTERNATIVE"
  priority          = 1
}

# Associa o fluxo de autenticação personalizado ao navegador no realm

resource "keycloak_authentication_bindings" "baita_bindings" {
  realm_id     = keycloak_realm.baita.id                           # Realm onde a associação será feita
  browser_flow = keycloak_authentication_flow.custom_browser.alias # Define o fluxo a ser usado quando o login vier de um navegador
}

# Configurações adicionais da execução username_password, define nível de garantia

resource "keycloak_authentication_execution_config" "username_password" {
  realm_id     = keycloak_realm.baita.id
  alias        = "username-password-context"                            # Nome identificador da configuração
  execution_id = keycloak_authentication_execution.username_password.id # ID da execução que será configurada

  config = {
    "authnContextClassRef" = "1" # Nível de garantia baixo
  }
}

# Configurações adicionais da execução TOTP

resource "keycloak_authentication_execution_config" "totp" {
  realm_id     = keycloak_realm.baita.id
  alias        = "totp-context"
  execution_id = keycloak_authentication_execution.totp.id

  config = {
    "authnContextClassRef" = "5" # Nível de garantia alto
  }
}
