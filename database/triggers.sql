
-- Triggers do jogo

-- Ajuste para garantir que mensagens de log detalhadas caibam na coluna
ALTER TABLE log MODIFY texto VARCHAR(255) NOT NULL;
$$

-- Trigger para quando uma nova conta de treinador for cadastrada
DROP TRIGGER IF EXISTS trg_treinador_novo_insert;
DELIMITER $$
CREATE TRIGGER trg_treinador_novo_insert
AFTER INSERT ON treinador
FOR EACH ROW
BEGIN
    INSERT INTO log (id_treinador, texto, data)
    VALUES (
        NEW.id_treinador,
        CONCAT('Treinador "', NEW.nome, '" registrado no sistema.'),
        NOW()
    );
END$$
DELIMITER ;

-- Trigger para quando o saldo em dinheiro do treinador for alterado
DROP TRIGGER IF EXISTS trg_treinador_dinheiro_update;
DELIMITER $$
CREATE TRIGGER trg_treinador_dinheiro_update
AFTER UPDATE ON treinador
FOR EACH ROW
BEGIN
    IF OLD.dinheiro <> NEW.dinheiro THEN
        INSERT INTO log (id_treinador, texto, data)
        VALUES (
            NEW.id_treinador,
            CONCAT('Saldo alterado: R$ ', OLD.dinheiro, ' -> R$ ', NEW.dinheiro),
            NOW()
        );
    END IF;
END$$
DELIMITER ;

-- Trigger para quando um novo Pokémon for obtido/capturado pelo treinador
DROP TRIGGER IF EXISTS trg_pokemon_capturado_insert;
DELIMITER $$
CREATE TRIGGER trg_pokemon_capturado_insert
AFTER INSERT ON pokemon
FOR EACH ROW
BEGIN
    INSERT INTO log (id_treinador, texto, data)
    VALUES (
        NEW.id_treinador,
        CONCAT('Novo Pokémon adicionado à equipe: ', NEW.nome, ' (ID: ', NEW.id_pokemon, ')'),
        NOW()
    );
END$$
DELIMITER ;

-- Trigger para quando um item for adquirido pela primeira vez no inventário
DROP TRIGGER IF EXISTS trg_treinador_item_insert;
DELIMITER $$
CREATE TRIGGER trg_treinador_item_insert
AFTER INSERT ON treinador_item
FOR EACH ROW
BEGIN
    INSERT INTO log (id_treinador, texto, data)
    VALUES (
        NEW.id_treinador,
        CONCAT('Item recebido no inventário (Item ID: ', NEW.id_item, ' | Quantidade: ', NEW.quantidade, ')'),
        NOW()
    );
END$$
DELIMITER ;

-- Trigger para quando a quantidade de um item for alterada (compra, uso ou descarte)
DROP TRIGGER IF EXISTS trg_treinador_item_update;
DELIMITER $$
CREATE TRIGGER trg_treinador_item_update
AFTER UPDATE ON treinador_item
FOR EACH ROW
BEGIN
    IF OLD.quantidade <> NEW.quantidade THEN
        INSERT INTO log (id_treinador, texto, data)
        VALUES (
            NEW.id_treinador,
            CONCAT('Quantidade de item alterada (Item ID: ', NEW.id_item, '): de ', OLD.quantidade, ' para ', NEW.quantidade),
            NOW()
        );
    END IF;
END$$
DELIMITER ;

