(() => {
    const estado = window.BATALHA_INICIAL;
    if (!estado) {
        return;
    }

    const els = {
        battleMenu: document.getElementById('battleMenu'),
        battleMessage: document.getElementById('battleMessage'),
        battleMessageText: document.getElementById('battleMessageText'),
        nextArrow: document.getElementById('nextArrow'),
        battleActions: document.getElementById('battleActions'),
        btnFight: document.getElementById('btnFight'),
        btnBag: document.getElementById('btnBag'),
        btnPokemon: document.getElementById('btnPokemon'),
        btnRun: document.getElementById('btnRun'),

        fightPanel: document.getElementById('fightPanel'),
        moveGrid: document.getElementById('moveGrid'),
        moveDetailType: document.getElementById('moveDetailType'),
        moveDetailCat: document.getElementById('moveDetailCat'),
        moveDetailPower: document.getElementById('moveDetailPower'),
        moveDetailAcc: document.getElementById('moveDetailAcc'),
        btnMoveBack: document.getElementById('btnMoveBack'),

        wildName: document.getElementById('wildName'),
        wildStatusBadge: document.getElementById('wildStatusBadge'),
        wildHpBar: document.getElementById('wildHpBar'),
        wildSpriteWrap: document.getElementById('wildSpriteWrap'),
        wildSprite: document.getElementById('wildSprite'),

        playerName: document.getElementById('playerName'),
        playerStatusBadge: document.getElementById('playerStatusBadge'),
        playerHpBar: document.getElementById('playerHpBar'),
        playerHpNumbers: document.getElementById('playerHpNumbers'),
        playerSpriteWrap: document.getElementById('playerSpriteWrap'),
        playerSprite: document.getElementById('playerSprite'),

        partyOverlay: document.getElementById('partyOverlay'),
        partyActiveSlot: document.getElementById('partyActiveSlot'),
        partyBenchList: document.getElementById('partyBenchList'),
        partyPrompt: document.getElementById('partyPrompt'),
        btnPartyCancel: document.getElementById('btnPartyCancel'),
        partyActionBox: document.getElementById('partyActionBox'),
        btnPartySwitch: document.getElementById('btnPartySwitch'),
        btnPartyActionCancel: document.getElementById('btnPartyActionCancel'),
    };

    const STATUS_LABELS = {
        veneno: 'PSN',
        paralisia: 'PAR',
        queimadura: 'BRN',
        sono: 'SLP',
        congelamento: 'FRZ',
    };

    const TYPE_LABELS_EN = {
        normal: 'NORMAL',
        lutador: 'FIGHTING',
        voador: 'FLYING',
        veneno: 'POISON',
        terra: 'GROUND',
        pedra: 'ROCK',
        inseto: 'BUG',
        fantasma: 'GHOST',
        aco: 'STEEL',
        fogo: 'FIRE',
        agua: 'WATER',
        planta: 'GRASS',
        eletrico: 'ELECTRIC',
        psiquico: 'PSYCHIC',
        gelo: 'ICE',
        dragao: 'DRAGON',
        sombrio: 'DARK',
    };

    let ocupado = false;
    let modoAtual = 'main'; // 'main' | 'fight' | 'message' | 'party'
    let pokemonSelecionadoParty = null;
    let resolverCliqueMensagem = null;

    function spriteFrontUrl(idPokedex) {
        return `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/versions/generation-viii/brilliant-diamond-shining-pearl/${idPokedex}.webp`;
    }

    function spriteFrontFallback(idPokedex) {
        return `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${idPokedex}.png`;
    }

    function spriteBackUrl(idPokedex) {
        return `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/versions/generation-viii/brilliant-diamond-shining-pearl/back/${idPokedex}.webp`;
    }

    function configurarFallbacksSprite() {
        els.wildSprite.onerror = () => {
            if (!els.wildSprite.dataset.fallback) {
                els.wildSprite.dataset.fallback = '1';
                els.wildSprite.src = spriteFrontFallback(estado.selvagem.id_pokedex);
            }
        };
        els.playerSprite.onerror = () => {
            if (!els.playerSprite.dataset.fallback) {
                els.playerSprite.dataset.fallback = '1';
                els.playerSprite.src = spriteFrontFallback(estado.jogador.id_pokedex);
            }
        };
    }

    function classeHp(pct) {
        if (pct > 50) return 'hp-high';
        if (pct > 20) return 'hp-mid';
        return 'hp-low';
    }

    function atualizarStatusBadge(el, status) {
        el.className = 'status-badge';
        if (!status || !STATUS_LABELS[status]) {
            el.classList.add('hidden');
            el.textContent = '';
            return;
        }
        el.textContent = STATUS_LABELS[status];
        el.classList.add(`status-${status}`);
    }

    function atualizarHud(hud) {
        const j = hud.jogador;
        const s = hud.selvagem;

        els.wildName.textContent = s.nome;
        const pctWild = s.hp_maximo > 0 ? Math.max(0, Math.min(100, (s.hp_atual / s.hp_maximo) * 100)) : 0;
        els.wildHpBar.style.width = `${pctWild}%`;
        els.wildHpBar.className = `hp-bar ${classeHp(pctWild)}`;
        atualizarStatusBadge(els.wildStatusBadge, s.status);

        if (els.wildSprite.dataset.pokedexId !== String(s.id_pokedex)) {
            els.wildSprite.dataset.pokedexId = String(s.id_pokedex);
            delete els.wildSprite.dataset.fallback;
            els.wildSprite.src = spriteFrontUrl(s.id_pokedex);
            els.wildSprite.alt = `${s.nome} selvagem`;
        }

        els.playerName.textContent = j.nome;
        const pctPlayer = j.hp_maximo > 0 ? Math.max(0, Math.min(100, (j.hp_atual / j.hp_maximo) * 100)) : 0;
        els.playerHpBar.style.width = `${pctPlayer}%`;
        els.playerHpBar.className = `hp-bar ${classeHp(pctPlayer)}`;
        els.playerHpNumbers.textContent = `${j.hp_atual} / ${j.hp_maximo}`;
        atualizarStatusBadge(els.playerStatusBadge, j.status);

        if (els.playerSprite.dataset.pokedexId !== String(j.id_pokedex)) {
            els.playerSprite.dataset.pokedexId = String(j.id_pokedex);
            delete els.playerSprite.dataset.fallback;
            els.playerSprite.src = spriteBackUrl(j.id_pokedex);
            els.playerSprite.alt = `Seu ${j.nome}`;
        }
        if (j.hp_atual > 0) {
            els.playerSpriteWrap.classList.remove('anim-faint');
        }
    }

    function definirModoMenu(modo) {
        modoAtual = modo;
        els.battleMenu.classList.remove('mode-message', 'mode-fight');
        els.battleMessage.classList.remove('hidden');
        els.battleActions.classList.remove('hidden');
        els.fightPanel.classList.add('hidden');
        els.nextArrow.classList.add('hidden');

        if (modo === 'main') {
            els.battleMessageText.innerHTML = `O que<br>${estado.jogador.nome} vai fazer?`;
            els.btnFight.focus();
        } else if (modo === 'fight') {
            els.battleMenu.classList.add('mode-fight');
            els.battleMessage.classList.add('hidden');
            els.battleActions.classList.add('hidden');
            els.fightPanel.classList.remove('hidden');
            renderizarGolpes();
        } else if (modo === 'message') {
            els.battleMenu.classList.add('mode-message');
            els.battleActions.classList.add('hidden');
        }
    }

    function atualizarDetalhesGolpe(mov) {
        if (!mov) return;
        const tipoEn = TYPE_LABELS_EN[mov.tipo_nome] || mov.tipo_nome.toUpperCase();
        els.moveDetailType.textContent = tipoEn;
        els.moveDetailType.className = `move-type-pill type-${mov.tipo_nome}`;
        els.moveDetailCat.textContent = mov.categoria === 'fis' ? 'PHYSICAL' : 'SPECIAL';
        els.moveDetailPower.textContent = mov.poder > 0 ? String(mov.poder) : '--';
        els.moveDetailAcc.textContent = mov.precisao > 0 ? `${mov.precisao}%` : '--';
    }

    function renderizarGolpes() {
        const movimentos = estado.jogador.movimentos || [];
        els.moveGrid.innerHTML = '';

        movimentos.forEach((mov, idx) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'move-btn' + (idx === 0 ? ' selected' : '');
            btn.dataset.moveId = String(mov.id_movimento);
            btn.innerHTML = `<span class="cursor">▶</span>${mov.nome}`;

            const destacar = () => {
                els.moveGrid.querySelectorAll('.move-btn').forEach((b) => b.classList.remove('selected'));
                btn.classList.add('selected');
                atualizarDetalhesGolpe(mov);
            };

            btn.addEventListener('mouseenter', destacar);
            btn.addEventListener('focus', destacar);
            btn.addEventListener('click', () => usarGolpe(mov.id_movimento));
            els.moveGrid.appendChild(btn);
        });

        for (let i = movimentos.length; i < 4; i++) {
            const vazio = document.createElement('button');
            vazio.type = 'button';
            vazio.className = 'move-btn';
            vazio.disabled = true;
            vazio.textContent = '---';
            els.moveGrid.appendChild(vazio);
        }

        if (movimentos[0]) {
            atualizarDetalhesGolpe(movimentos[0]);
            const primeiroBtn = els.moveGrid.querySelector('.move-btn');
            if (primeiroBtn) primeiroBtn.focus();
        }
    }

    function aplicarAnimacao(animacao) {
        const limpar = (el) => {
            el.classList.remove(
                'anim-player-attack',
                'anim-wild-attack',
                'anim-hit',
                'anim-faint'
            );
            void el.offsetWidth;
        };

        if (animacao === 'player-attack') {
            limpar(els.playerSpriteWrap);
            els.playerSpriteWrap.classList.add('anim-player-attack');
        } else if (animacao === 'wild-attack') {
            limpar(els.wildSpriteWrap);
            els.wildSpriteWrap.classList.add('anim-wild-attack');
        } else if (animacao === 'player-hit') {
            limpar(els.playerSpriteWrap);
            els.playerSpriteWrap.classList.add('anim-hit');
        } else if (animacao === 'wild-hit') {
            limpar(els.wildSpriteWrap);
            els.wildSpriteWrap.classList.add('anim-hit');
        } else if (animacao === 'player-faint') {
            limpar(els.playerSpriteWrap);
            els.playerSpriteWrap.classList.add('anim-faint');
        } else if (animacao === 'wild-faint') {
            limpar(els.wildSpriteWrap);
            els.wildSpriteWrap.classList.add('anim-faint');
        }
    }

    function aguardar(ms) {
        return new Promise((resolve) => setTimeout(resolve, ms));
    }

    function aguardarAvancoMensagem(tempoAutoMs = 1350) {
        return new Promise((resolve) => {
            let concluido = false;
            const finalizar = () => {
                if (concluido) return;
                concluido = true;
                resolverCliqueMensagem = null;
                clearTimeout(timer);
                els.nextArrow.classList.add('hidden');
                resolve();
            };

            els.nextArrow.classList.remove('hidden');
            resolverCliqueMensagem = finalizar;
            const timer = setTimeout(finalizar, tempoAutoMs);
        });
    }

    async function reproduzirEventos(eventos) {
        definirModoMenu('message');
        for (const ev of eventos) {
            if (ev.hud) {
                atualizarHud(ev.hud);
            }
            if (ev.animacao) {
                aplicarAnimacao(ev.animacao);
            }
            if (ev.mensagem) {
                els.battleMessageText.textContent = ev.mensagem;
                await aguardarAvancoMensagem(1400);
            } else {
                await aguardar(520);
            }
        }
    }

    async function enviarAcao(acao, body = {}) {
        if (ocupado) return;
        ocupado = true;
        definirModoMenu('message');

        try {
            const resp = await fetch(`?modulo=batalha&acao=${acao}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(body),
            });
            const data = await resp.json();

            if (!resp.ok || !data.ok) {
                els.battleMessageText.textContent = data.erro || 'Não foi possível executar essa ação.';
                await aguardarAvancoMensagem(1500);
                definirModoMenu('main');
                return;
            }

            if (Array.isArray(data.eventos) && data.eventos.length > 0) {
                await reproduzirEventos(data.eventos);
            }

            if (data.estado) {
                Object.assign(estado, data.estado);
                atualizarHud({ jogador: estado.jogador, selvagem: estado.selvagem });
            }

            if (data.redirect) {
                window.location.href = data.redirect;
                return;
            }

            if (estado.troca_obrigatoria) {
                abrirTelaPokemon(true);
            } else {
                definirModoMenu('main');
            }
        } catch (err) {
            els.battleMessageText.textContent = 'Erro de conexão durante a batalha.';
            await aguardarAvancoMensagem(1600);
            definirModoMenu('main');
        } finally {
            ocupado = false;
        }
    }

    function usarGolpe(idMovimento) {
        enviarAcao('atacar', { id_movimento: idMovimento });
    }

    function criarCardParty(poke, ehAtivo) {
        const btn = document.createElement('button');
        btn.type = 'button';
        const desmaiado = poke.hp_atual <= 0;
        btn.className = 'party-card' + (ehAtivo ? ' is-active' : '') + (desmaiado ? ' is-fainted' : '');

        const pct = poke.hp_maximo > 0 ? Math.max(0, Math.min(100, (poke.hp_atual / poke.hp_maximo) * 100)) : 0;
        const badgeStatus = poke.status && STATUS_LABELS[poke.status]
            ? `<span class="status-badge status-${poke.status}">${STATUS_LABELS[poke.status]}</span>`
            : (desmaiado ? '<span class="party-card-tag">DESMAIADO</span>' : (ehAtivo ? '<span class="party-card-tag">EM CAMPO</span>' : ''));

        btn.innerHTML = `
            <img
                class="party-card-sprite"
                src="${spriteFrontFallback(poke.id_pokedex)}"
                alt="${poke.nome}"
            >
            <div class="party-card-info">
                <div class="party-card-top">
                    <span class="party-card-name">${poke.nome}</span>
                    ${badgeStatus}
                </div>
                <div class="party-card-hp-row">
                    <span class="party-card-hp-label">HP</span>
                    <div class="hp-track">
                        <div class="hp-bar ${classeHp(pct)}" style="width: ${pct}%;"></div>
                    </div>
                </div>
                <div class="party-card-hp-numbers">${poke.hp_atual} / ${poke.hp_maximo}</div>
            </div>
        `;

        btn.addEventListener('click', () => selecionarPokemonParty(poke, btn));
        return btn;
    }

    function abrirTelaPokemon(obrigatoria = false) {
        modoAtual = 'party';
        pokemonSelecionadoParty = null;
        els.partyActionBox.classList.add('hidden');
        els.partyOverlay.classList.remove('hidden');

        els.partyPrompt.textContent = obrigatoria
            ? 'Escolha o próximo POKÉMON para batalhar!'
            : 'Escolha um POKÉMON.';

        if (obrigatoria) {
            els.btnPartyCancel.classList.add('hidden');
        } else {
            els.btnPartyCancel.classList.remove('hidden');
        }

        els.partyActiveSlot.innerHTML = '';
        els.partyBenchList.innerHTML = '';

        const equipe = estado.equipe || [];
        const ativo = equipe.find((p) => p.ativo) || equipe[0];
        const reservas = equipe.filter((p) => p !== ativo);

        if (ativo) {
            els.partyActiveSlot.appendChild(criarCardParty(ativo, true));
        }

        reservas.forEach((poke) => {
            els.partyBenchList.appendChild(criarCardParty(poke, false));
        });

        for (let i = reservas.length; i < 5; i++) {
            const vazio = document.createElement('div');
            vazio.className = 'party-empty-slot';
            vazio.textContent = 'SLOT VAZIO';
            els.partyBenchList.appendChild(vazio);
        }
    }

    function fecharTelaPokemon() {
        if (estado.troca_obrigatoria) return;
        pokemonSelecionadoParty = null;
        els.partyActionBox.classList.add('hidden');
        els.partyOverlay.classList.add('hidden');
        definirModoMenu('main');
    }

    function selecionarPokemonParty(poke, cardEl) {
        els.partyOverlay.querySelectorAll('.party-card').forEach((c) => c.classList.remove('selected'));
        cardEl.classList.add('selected');

        if (poke.ativo) {
            els.partyActionBox.classList.add('hidden');
            els.partyPrompt.textContent = `${poke.nome} já está em batalha!`;
            return;
        }

        if (poke.hp_atual <= 0) {
            els.partyActionBox.classList.add('hidden');
            els.partyPrompt.textContent = `${poke.nome} não tem energia para lutar!`;
            return;
        }

        pokemonSelecionadoParty = poke;
        els.partyPrompt.textContent = `O que fazer com ${poke.nome}?`;
        els.partyActionBox.classList.remove('hidden');
        els.btnPartySwitch.focus();
    }

    function confirmarTrocaPokemon() {
        if (!pokemonSelecionadoParty) return;
        const idPokemon = pokemonSelecionadoParty.id_pokemon;
        els.partyActionBox.classList.add('hidden');
        els.partyOverlay.classList.add('hidden');
        enviarAcao('trocar', { id_pokemon: idPokemon });
    }

    // Eventos dos botões
    els.btnFight.addEventListener('click', () => {
        if (ocupado) return;
        definirModoMenu('fight');
    });

    els.btnMoveBack.addEventListener('click', () => {
        if (ocupado) return;
        definirModoMenu('main');
    });

    els.btnPokemon.addEventListener('click', () => {
        if (ocupado) return;
        abrirTelaPokemon(false);
    });

    els.btnRun.addEventListener('click', () => {
        if (ocupado) return;
        enviarAcao('fugir');
    });

    els.btnBag.addEventListener('click', async () => {
        if (ocupado) return;
        ocupado = true;
        definirModoMenu('message');
        els.battleMessageText.textContent = 'A mochila ainda não está disponível em batalha!';
        await aguardarAvancoMensagem(1400);
        ocupado = false;
        definirModoMenu('main');
    });

    els.btnPartyCancel.addEventListener('click', fecharTelaPokemon);
    els.btnPartyActionCancel.addEventListener('click', () => {
        pokemonSelecionadoParty = null;
        els.partyActionBox.classList.add('hidden');
        els.partyPrompt.textContent = estado.troca_obrigatoria
            ? 'Escolha o próximo POKÉMON para batalhar!'
            : 'Escolha um POKÉMON.';
    });
    els.btnPartySwitch.addEventListener('click', confirmarTrocaPokemon);

    els.battleMessage.addEventListener('click', () => {
        if (resolverCliqueMensagem) {
            resolverCliqueMensagem();
        }
    });

    // Atalhos de teclado (Esc/Backspace voltam, Enter/Espaço avançam mensagem)
    document.addEventListener('keydown', (e) => {
        if (resolverCliqueMensagem && (e.key === 'Enter' || e.key === ' ')) {
            e.preventDefault();
            resolverCliqueMensagem();
            return;
        }

        if (e.key === 'Escape' || e.key === 'Backspace') {
            if (modoAtual === 'fight' && !ocupado) {
                e.preventDefault();
                definirModoMenu('main');
            } else if (modoAtual === 'party' && !ocupado) {
                e.preventDefault();
                if (!els.partyActionBox.classList.contains('hidden')) {
                    els.partyActionBox.classList.add('hidden');
                    pokemonSelecionadoParty = null;
                } else {
                    fecharTelaPokemon();
                }
            }
        }
    });

    // Inicialização da batalha (vai direto para o menu principal ou troca obrigatória)
    function iniciar() {
        configurarFallbacksSprite();
        atualizarHud({ jogador: estado.jogador, selvagem: estado.selvagem });
        if (estado.troca_obrigatoria) {
            abrirTelaPokemon(true);
        } else {
            definirModoMenu('main');
        }
    }

    iniciar();
})();
