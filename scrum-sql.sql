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
ADD COLUMN sprint_id INT DEFAULT NULL,
ADD COLUMN estimated_hours DECIMAL DEFAULT '0' NULL,
ADD COLUMN closing_summary TEXT NULL;

ALTER TABLE tblprojects
ADD COLUMN project_goals TEXT NULL;


-- ALTER COMMAND IF closing_summary isnt added, Copy and paste
-- alter table tblsprints add closing_summary TEXT NULL;


CREATE TABLE tblprojecttemplates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name TEXT,
    epics_and_stories JSON,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

alter table tblfiles add column description text null;

alter table tblprojects add column init_report text null;
alter table tblprojects add column final_report text null;
alter table tblproject_files add column story_id int null;

--
ALTER TABLE tblstaff ADD date_of_birth DATE;
ALTER TABLE tblstaff ADD Address TEXT;
ALTER TABLE tblstaff ADD staff_salary INT;
ALTER TABLE tblstaff ADD staff_title VARCHAR(255);
ALTER TABLE tblstaff ADD report_to INT DEFAULT 1;

ALTER TABLE tblstaff ADD gender VARCHAR(10) ;
ALTER TABLE tblstaff ADD marital_status VARCHAR(50);
ALTER TABLE tblstaff ADD national_identity VARCHAR(30) ;
ALTER TABLE tblstaff ADD emergency_contact_name VARCHAR(255) NULL;
ALTER TABLE tblstaff ADD emergency_contact_number VARCHAR(50) NULL;
ALTER TABLE tblstaff ADD personal_email_address VARCHAR(255) NULL;
ALTER TABLE tblstaff ADD  bank_name VARCHAR(255) NULL;
ALTER TABLE tblstaff ADD  bank_acc_no VARCHAR(50) NULL;
ALTER TABLE tblstaff ADD next_of_kin VARCHAR(255) NULL;
ALTER TABLE tblstaff ADD google_chat_id VARCHAR(255) NULL;


CREATE TABLE tblkudos (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    type ENUM('advice', 'kudos') NOT NULL,
    to_ VARCHAR(255) NOT NULL,
    principles TEXT NOT NULL,
    remarks TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    staff_id INT(11) NOT NULL,
    kudos_like TEXT NOT NULL,
    seen_by TEXT NULL
);

ALTER TABLE tblnewsfeed_posts ADD seen_by TEXT;

ALTER TABLE tblstaff MODIFY report_to INT DEFAULT NULL;
