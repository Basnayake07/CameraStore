CREATE DATABASE Camera_Warehouse;

USE Camera_Warehouse;

CREATE TABLE Users (
  UserID INT AUTO_INCREMENT PRIMARY KEY,
  Username VARCHAR(50) NOT NULL UNIQUE,
  PasswordHash VARCHAR(255) NOT NULL,
  PhoneNumber VARCHAR(10) NOT NULL UNIQUE,
  Role VARCHAR(50),
  CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Suppliers (
  SupplierID INT AUTO_INCREMENT PRIMARY KEY,
  SupplierName VARCHAR(100) NOT NULL,
  Location VARCHAR(100)  NOT NULL,
  ContactEmail VARCHAR(50) NOT NULL
);

CREATE TABLE shop (
    ShopID INT(4) AUTO_INCREMENT PRIMARY KEY,
    Man_name VARCHAR(20) NOT NULL,
    Address VARCHAR(100) NOT NULL,
    SEmail VARCHAR(50) NOT NULL
);

CREATE TABLE Products (
  ProductID INT AUTO_INCREMENT PRIMARY KEY,
  ProductName VARCHAR(100) NOT NULL,
  Brand VARCHAR(100) NOT NULL,
  Type VARCHAR(100) NOT NULL,
  SKU VARCHAR(50),
  DateAdded TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status VARCHAR(20) NOT NULL
);

CREATE TABLE DispatchOrders (
  DispatchOrderID INT AUTO_INCREMENT PRIMARY KEY,
  ProductID INT,
  Quantity INT,
  OrderDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  ShopID INT,
  FOREIGN KEY (ProductID) REFERENCES products(ProductID),
  FOREIGN KEY (ShopID) REFERENCES shop(ShopID)
);

CREATE TABLE PurchaseOrders (
  PurchaseOrderID INT AUTO_INCREMENT PRIMARY KEY,
  SupplierID INT,
  ProductID INT,
  QuantityOrdered INT,
  QuantityRecieved INT,
  UnitPrice DECIMAL(10, 2),
  Status VARCHAR(50),
  OrderDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (SupplierID) REFERENCES Suppliers(SupplierID),
  FOREIGN KEY (ProductID) REFERENCES products(ProductID)
);

CREATE TABLE Inventory (
  ProductID INT PRIMARY KEY,
  ProductName VARCHAR(100),
  Brand VARCHAR(100),
  Type VARCHAR(100),
  SKU VARCHAR(50),
  TotalQuantity INT,
  LastReceivedDate TIMESTAMP,
  TotalValue DECIMAL(10, 2)
);

CREATE TABLE productreceiveddate (
    PurchaseOrderID  INT(11),
    DateReceived     TIMESTAMP,
    quantity         INT(50),
    PRIMARY KEY (PurchaseOrderID, DateReceived),
    FOREIGN KEY (PurchaseOrderID) REFERENCES purchaseorders(PurchaseOrderID)
);


DELIMITER $$

CREATE TRIGGER after_purchase_order_update
AFTER UPDATE ON PurchaseOrders
FOR EACH ROW
BEGIN
    UPDATE Inventory i
    JOIN Products p ON i.ProductID = p.ProductID
    SET 
        i.ProductName = p.ProductName,
        i.Brand = p.Brand,
        i.Type = p.Type,
        i.SKU = p.SKU,
        i.TotalQuantity = COALESCE(i.TotalQuantity, 0) - COALESCE(OLD.QuantityRecieved, 0) + COALESCE(NEW.QuantityRecieved, 0),
        i.LastReceivedDate = GREATEST(COALESCE(i.LastReceivedDate, '1900-01-01'), COALESCE(NEW.OrderDate, '1900-01-01')),
        i.TotalValue = COALESCE(i.TotalValue, 0) - COALESCE(OLD.QuantityRecieved, 0) * COALESCE(OLD.UnitPrice, 0) 
                     + COALESCE(NEW.QuantityRecieved, 0) * COALESCE(NEW.UnitPrice, 0)
    WHERE i.ProductID = NEW.ProductID;
END$$

DELIMITER ;



DELIMITER $$

CREATE TRIGGER after_purchase_order_insert
AFTER INSERT ON PurchaseOrders
FOR EACH ROW
BEGIN
    INSERT INTO Inventory (ProductID, ProductName, Brand, Type, SKU, TotalQuantity, LastReceivedDate, TotalValue)
    SELECT 
        p.ProductID,
        p.ProductName,
        p.Brand,
        p.Type,
        p.SKU,
        COALESCE(NEW.QuantityRecieved, 0),
        COALESCE(NEW.OrderDate, '1900-01-01'),
        COALESCE(NEW.QuantityRecieved, 0) * COALESCE(NEW.UnitPrice, 0)
    FROM 
        Products p
    WHERE 
        p.ProductID = NEW.ProductID
    ON DUPLICATE KEY UPDATE 
        TotalQuantity = COALESCE(Inventory.TotalQuantity, 0) + COALESCE(NEW.QuantityRecieved, 0),
        LastReceivedDate = GREATEST(COALESCE(Inventory.LastReceivedDate, '1900-01-01'), COALESCE(NEW.OrderDate, '1900-01-01')),
        TotalValue = COALESCE(Inventory.TotalValue, 0) + COALESCE(NEW.QuantityRecieved, 0) * COALESCE(NEW.UnitPrice, 0);
END$$

DELIMITER ;

