<?php

namespace App\Models;

use App\Core\Model;

class ArticleModel extends Model
{
    private const SELECT = 'SELECT a.*, u.pseudo AS author, c.name AS category_name, c.slug AS category_slug
                            FROM articles a
                            JOIN users u ON u.id = a.author_id
                            LEFT JOIN categories c ON c.id = a.category_id';

    // Liste paginee des articles publies, avec filtre optionnel par categorie et recherche
    public function paginatePublished(int $page, int $perPage, ?string $categorySlug = null, ?string $search = null): array
    {
        $where = " WHERE a.status = 'published'";
        $params = [];

        if ($categorySlug !== null && $categorySlug !== '') {
            $where .= ' AND c.slug = :cat';
            $params[':cat'] = $categorySlug;
        }
        if ($search !== null && $search !== '') {
            $like = '%' . addcslashes($search, '%_\\') . '%';
            $where .= ' AND (a.title LIKE :q1 OR a.content LIKE :q2)';
            $params[':q1'] = $like;
            $params[':q2'] = $like;
        }

        $total = (int) $this->fetchValue(
            'SELECT COUNT(*) FROM articles a LEFT JOIN categories c ON c.id = a.category_id' . $where,
            $params
        );

        $params[':limit'] = $perPage;
        $params[':offset'] = ($page - 1) * $perPage;
        $items = $this->fetchAll(
            self::SELECT . $where . ' ORDER BY a.published_at DESC, a.id DESC LIMIT :limit OFFSET :offset',
            $params
        );

        return ['items' => $items, 'total' => $total];
    }

    public function findPublishedBySlug(string $slug): ?array
    {
        return $this->fetchOne(self::SELECT . " WHERE a.slug = :slug AND a.status = 'published'", [':slug' => $slug]);
    }

    public function findById(int $id): ?array
    {
        return $this->fetchOne(self::SELECT . ' WHERE a.id = :id', [':id' => $id]);
    }

    public function all(?int $authorId = null): array
    {
        if ($authorId !== null) {
            return $this->fetchAll(self::SELECT . ' WHERE a.author_id = :author ORDER BY a.created_at DESC', [':author' => $authorId]);
        }
        return $this->fetchAll(self::SELECT . ' ORDER BY a.created_at DESC');
    }

    public function slugExists(string $slug, ?int $exceptId = null): bool
    {
        return (bool) $this->fetchValue(
            'SELECT COUNT(*) FROM articles WHERE slug = :slug AND id <> :id',
            [':slug' => $slug, ':id' => $exceptId ?? 0]
        );
    }

    public function create(array $d): int
    {
        $this->run(
            'INSERT INTO articles (title, slug, content, cover_image, status, category_id, author_id, published_at)
             VALUES (:title, :slug, :content, :cover, :status, :cat, :author, :published)',
            [
                ':title'     => $d['title'],
                ':slug'      => $d['slug'],
                ':content'   => $d['content'],
                ':cover'     => $d['cover_image'],
                ':status'    => $d['status'],
                ':cat'       => $d['category_id'],
                ':author'    => $d['author_id'],
                ':published' => $d['status'] === 'published' ? date('Y-m-d H:i:s') : null,
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d): void
    {
        // published_at est renseignee seulement la premiere fois que l'article passe en "publie"
        $this->run(
            'UPDATE articles
             SET title = :title, slug = :slug, content = :content, cover_image = :cover,
                 status = :status, category_id = :cat,
                 published_at = CASE
                     WHEN :status2 = \'published\' AND published_at IS NULL THEN NOW()
                     ELSE published_at
                 END
             WHERE id = :id',
            [
                ':title'   => $d['title'],
                ':slug'    => $d['slug'],
                ':content' => $d['content'],
                ':cover'   => $d['cover_image'],
                ':status'  => $d['status'],
                ':status2' => $d['status'],
                ':cat'     => $d['category_id'],
                ':id'      => $id,
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->run('DELETE FROM articles WHERE id = :id', [':id' => $id]);
    }

    public function count(): int
    {
        return (int) $this->fetchValue('SELECT COUNT(*) FROM articles');
    }

    public function countPublished(): int
    {
        return (int) $this->fetchValue("SELECT COUNT(*) FROM articles WHERE status = 'published'");
    }
}
