
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS categoria;
DROP TABLE IF EXISTS efeito;
DROP TABLE IF EXISTS item;
DROP TABLE IF EXISTS log;
DROP TABLE IF EXISTS loja;
DROP TABLE IF EXISTS loja_item;
DROP TABLE IF EXISTS matinho;
DROP TABLE IF EXISTS matinho_pokedex;
DROP TABLE IF EXISTS movimento;
DROP TABLE IF EXISTS pokedex;
DROP TABLE IF EXISTS pokedex_tipo;
DROP TABLE IF EXISTS pokemon;
DROP TABLE IF EXISTS pokemon_movimento;
DROP TABLE IF EXISTS tipo;
DROP TABLE IF EXISTS treinador;
DROP TABLE IF EXISTS treinador_item;

SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;

CREATE TABLE categoria
(
  id_categoria BIGINT      NOT NULL AUTO_INCREMENT,
  nome         VARCHAR(50) NULL    ,
  descricao    VARCHAR(50) NULL    ,
  PRIMARY KEY (id_categoria)
);

CREATE TABLE efeito
(
  id_efeito BIGINT      NOT NULL,
  nome      VARCHAR(50) NOT NULL,
  chance    INT         NOT NULL,
  PRIMARY KEY (id_efeito)
);

CREATE TABLE item
(
  id_item      BIGINT      NOT NULL AUTO_INCREMENT,
  id_categoria BIGINT      NOT NULL,
  nome         VARCHAR(50) NULL    ,
  descricao    VARCHAR(50) NOT NULL,
  preco        INT         NOT NULL DEFAULT 100,
  PRIMARY KEY (id_item)
);

CREATE TABLE log
(
  id_log       BIGINT       NOT NULL AUTO_INCREMENT,
  id_treinador BIGINT       NOT NULL,
  texto        VARCHAR(250) NOT NULL,
  data         TIMESTAMP    NOT NULL,
  PRIMARY KEY (id_log)
);

CREATE TABLE loja
(
  id_loja BIGINT      NOT NULL AUTO_INCREMENT,
  nome    VARCHAR(50) NULL    ,
  PRIMARY KEY (id_loja)
);

CREATE TABLE loja_item
(
  id_loja BIGINT NOT NULL,
  id_item BIGINT NOT NULL
);

CREATE TABLE matinho
(
  id_matinho BIGINT      NOT NULL AUTO_INCREMENT,
  nome       VARCHAR(50) NULL    ,
  PRIMARY KEY (id_matinho)
);

CREATE TABLE matinho_pokedex
(
  id_matinho BIGINT NOT NULL,
  id_pokedex BIGINT NOT NULL
);

CREATE TABLE movimento
(
  id_movimento BIGINT      NOT NULL AUTO_INCREMENT,
  id_efeito    BIGINT      NULL    ,
  id_tipo      BIGINT      NOT NULL,
  nome         VARCHAR(50) NOT NULL,
  precisao     INT         NOT NULL,
  prioridade   INT         NOT NULL DEFAULT 0,
  poder        INT         NOT NULL,
  tipo         VARCHAR(3)  NOT NULL,
  PRIMARY KEY (id_movimento)
);

CREATE TABLE pokedex
(
  id_pokedex  BIGINT      NOT NULL,
  nome        VARCHAR(50) NOT NULL,
  fase        INT         NOT NULL,
  hp          INT         NOT NULL,
  atk         INT         NOT NULL,
  def         INT         NOT NULL,
  spa         INT         NOT NULL,
  spd         INT         NOT NULL,
  spe         INT         NOT NULL,
  id_evolucao INT         NULL    ,
  PRIMARY KEY (id_pokedex)
);

CREATE TABLE pokedex_tipo
(
  id_tipo    BIGINT NOT NULL,
  id_pokedex BIGINT NOT NULL
);

CREATE TABLE pokemon
(
  id_pokemon   BIGINT      NOT NULL AUTO_INCREMENT,
  id_treinador BIGINT      NOT NULL,
  id_pokedex   BIGINT      NOT NULL,
  nome         VARCHAR(20) NOT NULL,
  hp_atual     INT         NOT NULL,
  sexo         VARCHAR(15) NOT NULL,
  status       VARCHAR(30) NULL    ,
  PRIMARY KEY (id_pokemon)
);

CREATE TABLE pokemon_movimento
(
  id_pokemon   BIGINT NOT NULL,
  id_movimento BIGINT NOT NULL
);

CREATE TABLE tipo
(
  id_tipo   BIGINT      NOT NULL AUTO_INCREMENT,
  nome      VARCHAR(50) NOT NULL,
  res_d     VARCHAR(50) NULL    ,
  res_q     VARCHAR(50) NULL    ,
  frq_d     VARCHAR(50) NULL    ,
  frq_q     VARCHAR(50) NULL    ,
  imunidade VARCHAR(50) NULL    ,
  PRIMARY KEY (id_tipo)
);

CREATE TABLE treinador
(
  id_treinador BIGINT       NOT NULL AUTO_INCREMENT,
  nome         VARCHAR(100) NOT NULL,
  email        VARCHAR(50)  NOT NULL,
  senha        VARCHAR(50)  NOT NULL,
  sexo         VARCHAR(50)  NOT NULL,
  foto         VARCHAR(100) NULL    ,
  dinheiro     INT          NOT NULL DEFAULT 0,
  slot1        INT          NULL    ,
  slot2        INT          NULL    ,
  slot3        INT          NULL    ,
  slot4        INT          NULL    ,
  slot5        INT          NULL    ,
  slot6        INT          NULL    ,
  PRIMARY KEY (id_treinador)
);

ALTER TABLE treinador
  ADD CONSTRAINT UQ_treinador_email UNIQUE (email);

CREATE TABLE treinador_item
(
  id_treinador BIGINT NOT NULL,
  id_item      BIGINT NOT NULL,
  quantidade   INT    NULL     DEFAULT 0
);

ALTER TABLE pokemon
  ADD CONSTRAINT FK_treinador_TO_pokemon
    FOREIGN KEY (id_treinador)
    REFERENCES treinador (id_treinador);

ALTER TABLE treinador_item
  ADD CONSTRAINT FK_treinador_TO_treinador_item
    FOREIGN KEY (id_treinador)
    REFERENCES treinador (id_treinador);

ALTER TABLE treinador_item
  ADD CONSTRAINT FK_item_TO_treinador_item
    FOREIGN KEY (id_item)
    REFERENCES item (id_item);

ALTER TABLE loja_item
  ADD CONSTRAINT FK_loja_TO_loja_item
    FOREIGN KEY (id_loja)
    REFERENCES loja (id_loja);

ALTER TABLE loja_item
  ADD CONSTRAINT FK_item_TO_loja_item
    FOREIGN KEY (id_item)
    REFERENCES item (id_item);

ALTER TABLE pokedex_tipo
  ADD CONSTRAINT FK_tipo_TO_pokedex_tipo
    FOREIGN KEY (id_tipo)
    REFERENCES tipo (id_tipo);

ALTER TABLE pokedex_tipo
  ADD CONSTRAINT FK_pokedex_TO_pokedex_tipo
    FOREIGN KEY (id_pokedex)
    REFERENCES pokedex (id_pokedex);

ALTER TABLE pokemon
  ADD CONSTRAINT FK_pokedex_TO_pokemon
    FOREIGN KEY (id_pokedex)
    REFERENCES pokedex (id_pokedex);

ALTER TABLE pokemon_movimento
  ADD CONSTRAINT FK_pokemon_TO_pokemon_movimento
    FOREIGN KEY (id_pokemon)
    REFERENCES pokemon (id_pokemon);

ALTER TABLE pokemon_movimento
  ADD CONSTRAINT FK_movimento_TO_pokemon_movimento
    FOREIGN KEY (id_movimento)
    REFERENCES movimento (id_movimento);

ALTER TABLE matinho_pokedex
  ADD CONSTRAINT FK_matinho_TO_matinho_pokedex
    FOREIGN KEY (id_matinho)
    REFERENCES matinho (id_matinho);

ALTER TABLE matinho_pokedex
  ADD CONSTRAINT FK_pokedex_TO_matinho_pokedex
    FOREIGN KEY (id_pokedex)
    REFERENCES pokedex (id_pokedex);

ALTER TABLE item
  ADD CONSTRAINT FK_categoria_TO_item
    FOREIGN KEY (id_categoria)
    REFERENCES categoria (id_categoria);

ALTER TABLE log
  ADD CONSTRAINT FK_treinador_TO_log
    FOREIGN KEY (id_treinador)
    REFERENCES treinador (id_treinador);

ALTER TABLE movimento
  ADD CONSTRAINT FK_efeito_TO_movimento
    FOREIGN KEY (id_efeito)
    REFERENCES efeito (id_efeito);

ALTER TABLE movimento
  ADD CONSTRAINT FK_tipo_TO_movimento
    FOREIGN KEY (id_tipo)
    REFERENCES tipo (id_tipo);
