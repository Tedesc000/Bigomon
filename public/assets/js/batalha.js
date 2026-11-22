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

        foeParty: document.getElementById('foeParty'),
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
        btnPartySwitchLabel: document.getElementById('btnPartySwitchLabel'),

        pokeballSprite: document.getElementById('pokeballSprite'),
        bagOverlay: document.getElementById('bagOverlay'),
        bagList: document.getElementById('bagList'),
        bagDetailImg: document.getElementById('bagDetailImg'),
        bagDetailText: document.getElementById('bagDetailText'),
        bagPrompt: document.getElementById('bagPrompt'),
        btnBagCancel: document.getElementById('btnBagCancel'),
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

    // "selvagem" no matinho, "inimigo" contra treinador
    const SUFIXO_OPONENTE = estado.tipo === 'treinador' ? 'inimigo' : 'selvagem';

    let ocupado = false;
    let modoAtual = 'main'; // 'main' | 'fight' | 'message' | 'party' | 'bag'
    let pokemonSelecionadoParty = null;
    let itemEmUso = null; // item da bolsa aguardando a escolha do Pokémon alvo
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
                els.wildSprite.src = spriteFrontFallback(els.wildSprite.dataset.pokedexId || estado.oponente.id_pokedex);
            }
        };
        els.playerSprite.onerror = () => {
            if (!els.playerSprite.dataset.fallback) {
                els.playerSprite.dataset.fallback = '1';
                els.playerSprite.src = spriteFrontFallback(els.playerSprite.dataset.pokedexId || estado.jogador.id_pokedex);
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

    // Pokébolas do treinador adversário (só existe em batalha contra treinador)
    function atualizarPokebolasAdversario(restantes) {
        if (!els.foeParty || typeof restantes !== 'number') return;
        els.foeParty.querySelectorAll('.foe-ball').forEach((bola, idx) => {
            bola.classList.toggle('is-fainted', idx >= restantes);
        });
    }

    function atualizarHud(hud) {
        const j = hud.jogador;
        const s = hud.oponente;
        atualizarPokebolasAdversario(hud.oponente_restantes);

        els.wildName.textContent = s.nome;
        const pctWild = s.hp_maximo > 0 ? Math.max(0, Math.min(100, (s.hp_atual / s.hp_maximo) * 100)) : 0;
        els.wildHpBar.style.width = `${pctWild}%`;
        els.wildHpBar.className = `hp-bar ${classeHp(pctWild)}`;
        atualizarStatusBadge(els.wildStatusBadge, s.status);

        if (els.wildSprite.dataset.pokedexId !== String(s.id_pokedex)) {
            els.wildSprite.dataset.pokedexId = String(s.id_pokedex);
            delete els.wildSprite.dataset.fallback;
            els.wildSprite.src = spriteFrontUrl(s.id_pokedex);
            els.wildSprite.alt = `${s.nome} ${SUFIXO_OPONENTE}`;
        }
        if (s.hp_atual > 0) {
            els.wildSpriteWrap.classList.remove('anim-faint');
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

    function aplicarAnimacao(animacao, ev = null) {
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
        } else if (animacao === 'ball-throw') {
            limpar(els.wildSpriteWrap);
            if (ev && ev.bola_url) {
                els.pokeballSprite.src = ev.bola_url;
            }
            els.pokeballSprite.className = 'pokeball-sprite anim-ball-throw';
            els.wildSpriteWrap.classList.add('is-captured');
        } else if (animacao === 'ball-shake') {
            els.pokeballSprite.className = 'pokeball-sprite';
            void els.pokeballSprite.offsetWidth;
            els.pokeballSprite.className = 'pokeball-sprite anim-ball-shake';
        } else if (animacao === 'capture-ok') {
            els.pokeballSprite.className = 'pokeball-sprite is-caught';
        } else if (animacao === 'ball-break') {
            els.pokeballSprite.className = 'pokeball-sprite hidden';
            els.wildSpriteWrap.classList.remove('is-captured');
        }
    }


    // ---------- Efeitos visuais de golpe por tipo (puramente estéticos) ----------
    // shape: forma CSS | from: origem (top/bottom/center/around) | spread: dispersão em px
    const EFEITOS_TIPO = {
        normal:   { shape: 'star',   color: '#fff6d8', count: 8,  from: 'center', spread: 90,  dur: 520, glow: 'rgba(255,255,255,.7)' },
        lutador:  { shape: 'ring',   color: '#e8482c', count: 3,  from: 'center', spread: 0,   dur: 480, glow: 'rgba(255,120,60,.6)', shake: true },
        voador:   { shape: 'slash',  color: '#d8ecff', count: 4,  from: 'center', spread: 70,  dur: 520, glow: 'rgba(200,230,255,.35)' },
        veneno:   { shape: 'bubble', color: '#a64fd8', count: 12, from: 'bottom', spread: 110, dur: 800, glow: 'rgba(150,60,200,.4)' },
        terra:    { shape: 'rock',   color: '#a8793c', count: 10, from: 'bottom', spread: 120, dur: 700, glow: 'rgba(140,100,50,.4)', shake: true },
        pedra:    { shape: 'rock',   color: '#8d8a7c', count: 9,  from: 'top',    spread: 120, dur: 650, glow: 'rgba(120,120,110,.35)', shake: true },
        inseto:   { shape: 'dot',    color: '#8fc43a', count: 16, from: 'around', spread: 110, dur: 800, glow: 'rgba(140,200,50,.3)' },
        fantasma: { shape: 'orb',    color: '#7a4fb0', count: 8,  from: 'around', spread: 100, dur: 850, glow: 'rgba(60,20,100,.6)' },
        aco:      { shape: 'slash',  color: '#dfe6ee', count: 3,  from: 'center', spread: 60,  dur: 450, glow: 'rgba(220,230,240,.55)' },
        fogo:     { shape: 'flame',  color: '#ff7a1a', count: 12, from: 'bottom', spread: 100, dur: 800, glow: 'rgba(255,110,20,.55)' },
        agua:     { shape: 'drop',   color: '#3a9bf0', count: 14, from: 'top',    spread: 120, dur: 700, glow: 'rgba(60,150,240,.4)' },
        planta:   { shape: 'leaf',   color: '#4fb83a', count: 12, from: 'around', spread: 130, dur: 850, glow: 'rgba(80,190,60,.3)' },
        eletrico: { shape: 'bolt',   color: '#ffe53a', count: 6,  from: 'top',    spread: 100, dur: 600, glow: 'rgba(255,240,80,.75)', shake: true },
        psiquico: { shape: 'ring',   color: '#f45bb4', count: 4,  from: 'center', spread: 0,   dur: 900, glow: 'rgba(240,90,180,.4)' },
        gelo:     { shape: 'shard',  color: '#aeeeff', count: 12, from: 'around', spread: 120, dur: 750, glow: 'rgba(170,235,255,.5)' },
        dragao:   { shape: 'flame',  color: '#6a5cf0', count: 14, from: 'around', spread: 120, dur: 850, glow: 'rgba(100,70,230,.5)', shake: true },
        sombrio:  { shape: 'slash',  color: '#4a2a6a', count: 5,  from: 'center', spread: 80,  dur: 600, glow: 'rgba(20,0,40,.65)' },
    };

    function rand(min, max) {
        return min + Math.random() * (max - min);
    }

    function tocarEfeitoTipo(tipo, alvo) {
        const cfg = EFEITOS_TIPO[tipo];
        const wrap = alvo === 'wild' ? els.wildSpriteWrap : els.playerSpriteWrap;
        const tela = wrap && wrap.closest('.battle-screen');
        if (!cfg || !tela) return;

        const rt = tela.getBoundingClientRect();
        const rw = wrap.getBoundingClientRect();
        const layer = document.createElement('div');
        layer.className = 'fx-layer';
        layer.style.left = `${rw.left - rt.left}px`;
        layer.style.top = `${rw.top - rt.top}px`;
        layer.style.width = `${rw.width}px`;
        layer.style.height = `${rw.height}px`;

        const glow = document.createElement('div');
        glow.className = 'fx-glow';
        glow.style.background = `radial-gradient(circle, ${cfg.glow} 0%, transparent 65%)`;
        glow.style.animationDuration = `${cfg.dur}ms`;
        layer.appendChild(glow);

        const w = rw.width;
        const h = rw.height;
        for (let i = 0; i < cfg.count; i++) {
            const el = document.createElement('span');
            el.className = `fx-p fx-${cfg.shape}`;
            el.style.setProperty('--c', cfg.color);
            el.style.animationDuration = `${cfg.dur * rand(0.75, 1)}ms`;
            el.style.animationDelay = `${Math.round(rand(0, cfg.dur * 0.35))}ms`;

            let x = w / 2, y = h / 2, mx = 0, my = 0;
            const sp = cfg.spread;
            if (cfg.from === 'top') {
                x = w / 2 + rand(-sp, sp); y = rand(-h * 0.1, h * 0.1);
                mx = rand(-10, 10); my = h * rand(0.5, 0.9);
            } else if (cfg.from === 'bottom') {
                x = w / 2 + rand(-sp, sp); y = h * rand(0.75, 0.95);
                mx = rand(-20, 20); my = -h * rand(0.4, 0.8);
            } else if (cfg.from === 'around') {
                const a = rand(0, Math.PI * 2);
                x = w / 2 + Math.cos(a) * sp; y = h / 2 + Math.sin(a) * sp * 0.8;
                mx = -Math.cos(a) * sp * 0.7 + rand(-15, 15); my = -Math.sin(a) * sp * 0.6 + rand(-15, 15);
            } else {
                const a = rand(0, Math.PI * 2);
                x = w / 2 + rand(-15, 15); y = h / 2 + rand(-15, 15);
                mx = Math.cos(a) * sp * rand(0.4, 1); my = Math.sin(a) * sp * rand(0.4, 1);
            }
            const slashLike = cfg.shape === 'slash';
            el.style.left = `${x}px`;
            el.style.top = `${y}px`;
            el.style.setProperty('--mx', `${slashLike ? 0 : mx}px`);
            el.style.setProperty('--my', `${slashLike ? 0 : my}px`);
            el.style.setProperty('--r0', `${slashLike ? -35 + rand(-15, 15) : rand(-90, 90)}deg`);
            el.style.setProperty('--r1', `${slashLike ? -35 + rand(-15, 15) : rand(-360, 360)}deg`);
            el.style.setProperty('--s0', cfg.shape === 'ring' ? String(0.15 + i * 0.1) : '0.6');
            el.style.setProperty('--s1', cfg.shape === 'ring' ? String(2 + i * 0.5) : '1.1');
            if (slashLike) {
                el.style.left = `${w / 2 + rand(-sp, sp) * 0.5}px`;
                el.style.top = `${h / 2 + rand(-sp, sp) * 0.5}px`;
            }
            layer.appendChild(el);
        }

        tela.appendChild(layer);
        if (cfg.shake) {
            tela.classList.remove('fx-shake');
            void tela.offsetWidth;
            tela.classList.add('fx-shake');
        }
        setTimeout(() => {
            layer.remove();
            tela.classList.remove('fx-shake');
        }, cfg.dur * 1.6 + 100);
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

    async function registrarLogBatalha(dados) {
        try {
            await fetch('?modulo=batalha&acao=registrarLogBatalha', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dados),
            });
        } catch (e) {
            // Silencioso para não interromper a animação da batalha
        }
    }

    async function reproduzirEventos(eventos) {
        definirModoMenu('message');
        for (const ev of eventos) {
            if (ev.tipo_evento === 'log_ataque' && ev.log_dados) {
                registrarLogBatalha(ev.log_dados);
                continue;
            }
            if (ev.hud) {
                atualizarHud(ev.hud);
            }
            if (ev.animacao) {
                aplicarAnimacao(ev.animacao, ev);
            }
            if (ev.tipo_efeito && ev.animacao) {
                tocarEfeitoTipo(ev.tipo_efeito, ev.animacao.startsWith('wild') ? 'wild' : 'player');
            }
            if (ev.mensagem) {
                els.battleMessageText.textContent = ev.mensagem;
                await aguardarAvancoMensagem(1400);
            } else {
                await aguardar(ev.tipo_efeito ? 900 : 520);
            }
        }
    }

    function hudDoEstado() {
        return {
            jogador: estado.jogador,
            oponente: estado.oponente,
            oponente_restantes: estado.adversario ? estado.adversario.restantes : undefined,
        };
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
                atualizarHud(hudDoEstado());
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

    function textoPromptParty() {
        if (itemEmUso) return `Usar ${itemEmUso.nome.toUpperCase()} em qual POKÉMON?`;
        return estado.troca_obrigatoria
            ? 'Escolha o próximo POKÉMON para batalhar!'
            : 'Escolha um POKÉMON.';
    }

    function abrirTelaPokemon(obrigatoria = false) {
        modoAtual = 'party';
        pokemonSelecionadoParty = null;
        if (obrigatoria) itemEmUso = null;
        els.partyActionBox.classList.add('hidden');
        els.partyOverlay.classList.remove('hidden');
        els.btnPartySwitchLabel.textContent = itemEmUso ? 'USAR' : 'TROCAR';

        els.partyPrompt.textContent = textoPromptParty();

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
        if (itemEmUso) {
            // Cancelar a escolha do alvo volta pra bolsa
            itemEmUso = null;
            abrirBolsa();
            return;
        }
        definirModoMenu('main');
    }

    function selecionarPokemonParty(poke, cardEl) {
        els.partyOverlay.querySelectorAll('.party-card').forEach((c) => c.classList.remove('selected'));
        cardEl.classList.add('selected');

        // Modo item: qualquer Pokémon da equipe pode ser alvo (o servidor valida se o item tem efeito)
        if (itemEmUso) {
            pokemonSelecionadoParty = poke;
            els.partyPrompt.textContent = `Usar ${itemEmUso.nome.toUpperCase()} em ${poke.nome}?`;
            els.partyActionBox.classList.remove('hidden');
            els.btnPartySwitch.focus();
            return;
        }

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
        if (itemEmUso) {
            const idItem = itemEmUso.id_item;
            itemEmUso = null;
            enviarAcao('usarItem', { id_item: idItem, id_pokemon: idPokemon });
            return;
        }
        enviarAcao('trocar', { id_pokemon: idPokemon });
    }

    // ---------- Bolsa de itens da batalha ----------
    function abrirBolsa() {
        modoAtual = 'bag';
        els.bagOverlay.classList.remove('hidden');
        els.bagList.innerHTML = '';
        els.bagDetailImg.classList.add('hidden');
        els.bagDetailText.textContent = 'Escolha um item.';
        els.bagPrompt.textContent = 'Qual item usar?';

        const itens = Array.isArray(estado.itens) ? estado.itens : [];
        if (itens.length === 0) {
            const vazio = document.createElement('div');
            vazio.className = 'bag-empty';
            vazio.textContent = 'Nenhum item utilizável em batalha.';
            els.bagList.appendChild(vazio);
            return;
        }

        itens.forEach((item) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'bag-item' + (item.permitido ? '' : ' is-disabled');
            btn.innerHTML = `
                <img class="bag-item-img" src="${item.imagem}" alt="" onerror="this.style.visibility='hidden'">
                <span class="bag-item-name"></span>
                <span class="bag-item-qty">x${item.quantidade}</span>
            `;
            btn.querySelector('.bag-item-name').textContent = item.nome.toUpperCase();
            btn.addEventListener('mouseenter', () => mostrarDetalheItem(item));
            btn.addEventListener('focus', () => mostrarDetalheItem(item));
            btn.addEventListener('click', () => escolherItem(item));
            els.bagList.appendChild(btn);
        });

        const primeiro = els.bagList.querySelector('.bag-item');
        if (primeiro) primeiro.focus();
    }

    function mostrarDetalheItem(item) {
        els.bagDetailImg.src = item.imagem;
        els.bagDetailImg.classList.remove('hidden');
        els.bagDetailText.textContent = item.permitido
            ? item.descricao
            : 'Não dá pra capturar o Pokémon de outro treinador!';
    }

    function fecharBolsa(voltarMenu = true) {
        els.bagOverlay.classList.add('hidden');
        if (voltarMenu) definirModoMenu('main');
    }

    function escolherItem(item) {
        if (ocupado) return;
        if (!item.permitido) {
            mostrarDetalheItem(item);
            els.bagPrompt.textContent = 'Esse item não pode ser usado agora.';
            return;
        }
        fecharBolsa(false);
        if (item.tipo === 'pokebola') {
            enviarAcao('usarItem', { id_item: item.id_item });
            return;
        }
        itemEmUso = item;
        abrirTelaPokemon(false);
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

    els.btnBag.addEventListener('click', () => {
        if (ocupado) return;
        abrirBolsa();
    });
    els.btnBagCancel.addEventListener('click', () => fecharBolsa());

    els.btnPartyCancel.addEventListener('click', fecharTelaPokemon);
    els.btnPartyActionCancel.addEventListener('click', () => {
        pokemonSelecionadoParty = null;
        els.partyActionBox.classList.add('hidden');
        els.partyPrompt.textContent = textoPromptParty();
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
            } else if (modoAtual === 'bag' && !ocupado) {
                e.preventDefault();
                fecharBolsa();
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

    // Inicialização da batalha: toca a introdução (batalha contra treinador) e vai para o menu principal ou troca obrigatória
    async function iniciar() {
        configurarFallbacksSprite();
        atualizarHud(hudDoEstado());

        const introducao = Array.isArray(estado.eventos_iniciais) ? estado.eventos_iniciais : [];
        if (introducao.length > 0) {
            ocupado = true;
            await reproduzirEventos(introducao);
            ocupado = false;
        }

        if (estado.troca_obrigatoria) {
            abrirTelaPokemon(true);
        } else {
            definirModoMenu('main');
        }
    }

    iniciar();
})();
