<?php
session_start();

// Limpa todas as variáveis de sessão ativos
session_unset();

// Destrói a sessão no servidor
session_destroy();

// Redireciona de volta para o login com uma mensagem de sucesso
header("Location: ../public/index.html?status=desconectado");
exit; ?>