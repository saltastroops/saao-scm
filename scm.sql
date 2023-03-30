CREATE TABLE RFQ (
                     RFQ_Id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
                     Year INT NOT NULL COMMENT 'Year when this RFQ was issued',
                     Sequential_Number INT NOT NULL COMMENT 'Sequential number of this RFQ within its year',
                     CONSTRAINT RFQ_Year_Sequential_Number UNIQUE (Year, Sequential_Number)
);
