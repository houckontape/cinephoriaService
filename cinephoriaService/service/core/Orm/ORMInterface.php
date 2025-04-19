<?php

namespace core\Orm;

/**
 *
 */
interface ORMInterface
{
    /**
     * @uses $result = $orm->select('users', ['id', 'name'], ['id' => 1]);
     * @param string $table
     * @param array $columns
     * @param array $conditions
     * @return array
     */
    public function select(string $table, array $columns = ['*'], array $conditions = []): array;

    /**
     * @param string $table
     * @param array $data
     * @return bool
     * @uses $insert = $orm->insert('users', ['name' => 'Jean Dupont', 'email' => 'jean.dupont@example.com']);
     * echo $insert ? 'Insertion réussie' : 'Erreur lors de l\'insertion';
     */
    public function insert(string $table, array $data): bool;

    /**
     * @param string $table
     * @param array $data
     * @param array $conditions
     * @return bool
     * @uses $update = $orm->update('users', ['name' => 'John Doe'], ['id' => 1]);
     * echo $update ? 'Mise à jour réussie' : 'Erreur lors de la mise à jour';
     */
    public function update(string $table, array $data, array $conditions): bool;

    /**
     * @param string $table
     * @param array $conditions
     * @return bool
     * @uses $delete = $orm->delete('users', ['id' => 1]);
     * echo $delete ? 'Suppression réussie' : 'Erreur lors de la suppression';
     */
    public function delete(string $table, array $conditions): bool;
}