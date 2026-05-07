-- ============================================================
-- Real Estate Agency Portal Database
-- File: real_estate_portal.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS real_estate_portal_db;
USE real_estate_portal_db;

-- ============================================================
-- TABLES
-- ============================================================

CREATE TABLE Users (
    userId       INT NOT NULL UNIQUE AUTO_INCREMENT,
    userName     VARCHAR(50) NOT NULL UNIQUE,
    contactInfo  VARCHAR(200),
    passwordHash VARCHAR(255) NOT NULL,
    userType     ENUM('agent', 'buyer', 'renter') NOT NULL,
    PRIMARY KEY (userId)
);

CREATE TABLE Properties (
    propertyId   INT NOT NULL UNIQUE AUTO_INCREMENT,
    title        VARCHAR(100) NOT NULL,
    propertyType VARCHAR(50) NOT NULL,
    address      VARCHAR(200) NOT NULL,
    city         VARCHAR(100) NOT NULL,
    price        DECIMAL(12,2) NOT NULL,
    status       ENUM('available', 'sold', 'rented') NOT NULL DEFAULT 'available',
    agentId      INT NOT NULL,
    PRIMARY KEY (propertyId),
    FOREIGN KEY (agentId) REFERENCES Users(userId)
);

CREATE TABLE Inquiries (
    inquiryId   INT NOT NULL UNIQUE AUTO_INCREMENT,
    userId      INT NOT NULL,
    propertyId  INT NOT NULL,
    message     VARCHAR(255) NOT NULL,
    inquiryDate DATETIME NOT NULL,
    PRIMARY KEY (inquiryId),
    FOREIGN KEY (userId) REFERENCES Users(userId),
    FOREIGN KEY (propertyId) REFERENCES Properties(propertyId)
);

CREATE TABLE Transactions (
    transactionId   INT NOT NULL UNIQUE AUTO_INCREMENT,
    propertyId      INT NOT NULL,
    userId          INT NOT NULL,
    transactionType ENUM('sale', 'rental') NOT NULL,
    transactionDate DATETIME NOT NULL,
    amount          DECIMAL(12,2) NOT NULL,
    PRIMARY KEY (transactionId),
    FOREIGN KEY (propertyId) REFERENCES Properties(propertyId),
    FOREIGN KEY (userId) REFERENCES Users(userId)
);

CREATE TABLE Favorites (
    favoriteId INT NOT NULL UNIQUE AUTO_INCREMENT,
    userId     INT NOT NULL,
    propertyId INT NOT NULL,
    savedDate  DATETIME NOT NULL,
    PRIMARY KEY (favoriteId),
    FOREIGN KEY (userId) REFERENCES Users(userId),
    FOREIGN KEY (propertyId) REFERENCES Properties(propertyId)
);

-- ============================================================
-- STORED PROCEDURES
-- ============================================================

DELIMITER $$

CREATE PROCEDURE AddOrUpdateUser(
    IN p_userId      INT,
    IN p_userName    VARCHAR(50),
    IN p_contactInfo VARCHAR(200),
    IN p_passwordHash VARCHAR(255),
    IN p_userType    ENUM('agent', 'buyer', 'renter')
)
BEGIN
    IF p_userId IS NULL THEN
        INSERT INTO Users (userName, contactInfo, passwordHash, userType)
        VALUES (p_userName, p_contactInfo, p_passwordHash, p_userType);
    ELSE
        UPDATE Users
        SET userName     = p_userName,
            contactInfo  = p_contactInfo,
            passwordHash = p_passwordHash,
            userType     = p_userType
        WHERE userId = p_userId;
    END IF;
END$$

CREATE PROCEDURE ProcessTransaction(
    IN p_propertyId      INT,
    IN p_userId          INT,
    IN p_transactionType ENUM('sale', 'rental'),
    IN p_amount          DECIMAL(12,2)
)
BEGIN
    INSERT INTO Transactions (propertyId, userId, transactionType, transactionDate, amount)
    VALUES (p_propertyId, p_userId, p_transactionType, NOW(), p_amount);

    IF p_transactionType = 'sale' THEN
        UPDATE Properties SET status = 'sold'   WHERE propertyId = p_propertyId;
    ELSE
        UPDATE Properties SET status = 'rented' WHERE propertyId = p_propertyId;
    END IF;
END$$

DELIMITER ;

-- ============================================================
-- VIEW
-- ============================================================

CREATE VIEW PropertyListingView AS
SELECT
    p.propertyId,
    p.title,
    p.propertyType,
    p.city,
    p.price,
    p.status,
    u.userName AS agentName
FROM Properties p
JOIN Users u ON p.agentId = u.userId;

-- ============================================================
-- TRIGGER
-- ============================================================

DELIMITER $$

CREATE TRIGGER AfterTransactionInsert
AFTER INSERT ON Transactions
FOR EACH ROW
BEGIN
    IF NEW.transactionType = 'sale' THEN
        UPDATE Properties SET status = 'sold'   WHERE propertyId = NEW.propertyId;
    ELSE
        UPDATE Properties SET status = 'rented' WHERE propertyId = NEW.propertyId;
    END IF;
END$$

DELIMITER ;

-- ============================================================
-- SAMPLE DATA (at least 3 rows per table)
-- ============================================================

-- Users (passwords below are hashed versions of: agent123, buyer123, renter123)
INSERT INTO Users (userName, contactInfo, passwordHash, userType) VALUES
('agent_mike',  'mike@realtyco.com',   '$2y$10$eImiTXuWVxfM37uY4JANjQ==hashed1', 'agent'),
('buyer_sara',  'sara@email.com',      '$2y$10$eImiTXuWVxfM37uY4JANjQ==hashed2', 'buyer'),
('renter_john', 'john@email.com',      '$2y$10$eImiTXuWVxfM37uY4JANjQ==hashed3', 'renter');

-- Properties
INSERT INTO Properties (title, propertyType, address, city, price, status, agentId) VALUES
('Sunny Downtown Apartment', 'Apartment', '123 Main St',    'New York',    250000.00, 'available', 1),
('Modern Family House',      'House',     '456 Oak Avenue', 'Los Angeles', 450000.00, 'available', 1),
('Cozy Studio Flat',         'Studio',    '789 Pine Road',  'Chicago',     120000.00, 'available', 1);

-- Inquiries
INSERT INTO Inquiries (userId, propertyId, message, inquiryDate) VALUES
(2, 1, 'Is this property still available? I would like to schedule a viewing.', NOW()),
(3, 3, 'Can I rent this on a month-to-month basis?',                            NOW()),
(2, 2, 'What are the HOA fees for this house?',                                 NOW());

-- Transactions
INSERT INTO Transactions (propertyId, userId, transactionType, transactionDate, amount) VALUES
(1, 2, 'sale',   NOW(), 245000.00),
(3, 3, 'rental', NOW(), 1200.00),
(2, 2, 'sale',   NOW(), 440000.00);

-- Favorites
INSERT INTO Favorites (userId, propertyId, savedDate) VALUES
(2, 1, NOW()),
(3, 3, NOW()),
(2, 2, NOW());
