<?php
class Pokemon extends Modelo {
    private $id_pokemon;
    private $id_treinador;
    private $id_pokedex;
    private $nome;
    private $hp_atual;
    private $sexo;
    private $status;
    public function getIdPokemon(){
        return $this->id_pokemon;
    }
    public function getIdTreinador(){
        return $this->id_treinador;
    }
    public function getIdPokedex(){
        return $this->id_pokedex;
    }
    public function getNome(){
        return $this->nome;
    }
    public function getHpAtual(){
        return $this->hp_atual;
    }
    public function getSexo(){
        return $this->sexo;
    }
    public function getStatus(){
        return $this->status;
    }

    public function setIdPokemon($id_pokemon){
        $this->id_pokemon = $id_pokemon;
    }
    public function setIdTreinador($id_treinador){
        $this->id_treinador = $id_treinador;
    }
    public function setIdPokedex($id_pokedex){
        $this->id_pokedex = $id_pokedex;
    }
    public function setNome($nome){
        $this->nome = $nome;
    }
    public function setHpAtual($hp_atual){
        $this->hp_atual = $hp_atual;
    }
    public function setSexo($sexo){
        $this->sexo = $sexo;
    }
    public function setStatus($status){
        $this->status = $status;
    }

    public function setDadosPokedex($id_treinador){
        $dadosPokedex = $this->pdo->prepare("SELECT * FROM pokedex WHERE id_pokedex = :id_pokedex");
        $dadosPokedex->execute(['id_pokedex' => $this->id_pokedex]);
        $dados = $dadosPokedex->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            return false;
        }

        $nome = $dados['nome'];
        $hp_atual = $dados['hp'];
        $nSexo = rand(0,1);
        if($nSexo == 0){
            $sexo = 'Masculino';
        } else {
            $sexo = 'Feminino';
        }
        $stmt = $this->pdo->prepare("INSERT INTO pokemon (id_treinador, id_pokedex, nome, hp_atual, sexo) VALUES (:id_treinador, :id_pokedex, :nome, :hp_atual, :sexo)");
        $sucesso = $stmt->execute([
            'id_treinador' => $id_treinador,
            'id_pokedex' => $this->id_pokedex,
            'nome' => $nome,
            'hp_atual' => $hp_atual,
            'sexo' => $sexo
        ]);
        if ($sucesso) {
            $this->id_pokemon = $this->pdo->lastInsertId();
        }
        return $sucesso;
    }
}