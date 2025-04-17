provider "keycloak" {
  client_id = "admin-cli"
  username  = "admin"
  password  = "admin"
  url       = "http://localhost:8080"
  realm     = "master"
}

# configurações básicas para o keycloak

resource "keycloak_realm" "baita" {
  realm        = "baita-realm"
  enabled      = true
  display_name = "Baita Realm"

  registration_allowed     = true # auto registro
  edit_username_allowed    = true # edição de username
  reset_password_allowed   = true # edição de senha
  verify_email             = true # verificação de email
  login_with_email_allowed = true # login com email
}

# configurações do realm no keycloak
