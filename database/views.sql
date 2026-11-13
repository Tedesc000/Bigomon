-- views para os logs

--View Geral: Todos os logs
CREATE OR REPLACE VIEW view_logs_todos AS
SELECT id_log, id_treinador, texto, data
FROM log;

--  View por Categoria: Captura e Ações de Pokémon
CREATE OR REPLACE VIEW view_logs_pokemon AS
SELECT id_log, id_treinador, texto, data
FROM log
WHERE texto LIKE '%Pokémon%' OR texto LIKE '%Pokemon%';

-- View por Categoria: Alteração de Saldo / Dinheiro
CREATE OR REPLACE VIEW view_logs_saldo AS
SELECT id_log, id_treinador, texto, data
FROM log
WHERE texto LIKE 'Saldo alterado%';

-- View por Categoria: Itens e Inventário (recebimento ou alteração de quantidade)
CREATE OR REPLACE VIEW view_logs_item AS
SELECT id_log, id_treinador, texto, data
FROM log
WHERE texto LIKE 'Item recebido%' OR texto LIKE 'Quantidade de item alterada%';

--  View por Categoria: Registro de Treinador
CREATE OR REPLACE VIEW view_logs_treinador AS
SELECT id_log, id_treinador, texto, data
FROM log
WHERE texto LIKE '%registrado no sistema%';

-- View por Data: Logs registrados Hoje
CREATE OR REPLACE VIEW view_logs_hoje AS
SELECT id_log, id_treinador, texto, data
FROM log
WHERE DATE(data) = CURDATE();

-- View por Data: Logs dos Últimos 7 Dias
CREATE OR REPLACE VIEW view_logs_semana AS
SELECT id_log, id_treinador, texto, data
FROM log
WHERE data >= NOW() - INTERVAL 7 DAY;

--  View por Data: Logs dos Últimos 30 Dias
CREATE OR REPLACE VIEW view_logs_mes AS
SELECT id_log, id_treinador, texto, data
FROM log
WHERE data >= NOW() - INTERVAL 30 DAY;

