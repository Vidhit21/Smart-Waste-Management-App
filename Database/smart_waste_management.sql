CREATE DATABASE IF NOT EXISTS smart_waste_management;
USE smart_waste_management;

CREATE TABLE Areas (
    area_id INT AUTO_INCREMENT PRIMARY KEY,
    area_name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE Streets (
    street_id INT AUTO_INCREMENT PRIMARY KEY,
    area_id INT NOT NULL,
    street_name VARCHAR(255) NOT NULL,
    sequence_number INT NOT NULL,
    CONSTRAINT fk_street_area FOREIGN KEY (area_id) REFERENCES Areas(area_id),
    CONSTRAINT unique_area_street UNIQUE (area_id, street_name)
);
--  1. Core Tables: Address and Users

--  Address table holds detailed location info.
CREATE TABLE Address (
    address_id INT AUTO_INCREMENT PRIMARY KEY,
    street_id INT NOT NULL,
    house_number VARCHAR(50),  -- e.g., house number or building name
    pincode VARCHAR(10),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    CONSTRAINT fk_address_street FOREIGN KEY (street_id) REFERENCES Streets(street_id)
);


-- Users table stores common authentication and profile info.
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15),
    password_hash VARCHAR(255) NOT NULL,
    user_type ENUM('Resident','Worker','Authority','Agency') NOT NULL,
    address_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_address FOREIGN KEY (address_id) REFERENCES Address(address_id)
);

-- 2. Role-Specific Tables

-- Residents table for additional resident-specific details.
CREATE TABLE Residents (
    resident_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    recycling_points INT DEFAULT 0,
    extra_details TEXT,
    CONSTRAINT fk_resident_user FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- Workers table for waste collection workers.
CREATE TABLE Workers (
    worker_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vehicle_info VARCHAR(100),
    work_history TEXT,
    CONSTRAINT fk_worker_user FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- Authorities table for municipal authority details.
CREATE TABLE Authorities (
    authority_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    department VARCHAR(100),
    CONSTRAINT fk_authority_user FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- Agencies table for environmental agencies.
CREATE TABLE Agencies (
    agency_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    organization_name VARCHAR(100),
    CONSTRAINT fk_agency_user FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- 3. Application Functionalities

-- Waste Collection Schedule by area (linked to Address).
CREATE TABLE WasteCollectionSchedules (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    street_id INT NOT NULL,
    collection_day ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    time_slot TIME NOT NULL,
    notes TEXT,
    CONSTRAINT fk_schedule_street FOREIGN KEY (street_id) REFERENCES Streets(street_id)
);

-- Reports table for residents reporting issues.
CREATE TABLE IssueReports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    resident_id INT NOT NULL,
    issue_type VARCHAR(100),
    description TEXT,
    status ENUM('Pending', 'In Progress', 'Resolved', 'Rejected') DEFAULT 'Pending',
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_report_resident FOREIGN KEY (resident_id) REFERENCES Residents(resident_id)
);

-- Bulk Pickup Requests for residents needing extra collection.
CREATE TABLE BulkPickupRequests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    resident_id INT NOT NULL,
    request_details TEXT,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bulk_request_resident FOREIGN KEY (resident_id) REFERENCES Residents(resident_id)
);

-- Recycling Rewards: tracking incentive points.
CREATE TABLE RecyclingRewards (
    reward_id INT AUTO_INCREMENT PRIMARY KEY,
    resident_id INT NOT NULL,
    points_earned INT,
    reward_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description TEXT,
    CONSTRAINT fk_reward_resident FOREIGN KEY (resident_id) REFERENCES Residents(resident_id)
);

-- Notifications table for all users.
CREATE TABLE Notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notification_user FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- 4. Waste Collection Worker Functionalities

-- Collection Tasks: tasks assigned to workers.
CREATE TABLE CollectionTasks (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    worker_id INT NOT NULL,
    address_id INT,  -- Location for the task.
    scheduled_date DATE,
    scheduled_time TIME,
    status ENUM('Assigned','Completed','Delayed','Missed') DEFAULT 'Assigned',
    remarks TEXT,
    CONSTRAINT fk_task_worker FOREIGN KEY (worker_id) REFERENCES Workers(worker_id),
    CONSTRAINT fk_task_address FOREIGN KEY (address_id) REFERENCES Address(address_id)
);

-- Status Reports: workers update the task status.
CREATE TABLE CollectionStatusReports (
    status_report_id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    updated_status ENUM('Collected','Delayed','Missed') DEFAULT 'Collected',
    report_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    remarks TEXT,
    CONSTRAINT fk_status_task FOREIGN KEY (task_id) REFERENCES CollectionTasks(task_id)
);

-- Feedback: residents provide ratings for worker performance.
CREATE TABLE WorkerFeedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    worker_id INT NOT NULL,
    resident_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comments TEXT,
    feedback_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_feedback_worker FOREIGN KEY (worker_id) REFERENCES Workers(worker_id),
    CONSTRAINT fk_feedback_resident FOREIGN KEY (resident_id) REFERENCES Residents(resident_id)
);

-- 5. Municipal Authority Functionalities

-- Task Assignments: authorities assign tasks to workers.
CREATE TABLE TaskAssignments (
    assignment_id INT AUTO_INCREMENT PRIMARY KEY,
    authority_id INT NOT NULL,
    worker_id INT NOT NULL,
    address_id INT,  -- Assignment location.
    assigned_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    details TEXT,
    CONSTRAINT fk_assignment_authority FOREIGN KEY (authority_id) REFERENCES Authorities(authority_id),
    CONSTRAINT fk_assignment_worker FOREIGN KEY (worker_id) REFERENCES Workers(worker_id),
    CONSTRAINT fk_assignment_address FOREIGN KEY (address_id) REFERENCES Address(address_id)
);

-- System Settings: global configurations and policies.
CREATE TABLE SystemSettings (
    setting_id INT AUTO_INCREMENT PRIMARY KEY,
    setting_name VARCHAR(100) UNIQUE NOT NULL,
    setting_value VARCHAR(255) NOT NULL
);

-- 6. Environmental Agency Functionalities

-- Environmental Reports: detailed waste and recycling data.
CREATE TABLE EnvironmentalReports (
    env_report_id INT AUTO_INCREMENT PRIMARY KEY,
    agency_id INT NOT NULL,
    report_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    report_data TEXT,
    CONSTRAINT fk_env_report_agency FOREIGN KEY (agency_id) REFERENCES Agencies(agency_id)
);

-- User Behavior Analytics: aggregated usage and behavior data.
CREATE TABLE UserBehaviorAnalytics (
    analytics_id INT AUTO_INCREMENT PRIMARY KEY,
    period_start DATE,
    period_end DATE,
    data_summary TEXT
);

-- Policy Recommendations: suggestions submitted by agencies.
CREATE TABLE PolicyRecommendations (
    recommendation_id INT AUTO_INCREMENT PRIMARY KEY,
    agency_id INT NOT NULL,
    recommendation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    recommendation TEXT,
    status ENUM('Pending','Accepted','Rejected') DEFAULT 'Pending',
    CONSTRAINT fk_policy_agency FOREIGN KEY (agency_id) REFERENCES Agencies(agency_id)
);

-- 7. Additional Data Tables (from provided design)

-- WasteData: records of waste collection by type and weight.
CREATE TABLE WasteData (
    waste_id INT AUTO_INCREMENT PRIMARY KEY,
    address_id INT,
    waste_date DATE,
    waste_type ENUM('Organic', 'Plastic', 'Glass', 'E-Waste', 'General'),
    weight_kg DECIMAL(5,2),
    CONSTRAINT fk_waste_address FOREIGN KEY (address_id) REFERENCES Address(address_id)
);

-- WasteCollectionRoute: optimized routes for waste collectors.
-- (location_ids can be stored as JSON if supported or as a comma-separated list)
CREATE TABLE WasteCollectionRoute (
    route_id INT AUTO_INCREMENT PRIMARY KEY,
    worker_id INT,
    location_ids TEXT,
    optimized_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_route_worker FOREIGN KEY (worker_id) REFERENCES Workers(worker_id)
);


