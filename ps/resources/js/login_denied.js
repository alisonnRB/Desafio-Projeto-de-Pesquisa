document.getElementById('login-button').addEventListener('click', async () => {
    const response = await fetch('/api/keycloak/client');
    const config = await response.json();

    const keycloakUrl = config.keycloak_url;
    const realm = config.realm;

    window.location.href = `${keycloakUrl}/realms/${realm}/account`;
});
