# Desafio-Projeto-de-Pesquisa
Desafio técnico do processo seletivo aberto para integrar o GT-BAITA, Construir um ecossitema baseado em OpenID Connect, usando ferramentas modernas de DevOps, automação e segurança. O desafio avalia sua capacidade de trabalhar com infraestrutura automatizada, protocolos de identidade e testes reprodutíveis — elementos centrais no GT-BAITA.

---

## 🛠️ Tecnologias Utilizadas

- **PI:** Keycloak
- **PS:** Laravel, Vite, PHP, mySql
- **Infraestrutura:** Docker, Terraform

---

## ✏️ Visão geral da infraestrutura docker:

![Texto alternativo](./infra_dokcer.png)

---


## 📂 Estrutura do Projeto

```
Desafio-Projeto-de-Pesquisa/
├── docker/                 # Configurações e arquivos relacionados ao Docker
├── ps/                     # Scripts e arquivos específicos do projeto
├── terraform/              # Arquivos de infraestrutura como código com Terraform
├── .gitlab-ci.yml          # Configuração de integração contínua para o GitLab CI
├── README.md               # Documentação principal do projeto
```

---

## 🚀 Como Rodar o Projeto

### 📌 Requisitos

Antes de iniciar, certifique-se de ter instalado:
- Docker (versão mais recente recomendada)

### 📜 Passos para Instalação

1. **Clone o repositório:**
   ```bash
   git clone https://github.com/alisonnRB/Desafio-Projeto-de-Pesquisa.git
   cd Desafio-Projeto-de-Pesquisa/
   ```

2. **Suba os containers da aplicação:**
   (Certifique-se que não há containers conflitantes para a instalação)
   ```bash
   cd docker
   docker compose up
   ```

3. **Pronto:**
   A aplicação PI estará disponível em [http://localhost:8080].
   A aplicação PS estará disponível em [http://localhost:8081].

---

## ⛑️ Como Testar o Projeto

### 🔧 Passos para os Testes

1. **Certifique-se de estar no local correto:**
   (Com a aplicação rodando)
   ```bash
   cd docker
   ```

2. **Rode os testes automatizados do Laravel:**
   ```bash
   docker compose -f docker-compose.yml exec ps php artisan test --env=testing
   ```

3. **Pronto:**
   Você verá os testes realizados:
   - **dynamic client creation:** Testa a criação dinamica de clientes no PI.
   - **login oidc:** Testa as etapas de login OIDC.
   - **attribute in user** Testa a correspondecia dos atributos no token de login.
   - **redirect when user has low access level:** Testa o nivel de acesso do usuario.

---

## 📨 Como fazer um registro dinamico

1. **Obtenha um token de acesso do Keycloak (via client credentials)**
   (Com a aplicação rodando)
   ```bash
   curl -X POST http://localhost:8080/realms/baita-realm/protocol/openid-connect/token \
   -H "Content-Type: application/x-www-form-urlencoded" \
   -d "grant_type=client_credentials" \
   -d "client_id=registrador" \
   -d "client_secret=secretKey"
   ```
   (Lembre-se caso o PI estiver em produção, um registrador publico deve ser criado)

2. **Registre dinamicamente seu cliente**
   Com o token de acesso obtido e sua rota de callback, envie os dados do cliente para o endpoint de registro dinâmico:

   ```bash
   curl -X POST http://localhost:8080/realms/baita-realm/clients-registrations/openid-connect \
   -H "Content-Type: application/json" \
   -H "Authorization: Bearer SEU_ACCESS_TOKEN" \
   -d '{
       "client_name": "meu-client-ficticio",
       "redirect_uris": ["http://SuaRotaDe.com/callback"],
       "grant_types": ["authorization_code"],
       "response_types": ["code"],
       "token_endpoint_auth_method": "client_secret_post"
     }'
   ```

2. **Armazene com segurança as credenciais do cliente gerado**
   Não exponha client_secret ou registration_access_token publicamente.
   Exemplo de resposta:
   ```bash
   {
    "client_id": "meu-client-ficticio",
    "client_secret": "segredo-gerado",
    "registration_access_token": "token-de-registro",
    "registration_client_uri": "https://.../clients/ID"
   }
   ```
