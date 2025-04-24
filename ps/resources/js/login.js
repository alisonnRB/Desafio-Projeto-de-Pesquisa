document.getElementById('login-button').addEventListener('click', async () => {
    const response = await fetch('/api/keycloak/client'); // endpoint que busca config do banco
    const config = await response.json();

    const keycloakUrl = config.keycloak_url;
    const realm = config.realm;
    const clientId = config.client_id;
    const redirectUri = "http://localhost:8081/callback";

    const responseType = 'code';

    window.location.href = `${keycloakUrl}/realms/${realm}/protocol/openid-connect/auth?response_type=${responseType}&client_id=${clientId}&redirect_uri=${redirectUri}`;
});
