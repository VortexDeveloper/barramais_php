<?php
class Newsletter {
  protected $pdo;
  private $dsn = "mysql:host=barramais.mysql.dbaas.com.br;dbname=barramais";
  private $user = "barramais";
  private $password = "Loc@1020";
  private $table = "newsletter";


  public function __construct() {
    $this->connect();
  }

  private function connect() {
    if(isset($this->pdo)) return;

    try {
      $this->pdo = new PDO($this->dsn, $this->user, $this->password);
    } catch (PDOException $e) {
      echo $e->getMessage();
    }

  }

  public function select($email=NULL) {
    if ($email) {
      try {
        $stmt = $this->pdo->prepare("SELECT * FROM $this->table WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll();
      } catch (PDOException $e) {
        echo $e->getMessage();
      }
    }

    return false;
  }

  public function save($email=NULL) {
    if ($email) {
      if($this->select($email)) throw new Exception('Email já cadastrado na nossa newsletter! :)');

      try {
        $stmt = $this->pdo->prepare("INSERT INTO $this->table (email) VALUES (:email)");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        return $stmt->execute();
      } catch (PDOException $e) {
        echo $e->getMessage();
      }
    }
    return false;
  }

  public function get_pdo() {
    return $this->pdo;
  }
}
?>
