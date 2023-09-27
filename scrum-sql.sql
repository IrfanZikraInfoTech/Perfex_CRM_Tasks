-- Epics Table
CREATE TABLE tblepics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE tblsprints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    project_id INT NOT NULL,
    status INT NOT NULL DEFAULT 0,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    date_started DATE NULL,
    date_ended DATE NULL,
    closing_summary TEXT NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NULL
);


-- Altering tblTasks Table
ALTER TABLE tbltasks
ADD COLUMN epic_id INT DEFAULT NULL,
ADD COLUMN sprint_id INT DEFAULT NULL;

-- ALTER COMMAND IF closing_summary isnt added, Copy and paste
-- alter table tblsprints add closing_summary TEXT NULL;
