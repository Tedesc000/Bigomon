(() => {
    let slots = Array.isArray(window.EQUIPE_INICIAL) ? window.EQUIPE_INICIAL : [null, null, null, null, null, null];

    const els = {
        partySlot1: document.getElementById('partySlot1'),
        partyBench: document.getElementById('partyBench'),
        partyPrompt: document.getElementById('partyPrompt'),
        partyActionBox: document.getElementById('partyActionBox'),
        btnActionSummary: document.getElementById('btnActionSummary'),
        btnActionSwitch: document.getElementById('btnActionSwitch'),
        btnActionCancel: document.getElementById('btnActionCancel'),

        summaryOverlay: document.getElementById('summaryOverlay'),
        tabStats: document.getElementById('tabStats'),
        tabMoves: document.getElementById('tabMoves'),
        panelStats: document.getElementById('panelStats'),
        panelMoves: document.getElementById('panelMoves'),
        btnSummaryPrev: document.getElementById('btnSummaryPrev'),
        btnSummaryNext: document.getElementById('btnSummaryNext'),
        btnSummaryClose: document.getElementById('btnSummaryClose'),

        summaryDex: document.getElementById('summaryDex'),
        summaryGender: document.getElementById('summaryGender'),
        summaryName: document.getElementById('summaryName'),
        summarySprite: document.getElementById('summarySprite'),
        summaryTypes: document.getElementById('summaryTypes'),
        summaryStatusBadge: document.getElementById('summaryStatusBadge'),
        summaryHpBar: document.getElementById('summaryHpBar'),
        summaryHpNumbers: document.getElementById('summaryHpNumbers'),

        summaryStatsList: document.getElementById('summaryStatsList'),
        summaryStatTotal: document.getElementById('summaryStatTotal'),
        summaryStage: document.getElementById('summaryStage'),
        summarySlotNum: document.getElementById('summarySlotNum'),

        summaryMovesList: document.getElementById('summaryMovesList'),
        inspType: document.getElementById('inspType'),
        inspCat: document.getElementById('inspCat'),
        inspPower: document.getElementById('inspPower'),
        inspAcc: document.getElementById('inspAcc'),
        inspEffect: document.getElementById('inspEffect'),
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

    const EFFECT_LABELS = {
        veneno: 'Veneno (PSN)',
        paralisia: 'Paralisia (PAR)',
        queimadura: 'Queimadura (BRN)',
        sono: 'Sono (SLP)',
        congelamento: 'Congelamento (FRZ)',
    };

    let slotSelecionado = null; // 0..5
    let modoTrocaOrigem = null; // 0..5 quando está trocando ordem
    let summaryIdx = null;      // 0..5 quando o Summary está aberto
    let abaSummary = 'stats';   // 'stats' | 'moves'
    let ocupandoRequisicao = false;

    function spriteFrontUrl(idPokedex) {
        return `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/versions/generation-viii/brilliant-diamond-shining-pearl/${idPokedex}.webp`;
    }

    function spriteFallbackUrl(idPokedex) {
        return `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${idPokedex}.png`;
    }

    function classeHp(pct) {
        if (pct > 50) return 'hp-high';
        if (pct > 20) return 'hp-mid';
        return 'hp-low';
    }

    function renderizarEquipe() {
        els.partySlot1.innerHTML = '';
        els.partyBench.innerHTML = '';

        for (let i = 0; i < 6; i++) {
            const poke = slots[i];
            const ehLead = i === 0;
            const container = ehLead ? els.partySlot1 : els.partyBench;

            if (poke) {
                const btn = document.createElement('button');
                btn.type = 'button';
                const desmaiado = poke.hp_atual <= 0;
                const pct = poke.hp_maximo > 0 ? Math.max(0, Math.min(100, (poke.hp_atual / poke.hp_maximo) * 100)) : 0;

                btn.className = 'party-card'
                    + (ehLead ? ' is-lead' : '')
                    + (desmaiado ? ' is-fainted' : '')
                    + (slotSelecionado === i ? ' selected' : '')
                    + (modoTrocaOrigem === i ? ' swap-source' : '');

                const badgeStatus = poke.status && STATUS_LABELS[poke.status]
                    ? `<span class="status-badge status-${poke.status}">${STATUS_LABELS[poke.status]}</span>`
                    : (desmaiado
                        ? '<span class="party-card-tag">DESMAIADO</span>'
                        : (ehLead ? '<span class="party-card-tag">SLOT 1</span>' : `<span class="party-card-tag">SLOT ${i + 1}</span>`));

                btn.innerHTML = `
                    <img
                        class="party-card-sprite"
                        src="${spriteFallbackUrl(poke.id_pokedex)}"
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

                btn.addEventListener('click', () => clicarSlot(i));
                container.appendChild(btn);
            } else {
                const vazio = document.createElement('button');
                vazio.type = 'button';
                vazio.className = 'party-empty-slot' + (modoTrocaOrigem !== null ? ' swap-target' : '');
                vazio.textContent = `SLOT ${i + 1} VAZIO`;
                vazio.disabled = modoTrocaOrigem === null;
                vazio.addEventListener('click', () => clicarSlot(i));
                container.appendChild(vazio);
            }
        }
    }

    function clicarSlot(idx) {
        if (ocupandoRequisicao) return;

        // Se está no modo SWITCH (reordenando slots)
        if (modoTrocaOrigem !== null) {
            if (modoTrocaOrigem === idx) {
                cancelarModoTroca();
                return;
            }
            executarTrocaDeSlots(modoTrocaOrigem, idx);
            return;
        }

        const poke = slots[idx];
        if (!poke) return;

        slotSelecionado = idx;
        renderizarEquipe();
        els.partyPrompt.textContent = `O que fazer com ${poke.nome}?`;
        els.partyActionBox.classList.remove('hidden');
        els.btnActionSummary.focus();
    }

    function fecharMiniMenu() {
        slotSelecionado = null;
        els.partyActionBox.classList.add('hidden');
        els.partyPrompt.textContent = 'Escolha um POKÉMON.';
        renderizarEquipe();
    }

    function iniciarModoTroca() {
        if (slotSelecionado === null) return;
        const poke = slots[slotSelecionado];
        modoTrocaOrigem = slotSelecionado;
        slotSelecionado = null;
        els.partyActionBox.classList.add('hidden');
        els.partyPrompt.textContent = `Mover ${poke.nome} para qual slot?`;
        renderizarEquipe();
    }

    function cancelarModoTroca() {
        modoTrocaOrigem = null;
        els.partyPrompt.textContent = 'Escolha um POKÉMON.';
        renderizarEquipe();
    }

    async function executarTrocaDeSlots(idxA, idxB) {
        ocupandoRequisicao = true;
        els.partyPrompt.textContent = 'Atualizando ordem da equipe...';

        try {
            const resp = await fetch('?modulo=bolsa&acao=trocarSlot', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ slot_a: idxA + 1, slot_b: idxB + 1 }),
            });
            const data = await resp.json();

            if (resp.ok && data.ok && Array.isArray(data.slots)) {
                slots = data.slots;
                modoTrocaOrigem = null;
                els.partyPrompt.textContent = 'Ordem da equipe atualizada!';
            } else {
                modoTrocaOrigem = null;
                els.partyPrompt.textContent = data.erro || 'Não foi possível trocar os slots.';
            }
        } catch (err) {
            modoTrocaOrigem = null;
            els.partyPrompt.textContent = 'Erro de conexão ao atualizar a equipe.';
        } finally {
            ocupandoRequisicao = false;
            renderizarEquipe();
        }
    }

    // Summary (Estilo Pokémon Emerald)
    function abrirSummary(idx) {
        if (!slots[idx]) return;
        summaryIdx = idx;
        els.partyActionBox.classList.add('hidden');
        els.summaryOverlay.classList.remove('hidden');
        definirAbaSummary('stats');
        renderizarSummary();
    }

    function fecharSummary() {
        summaryIdx = null;
        els.summaryOverlay.classList.add('hidden');
        fecharMiniMenu();
    }

    function definirAbaSummary(aba) {
        abaSummary = aba;
        const ehStats = aba === 'stats';
        els.tabStats.classList.toggle('active', ehStats);
        els.tabStats.setAttribute('aria-selected', String(ehStats));
        els.tabMoves.classList.toggle('active', !ehStats);
        els.tabMoves.setAttribute('aria-selected', String(!ehStats));
        els.panelStats.classList.toggle('hidden', !ehStats);
        els.panelMoves.classList.toggle('hidden', ehStats);
    }

    function navegarSummary(direcao) {
        if (summaryIdx === null) return;
        const indicesValidos = slots
            .map((p, i) => (p ? i : -1))
            .filter((i) => i !== -1);

        if (indicesValidos.length <= 1) return;
        const posAtual = indicesValidos.indexOf(summaryIdx);
        const novaPos = (posAtual + direcao + indicesValidos.length) % indicesValidos.length;
        summaryIdx = indicesValidos[novaPos];
        renderizarSummary();
    }

    function renderizarSummary() {
        const poke = slots[summaryIdx];
        if (!poke) return;

        els.summaryDex.textContent = `#${String(poke.id_pokedex).padStart(3, '0')}`;
        els.summaryName.textContent = poke.nome;

        els.summaryGender.className = 'summary-gender';
        if (poke.sexo === 'masculino') {
            els.summaryGender.textContent = '♂';
            els.summaryGender.classList.add('male');
        } else if (poke.sexo === 'feminino') {
            els.summaryGender.textContent = '♀';
            els.summaryGender.classList.add('female');
        } else {
            els.summaryGender.textContent = '';
        }

        delete els.summarySprite.dataset.fallback;
        els.summarySprite.src = spriteFrontUrl(poke.id_pokedex);
        els.summarySprite.alt = poke.nome;
        els.summarySprite.onerror = () => {
            if (!els.summarySprite.dataset.fallback) {
                els.summarySprite.dataset.fallback = '1';
                els.summarySprite.src = spriteFallbackUrl(poke.id_pokedex);
            }
        };

        // Tipos em inglês
        els.summaryTypes.innerHTML = '';
        (poke.tipos || []).forEach((tipo) => {
            const pill = document.createElement('span');
            pill.className = `move-type-pill type-${tipo}`;
            pill.textContent = TYPE_LABELS_EN[tipo] || tipo.toUpperCase();
            els.summaryTypes.appendChild(pill);
        });

        // HP e Status
        const pctHp = poke.hp_maximo > 0 ? Math.max(0, Math.min(100, (poke.hp_atual / poke.hp_maximo) * 100)) : 0;
        els.summaryHpBar.style.width = `${pctHp}%`;
        els.summaryHpBar.className = `hp-bar ${classeHp(pctHp)}`;
        els.summaryHpNumbers.textContent = `${poke.hp_atual} / ${poke.hp_maximo}`;

        els.summaryStatusBadge.className = 'status-badge';
        if (poke.status && STATUS_LABELS[poke.status]) {
            els.summaryStatusBadge.textContent = STATUS_LABELS[poke.status];
            els.summaryStatusBadge.classList.add(`status-${poke.status}`);
        } else {
            els.summaryStatusBadge.classList.add('hidden');
            els.summaryStatusBadge.textContent = '';
        }

        // Aba 1: Stats
        const statsDef = [
            { label: 'HP', val: poke.hp_maximo, cls: 'stat-hp' },
            { label: 'ATK', val: poke.atk, cls: 'stat-atk' },
            { label: 'DEF', val: poke.def, cls: 'stat-def' },
            { label: 'SP. ATK', val: poke.spa, cls: 'stat-spa' },
            { label: 'SP. DEF', val: poke.spd, cls: 'stat-spd' },
            { label: 'SPEED', val: poke.spe, cls: 'stat-spe' },
        ];

        els.summaryStatsList.innerHTML = '';
        let total = 0;
        statsDef.forEach((st) => {
            total += st.val;
            const pctStat = Math.min(100, Math.round((st.val / 180) * 100));
            const row = document.createElement('div');
            row.className = 'stat-row';
            row.innerHTML = `
                <span class="stat-label">${st.label}</span>
                <span class="stat-value">${st.val}</span>
                <div class="stat-bar-track">
                    <div class="stat-bar-fill ${st.cls}" style="width: ${pctStat}%;"></div>
                </div>
            `;
            els.summaryStatsList.appendChild(row);
        });

        els.summaryStatTotal.textContent = String(total);
        els.summaryStage.textContent = String(poke.fase || 1);
        els.summarySlotNum.textContent = `#${summaryIdx + 1}`;

        // Aba 2: Moves
        const movimentos = poke.movimentos || [];
        els.summaryMovesList.innerHTML = '';

        movimentos.forEach((mov, idx) => {
            const tipoEn = TYPE_LABELS_EN[mov.tipo_nome] || mov.tipo_nome.toUpperCase();
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'summary-move-row' + (idx === 0 ? ' selected' : '');
            btn.innerHTML = `
                <span class="summary-move-name-wrap"><span class="cursor">▶</span>${mov.nome}</span>
                <span class="move-type-pill type-${mov.tipo_nome}">${tipoEn}</span>
            `;

            const inspecionar = () => {
                els.summaryMovesList.querySelectorAll('.summary-move-row').forEach((r) => r.classList.remove('selected'));
                btn.classList.add('selected');
                atualizarInspetorGolpe(mov);
            };

            btn.addEventListener('mouseenter', inspecionar);
            btn.addEventListener('focus', inspecionar);
            btn.addEventListener('click', inspecionar);
            els.summaryMovesList.appendChild(btn);
        });

        if (movimentos[0]) {
            atualizarInspetorGolpe(movimentos[0]);
        } else {
            els.inspType.textContent = '--';
            els.inspCat.textContent = '--';
            els.inspPower.textContent = '--';
            els.inspAcc.textContent = '--';
            els.inspEffect.textContent = 'Nenhum golpe cadastrado.';
        }
    }

    function atualizarInspetorGolpe(mov) {
        const tipoEn = TYPE_LABELS_EN[mov.tipo_nome] || mov.tipo_nome.toUpperCase();
        els.inspType.textContent = tipoEn;
        els.inspType.className = `move-type-pill type-${mov.tipo_nome}`;
        els.inspCat.textContent = mov.categoria === 'fis' ? 'PHYSICAL' : 'SPECIAL';
        els.inspPower.textContent = mov.poder > 0 ? String(mov.poder) : '--';
        els.inspAcc.textContent = mov.precisao > 0 ? `${mov.precisao}%` : '--';

        if (mov.efeito_nome && mov.efeito_chance > 0) {
            const labelEf = EFFECT_LABELS[mov.efeito_nome] || mov.efeito_nome;
            els.inspEffect.textContent = `Efeito: ${mov.efeito_chance}% de chance de causar ${labelEf}.`;
        } else if (mov.prioridade > 0) {
            els.inspEffect.textContent = `Prioridade +${mov.prioridade}: costuma atacar primeiro.`;
        } else {
            els.inspEffect.textContent = 'Sem efeito secundário.';
        }
    }

    // Listeners
    els.btnActionSummary.addEventListener('click', () => {
        if (slotSelecionado !== null) abrirSummary(slotSelecionado);
    });
    els.btnActionSwitch.addEventListener('click', iniciarModoTroca);
    els.btnActionCancel.addEventListener('click', fecharMiniMenu);

    els.tabStats.addEventListener('click', () => definirAbaSummary('stats'));
    els.tabMoves.addEventListener('click', () => definirAbaSummary('moves'));
    els.btnSummaryPrev.addEventListener('click', () => navegarSummary(-1));
    els.btnSummaryNext.addEventListener('click', () => navegarSummary(1));
    els.btnSummaryClose.addEventListener('click', fecharSummary);

    document.addEventListener('keydown', (e) => {
        if (summaryIdx !== null) {
            if (e.key === 'Escape' || e.key === 'Backspace') {
                e.preventDefault();
                fecharSummary();
            } else if (e.key === 'ArrowLeft') {
                e.preventDefault();
                definirAbaSummary('stats');
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                definirAbaSummary('moves');
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                navegarSummary(-1);
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                navegarSummary(1);
            }
            return;
        }

        if (e.key === 'Escape' || e.key === 'Backspace') {
            if (modoTrocaOrigem !== null) {
                e.preventDefault();
                cancelarModoTroca();
            } else if (!els.partyActionBox.classList.contains('hidden')) {
                e.preventDefault();
                fecharMiniMenu();
            }
        }
    });

    renderizarEquipe();
})();
