INSERT INTO pokemon (id_pokemon, id_treinador, id_pokedex, nome, hp_atual, sexo, status) VALUES
(115, 1, 260, 'swampert', 100, 'masculino', NULL), -- Swampert
(116, 1, 291, 'ninjask', 61, 'masculino', NULL), -- Ninjask
(117, 1, 302, 'torkoal', 61, 'masculino', NULL); -- Torkoal

INSERT INTO pokemon_movimento (id_pokemon, id_movimento) VALUES
-- Swampert: 
(115, 18), (115, 43), (115, 59), (115, 24),
-- Ninjask: 
(116, 28), (116, 39), (116, 52), (116, 15),
-- Torkoal: 
(117, 28), (117, 39), (117, 52), (117, 15);

UPDATE treinador SET slot2=115, slot3=116, slot4=117 WHERE id_treinador=1;