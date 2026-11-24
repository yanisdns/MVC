<?php

namespace App\Models;

use App\Core\Model;

class CategoryModel extends Model
{
    public function all(): array
    {
        return $this->fetchAll('SELECT * FROM categories ORDER BY name');
    }

    // Categories avec le nombre d'articles publies (pour la barre laterale)
    public function allWithCount(): array
    {
        return $this->fetchAll(
            "SELECT c.*, COUNT(a.id) AS nb_articles
             FROM categories c
             LEFT JOIN articles a ON a.category_id = c.id AND a.status = 'published'
             GROUP BY c.id
             ORDER BY c.name"
        );
    }

    // Version admin : compte tous les articles, brouillons compris
    public function allWithTotals(): array
    {
        return $this->fetchAll(
            'SELECT c.*, COUNT(a.id) AS nb_articles
             FROM categories c
             LEFT JOIN articles a ON a.category_id = c.id
             GROUP BY c.id
             ORDER BY c.name'
        );
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM categories WHERE id = :id', [':id' => $id]);
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->fetchOne('SELECT * FROM categories WHERE slug = :slug', [':slug' => $slug]);
    }

    public function slugExists(string $slug, ?int $exceptId = null): bool
    {
        return (bool) $this->fetchValue(
            'SELECT COUNT(*) FROM categories WHERE slug = :slug AND id <> :id',
            [':slug' => $slug, ':id' => $exceptId ?? 0]
        );
    }

    public function create(string $name, string $slug): void
    {
        $this->run('INSERT INTO categories (name, slug) VALUES (:name, :slug)', [':name' => $name, ':slug' => $slug]);
    }

    public function update(int $id, string $name, string $slug): void
    {
        $this->run(
            'UPDATE categories SET name = :name, slug = :slug WHERE id = :id',
            [':name' => $name, ':slug' => $slug, ':id' => $id]
        );
    }

    public function delete(int $id): void
    {
        $this->run('DELETE FROM categories WHERE id = :id', [':id' => $id]);
    }

    public function count(): int
    {
        return (int) $this->fetchValue('SELECT COUNT(*) FROM categories');
    }
}
