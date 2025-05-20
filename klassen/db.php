<?php
class Database
{
    private $host = 'localhost';
    private $dbname = 'roadmate'; // Pas aan indien anders
    private $username = 'root';   // Pas aan indien je een andere gebruiker hebt
    private $password = '';       // Voeg je wachtwoord toe indien nodig
    private $pdo;

    public function __construct()
    {
        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->username,
                $this->password
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Verbinding met database mislukt: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}
?>
