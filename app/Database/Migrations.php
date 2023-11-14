<?php
namespace app\Database;
class Migration
{
    protected $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
}
