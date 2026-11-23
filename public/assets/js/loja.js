(() => {
    const saldoEl = document.getElementById('saldoLoja');
    const forms = document.querySelectorAll('.formComprarItem');

    function atualizarBotoesSaldo(novoSaldo) {
        document.querySelectorAll('.cardItemLoja').forEach(card => {
            const form = card.querySelector('.formComprarItem');
            if (!form) return;
            const botao = form.querySelector('.botaoComprarItem');
            if (!botao) return;

            // Se for TM adquirido, não altera
            if (botao.disabled && botao.getAttribute('title') === 'Você já tem esse TM') {
                return;
            }

            const preco = parseFloat(card.dataset.preco || 0);
            const semSaldo = preco > novoSaldo;
            botao.disabled = semSaldo;
            botao.setAttribute('title', semSaldo ? 'Saldo insuficiente' : 'Comprar item');
        });
    }

    forms.forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const botao = form.querySelector('.botaoComprarItem');
            if (botao && botao.disabled) return;

            const inputId = form.querySelector('input[name="id_item"]');
            const idItem = inputId ? inputId.value : null;
            if (!idItem) return;

            if (botao) botao.disabled = true;

            try {
                const resp = await fetch('?modulo=loja&acao=comprar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ id_item: Number(idItem) })
                });

                const data = await resp.json();

                if (!resp.ok || !data.ok) {
                    alert(data.erro || 'Não foi possível comprar este item.');
                    if (botao) botao.disabled = false;
                    return;
                }

                // Atualiza o saldo no topo
                if (saldoEl) {
                    saldoEl.innerHTML = `BIGOREAIS: R$ ${data.novoSaldoFormatado} <img id="dinheiro" src="../public/assets/img/icons/pokedollar.webp" alt="bigoreais">`;
                }

                // Se comprou um TM, marca como adquirido
                if (data.eh_tm) {
                    form.innerHTML = `
                        <button type="button" class="botaoComprarItem" disabled title="Você já tem esse TM">
                            <span class="defaultText">ADQUIRIDO</span>
                        </button>
                    `;
                }

                // Atualiza botões cujo saldo não seja mais suficiente
                atualizarBotoesSaldo(data.novoSaldo);

            } catch (err) {
                alert('Erro de conexão ao tentar comprar.');
                if (botao) botao.disabled = false;
            }
        });
    });
})();
