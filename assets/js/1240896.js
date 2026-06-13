document.addEventListener("DOMContentLoaded", function () {
    // 1. Captura o formulário pelo ID (garante que o teu <form> tem id="formPesquisa")
    const form = document.getElementById("formPesquisa");

    if (form) {
        form.addEventListener("submit", function (event) {
            // 2. Seleciona todos os inputs de texto dentro do formulário
            const inputs = form.querySelectorAll('input[type="text"]');

            inputs.forEach(function (input) {
                // Equivalente ao trim() do PHP
                let valor = input.value.trim();

                // Equivalente ao str_replace dos caracteres de controlo (\r, \n, \t)
                // Esta regex remove quebras de linha e tabulações invisíveis
                valor = valor.replace(/[\r\n\t]/g, '');

                // Opcional: Remove espaços duplos no meio do texto (ex: "Raio   X" vira "Raio X")
                valor = valor.replace(/\s+/g, ' ');

                // 3. Devolve o valor limpo de volta ao input antes do envio final
                input.value = valor;
            });

            // O formulário segue viagem para o PHP com os dados já limpos!
        });
    }
});