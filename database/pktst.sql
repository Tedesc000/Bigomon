INSERT INTO pokemon (id_pokemon, id_treinador, id_pokedex, nome, hp_atual, sexo, status) VALUES
(115, 1, 260, 'swampert', 100, 'masculino', NULL), -- Swampert
(116, 1, 291, 'ninjask', 61, 'masculino', NULL), -- Ninjask

INSERT INTO pokemon_movimento (id_pokemon, id_movimento) VALUES
-- Swampert: 
(115, 18), (115, 43), (115, 59), (115, 24),
-- Ninjask: 
(116, 28), (116, 39), (116, 52), (116, 15);