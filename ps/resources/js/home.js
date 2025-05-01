class UserPage {
    user;
    element_box;
    button;

    constructor() {
        this.element_box = document.querySelector(".atribute-box");
        this.init();
    }

    async init() {
        this.user = await this.fetchUserInfo();
        this.innerContent();
    }

    async fetchUserInfo() {
        try {
            const response = await fetch('http://localhost:8081/keycloak/userInfo', {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`Erro: ${response.status}`);
            }

            const data = await response.json();
            console.log('Dados do usuário:', data);
            return data;
        } catch (error) {
            console.error('Erro ao buscar informações do usuário:', error);
            return null;
        }
    }

    innerContent() {
        if (!this.user) return;

        for (const [key, value] of Object.entries(this.user)) {
            if (["id", "created_at", "updated_at", "email_verified_at"].includes(key)) {
                continue;
            }

            let displayKey = key;
            let displayValue = value;

            if (key === "acr") {
                displayKey = "Nível de Garantia";
                switch (String(value)) {
                    case "0":
                        displayValue = "baixo";
                        break;
                    case "1":
                        displayValue = "médio";
                        break;
                    case "2":
                        displayValue = "alto";
                        break;
                    default:
                        displayValue = "inválido";
                }
            }

            const span = document.createElement('span');
            span.className = 'atribute';
            span.innerHTML = `<h2>${displayKey.toUpperCase()}:&nbsp;</h2><p>${String(displayValue).toUpperCase()}</p>`;
            this.element_box.appendChild(span);
        }
    }

    async logout() {
        await fetch('/keycloak/logout', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
    }
}

const page = new UserPage();

document.getElementById('logout').addEventListener('click', async () => {
    await page.logout();
    window.location.href = '/';
});