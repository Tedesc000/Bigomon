<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/bolsaPokemons.css">
    <title>Bigomon - Bolsa Pokémon</title>
</head>
<body>
    <main class="party-screen" aria-label="Bolsa Pokémon">

        <!-- ===== EQUIPE (6 slots) ===== -->
        <div class="party-layout">
            <div class="party-active-column" id="partySlot1"></div>
            <div class="party-bench-column" id="partyBench"></div>
        </div>

        <!-- ===== TODOS OS POKÉMONS (box) ===== -->
         <?php if(empty($pokemons)){?>

		    <div class="box-section-header">Você não tem nenhum Pokémon na bolsa!</div>

	    <?php }else{ ?>
        <div class="box-section-header">TODOS OS POKÉMON</div>
        <div class="box-pokemons-grid" id="boxPokemonsGrid"></div>
        <?php }?>

        <!-- ===== FOOTER ===== -->
        <footer class="party-footer">
            <div class="party-prompt" id="partyPrompt">Escolha um POKÉMON.</div>
            <a href="?modulo=mapa&acao=<?= urlencode($mapaRetorno) ?>" class="party-cancel-btn" id="btnVoltarMapa">VOLTAR</a>
        </footer>

        <!-- ===== MINI-MENU (SUMMARY / SWITCH / CANCEL) ===== -->
        <div class="party-action-box hidden" id="partyActionBox" role="menu">
            <button type="button" class="party-action-item" id="btnActionSummary"><span class="cursor">▶</span> SUMMARY</button>
            <button type="button" class="party-action-item" id="btnActionSwitch"><span class="cursor">▶</span> SWITCH</button>
            <button type="button" class="party-action-item" id="btnActionCancel"><span class="cursor">▶</span> CANCEL</button>
        </div>

        <!-- ===== SUMMARY OVERLAY ===== -->
        <section class="summary-overlay hidden" id="summaryOverlay" aria-label="Resumo do Pokémon" aria-modal="true" role="dialog">
            <header class="summary-header">
                <div class="summary-tabs" role="tablist">
                    <button type="button" class="summary-tab active" id="tabStats" data-tab="stats" role="tab" aria-selected="true">1. STATS &amp; INFO</button>
                    <button type="button" class="summary-tab" id="tabMoves" data-tab="moves" role="tab" aria-selected="false">2. MOVES</button>
                </div>
                <div class="summary-nav">
                    <button type="button" class="summary-nav-btn" id="btnSummaryPrev" title="Pokémon anterior">◀</button>
                    <button type="button" class="summary-nav-btn" id="btnSummaryNext" title="Próximo Pokémon">▶</button>
                    <button type="button" class="summary-close-btn" id="btnSummaryClose">BACK</button>
                </div>
            </header>

            <div class="summary-body">
                <aside class="summary-identity">
                    <div class="summary-id-top">
                        <span class="summary-dex" id="summaryDex">#000</span>
                        <span class="summary-gender" id="summaryGender"></span>
                    </div>
                    <h1 class="summary-name" id="summaryName">POKÉMON</h1>
                    <div class="summary-sprite-frame">
                        <img id="summarySprite" src="" alt="Sprite do Pokémon">
                    </div>
                    <div class="summary-types" id="summaryTypes"></div>
                    <div class="summary-hp-card">
                        <div class="summary-hp-header">
                            <span class="summary-hp-title">HP</span>
                            <span class="status-badge hidden" id="summaryStatusBadge"></span>
                        </div>
                        <div class="hp-track">
                            <div class="hp-bar hp-high" id="summaryHpBar" style="width: 100%;"></div>
                        </div>
                        <div class="summary-hp-numbers" id="summaryHpNumbers">0 / 0</div>
                    </div>
                </aside>

                <div class="summary-content">
                    <div class="summary-tab-panel" id="panelStats">
                        <h2 class="summary-section-title">POKÉMON STATS</h2>
                        <div class="stats-list" id="summaryStatsList"></div>
                        <div class="summary-meta-row">
                            <div class="summary-meta-box">
                                <span class="meta-label">TOTAL</span>
                                <span class="meta-value" id="summaryStatTotal">0</span>
                            </div>
                            <div class="summary-meta-box">
                                <span class="meta-label">STAGE</span>
                                <span class="meta-value" id="summaryStage">1</span>
                            </div>
                            <div class="summary-meta-box">
                                <span class="meta-label">SLOT</span>
                                <span class="meta-value" id="summarySlotNum">#1</span>
                            </div>
                        </div>
                    </div>

                    <div class="summary-tab-panel hidden" id="panelMoves">
                        <h2 class="summary-section-title">KNOWN MOVES</h2>
                        <div class="summary-moves-list" id="summaryMovesList"></div>
                        <div class="summary-move-inspector" id="summaryMoveInspector">
                            <div class="inspector-grid">
                                <div class="inspector-item">
                                    <span class="inspector-label">TYPE/</span>
                                    <span class="move-type-pill" id="inspType">NORMAL</span>
                                </div>
                                <div class="inspector-item">
                                    <span class="inspector-label">CAT/</span>
                                    <span class="inspector-value" id="inspCat">PHYSICAL</span>
                                </div>
                                <div class="inspector-item">
                                    <span class="inspector-label">POWER/</span>
                                    <span class="inspector-value" id="inspPower">40</span>
                                </div>
                                <div class="inspector-item">
                                    <span class="inspector-label">ACC/</span>
                                    <span class="inspector-value" id="inspAcc">100%</span>
                                </div>
                            </div>
                            <div class="inspector-effect" id="inspEffect">Sem efeito secundário.</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <script>
        window.EQUIPE_INICIAL = <?= json_encode($slots,    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        window.TODOS_POKEMONS = <?= json_encode($pokemons, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    </script>
    <script src="assets/js/bolsaPokemons.js"></script>
</body>
</html>
