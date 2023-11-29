<?
namespace worker;

require_once("connexionConfig.php");

use PDO;
use PDOException;

class Connexion {
    private static $_instance = null;
    private $pdo;

    private function __construct()
    {
        try {
            $this->pdo = new PDO(DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS, array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ));
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Can not connect to he database. " . $e->getMessage();
            die();
        }
    }

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new connexion();
        }
        return self::$_instance;
    }

    public function selectQuery(string $query, array $params)
    {
        $queryPrepared = $this->pdo->prepare($query);
        $queryPrepared->execute($params);
        $result = $queryPrepared->fetchAll();
        if ($result == false) throw new PDOException("the response of the select query is false");
        return $result;
    }

    public function selectQueryOne(string $query, array $params)
    {
        $queryPrepared = $this->pdo->prepare($query);
        $queryPrepared->execute($params);
        $result = $queryPrepared->fetch();
        if ($result == false) throw new PDOException("Not found");
        return $result;
    }

    public function executeQuery(string $query, array $params)
    {
        $queryPrepared = $this->pdo->prepare($query);
        if (!$queryPrepared->execute($params)) {
            throw new PDOException("The query has fail, no further information");
        }
    }


    public function startTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    public function addQueryToTransaction($query, $params)
    {
        $res = false;
        if ($this->pdo->inTransaction()) {
            $maQuery = $this->pdo->prepare($query);
            $res = $maQuery->execute($params);
        }
        return $res;
    }

    public function commitTransaction()
    {
        $res = false;
        if ($this->pdo->inTransaction()) {
            $res = $this->pdo->commit();
        }
        return $res;
    }

    public function rollbackTransaction()
    {
        $res = false;
        if ($this->pdo->inTransaction()) {
            $res = $this->pdo->rollBack();
        }
        return $res;
    }
}