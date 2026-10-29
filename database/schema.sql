-- DROP DATABASE IF EXISTS postcard;
-- utf8mb4 stores every character (æøå, Ukrainian letters, emoji);
-- utf8mb4_danish_ci sorts the Danish way: æ, ø, å come after z.
-- CREATE DATABASE postcard CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci;
-- USE postcard;

-- Rules used in every table:
-- NOT NULL  = the value is required. Only optional fields may be NULL.
-- DEFAULT   = the value MySQL uses when the INSERT leaves the column out.
-- ON DELETE CASCADE = deleting the parent row also deletes these rows
--   (used for everything that belongs to a post or an experience).
--   Users and groups are never deleted, only given a status, so their
--   foreign keys keep the default rule: a delete is blocked while rows point to them.

-- User table
CREATE TABLE users (
  user_id INT NOT NULL AUTO_INCREMENT,
  last_name VARCHAR(100) NOT NULL,
  first_name VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  profile_image VARCHAR(255) NULL,
  bio TEXT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  birthdate DATE NOT NULL,
  user_status ENUM('active', 'blocked', 'deleted') NOT NULL DEFAULT 'active',
  is_operator TINYINT(1) NOT NULL DEFAULT 0,
  email_verified_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id)
);

-- User groups table
CREATE TABLE user_groups (
  group_id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  description TEXT NULL,
  posting_mode ENUM('open', 'permission') NOT NULL DEFAULT 'open',
  default_post_limit INT NOT NULL DEFAULT 20,
  style VARCHAR(30) NOT NULL DEFAULT 'postcard',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (group_id)
);

-- Group members connection table
CREATE TABLE group_members (
  user_id INT NOT NULL,
  group_id INT NOT NULL,
  member_role ENUM('member', 'group_admin') NOT NULL DEFAULT 'member',
  member_status ENUM('active', 'left', 'removed') NOT NULL DEFAULT 'active',
  joined_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, group_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (group_id) REFERENCES user_groups(group_id)
);

-- Experiences table
CREATE TABLE experiences (
  experience_id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  description TEXT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  group_id INT NOT NULL,
  user_id INT NOT NULL,
  experience_status ENUM('pending', 'approved', 'rejected', 'closed') NOT NULL DEFAULT 'pending',
  post_limit INT NOT NULL DEFAULT 20,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (experience_id),
  FOREIGN KEY (group_id) REFERENCES user_groups(group_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Posts table
CREATE TABLE posts (
  post_id INT NOT NULL AUTO_INCREMENT,
  caption TEXT NULL,
  location VARCHAR(150) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  experience_id INT NOT NULL,
  is_sticky TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (post_id),
  FOREIGN KEY (experience_id) REFERENCES experiences(experience_id) ON DELETE CASCADE
);

-- Post images table
-- The photos are stored on Cloudinary. image_url is the address of the photo;
-- public_id is Cloudinary's name for it, needed to delete the photo there.
-- (Deleting a post removes these rows; PHP must delete the files on Cloudinary.)
CREATE TABLE post_images (
  image_id INT NOT NULL AUTO_INCREMENT,
  image_url VARCHAR(255) NOT NULL,
  public_id VARCHAR(255) NULL,
  post_id INT NOT NULL,
  alt_text VARCHAR(255) NULL,
  sort_order TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (image_id),
  FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE
);

-- Comments table
CREATE TABLE comments (
  comment_id INT NOT NULL AUTO_INCREMENT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  body TEXT NOT NULL,
  post_id INT NOT NULL,
  user_id INT NOT NULL,
  parent_comment_id INT NULL,
  PRIMARY KEY (comment_id),
  FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (parent_comment_id) REFERENCES comments(comment_id) ON DELETE CASCADE
);

-- Likes table
CREATE TABLE likes (
  user_id INT NOT NULL,
  post_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, post_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE
);

-- Post views table
CREATE TABLE post_views (
  user_id INT NOT NULL,
  post_id INT NOT NULL,
  viewed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, post_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE
);

-- User tokens table
CREATE TABLE user_tokens (
  token_id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  token_hash CHAR(64) NOT NULL UNIQUE,
  token_type ENUM('verify_email', 'reset_password') NOT NULL,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  PRIMARY KEY (token_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Invitations table
CREATE TABLE invitations (
  invitation_id INT NOT NULL AUTO_INCREMENT,
  group_id INT NOT NULL,
  created_by INT NOT NULL,
  token_hash CHAR(64) NOT NULL UNIQUE,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (invitation_id),
  FOREIGN KEY (group_id) REFERENCES user_groups(group_id),
  FOREIGN KEY (created_by) REFERENCES users(user_id)
);

--  Site content table
CREATE TABLE site_content (
  content_key VARCHAR(50) NOT NULL,
  title VARCHAR(150) NOT NULL,
  body TEXT NOT NULL,
  updated_by INT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (content_key),
  FOREIGN KEY (updated_by) REFERENCES users(user_id)
);

-- Site settings table
CREATE TABLE site_settings (
  setting_key VARCHAR(50) NOT NULL,
  setting_value VARCHAR(255) NOT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (setting_key)
);

-- Indexes
CREATE INDEX idx_group_members_group_status ON group_members (group_id, member_status);

-- Triggers
DELIMITER //

CREATE TRIGGER trg_group_members_keep_admin
BEFORE UPDATE ON group_members
FOR EACH ROW
BEGIN
    IF OLD.member_role = 'group_admin' AND OLD.member_status = 'active'
       AND (NEW.member_role <> 'group_admin' OR NEW.member_status <> 'active')
       AND (SELECT COUNT(*)
              FROM group_members
             WHERE group_id = OLD.group_id
               AND member_role = 'group_admin'
               AND member_status = 'active'
               AND user_id <> OLD.user_id) = 0
    THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Gruppen skal have mindst én admin.';
    END IF;
END//

DELIMITER ;
