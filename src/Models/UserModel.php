<?php

namespace App\Models;

use App\Core\Model;

class UserModel extends Model
{
    public function findById(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM users WHERE id = :id', [':id' => $id]);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->fetchOne('SELECT * FROM users WHERE email = :email', [':email' => $email]);
    }

    public function findByPseudo(string $pseudo): ?array
    {
        return $this->fetchOne('SELECT * FROM users WHERE pseudo = :pseudo', [':pseudo' => $pseudo]);
    }

    public function create(string $pseudo, string $email, string $password): int
    {
        $this->run(
            'INSERT INTO users (pseudo, email, password) VALUES (:pseudo, :email, :password)',
            [':pseudo' => $pseudo, ':email' => $email, ':password' => password_hash($password, PASSWORD_DEFAULT)]
        );
        return (int) $this->db->lastInsertId();
    }

    public function updateProfile(int $id, string $pseudo, string $email): void
    {
        $this->run(
            'UPDATE users SET pseudo = :pseudo, email = :email WHERE id = :id',
            [':pseudo' => $pseudo, ':email' => $email, ':id' => $id]
        );
    }

    public function updatePassword(int $id, string $password): void
    {
        $this->run(
            'UPDATE users SET password = :password WHERE id = :id',
            [':password' => password_hash($password, PASSWORD_DEFAULT), ':id' => $id]
        );
    }

    public function all(): array
    {
        return $this->fetchAll('SELECT id, pseudo, email, role, is_banned, created_at FROM users ORDER BY created_at DESC');
    }

    public function setRole(int $id, string $role): void
    {
        $this->run('UPDATE users SET role = :role WHERE id = :id', [':role' => $role, ':id' => $id]);
    }

    public function setBanned(int $id, bool $banned): void
    {
        $this->run('UPDATE users SET is_banned = :b WHERE id = :id', [':b' => $banned ? 1 : 0, ':id' => $id]);
    }

    public function delete(int $id): void
    {
        $this->run('DELETE FROM users WHERE id = :id', [':id' => $id]);
    }

    public function count(): int
    {
        return (int) $this->fetchValue('SELECT COUNT(*) FROM users');
    }
}
