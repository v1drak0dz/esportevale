<?php
// Models/News.php

class News {
    private $db;
    private $getQuery;
    private $allowedTags;

    public function __construct() {
        $this->db = Database::getInstance(); // Certifique-se que sua classe Database tem esse método
        $this->getQuery = 'SELECT p.id as post_id, p.title as post_title, p.content as post_content, p.modified_at as post_modified, p.created_at as post_created, u.id as post_author_id, u.name as post_author_name FROM posts p, users u WHERE p.author = u.id';
        $this->allowedTags = '<p><a><b><strong><i><em><ul><ol><li><br><span><img><h1><h2><h3><h4><h5><h6>';
    }

    public function getNews() {
        $stmt = $this->db->prepare($this->getQuery . ' ORDER BY p.modified_at DESC');
        $stmt->execute();
        $items = array();
        while ($row = $stmt->fetchObject()) {
            array_push($items, $row);
        }
        return $this->convertAndCheck($items);
    }

    public function getNewsByUser($user_id) {
        $stmt = $this->db->prepare($this->getQuery . ' AND u.id = :user ORDER BY p.modified_at DESC');
        $stmt->bindParam(':user', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $items = array();
        while ($row = $stmt->fetchObject()) {
            array_push($items, $row);
        }
        return $this->convertAndCheck($items);
    }

    public function getNewsById($id) {
        $stmt = $this->db->prepare($this->getQuery . ' AND p.id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $this->convertAndCheck($stmt->fetchObject());
    }

    public function getNewsLimited($limit) {
        $stmt = $this->db->prepare($this->getQuery . ' ORDER BY p.created_at DESC LIMIT :limit');
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $items = array();
        while ($row = $stmt->fetchObject()) {
            array_push($items, $row);
        }
        return $this->convertAndCheck($items);
    }

    private function convertAndCheck($returnvalue) {
        if ($returnvalue) {
            return $returnvalue;
        } else {
            return null;
        }
    }

    public function createPost($title, $content, $author) {
        $stmt = $this->db->prepare('INSERT INTO posts (title, content, author, created_at) VALUES (:title, :content, :author, :created_at)');
        $filteredContent = strip_tags($content, $this->allowedTags);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':content', $filteredContent, PDO::PARAM_STR);
        $stmt->bindParam(':author', $author, PDO::PARAM_INT);
        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':created_at', $created_at, PDO::PARAM_STR);
        $stmt->execute();

        return $this->db->lastInsertId();
    }

    public function createTag($tag) {
        $stmt = $this->db->prepare('INSERT OR IGNORE INTO tags (nome) VALUES (:tag)');
        $stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
        $stmt->execute();

        $stmt = $this->db->prepare('SELECT id FROM tags WHERE nome = :tag');
        $stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function createPostTag($post_id, $tag_id) {
        $stmt = $this->db->prepare('INSERT INTO post_tags (post_id, tag_id) VALUES (:post_id, :tag_id)');
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->bindParam(':tag_id', $tag_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function addNews($title, $content, $tags, $author) {
        $postId = $this->createPost($title, $content, $author);

        foreach (explode(',', $tags) as $tag) {
            error_log('tag: ' . $tag);
            $tagId = $this->createTag(trim($tag));
            $this->createPostTag($postId, $tagId);
        }
    }

    public function getPostTags($id) {
        $stmt = $this->db->prepare("SELECT t.nome FROM tags t, post_tags pt WHERE t.id = pt.tag_id AND pt.post_id = :post_id");
        $stmt->bindParam(':post_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getNewsBySearch($query)
    {
        $query = "SELECT p.title, p.created_at, u.name FROM posts p, post_tags pt, tags t, users u WHERE u.id = p.author AND p.id = pt.post_id AND t.id = pt.tag_id";
        $query .= " p.title LIKE :query OR p.content LIKE :query OR t.nome LIKE :query";
        $query .= " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':query', $query, PDO::PARAM_STR);
        $stmt->execute();
        $items = array();
        while ($row = $stmt->fetchObject()) {
            array_push($items, $row);
        }
        return $this->convertAndCheck($items);
    }

    public function deleteNews($id) {
        $stmt = $this->db->prepare('DELETE FROM posts WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getNewsCommentary($id) {
        $stmt = $this->db->prepare("SELECT c.*, u.name as user_name FROM comments c, users u WHERE u.id = c.author AND post_id = :post_id ORDER BY created_at ASC");
        $stmt->execute(array(':post_id' => $id));
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $comments;
    }

    public function saveCommentary($id, $content, $author) {
        $stmt = $this->db->prepare('INSERT INTO comments (post_id, author, content) VALUES (:post_id, :author, :content)');
        $stmt->bindParam(':post_id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':author', $author, PDO::PARAM_INT);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function postLikeCount($id) {
        $stmt = $this->db->prepare('SELECT COUNT(id) FROM likes WHERE post_id = :post_id');
        $stmt->bindParam(':post_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function userLikedPost($user_id, $post_id) {
        $stmt = $this->db->prepare('SELECT 1 FROM likes WHERE user_id = :user_id AND post_id = :post_id');
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() !== false;
    }

    public function toggleLike($user_id, $post_id) {
        if ($this->userLikedPost($user_id, $post_id)) {
            $stmt = $this->db->prepare('DELETE FROM likes WHERE user_id = :user_id AND post_id = :post_id');
        }
        else {
            $stmt = $this->db->prepare('INSERT INTO likes (user_id, post_id) VALUES (:user_id, :post_id)');
        }
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
