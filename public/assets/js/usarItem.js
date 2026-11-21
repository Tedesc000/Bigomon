// Bolsa de itens fora da batalha: abas, detalhe do item, escolha do Pokémon (e do ataque a esquecer no TM)
(() => {
    const equipe = Array.isArray(window.EQUIPE_BOLSA) ? window.EQUIPE_BOLSA : [];

    const STATUS_LABELS = {
        veneno: 'PSN',
        paralisia: 'PAR',
        queimadura: 'BRN',
        sono: 'SLP',
        congelamento: 'FRZ',
    };

    const els = {
        abas: document.querySelectorAll('.bolsaAba'),
        itens: document.querySelectorAll('.bolsaItem'),
        vazia: document.getElementById('bolsaVazia'),
        detalheImg: document.getElementById('bolsaDetalheImg'),
        detalheNome: document.getElementById('bolsaDetalheNome'),
        detalheTexto: document.getElementById('bolsaDetalheTexto'),
        botaoUsar: document.getElementById('bolsaBotaoUsar'),

        party: document.getElementById('bolsaParty'),
        partyLista: document.getElementById('bolsaPartyLista'),
        partyPrompt: document.getElementById('bolsaPartyPrompt'),
        partyCancelar: document.getElementById('bolsaPartyCancelar'),

        msgFundo: document.getElementById('bolsaMensagemFundo'),
        msgCaixa: document.getElementById('bolsaMensagem'),
        msgImg: document.getElementById('bolsaMensagemImg'),
        msgTexto: document.getElementById('bolsaMensagemTexto'),
    };

    // Mesmos rótulos em inglês da tela de batalha
    const TIPOS_EN = {
        normal: 'NORMAL', lutador: 'FIGHTING', voador: 'FLYING', veneno: 'POISON', terra: 'GROUND',
        pedra: 'ROCK', inseto: 'BUG', fantasma: 'GHOST', aco: 'STEEL', fogo: 'FIRE', agua: 'WATER',
        planta: 'GRASS', eletrico: 'ELECTRIC', psiquico: 'PSYCHIC', gelo: 'ICE', dragao: 'DRAGON', sombrio: 'DARK',
    };

    let itemSelecionado = null;
    let pokemonDoTM = null; // Pokémon escolhido quando o TM pede o ataque a esquecer
    let enviando = false;
    let aoFecharMensagem = null;

    function sprite(idPokedex) {
        return `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${idPokedex}.png`;
    }

    function classeHp(pct) {
        if (pct > 50) return 'hp-high';
        if (pct > 20) return 'hp-mid';
        return 'hp-low';
    }

    function dadosItem(btn) {
        return {
            id_item: Number(btn.dataset.idItem),
            nome: btn.dataset.nome,
            descricao: btn.dataset.descricao,
            tipo: btn.dataset.tipo,
            usavel: btn.dataset.usavel === '1',
            imagem: btn.dataset.imagem,
            botao: btn,
        };
    }

    // ---------- Abas e detalhe ----------
    function filtrarBolso(bolso) {
        els.abas.forEach((aba) => aba.classList.toggle('ativa', aba.dataset.bolso === bolso));
        let visiveis = 0;
        els.itens.forEach((btn) => {
            const mostra = bolso === 'todos' || btn.dataset.bolso === bolso;
            btn.parentElement.classList.toggle('hidden', !mostra);
            if (mostra) visiveis++;
        });
        if (els.itens.length > 0) {
            els.vazia.classList.toggle('hidden', visiveis > 0);
        }
        if (itemSelecionado && itemSelecionado.botao.parentElement.classList.contains('hidden')) {
            limparDetalhe();
        }
    }

    function limparDetalhe() {
        itemSelecionado = null;
        els.itens.forEach((b) => b.classList.remove('selecionado'));
        els.detalheImg.classList.add('hidden');
        els.detalheNome.textContent = 'BOLSA';
        els.detalheTexto.textContent = 'Escolha um item para ver o que ele faz.';
        els.botaoUsar.classList.add('hidden');
    }

    function selecionarItem(btn) {
        itemSelecionado = dadosItem(btn);
        els.itens.forEach((b) => b.classList.toggle('selecionado', b === btn));
        els.detalheImg.src = itemSelecionado.imagem;
        els.detalheImg.classList.remove('hidden');
        els.detalheNome.textContent = itemSelecionado.nome.toUpperCase();

        let texto = itemSelecionado.descricao;
        if (itemSelecionado.tipo === 'pokebola') texto += ' Só pode ser usada em batalhas no matinho.';
        if (itemSelecionado.tipo === 'tm') texto += ' Pode ser usado quantas vezes quiser.';
        els.detalheTexto.textContent = texto;

        els.botaoUsar.classList.toggle('hidden', !itemSelecionado.usavel);
    }

    // ---------- Escolha do Pokémon ----------
    function abrirParty() {
        if (!itemSelecionado || !itemSelecionado.usavel) return;
        pokemonDoTM = null;
        els.party.classList.remove('hidden');
        renderizarEquipe();
    }

    function fecharParty() {
        if (enviando) return;
        pokemonDoTM = null;
        els.party.classList.add('hidden');
    }

    function tagDoPokemon(poke) {
        if (itemSelecionado.tipo === 'evolucao') {
            return poke.pode_evoluir
                ? '<span class="bolsaTag bolsaTagOk">PODE EVOLUIR</span>'
                : '<span class="bolsaTag">NÃO EVOLUI</span>';
        }
        if (poke.hp_atual <= 0) return '<span class="bolsaTag bolsaTagDesmaiado">DESMAIADO</span>';
        if (poke.status && STATUS_LABELS[poke.status]) {
            return `<span class="status-badge status-${poke.status}">${STATUS_LABELS[poke.status]}</span>`;
        }
        return '';
    }

    function renderizarEquipe() {
        els.partyLista.innerHTML = '';
        els.partyPrompt.textContent = itemSelecionado.tipo === 'tm'
            ? `Ensinar ${itemSelecionado.nome.replace(/^TM\s*/i, '').toUpperCase()} para qual POKÉMON?`
            : `Usar ${itemSelecionado.nome.toUpperCase()} em qual POKÉMON?`;

        if (equipe.length === 0) {
            els.partyPrompt.textContent = 'Você não tem POKÉMON na equipe.';
            return;
        }

        equipe.forEach((poke) => {
            const pct = poke.hp_maximo > 0 ? Math.max(0, Math.min(100, (poke.hp_atual / poke.hp_maximo) * 100)) : 0;
            const card = document.createElement('button');
            card.type = 'button';
            card.className = 'bolsaCard' + (poke.hp_atual <= 0 ? ' desmaiado' : '');
            card.innerHTML = `
                <img class="bolsaCardSprite" src="${sprite(poke.id_pokedex)}" alt="">
                <span class="bolsaCardInfo">
                    <span class="bolsaCardTopo">
                        <span class="bolsaCardNome"></span>
                        ${tagDoPokemon(poke)}
                    </span>
                    <span class="bolsaHpLinha">
                        <span class="bolsaHpRotulo">HP</span>
                        <span class="bolsaHpTrilho"><span class="bolsaHpBarra ${classeHp(pct)}" style="width:${pct}%"></span></span>
                    </span>
                    <span class="bolsaHpNumeros">${poke.hp_atual} / ${poke.hp_maximo}</span>
                </span>
            `;
            card.querySelector('.bolsaCardNome').textContent = poke.nome;
            card.addEventListener('click', () => escolherPokemon(poke));
            els.partyLista.appendChild(card);
        });

        const primeiro = els.partyLista.querySelector('.bolsaCard');
        if (primeiro) primeiro.focus();
    }

    function escolherPokemon(poke) {
        if (enviando) return;
        if (itemSelecionado.tipo !== 'tm' || (poke.movimentos || []).length < 4) {
            enviar({ id_item: itemSelecionado.id_item, id_pokemon: poke.id_pokemon });
            return;
        }
        pokemonDoTM = poke;
        renderizarMovimentos(poke);
    }

    // TM com 4 ataques: escolher qual esquecer
    function renderizarMovimentos(poke) {
        els.partyLista.innerHTML = '';
        els.partyPrompt.textContent = `${poke.nome} já conhece 4 ataques. Qual esquecer?`;

        poke.movimentos.forEach((mov) => {
            const card = document.createElement('button');
            card.type = 'button';
            card.className = 'bolsaCard bolsaCardMov';
            card.innerHTML = `
                <span class="bolsaCardNome"></span>
                <span class="bolsaMovTipo type-${mov.tipo_nome}"></span>
                <span class="bolsaMovPoder">POW ${mov.poder}</span>
            `;
            card.querySelector('.bolsaCardNome').textContent = mov.nome.toUpperCase();
            card.querySelector('.bolsaMovTipo').textContent = TIPOS_EN[mov.tipo_nome] || (mov.tipo_nome || '').toUpperCase();
            card.addEventListener('click', () => enviar({
                id_item: itemSelecionado.id_item,
                id_pokemon: poke.id_pokemon,
                id_movimento_esquecer: mov.id_movimento,
            }));
            els.partyLista.appendChild(card);
        });

        const primeiro = els.partyLista.querySelector('.bolsaCard');
        if (primeiro) primeiro.focus();
    }

    // ---------- Mensagem de resultado ----------
    function mostrarMensagem(texto, imagem, depois) {
        els.msgTexto.textContent = texto;
        if (imagem) {
            els.msgImg.src = imagem;
            els.msgImg.classList.remove('hidden');
        } else {
            els.msgImg.classList.add('hidden');
        }
        aoFecharMensagem = depois || null;
        els.msgFundo.classList.remove('hidden');
        els.msgCaixa.focus();
    }

    function fecharMensagem() {
        if (els.msgFundo.classList.contains('hidden')) return;
        els.msgFundo.classList.add('hidden');
        const depois = aoFecharMensagem;
        aoFecharMensagem = null;
        if (depois) depois();
    }

    async function enviar(body) {
        if (enviando) return;
        enviando = true;
        const item = itemSelecionado;

        try {
            const resp = await fetch('?modulo=item&acao=usar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(body),
            });
            const data = await resp.json();
            enviando = false;

            if (!resp.ok || !data.ok) {
                // Erro: mantém a tela de escolha aberta para tentar outro Pokémon
                mostrarMensagem(data.erro || 'Não foi possível usar o item.', item.imagem, null);
                return;
            }

            fecharParty();
            const imagem = item.tipo === 'evolucao' && data.id_pokedex ? sprite(data.id_pokedex) : item.imagem;
            mostrarMensagem((data.mensagens || []).join('\n'), imagem, () => window.location.reload());
        } catch (err) {
            enviando = false;
            mostrarMensagem('Erro de conexão ao usar o item.', item.imagem, null);
        }
    }

    // ---------- Eventos ----------
    els.abas.forEach((aba) => aba.addEventListener('click', () => filtrarBolso(aba.dataset.bolso)));
    els.itens.forEach((btn) => {
        btn.addEventListener('click', () => selecionarItem(btn));
        btn.addEventListener('dblclick', () => {
            selecionarItem(btn);
            abrirParty();
        });
    });
    els.botaoUsar.addEventListener('click', abrirParty);
    els.partyCancelar.addEventListener('click', () => {
        if (pokemonDoTM) {
            pokemonDoTM = null;
            renderizarEquipe();
            return;
        }
        fecharParty();
    });
    els.msgFundo.addEventListener('click', fecharMensagem);

    document.addEventListener('keydown', (e) => {
        if (!els.msgFundo.classList.contains('hidden')) {
            if (['Enter', ' ', 'Escape'].includes(e.key)) {
                e.preventDefault();
                fecharMensagem();
            }
            return;
        }
        if (e.key === 'Escape' && !els.party.classList.contains('hidden')) {
            e.preventDefault();
            els.partyCancelar.click();
        }
    });
})();
