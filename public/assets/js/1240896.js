
    // 1. Mapeamos o formulário e a zona de erro do HTML
    const form = document.getElementById('formLogin');
    const msgErro = document.getElementById('mensagem-erro');

    // 2. Ouvimos o momento em que o utilizador clica no botão "Entrar"
    form.addEventListener('submit', function(event) {
        
        // Impede o formulário de recarregar a página automaticamente
        event.preventDefault(); 

        // 3. Capturamos os valores escritos nos inputs
        const usernameDigitado = document.getElementById('username').value.trim();
        const passwordDigitada = document.getElementById('password').value;

        // 4. Definimos o utilizador e senha corretos (Estático para teste)
        const userCorreto = "admin";
        const passCorreta = "1234";

        // 5. Fazemos a verificação
        if (usernameDigitado === userCorreto && passwordDigitada === passCorreta) {
            
            // Se estiver correto, simula o início de sessão guardando no armazenamento local do navegador
            localStorage.setItem('usuario_logado', 'true');
            
            // Redireciona para o teu Dashboard
            window.location.href = "../private/dashboard.php";
            
        } else {
            // Se estiver errado, mostra o alerta oculto do Bootstrap com a mensagem
            msgErro.textContent = "Utilizador ou password incorretos!";
            msgErro.classList.remove('d-none');
        }
    });
