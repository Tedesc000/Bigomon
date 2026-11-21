INSERT INTO treinador (id_treinador, nome, email, senha, sexo, foto, dinheiro, slot1, slot2, slot3, slot4, slot5, slot6) VALUES
-- Usuario padrão: slot1=Pokemon 1 (Mudkip)
(1, 'joao', 'joao@joao.com', 'joao', 'masculino', 'fotoPerfilDefault.webp', 1000, NULL, NULL, NULL, NULL, NULL, NULL),
-- NPCs: a coluna dinheiro é a recompensa paga ao jogador quando ele vence o treinador
-- Líderes de ginásio (recompensa = 1000 x número do ginásio)
(2, 'Roxanne', 'npc-02@npc.invalid', 'NPC_SEM_LOGIN', 'feminino', NULL, 1000, 2, 3, 58, NULL, NULL, NULL),
(3, 'Brawly', 'npc-03@npc.invalid', 'NPC_SEM_LOGIN', 'masculino', NULL, 2000, 4, 5, NULL, NULL, NULL, NULL),
(4, 'Wattson', 'npc-04@npc.invalid', 'NPC_SEM_LOGIN', 'masculino', NULL, 3000, 6, 7, 60, NULL, NULL, NULL),
(5, 'Flannery', 'npc-05@npc.invalid', 'NPC_SEM_LOGIN', 'feminino', NULL, 4000, 8, 9, 62, NULL, NULL, NULL),
(6, 'Norman', 'npc-06@npc.invalid', 'NPC_SEM_LOGIN', 'masculino', NULL, 5000, 10, 11, 64, 65, NULL, NULL),
(7, 'Winona', 'npc-07@npc.invalid', 'NPC_SEM_LOGIN', 'feminino', NULL, 6000, 12, 13, 66, 67, 68, NULL),
-- Tate & Liza viraram um único treinador com o time dos dois (o id 9 ficou vago de propósito)
(8, 'Tate & Liza', 'npc-08@npc.invalid', 'NPC_SEM_LOGIN', 'desconhecido', NULL, 7000, 14, 69, 15, 70, NULL, NULL),
(10, 'Juan', 'npc-10@npc.invalid', 'NPC_SEM_LOGIN', 'masculino', NULL, 8000, 16, 17, 71, 72, 73, NULL),
-- Elite Four: Sidney, Phoebe, Glacia e Drake
(11, 'Sidney', 'npc-11@npc.invalid', 'NPC_SEM_LOGIN', 'masculino', NULL, 5000, 18, 19, 20, 74, 75, NULL),
(12, 'Phoebe', 'npc-12@npc.invalid', 'NPC_SEM_LOGIN', 'feminino', NULL, 5000, 21, 22, 23, 76, 77, NULL),
(13, 'Glacia', 'npc-13@npc.invalid', 'NPC_SEM_LOGIN', 'feminino', NULL, 5000, 24, 25, 26, 78, 79, NULL),
(14, 'Drake', 'npc-14@npc.invalid', 'NPC_SEM_LOGIN', 'masculino', NULL, 5000, 27, 28, 29, 80, 81, NULL),
-- Campeao: Wallace
(15, 'Wallace', 'npc-15@npc.invalid', 'NPC_SEM_LOGIN', 'masculino', NULL, 10000, 30, 31, 32, 33, 82, 83),
-- Treinadores genéricos: 1 e 2 ficam dentro do ginásio (150 x nº do ginásio),
-- o 3 fica fora do ginásio, no mapa, e pode ser enfrentado infinitas vezes (100 x nº do ginásio)
-- Rustboro
(16, 'Treinador Rustboro 1', 'npc-16@npc.invalid', 'NPC_SEM_LOGIN', 'masculino', NULL, 150, 34, 100, NULL, NULL, NULL, NULL),
(17, 'Treinador Rustboro 2', 'npc-17@npc.invalid', 'NPC_SEM_LOGIN', 'feminino', NULL, 150, 35, 101, NULL, NULL, NULL, NULL),
(18, 'Treinador Rustboro 3', 'npc-18@npc.invalid', 'NPC_SEM_LOGIN', 'masculino', NULL, 100, 36, 84, 85, NULL, NULL, NULL),
-- Dewford
(19, 'Treinador Dewford 1', 'npc-19@npc.invalid', 'NPC_SEM_LOGIN', 'feminino', NULL, 300, 37, 102, NULL, NULL, NULL, NULL),
(20, 'Treinador Dewford 2', 'npc-20@npc.invalid', 'NPC_SEM_LOGIN', 'masculino', NULL, 300, 38, 103, NULL, NULL, NULL, NULL),
(21, 'Treinador Dewford 3', 'npc-21@npc.invalid', 'NPC_SEM_LOGIN', 'feminino', NULL, 200, 39, 86, 87, NULL, NULL, NULL),
-- Mauville
(22, 'Treinador Mauville 1', 'npc-22@npc.invalid', 'NPC_SEM_LOGIN', 'masculino', NULL, 450, 40, 61, NULL, NULL, NULL, NULL),
(23, 'Treinador Mauville 2', 'npc-23@npc.invalid', 'NPC_SEM_LOGIN', 'feminino', NULL, 450, 41, 104, NULL, NULL, NULL, NULL),
(24, 'Treinador Mauville 3', 'npc-24@npc.invalid', 'NPC_SEM_LOGIN', 'masculino', NULL, 300, 42, 88, 89, NULL, NULL, NULL),
-- Lavaridge
(25, 'Treinador Lavaridge 1', 'npc-25@npc.invalid', 'NPC_SEM_LOGIN', 'feminino', NULL, 600, 43, 63, NULL, NULL, NULL, NULL),
(26, 'Treinador Lavaridge 2', 'npc-26@npc.invalid', 'NPC_SEM_LOGIN', 'masculino', NULL, 600, 44, 105, NULL, NULL, NULL, NULL),
(27, 'Treinador Lavaridge 3', 'npc-27@npc.invalid', 'NPC_SEM_LOGIN', 'feminino', NULL, 400, 45, 90, 91, NULL, NULL, NULL),
-- Petalburg
(28, 'Treinador Petalburg 1', 'npc-28@npc.invalid', 'NPC_SEM_LOGIN', 'masculino', NULL, 750, 46, 106, NULL, NULL, NULL, NULL),
(29, 'Treinador Petalburg 2', 'npc-29@npc.invalid', 'NPC_SEM_LOGIN', 'feminino', NULL, 750, 47, 107, NULL, NULL, NULL, NULL),
(30, 'Treinador Petalburg 3', 'npc-30@npc.invalid', 'NPC_SEM_LOGIN', 'masculino', NULL, 500, 48, 92, 93, NULL, NULL, NULL),
-- Fortree
(31, 'Treinador Fortree 1', 'npc-31@npc.invalid', 'NPC_SEM_LOGIN', 'feminino', NULL, 900, 49, 108, NULL, NULL, NULL, NULL),
(32, 'Treinador Fortree 2', 'npc-32@npc.invalid', 'NPC_SEM_LOGIN', 'masculino', NULL, 900, 50, 109, NULL, NULL, NULL, NULL),
(33, 'Treinador Fortree 3', 'npc-33@npc.invalid', 'NPC_SEM_LOGIN', 'feminino', NULL, 600, 51, 94, 95, NULL, NULL, NULL),
-- Mossdeep
(34, 'Treinador Mossdeep 1', 'npc-34@npc.invalid', 'NPC_SEM_LOGIN', 'masculino', NULL, 1050, 52, 110, NULL, NULL, NULL, NULL),
(35, 'Treinador Mossdeep 2', 'npc-35@npc.invalid', 'NPC_SEM_LOGIN', 'feminino', NULL, 1050, 53, 111, NULL, NULL, NULL, NULL),
(36, 'Treinador Mossdeep 3', 'npc-36@npc.invalid', 'NPC_SEM_LOGIN', 'masculino', NULL, 700, 54, 96, 97, NULL, NULL, NULL),
-- Sootopolis
(37, 'Treinador Sootopolis 1', 'npc-37@npc.invalid', 'NPC_SEM_LOGIN', 'feminino', NULL, 1200, 55, 112, NULL, NULL, NULL, NULL),
(38, 'Treinador Sootopolis 2', 'npc-38@npc.invalid', 'NPC_SEM_LOGIN', 'masculino', NULL, 1200, 56, 113, NULL, NULL, NULL, NULL),
(39, 'Treinador Sootopolis 3', 'npc-39@npc.invalid', 'NPC_SEM_LOGIN', 'feminino', NULL, 800, 57, 98, 99, NULL, NULL, NULL);
