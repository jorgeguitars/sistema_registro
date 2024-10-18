CREATE TABLE posts (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    userid INT(11) UNSIGNED NOT NULL,
    categoryid INT(11) UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userid) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (categoryid) REFERENCES categories(id) ON DELETE CASCADE
);
