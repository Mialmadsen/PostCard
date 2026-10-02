DROP DATABASE IF EXISTS postcard;
CREATE DATABASE postcard;
USE postcard;

-- User table
CREATE TABLE users (
  user_id INT NOT NULL AUTO_INCREMENT,
  last_name VARCHAR(100),
  first_name VARCHAR(100),
  username VARCHAR(50) UNIQUE,
  password_hash VARCHAR(255),
  profile_image VARCHAR(255),
  bio TEXT,
  email VARCHAR(255) UNIQUE,
  birthdate DATE,
  user_status ENUM('active', 'blocked', 'deleted'),
  is_operator TINYINT(1),
  email_verified_at DATETIME NULL,
  created_at DATETIME,
  PRIMARY KEY (user_id)
);

-- User groups table
CREATE TABLE user_groups (
  group_id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(100),
  description TEXT,
  posting_mode ENUM('open', 'permission'),
  default_post_limit INT,
  style VARCHAR(30),
  created_at DATETIME,
  PRIMARY KEY (group_id)
);

-- Group members connection table
CREATE TABLE group_members (
  user_id INT NOT NULL,
  group_id INT NOT NULL,
  member_role ENUM('member', 'group_admin'),
  member_status ENUM('active', 'left', 'removed'),
  joined_at DATETIME,
  PRIMARY KEY (user_id, group_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (group_id) REFERENCES user_groups(group_id)
);

-- Experiences table
CREATE TABLE experiences (
  experience_id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(100),
  description TEXT,
  start_date DATE,
  end_date DATE,
  group_id INT,
  user_id INT,
  experience_status ENUM('pending', 'approved', 'rejected', 'closed'),
  post_limit INT,
  created_at DATETIME,
  PRIMARY KEY (experience_id),
  FOREIGN KEY (group_id) REFERENCES user_groups(group_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Posts table
CREATE TABLE posts (
  post_id INT NOT NULL AUTO_INCREMENT,
  caption TEXT,
  location VARCHAR(150),
  created_at DATETIME,
  experience_id INT,
  is_sticky TINYINT(1),
  PRIMARY KEY (post_id),
  FOREIGN KEY (experience_id) REFERENCES experiences(experience_id)
);

-- Post images table
CREATE TABLE post_images (
  image_id INT NOT NULL AUTO_INCREMENT,
  file_path VARCHAR(255),
  post_id INT,
  alt_text VARCHAR(255),
  sort_order TINYINT,
  PRIMARY KEY (image_id),
  FOREIGN KEY (post_id) REFERENCES posts(post_id)
);

-- Comments table
CREATE TABLE comments (
  comment_id INT NOT NULL AUTO_INCREMENT,
  created_at DATETIME,
  body TEXT,
  post_id INT,
  user_id INT,
  parent_comment_id INT NULL,
  PRIMARY KEY (comment_id),
  FOREIGN KEY (post_id) REFERENCES posts(post_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (parent_comment_id) REFERENCES comments(comment_id)
);

-- Likes table
CREATE TABLE likes (
  user_id INT NOT NULL,
  post_id INT NOT NULL,
  created_at DATETIME,
  PRIMARY KEY (user_id, post_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (post_id) REFERENCES posts(post_id)
);

-- Post views table
CREATE TABLE post_views (
  user_id INT NOT NULL,
  post_id INT NOT NULL,
  viewed_at DATETIME,
  PRIMARY KEY (user_id, post_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (post_id) REFERENCES posts(post_id)
);

-- User tokens table
CREATE TABLE user_tokens (
  token_id INT NOT NULL AUTO_INCREMENT,
  user_id INT,
  token_hash CHAR(64) UNIQUE,
  token_type ENUM('verify_email', 'reset_password'),
  expires_at DATETIME,
  used_at DATETIME NULL,
  PRIMARY KEY (token_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Invitations table
CREATE TABLE invitations (
  invitation_id INT NOT NULL AUTO_INCREMENT,
  group_id INT,
  created_by INT,
  token_hash CHAR(64) UNIQUE,
  expires_at DATETIME,
  used_at DATETIME NULL,
  created_at DATETIME,
  PRIMARY KEY (invitation_id),
  FOREIGN KEY (group_id) REFERENCES user_groups(group_id),
  FOREIGN KEY (created_by) REFERENCES users(user_id)
);

--  Site content table
CREATE TABLE site_content (
  content_key VARCHAR(50),
  title VARCHAR(150),
  body TEXT,
  updated_by INT,
  updated_at DATETIME,
  PRIMARY KEY (content_key),
  FOREIGN KEY (updated_by) REFERENCES users(user_id)
);

-- Site settings table
CREATE TABLE site_settings (
  setting_key VARCHAR(50),
  setting_value VARCHAR(255),
  updated_at DATETIME,
  PRIMARY KEY (setting_key)
);