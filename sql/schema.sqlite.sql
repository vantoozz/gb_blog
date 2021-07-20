CREATE TABLE users (
    uuid          VARCHAR(36)
        CONSTRAINT users_id_pk
            PRIMARY KEY,
    first_name    VARCHAR(50) NOT NULL,
    last_name     VARCHAR(50) NOT NULL,
    username      VARCHAR(50) NOT NULL,
    password_hash VARCHAR(64) NOT NULL,
    password_salt VARCHAR(40) NOT NULL,
    created_at    VARCHAR(19) NOT NULL,
    updated_at    VARCHAR(19) NOT NULL
);

CREATE UNIQUE INDEX users_username_uindex
    ON users(username);

CREATE TABLE posts (
    uuid        VARCHAR(36)
        CONSTRAINT posts_id_pk
            PRIMARY KEY,
    author_uuid VARCHAR(36),
    title       VARCHAR(200) NOT NULL,
    text        TEXT         NOT NULL,
    created_at  VARCHAR(19)  NOT NULL,
    updated_at  VARCHAR(19)  NOT NULL
);

CREATE INDEX posts_author_uuid_index
    ON posts(author_uuid);
