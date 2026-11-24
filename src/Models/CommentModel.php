<?php

namespace App\Models;

use App\Core\Model;

class CommentModel extends Model
{
    public function approvedForArticle(int $articleId): array
    {
        return $this->fetchAll(
            "SELECT c.*, u.pseudo
             FROM comments c
             JOIN users u ON u.id = c.user_id
             WHERE c.article_id = :id AND c.status = 'approved'
             ORDER BY c.created_at ASC",
            [':id' => $articleId]
        );
    }

    public function create(int $articleId, int $userId, string $content, string $status): void
    {
        $this->run(
            'INSERT INTO comments (article_id, user_id, content, status) VALUES (:a, :u, :c, :s)',
            [':a' => $articleId, ':u' => $userId, ':c' => $content, ':s' => $status]
        );
    }

    public function all(?string $status = null): array
    {
        $sql = 'SELECT c.*, u.pseudo, a.title AS article_title, a.slug AS article_slug
                FROM comments c
                JOIN users u ON u.id = c.user_id
                JOIN articles a ON a.id = c.article_id';
        $params = [];
        if ($status !== null) {
            $sql .= ' WHERE c.status = :status';
            $params[':status'] = $status;
        }
        return $this->fetchAll($sql . ' ORDER BY c.created_at DESC', $params);
    }

    public function setStatus(int $id, string $status): void
    {
        $this->run('UPDATE comments SET status = :s WHERE id = :id', [':s' => $status, ':id' => $id]);
    }

    public function delete(int $id): void
    {
        $this->run('DELETE FROM comments WHERE id = :id', [':id' => $id]);
    }

    public function count(): int
    {
        return (int) $this->fetchValue('SELECT COUNT(*) FROM comments');
    }

    public function countPending(): int
    {
        return (int) $this->fetchValue("SELECT COUNT(*) FROM comments WHERE status = 'pending'");
    }
}
