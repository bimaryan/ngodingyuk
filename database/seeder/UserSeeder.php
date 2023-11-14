<?php
namespace database\seeder;

use app\Database\Migration;
class UserSeeder extends Migration
{
    private $tableName = "users";
    
    public function run()
    {
        // Hapus data yang ada sebelumnya (optional)
        // $this->pdo->exec('TRUNCATE TABLE ' . $this->tableName);

        // Tambahkan data dummy
        $this->pdo->exec("
            INSERT INTO {$this->tableName} (name, email, created_at, updated_at)
            VALUES
            ('John Doe', 'john@example.com', NOW(), NOW()),
            ('Jane Doe', 'jane@example.com', NOW(), NOW()),
            ('Bob Smith', 'bob@example.com', NOW(), NOW())
        ");
        return "Seed $this->tableName Susccesfully\n";
    }
}
