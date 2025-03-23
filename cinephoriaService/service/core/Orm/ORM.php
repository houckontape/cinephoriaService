<?php

namespace core\Orm;

use PDO;

class ORM implements ORMInterface
{
    private PDO $connection;

    function __construct()
    {
        $this->connection = DatabaseConnection::getConnection();
    }
    /**
     * @inheritDoc
     */
    public function select(string $table, array $columns = ['*'], array $conditions = []): array
    {
        $columnsString = implode(', ', $columns);
        $query = "SELECT $columnsString FROM $table";

        // Ajouter les conditions si elles sont spécifiées
        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $key => $value) {
                $whereClauses[] = "$key = :$key";
            }
            $query .= ' WHERE ' . implode(' AND ', $whereClauses);
        }

        $stmt = $this->connection->prepare($query);

        // Associer les valeurs des conditions
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @inheritDoc
     */
    public function insert(string $table, array $data): bool
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->connection->prepare($query);

        // Associer les valeurs des données
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }

    /**
     * @inheritDoc
     */
    public function update(string $table, array $data, array $conditions): bool
    {
        $setClauses = [];
        foreach ($data as $key => $value) {
            $setClauses[] = "$key = :$key";
        }

        $query = "UPDATE $table SET " . implode(', ', $setClauses);

        // Ajouter les conditions si elles existent
        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $key => $value) {
                $whereClauses[] = "$key = :cond_$key";
            }
            $query .= ' WHERE ' . implode(' AND ', $whereClauses);
        }

        $stmt = $this->connection->prepare($query);

        // Associer les valeurs des données
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        // Associer les valeurs des conditions
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":cond_$key", $value);
        }

        return $stmt->execute();
    }

    /**
     * @inheritDoc
     */
    public function delete(string $table, array $conditions): bool
    {
        $query = "DELETE FROM $table";

        // Ajouter les conditions si elles sont spécifiées
        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $key => $value) {
                $whereClauses[] = "$key = :$key";
            }
            $query .= ' WHERE ' . implode(' AND ', $whereClauses);
        }

        $stmt = $this->connection->prepare($query);

        // Associer les valeurs des conditions
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }

    /**
     * Insert data into a specified table and return the ID of the inserted row.
     *
     * @param string $table
     * @param array $data
     * @return int|null
     */

    public function insertAndGetId(string $table, array $data): ?int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->connection->prepare($query);

        // Associate the data values
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        if ($stmt->execute()) {
            return (int)$this->connection->lastInsertId();
        }

        return null;
    }
}

