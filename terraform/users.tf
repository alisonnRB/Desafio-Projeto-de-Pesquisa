# Criação do usuário "user1"

resource "keycloak_user" "user1" {
  realm_id   = keycloak_realm.baita.id # Referência ao realm onde o usuário será criado
  username   = "user1"                 # Nome de usuário
  first_name = "User"                  # Primeiro nome do usuário
  last_name  = "Um"                    # Sobrenome do usuário
  email      = "user1@exemplo.com"     # E-mail do usuário
  enabled    = true                    # O usuário estará habilitado

  # Definição da senha inicial do usuário
  initial_password {
    value     = "password123" # Senha inicial do usuário
    temporary = false         # A senha não é temporária
  }
}

# Criação do usuário "user2"

resource "keycloak_user" "user2" {
  realm_id   = keycloak_realm.baita.id
  username   = "user2"
  first_name = "User"
  last_name  = "Dois"
  email      = "user2@exemplo.com"
  enabled    = true


  initial_password {
    value     = "password123"
    temporary = false
  }
}
