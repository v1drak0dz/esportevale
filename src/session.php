<?php

class Session {
    private static $instance = null;

    // Construtor privado para impedir instância externa
    public function __construct() {
        if (session_id() == '') {
            session_start();
        }
    }

    // Singleton: retorna a única instância da classe
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Session();
        }
        return self::$instance;
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function setAlert($values = array()) {
        $this->set('alert', true);
        $this->set('alert_type', $values['type']);
        $this->set('alert_text', $values['text']);
    }

    public function getAlert() {
        return array(
            'alert' => $this->get('alert'),
            'alert_type' => $this->get('alert_type'),
            'alert_text' => $this->get('alert_text')
        );
    }

    public function getUserId() {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }

    public function removeAlert() {
        $this->remove('alert');
        $this->remove('alert_type');
        $this->remove('alert_text');
    }

    public function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function isAdmin() {
        return isset($_SESSION['user']) && $_SESSION['user'] == 'admin';
    }

    public function has($key) {
        return isset($_SESSION[$key]);
    }

    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public function destroy() {
        $_SESSION = array();
        if (session_id() != '' || isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        session_destroy();
    }
}
