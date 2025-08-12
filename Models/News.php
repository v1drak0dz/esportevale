<?php
// Models/News.php

class News {
    private $db;
    private $getQuery;
    private $allowedTags;

    public function __construct() {
        $this->db = Database::getInstance(); // mysqli connection
        $this->getQuery = 'SELECT p.capa as post_capa, p.id as post_id, p.title as post_title, p.content as post_content, p.modified_at as post_modified, p.created_at as post_created, u.id as post_author_id, u.name as post_author_name FROM posts p, users u WHERE p.author = u.id';
        $this->allowedTags = '<p><a><b><strong><i><em><ul><ol><li><br><span><img><h1><h2><h3><h4><h5><h6>';
    }

    public function getNews() {
        $query = $this->getQuery . ' ORDER BY p.modified_at DESC';
        $result = $this->db->query($query);
        $items = [];
        while ($row = $result->fetch_object()) {
            $items[] = $row;
        }
        return $this->convertAndCheck($items);
    }

    public function getNewsByUser($user_id) {
        $query = $this->getQuery . ' AND u.id = ? ORDER BY p.modified_at DESC';
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_object()) {
            $items[] = $row;
        }
        $stmt->close();
        return $this->convertAndCheck($items);
    }

    public function getNewsById($id) {
        $query = $this->getQuery . ' AND p.id = ?';
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_object();
        $stmt->close();
        return $this->convertAndCheck($item);
    }

    public function getNewsLimited($limit) {
        $query = $this->getQuery . ' ORDER BY p.created_at DESC LIMIT ?';
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_object()) {
            $items[] = $row;
        }
        $stmt->close();
        return $this->convertAndCheck($items);
    }

    private function convertAndCheck($returnvalue) {
        return $returnvalue ?: null;
    }

    public function createPost($title, $content, $author, $capa) {
        $query = 'INSERT INTO posts (title, content, author, created_at, capa) VALUES (?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($query);
        $filteredContent = strip_tags($content, $this->allowedTags);
        $created_at = date('Y-m-d H:i:s');
        $stmt->bind_param('ssiss', $title, $filteredContent, $author, $created_at, $capa);
        $stmt->execute();
        $insertId = $stmt->insert_id;
        $stmt->close();
        return $insertId;
    }

    public function createTag($tag) {
        // Insert or ignore pattern: MySQL doesn't have INSERT OR IGNORE, so use INSERT IGNORE
        $query = 'INSERT IGNORE INTO tags (nome) VALUES (?)';
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $tag);
        $stmt->execute();
        $stmt->close();

        $query = 'SELECT id FROM tags WHERE nome = ?';
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $tag);
        $stmt->execute();
        $result = $stmt->get_result();
        $tagId = null;
        if ($row = $result->fetch_assoc()) {
            $tagId = $row['id'];
        }
        $stmt->close();
        return $tagId;
    }

    public function createPostTag($post_id, $tag_id) {
        $query = 'INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)';
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $post_id, $tag_id);
        $stmt->execute();
        $stmt->close();
    }

    public function addNews($title, $content, $tags, $author, $capa) {
        $postId = $this->createPost($title, $content, $author, $capa);
        foreach (explode(',', $tags) as $tag) {
            error_log('tag: ' . $tag);
            $tagId = $this->createTag(trim($tag));
            $this->createPostTag($postId, $tagId);
        }
    }

    public function getPostTags($id) {
        $query = "SELECT t.nome FROM tags t, post_tags pt WHERE t.id = pt.tag_id AND pt.post_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $tags = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $tags;
    }

    public function getNewsBySearch($searchTerm)
    {
        $searchTerm = "%{$searchTerm}%";
        $query = "SELECT p.title, p.created_at, u.name FROM posts p, post_tags pt, tags t, users u WHERE u.id = p.author AND p.id = pt.post_id AND t.id = pt.tag_id AND (p.title LIKE ? OR p.content LIKE ? OR t.nome LIKE ?) ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_object()) {
            $items[] = $row;
        }
        $stmt->close();
        return $this->convertAndCheck($items);
    }

    public function deletePost($id)
    {
        $query = 'DELETE FROM posts WHERE id = ?';
        $stmt = $this->db->prepare($query);

        if ($stmt === false) {
            throw new Exception('Erro ao preparar a query: ' . $this->db->error);
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            // Nenhum post deletado (ID não encontrado?)
            error_log("Nenhum post deletado com id = $id");
        }

        $stmt->close();
    }


    public function getNewsCommentary($id) {
        $query = "SELECT c.*, u.name as user_name FROM comments c, users u WHERE u.id = c.author AND post_id = ? ORDER BY created_at ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $comments = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $comments;
    }

    public function saveCommentary($id, $content, $author) {
        $query = 'INSERT INTO comments (post_id, author, content) VALUES (?, ?, ?)';
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('iis', $id, $author, $content);
        $stmt->execute();
        $stmt->close();
    }

    public function postLikeCount($id) {
        $query = 'SELECT COUNT(id) as cnt FROM likes WHERE post_id = ?';
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = 0;
        if ($row = $result->fetch_assoc()) {
            $count = $row['cnt'];
        }
        $stmt->close();
        return $count;
    }

    public function userLikedPost($user_id, $post_id) {
        $query = 'SELECT 1 FROM likes WHERE user_id = ? AND post_id = ? LIMIT 1';
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $user_id, $post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $liked = $result->num_rows > 0;
        $stmt->close();
        return $liked;
    }

    public function toggleLike($user_id, $post_id) {
        if ($this->userLikedPost($user_id, $post_id)) {
            $query = 'DELETE FROM likes WHERE user_id = ? AND post_id = ?';
        } else {
            $query = 'INSERT INTO likes (user_id, post_id) VALUES (?, ?)';
        }
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $user_id, $post_id);
        $stmt->execute();
        $stmt->close();
    }

    public function getVideos() {
        $query = "SELECT v.id as video_id, v.capa as video_capa, v.category as video_category, v.title as video_title, v.url as video_url, u.name as video_author, v.created_at as video_created_at FROM videos v, users u WHERE v.author = u.id ORDER BY v.created_at DESC";
        $result = $this->db->query($query);
        $items = [];
        while ($row = $result->fetch_object()) {
            $items[] = $row;
        }
        return $this->convertAndCheck($items);
    }

    public function getVideosLimited($limit) {
        $query = "SELECT v.id as video_id, v.capa as video_capa, v.category as video_category, v.title as video_title, v.url as video_url, u.name as video_author, v.created_at as video_created_at FROM videos v, users u WHERE v.author = u.id ORDER BY v.created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_object()) {
            $items[] = $row;
        }
        $stmt->close();
        return $this->convertAndCheck($items);
    }

    public function deleteVideos($id) {
        $query = 'DELETE FROM videos WHERE id = ?';
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }

    public function getVideoById($id) {
        $query = 'SELECT v.id as video_id, v.capa as video_capa, v.category as video_category, v.title as video_title, v.url as video_url, u.name as video_author, v.created_at as video_created_at FROM videos v, users u WHERE u.id = v.author AND v.id = ?';
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $video = $result->fetch_object();
        $stmt->close();
        return $video;
    }

    public function saveVideo($id, $title, $url, $author, $category, $capa) {
        if ($id != null) {
            $query = 'UPDATE videos SET title = ?, url = ?, author = ?, category = ?, capa = ? WHERE id = ?';
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ssisii', $title, $url, $author, $category, $capa, $id);
        } else {
            $query = 'INSERT INTO videos (title, url, author, category, capa) VALUES (?, ?, ?, ?, ?)';
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ssiss', $title, $url, $author, $category, $capa);
        }
        $stmt->execute();
        $stmt->close();
    }

    public function getVideosByQuery($searchTerm) {
        $searchTerm = "%{$searchTerm}%";
        $query = "SELECT v.capa as video_capa, v.category as video_category, v.title as video_title, v.url as video_url, u.name as video_author, v.created_at as video_created_at FROM videos v, users u WHERE v.author = u.id AND (v.title LIKE ? OR v.category LIKE ?) ORDER BY v.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ss', $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_object()) {
            $items[] = $row;
        }
        $stmt->close();
        return $this->convertAndCheck($items);
    }
}
