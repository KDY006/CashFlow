<?php
class UserDTO {
    private $id;
    private $full_name;
    private $email;
    private $password_hash;

    public function __construct($full_name, $email, $password_hash, $id = null) {
        $this->full_name = $full_name;
        $this->email = $email;
        $this->password_hash = $password_hash;
        $this->id = $id;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getFullName() { return $this->full_name; }
    public function getEmail() { return $this->email; }
    public function getPasswordHash() { return $this->password_hash; }

    // Setters
    public function setId($id) { $this->id = $id; }
}
?>