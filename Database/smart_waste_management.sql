CREATE DATABASE smart_waste_management;
USE smart_waste_management;


-- Table: Address
CREATE TABLE Address (
    address_id INT AUTO_INCREMENT PRIMARY KEY,
    division VARCHAR(100),
    street VARCHAR(255),
    pincode VARCHAR(10),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8)
);

-- Table: User
CREATE TABLE User (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(15),
    password VARCHAR(255) NOT NULL,
    user_type ENUM('Resident','Collector','Authority','Agency') NOT NULL,
    address_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_address FOREIGN KEY (address_id) REFERENCES Address(address_id)
);

-- Table: Task
CREATE TABLE Task (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    collector_id INT,
    location_id INT,
    task_date DATE,
    status ENUM('Pending', 'In Progress', 'Completed', 'Missed'),
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_task_collector FOREIGN KEY (collector_id) REFERENCES User(user_id),
    CONSTRAINT fk_task_location FOREIGN KEY (location_id) REFERENCES Address(address_id)
);

-- Table: Schedule
CREATE TABLE Schedule (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    location_id INT,
    collection_day ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
    time_slot TIME,
    CONSTRAINT fk_schedule_location FOREIGN KEY (location_id) REFERENCES Address(address_id)
);

-- Table: Report
CREATE TABLE Report (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    issue_type VARCHAR(255),
    description TEXT,
    status ENUM('Pending', 'Resolved', 'Rejected'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_report_user FOREIGN KEY (user_id) REFERENCES User(user_id)
);

-- Table: Feedback
CREATE TABLE Feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_feedback_user FOREIGN KEY (user_id) REFERENCES User(user_id)
);

-- Table: Reward
CREATE TABLE Reward (
    reward_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    points INT,
    earned_date DATE,
    redeemed BOOLEAN DEFAULT FALSE,
    CONSTRAINT fk_reward_user FOREIGN KEY (user_id) REFERENCES User(user_id)
);

-- Table: WasteData
CREATE TABLE WasteData (
    waste_id INT AUTO_INCREMENT PRIMARY KEY,
    location_id INT,
    date DATE,
    waste_type ENUM('Organic', 'Plastic', 'Glass', 'E-Waste', 'General'),
    weight_kg DECIMAL(5,2),
    CONSTRAINT fk_waste_data_location FOREIGN KEY (location_id) REFERENCES Address(address_id)
);

-- Table: WasteCollectionRoute
CREATE TABLE WasteCollectionRoute (
    route_id INT AUTO_INCREMENT PRIMARY KEY,
    collector_id INT,
    location_ids TEXT, -- Could be stored as JSON if supported by your RDBMS
    optimized_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_route_collector FOREIGN KEY (collector_id) REFERENCES User(user_id)
);
