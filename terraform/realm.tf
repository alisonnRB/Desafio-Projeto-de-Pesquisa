# Criação do realm

resource "keycloak_realm" "baita" {
  realm        = "baita-realm" # Nome do realm
  enabled      = true          # Realm habilitado (disponível)
  display_name = "Baita Realm" # Nome exibido do realm

  registration_allowed     = true  # Permite o auto registro dos usuários
  edit_username_allowed    = true  # Permite que usuários alterem seu nome de usuário
  reset_password_allowed   = true  # Permite que os usuários redefinam suas senhas
  verify_email             = false # Desabilita a verificação de e-mail
  login_with_email_allowed = true  # Habilita o uso do e-mail como forma de login

  otp_policy {
    type              = "totp"     # Habilita a autenticação com TOTP
    algorithm         = "HmacSHA1" # Algoritmo de hash usado para o TOTP
    initial_counter   = 0          # Contador inicial para a geração dos códigos TOTP
    digits            = 6          # Quantidade de dígitos gerados pelo TOTP
    period            = 30         # Tempo em segundos para cada código TOTP
    look_ahead_window = 1          # Número de janelas de tempo que podem ser verificadas (como margem de erro)
  }
}

