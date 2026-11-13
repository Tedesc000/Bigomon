<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/inicioLogin.css">
    <?php
    $categoria = $categoria ?? ($_GET['categoria'] ?? 'todos');
    $periodo   = $periodo ?? ($_GET['periodo'] ?? 'todos');
    $logs      = $logs ?? [];
    ?>
    <title>Histórico de Logs</title>
</head>
<body id="home">
    <div id="container">
        <div id="tituloContainer">
            <img src="assets/img/inicioLogin/LogoBigomon.png" alt="Bigomon" id="titulo" class="logoBigomon logoVerLogs">
        </div>

        <div id="formLogs">
            <!-- Formulário com dois selects para filtrar pelas views -->
            <form action="index.php" method="GET" id="filtrosLogs">
                <input type="hidden" name="modulo" value="treinador">
                <input type="hidden" name="acao" value="verLogs">

                <!-- Select de Categoria-->
                <select name="categoria" id="selectCategoria" class="selectLogs" onchange="this.form.submit()">
                    <option value="todos" <?= ($categoria === 'todos') ? 'selected' : '' ?>>Todas as Ações</option>
                    <option value="pokemon" <?= ($categoria === 'pokemon') ? 'selected' : '' ?>>Captura de Pokémon</option>
                    <option value="saldo" <?= ($categoria === 'saldo') ? 'selected' : '' ?>>Alteração de Saldo</option>
                    <option value="item" <?= ($categoria === 'item') ? 'selected' : '' ?>>Itens e Inventário</option>
                    <option value="treinador" <?= ($categoria === 'treinador') ? 'selected' : '' ?>>Cadastro / Treinador</option>
                </select>

                <!-- Select de filtro por Data-->
                <select name="periodo" id="selectPeriodo" class="selectLogs" onchange="this.form.submit()">
                    <option value="todos" <?= ($periodo === 'todos') ? 'selected' : '' ?>>Todo o Histórico</option>
                    <option value="hoje" <?= ($periodo === 'hoje') ? 'selected' : '' ?>>Hoje</option>
                    <option value="7dias" <?= ($periodo === '7dias') ? 'selected' : '' ?>>Últimos 7 dias</option>
                    <option value="30dias" <?= ($periodo === '30dias') ? 'selected' : '' ?>>Últimos 30 dias</option>
                </select>
            </form>

            <!-- Tabela com os dados retornados da view-->
            <div id="tabelaLogsWrapper">
                <table id="tabelaLogs">
                    <thead>
                        <tr>
                            <th style="width: 25%;">DATA / HORA</th>
                            <th style="width: 75%;">AÇÃO REGISTRADA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($logs)): ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i:s', strtotime($log['data'])) ?></td>
                                    <td><?= htmlspecialchars($log['texto']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" style="text-align: center; padding: 25px; color: #666; font-size: 1.8rem;">
                                    Nenhum log encontrado para o filtro selecionado.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="btnVerLogsVoltar">
            <a href="?modulo=treinador&acao=verPerfil" class="botaoInicio">
                <span class="hoverText">▸</span>
                <span class="defaultText">VOLTAR</span>
            </a>
        </div> 
    </div>
</body>
</html>
