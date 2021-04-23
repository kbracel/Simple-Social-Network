DROP TABLE IF EXISTS user_account;
CREATE TABLE user_account(
    user_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    user_password VARCHAR(255) NOT NULL,
    user_type ENUM('user', 'admin') NOT NULL,
    fname VARCHAR(30) NOT NULL,
    lname VARCHAR(30) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    mailing_address VARCHAR(500) NOT NULL,
    email_address VARCHAR(500) NOT NULL,
    birthday DATE NOT NULL,
    register_date DATE NOT NULL,
    gender VARCHAR(100),
    relationship_status VARCHAR(100),
    interested_in VARCHAR(100),
    activities TEXT,
    interests TEXT,
    about TEXT,
    quote TEXT,
    hometown VARCHAR(100),
    current_location VARCHAR(100),
    PRIMARY KEY (user_id),
    UNIQUE KEY(username, email_address)
);

DROP TABLE IF EXISTS user_post;
CREATE TABLE user_post (
    post_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    date_posted DATETIME NOT NULL,
    post_text TEXT NOT NULL,
    PRIMARY KEY (post_id),
    FOREIGN KEY (user_id) REFERENCES user_account(user_id)
);

DROP TABLE IF EXISTS connection;
CREATE TABLE connection(
    connection_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id1 INT UNSIGNED NOT NULL,
    user_id2 INT UNSIGNED NOT NULL,
    date_connected DATE NOT NULL,
    PRIMARY KEY (connection_id),
    FOREIGN KEY (user_id1) REFERENCES user_account(user_id),
    FOREIGN KEY (user_id2) REFERENCES user_account(user_id)
);

DROP TABLE IF EXISTS connection_request;
CREATE TABLE connection_request(
    request_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id_from INT UNSIGNED NOT NULL,
    user_id_to INT UNSIGNED NOT NULL,
    date_sent DATETIME NOT NULL,
    PRIMARY KEY (request_id),
    FOREIGN KEY (user_id_from) REFERENCES user_account(user_id),
    FOREIGN KEY (user_id_to) REFERENCES user_account(user_id)
);

DROP TABLE IF EXISTS user_page;
CREATE TABLE user_page(
    page_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    about TEXT,
    date_created DATE NOT NULL,
    PRIMARY KEY (page_id),
    FOREIGN KEY (user_id) REFERENCES user_account(user_id)
);

DROP TABLE IF EXISTS user_joins_page;
CREATE TABLE user_joins_page(
    join_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    page_id INT UNSIGNED NOT NULL,
    date_joined DATE NOT NULL,
    PRIMARY KEY (join_id),
    FOREIGN KEY (user_id) REFERENCES user_account(user_id),
    FOREIGN KEY (page_id) REFERENCES user_page(page_id)
);

DROP TABLE IF EXISTS page_post;
CREATE TABLE page_post(
    post_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    page_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    date_posted DATETIME NOT NULL,
    post_text TEXT NOT NULL,
    PRIMARY KEY (post_id),
    FOREIGN KEY (page_id) REFERENCES user_page(page_id),
    FOREIGN KEY (user_id) REFERENCES user_account(user_id)
);
