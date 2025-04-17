resource "keycloak_user" "user1" {
  realm_id   = keycloak_realm.baita.id
  username   = "user1"
  first_name = "User"
  last_name  = "Um"
  email      = "user1@exemplo.com"
  enabled    = true

  attributes = {
    nivel_acesso = "alto"
  }

  initial_password {
    value     = "senhaFortinha123"
    temporary = false
  }
}

resource "keycloak_user" "user2" {
  realm_id   = keycloak_realm.baita.id
  username   = "user2"
  first_name = "User"
  last_name  = "Dois"
  email      = "user2@exemplo.com"
  enabled    = true

  attributes = {
    nivel_acesso = "alto"
  }

  initial_password {
    value     = "senhaFortinha123"
    temporary = false
  }
}

# criação dos usuarios com atributos personalizados
