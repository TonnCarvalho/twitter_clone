<?php

namespace App\Models;

use MF\Model\Model;

class Usuario extends Model
{
    private $id;
    private $nome;
    private $email;
    private $senha;

    //atributos privados, precisam de métodos mágicos para manipular os dados.
    public function __get($atributo)
    {
        return $this->$atributo;
    }

    //Atribui o valor ao atributo, que vem da controller.
    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }

    //Salvar cadastro
    public function salvarCadastro()
    {
        $query = "INSERT INTO usuarios(nome, email, senha) VALUES (:nome, :email, :senha)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', $this->__get('nome'));
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->execute();

        return $this; //retorna o proprio objeto
    }

    //Validar se um cadastro pode ser feito
    public function validarCadastro()
    {
        $valido = true;

        if (strlen($this->__get('nome')) < 3) {
            $valido = false;
        }
        if (strlen($this->__get('email')) < 3) {
            $valido = false;
        }
        if (strlen($this->__get('senha')) < 3) {
            $valido = false;
        }
        return $valido;
    }

    //Recuperar um usuario pelo e-mail
    public function getUsuarioPorEmail()
    {
        $query = 'SELECT nome, email, senha 
        FROM usuarios 
        WHERE email = :email';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function autenticar()
    {
        $query = "SELECT id, nome, email 
        FROM usuarios 
        WHERE email = :email 
        AND senha = :senha";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->execute();

        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($usuario['id'] != '' && $usuario['nome'] != '') {
            $this->__set('id', $usuario['id']);
            $this->__set('nome', $usuario['nome']);
        }

        return $this;
    }
    /**
     * Retorna os usuarios buscado em /quem_seguir
     * @return void
     */
    public function getAll()
    {
        $query = "SELECT u.id, u.nome, u.email,
        (
        SELECT count(*)
        FROM usuarios_seguidores as us
        WHERE us.id_usuario = :id_usuario
        AND us.id_usuario_seguindo = u.id
        ) as seguindo_sn
        FROM usuarios as u
        WHERE u.nome
        LIKE :nome
        AND u.id != :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', '%' . $this->__get('nome') . '%');
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    //Informações dos usuarios
    public function getInfoUser()
    {
        $query = "SELECT * FROM usuarios WHERE id = :id_usuarios";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_usuarios', $this->__get('id'));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    // //Total de Tweets
    public function getAllTweet()
    {
        $query = "SELECT count(*) as tota_tweet FROM tweets WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    // //Total de usuarios que estamos seguindo
    public function getFollowing()
    {
        $query = "SELECT count(*) as total_seguindo FROM usuarios_seguidores WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    // //Total de seguidores
    public function getFollowers()
    {
        $query = "SELECT count(*) as total_seguidores FROM usuarios_seguidores WHERE id_usuario_seguindo = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
