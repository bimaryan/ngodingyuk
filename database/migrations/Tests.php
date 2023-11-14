<?php
namespace database\migrations;
use app\Database\Migration;
class Tests extends Migration
{
    private $tableName = "Tests";

    public function up()
    {
        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS ' . $this->tableName . ' (
                id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(255),
                email VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ');
        
        return $this->tableName . " Create succesfully";
    }

    public function down()
    {
        $this->pdo->exec('DROP TABLE IF EXISTS ' . $this->tableName);
        return $this->tableName . " Drop succesfully";
    }
}
