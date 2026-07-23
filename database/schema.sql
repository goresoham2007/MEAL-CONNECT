-- =====================================================================
-- MEAL-CONNECT DATABASE SCHEMA
-- Import this file in phpMyAdmin (XAMPP) to create the database,
-- all tables, and demo seed data (26 mess / cloud kitchens across Pune).
-- =====================================================================

CREATE DATABASE IF NOT EXISTS mealconnect CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mealconnect;

-- ---------------------------------------------------------------------
-- STUDENTS (student user accounts)
-- ---------------------------------------------------------------------
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(50) NOT NULL,
    phone VARCHAR(10) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    college_name VARCHAR(120) DEFAULT NULL,
    course VARCHAR(80) DEFAULT NULL,
    year VARCHAR(20) DEFAULT NULL,
    profile_photo VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- OWNERS (mess owner accounts)
-- ---------------------------------------------------------------------
CREATE TABLE owners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(50) NOT NULL,
    phone VARCHAR(10) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    business_name VARCHAR(120) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- MESS / CLOUD KITCHENS / STARTUPS
-- ---------------------------------------------------------------------
CREATE TABLE mess (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT DEFAULT NULL,
    name VARCHAR(150) NOT NULL,
    type ENUM('Mess','Cloud Kitchen','Startup') NOT NULL DEFAULT 'Mess',
    veg_type ENUM('Pure Veg','Non-Veg Special','Both') NOT NULL DEFAULT 'Both',
    location_area VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    latitude DECIMAL(10,7) NOT NULL,
    longitude DECIMAL(10,7) NOT NULL,
    description TEXT,
    cover_image VARCHAR(500),
    rating DECIMAL(2,1) DEFAULT 4.0,
    price_per_month INT NOT NULL,
    tags VARCHAR(255),
    facilities VARCHAR(255),
    owner_name VARCHAR(80),
    owner_contact VARCHAR(15),
    budget_friendly TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES owners(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- MESS IMAGE GALLERY
-- ---------------------------------------------------------------------
CREATE TABLE mess_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mess_id INT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    FOREIGN KEY (mess_id) REFERENCES mess(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- WEEKLY MENU
-- ---------------------------------------------------------------------
CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mess_id INT NOT NULL,
    day_of_week ENUM('Mon','Tue','Wed','Thu','Fri','Sat','Sun') NOT NULL,
    meal_type ENUM('Breakfast','Lunch','Dinner') NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    FOREIGN KEY (mess_id) REFERENCES mess(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- MEMBERSHIP PLANS
-- ---------------------------------------------------------------------
CREATE TABLE plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mess_id INT NOT NULL,
    plan_name VARCHAR(60) NOT NULL,
    duration_days INT NOT NULL,
    price INT NOT NULL,
    meals_per_day VARCHAR(40) NOT NULL,
    menu_type VARCHAR(40) NOT NULL,
    savings_note VARCHAR(120) DEFAULT NULL,
    FOREIGN KEY (mess_id) REFERENCES mess(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- TRIAL PLANS (2-day / 3-day trial before full membership)
-- ---------------------------------------------------------------------
CREATE TABLE trial_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mess_id INT NOT NULL,
    trial_days INT NOT NULL,
    price INT NOT NULL,
    FOREIGN KEY (mess_id) REFERENCES mess(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- SUBSCRIPTIONS (student joins a mess via a plan OR a trial)
-- ---------------------------------------------------------------------
CREATE TABLE subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    mess_id INT NOT NULL,
    plan_id INT DEFAULT NULL,
    trial_id INT DEFAULT NULL,
    plan_label VARCHAR(80) NOT NULL,
    join_date DATE NOT NULL,
    expiry_date DATE NOT NULL,
    amount INT NOT NULL,
    transaction_id VARCHAR(30) NOT NULL,
    status ENUM('Active','Expired','Cancelled') DEFAULT 'Active',
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (mess_id) REFERENCES mess(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- MONTHLY ATTENDANCE
-- ---------------------------------------------------------------------
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    mess_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    status ENUM('Present','Absent','Holiday') NOT NULL DEFAULT 'Present',
    UNIQUE KEY uniq_day (student_id, mess_id, attendance_date),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (mess_id) REFERENCES mess(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- FEEDBACK
-- ---------------------------------------------------------------------
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    mess_id INT NOT NULL,
    rating TINYINT NOT NULL,
    review TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (mess_id) REFERENCES mess(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- ENQUIRIES ("Enquire Now" messages to owners)
-- ---------------------------------------------------------------------
CREATE TABLE enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    mess_id INT NOT NULL,
    message VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (mess_id) REFERENCES mess(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- SEED DATA
-- =====================================================================

-- Demo owner account (password: Owner@123)  -- bcrypt hash generated with PHP password_hash()
INSERT INTO owners (full_name, phone, password_hash, business_name) VALUES
('Ramesh Deshmukh', '9876500001', '$2y$10$Yb6H0hj0N1c1c1c1c1c1cO7Q9m5F2mYV1qkYqYqYqYqYqYqYqYqYu', 'Deshmukh Home Mess'),
('Suvarna Patil', '9876500002', '$2y$10$Yb6H0hj0N1c1c1c1c1c1cO7Q9m5F2mYV1qkYqYqYqYqYqYqYqYqYu', 'Patil Tiffin Services');

-- NOTE: the password hashes above are placeholders. Real, working hashes
-- for the demo password "Owner@123" and "Student@123" are inserted by
-- setup.php on first run (see /mealconnect/setup.php) so login works
-- out of the box without needing a PHP CLI to pre-hash values.

-- 26 mess / cloud kitchens / startups spread across Pune (not just Chakan)
INSERT INTO mess
(name, type, veg_type, location_area, address, latitude, longitude, description, cover_image, rating, price_per_month, tags, facilities, owner_name, owner_contact, budget_friendly)
VALUES
('Annapurna Ghar Mess','Mess','Pure Veg','Narhe','Shivtirth Nagar, Narhe, Pune',18.4529,73.8087,'Authentic Maharashtrian home-style thali served with unlimited chapati and varan-bhaat. Run by a local family for over 8 years.','https://commons.wikimedia.org/wiki/Special:FilePath/Traditional_North_Indian_Thali.jpg?width=600',4.5,2200,'Home-style,2 Meals,Unlimited Roti','RO Water,Veg Only Kitchen,Home Delivery','Ramesh Deshmukh','9876500001',1),
('Katraj Sai Tiffin','Mess','Both','Katraj','Near Katraj Chowk, Pune',18.4576,73.8677,'Popular tiffin service near Katraj offering both veg and non-veg thalis with a rotating weekly menu.','https://commons.wikimedia.org/wiki/Special:FilePath/Tiffin_box.jpg?width=600',4.2,2400,'2 Meals,Non-Veg Available','RO Water,Veg/Non-Veg Separate,AC Dining','Suvarna Patil','9876500002',0),
('Chakan Farmhouse Mess','Mess','Pure Veg','Chakan','MIDC Road, Chakan, Pune',18.7550,73.8580,'Farm-fresh vegetables sourced daily, ideal for students and IT/MIDC employees around Chakan.','https://commons.wikimedia.org/wiki/Special:FilePath/A_thali_with_daal_roti_bhindi_ki_sabzi_and_mango_pickle.jpg?width=600',4.3,2100,'Home-style,Budget Friendly','RO Water,Parking Available','Anil Jadhav','9876500003',1),
('Hinjewadi Cloud Bites','Cloud Kitchen','Non-Veg Special','Hinjewadi','Phase 1, Hinjewadi IT Park, Pune',18.5913,73.7389,'Delivery-only cloud kitchen serving North Indian and Chinese non-veg meals to IT professionals and students.','https://commons.wikimedia.org/wiki/Special:FilePath/Chicken_curry.jpg?width=600',4.1,3200,'Cloud Kitchen,Non-Veg Available','Contactless Delivery,Hygiene Certified','Vikram Rao','9876500004',0),
('Wakad Comfort Kitchen','Cloud Kitchen','Both','Wakad','Datta Mandir Road, Wakad, Pune',18.5993,73.7629,'Comfort-food style cloud kitchen with daily changing thalis, quick delivery within 30 minutes.','https://commons.wikimedia.org/wiki/Special:FilePath/Popula_dabbas.JPG?width=600',4.0,2900,'Cloud Kitchen,2 Meals','Hygiene Certified,Fast Delivery','Pooja Shah','9876500005',0),
('Kothrud Annapurna Bhojanalay','Mess','Pure Veg','Kothrud','Karve Road, Kothrud, Pune',18.5074,73.8077,'Traditional sit-down mess famous for unlimited thali and homely Maharashtrian taste.','https://commons.wikimedia.org/wiki/Special:FilePath/Veg_Punjabi_Thaali.jpg?width=600',4.6,2500,'Home-style,Unlimited Thali','RO Water,Dine-in,AC Dining','Sunita Kulkarni','9876500006',0),
('Hadapsar Spice Route','Startup','Non-Veg Special','Hadapsar','Magarpatta Road, Hadapsar, Pune',18.5089,73.9260,'A small food startup by two culinary graduates focused on high-protein non-veg tiffins.','https://commons.wikimedia.org/wiki/Special:FilePath/Chicken_Tikka_Masala_Curry.png?width=600',4.0,3400,'Startup,High Protein','Hygiene Certified,Custom Meal Plans','Rohan Bhosale','9876500007',0),
('Viman Nagar Tiffin Co.','Startup','Both','Viman Nagar','Clover Park Road, Viman Nagar, Pune',18.5679,73.9143,'Modern tiffin startup with app-based menu selection and eco-friendly packaging.','https://commons.wikimedia.org/wiki/Special:FilePath/Awadhi_Vegetable_Biryani.jpg?width=600',4.3,3300,'Startup,Eco Packaging','Contactless Delivery,Custom Meal Plans','Neha Deshpande','9876500008',0),
('Kharadi Home Kitchen','Mess','Pure Veg','Kharadi','EON IT Park Road, Kharadi, Pune',18.5515,73.9430,'Pure vegetarian home kitchen serving students and working professionals near EON IT Park.','https://commons.wikimedia.org/wiki/Special:FilePath/Indian_Veg_Thali.JPG?width=600',4.2,2800,'Home-style,2 Meals','RO Water,Veg Only Kitchen','Manisha Joshi','9876500009',0),
('Baner Green Bowl','Cloud Kitchen','Pure Veg','Baner','Baner-Pashan Link Road, Pune',18.5590,73.7868,'Health-focused cloud kitchen offering millet-based thalis and low-oil cooking.','https://commons.wikimedia.org/wiki/Special:FilePath/Palak_paneer_with_rice.jpg?width=600',4.4,3100,'Cloud Kitchen,Healthy','Hygiene Certified,Low Oil Cooking','Aditi Kale','9876500010',0),
('Aundh Ghar Ka Khana','Mess','Both','Aundh','ITI Road, Aundh, Pune',18.5590,73.8070,'Ghar-jaisa khana with a rotating weekly menu and optional Sunday non-veg special.','https://commons.wikimedia.org/wiki/Special:FilePath/The_Gujarati_Thali.jpg?width=600',4.1,2600,'Home-style,Sunday Special','RO Water,Dine-in','Prakash Nikam','9876500011',1),
('Camp Royal Mess','Mess','Non-Veg Special','Pune Camp','MG Road, Camp, Pune',18.5122,73.8792,'Old-established mess near Camp known for its Sunday mutton thali and quick service.','https://commons.wikimedia.org/wiki/Special:FilePath/Bengali_Mutton_Curry.JPG?width=600',4.3,3000,'Non-Veg Available,2 Meals','RO Water,Dine-in,Parking Available','Iqbal Shaikh','9876500012',0),
('Warje Annapurna Tiffins','Mess','Pure Veg','Warje','NDA Road, Warje, Pune',18.4783,73.8067,'Simple, hygienic, home-style vegetarian tiffins delivered twice a day.','https://commons.wikimedia.org/wiki/Special:FilePath/Marwadi_Gujarati_Thali.jpg?width=600',4.0,2000,'Budget Friendly,Home-style','RO Water,Home Delivery','Sanjay More','9876500013',1),
('Bibwewadi Swad Ghar','Mess','Both','Bibwewadi','Bibwewadi Road, Pune',18.4720,73.8620,'Family-run mess serving both veg and non-veg thalis with a focus on freshly ground spices.','https://commons.wikimedia.org/wiki/Special:FilePath/Roti-Sabzi-Raita.JPG?width=600',4.2,2300,'Home-style,2 Meals','RO Water,Veg/Non-Veg Separate','Ganesh Pawar','9876500014',1),
('Dhanori Foodie Hub','Cloud Kitchen','Non-Veg Special','Dhanori','Vishal Nagar Road, Dhanori, Pune',18.5786,73.8992,'Cloud kitchen popular among students for budget non-veg combo meals.','https://commons.wikimedia.org/wiki/Special:FilePath/Egg_curry.jpg?width=600',3.9,2700,'Cloud Kitchen,Budget Friendly','Hygiene Certified,Fast Delivery','Om Kadam','9876500015',1),
('Wagholi Student Mess','Mess','Pure Veg','Wagholi','Pune-Nagar Road, Wagholi, Pune',18.5786,73.9880,'Popular among engineering students staying near Wagholi hostels, unlimited roti-bhaji.','https://commons.wikimedia.org/wiki/Special:FilePath/A_Typical_Indian_Thali.jpg?width=600',4.1,1900,'Budget Friendly,Unlimited Roti','RO Water,Dine-in','Yogesh Gaikwad','9876500016',1),
('Magarpatta Cloud Kitchen','Cloud Kitchen','Both','Magarpatta','Magarpatta City, Hadapsar, Pune',18.5150,73.9280,'Corporate-friendly cloud kitchen with subscription tiffins delivered to Magarpatta offices.','https://commons.wikimedia.org/wiki/Special:FilePath/Paneer_Butter_Masala.jpg?width=600',4.3,3200,'Cloud Kitchen,Corporate Friendly','Contactless Delivery,Hygiene Certified','Reema Save','9876500017',0),
('Karve Nagar Amrut Mess','Mess','Pure Veg','Karve Nagar','DSK Road, Karve Nagar, Pune',18.4890,73.8130,'Simple satvik vegetarian mess popular with nearby college students.','https://commons.wikimedia.org/wiki/Special:FilePath/Gujarati_thali.jpg?width=600',4.0,2100,'Home-style,Budget Friendly','RO Water,Dine-in','Meera Apte','9876500018',1),
('Sinhagad Road Tiffin Point','Mess','Both','Sinhagad Road','Vadgaon Budruk, Sinhagad Road, Pune',18.4600,73.8280,'Reliable daily tiffin service with veg and non-veg options for nearby colleges.','https://commons.wikimedia.org/wiki/Special:FilePath/Dabbawala.jpg?width=600',3.8,2200,'2 Meals,Non-Veg Available','RO Water,Home Delivery','Santosh Bhagat','9876500019',1),
('Pashan Millet Kitchen','Cloud Kitchen','Pure Veg','Pashan','Sus Road, Pashan, Pune',18.5320,73.7870,'Health-conscious cloud kitchen focused on millet rotis and diet-friendly thalis.','https://commons.wikimedia.org/wiki/Special:FilePath/A_Typical_Kerala_Spread.jpg?width=600',4.4,3400,'Cloud Kitchen,Healthy','Hygiene Certified,Diet Plans','Kavita Naik','9876500020',0),
('Deccan Ghar Bhojan','Mess','Both','Deccan','JM Road, Deccan Gymkhana, Pune',18.5158,73.8412,'Centrally located mess near Deccan, popular with FC Road college students.','https://commons.wikimedia.org/wiki/Special:FilePath/A_typical_south_Indian_lunch_plate.jpg?width=600',4.2,2500,'Home-style,2 Meals','RO Water,Dine-in,AC Dining','Abhijit Kulkarni','9876500021',0),
('Shivajinagar Quick Meals','Cloud Kitchen','Non-Veg Special','Shivajinagar','JM Road, Shivajinagar, Pune',18.5308,73.8474,'Fast, affordable non-veg meal delivery aimed at hostel and PG students.','https://commons.wikimedia.org/wiki/Special:FilePath/Fish_Curry_Kerala.jpg?width=600',3.9,2600,'Cloud Kitchen,Budget Friendly','Hygiene Certified,Fast Delivery','Faisal Ansari','9876500022',1),
('Swargate Annapurna Mess','Mess','Pure Veg','Swargate','Swargate ST Stand Road, Pune',18.5010,73.8620,'Old-style Maharashtrian mess with a loyal student crowd for over a decade.','https://commons.wikimedia.org/wiki/Special:FilePath/North_Indian_thali.jpg?width=600',4.1,2000,'Budget Friendly,Home-style','RO Water,Dine-in','Vaishali Randive','9876500023',1),
('Kondhwa Foodies Nest','Startup','Both','Kondhwa','NIBM Road, Kondhwa, Pune',18.4650,73.8930,'Startup by two friends offering flexible weekly and monthly meal plans.','https://commons.wikimedia.org/wiki/Special:FilePath/Idli_Sambar.JPG?width=600',4.0,2900,'Startup,Flexible Plans','Contactless Delivery,Custom Meal Plans','Tejas Gaikwad','9876500024',0),
('Balewadi Fit Kitchen','Cloud Kitchen','Both','Balewadi','Balewadi High Street, Pune',18.5730,73.7690,'Fitness-focused cloud kitchen offering high-protein and calorie-counted thalis.','https://commons.wikimedia.org/wiki/Special:FilePath/TandooriPaneer.JPG?width=600',4.5,3600,'Cloud Kitchen,High Protein','Hygiene Certified,Diet Plans','Nikhil Sawant','9876500025',0),
('Narhe Vijay Mess','Mess','Non-Veg Special','Narhe','Narhe Gaon Road, Pune',18.4529,73.8087,'Popular non-veg mess among engineering students near Narhe with generous portions.','https://commons.wikimedia.org/wiki/Special:FilePath/Assamese_non-veg_thali.jpg?width=600',4.2,2700,'Non-Veg Available,Budget Friendly','RO Water,Dine-in,Parking Available','Vijay Chavan','9876500026',1);

-- Sample weekly menu for mess id 1 (repeated pattern applied by app for others if empty)
INSERT INTO menu_items (mess_id, day_of_week, meal_type, item_name) VALUES
(1,'Mon','Lunch','Varan Bhaat, Chapati, Batata Bhaji, Koshimbir'),
(1,'Mon','Dinner','Amti Bhaat, Chapati, Bhindi Fry, Papad'),
(1,'Tue','Lunch','Puran Poli, Katachi Amti, Bhaat'),
(1,'Tue','Dinner','Chapati, Vangi Bhaji, Dal, Bhaat'),
(1,'Wed','Lunch','Chapati, Matki Usal, Bhaat, Koshimbir'),
(1,'Wed','Dinner','Khichdi, Kadhi, Papad'),
(1,'Thu','Lunch','Chapati, Palak Paneer, Dal, Bhaat'),
(1,'Thu','Dinner','Chapati, Mix Veg, Amti, Bhaat'),
(1,'Fri','Lunch','Chapati, Batata Vada, Dal, Bhaat'),
(1,'Fri','Dinner','Pav Bhaji, Salad'),
(1,'Sat','Lunch','Chapati, Chana Masala, Bhaat, Papad'),
(1,'Sat','Dinner','Misal Pav'),
(1,'Sun','Lunch','Veg Pulao, Raita, Papad, Sweet'),
(1,'Sun','Dinner','Chapati, Paneer Butter Masala, Dal, Bhaat');

-- Plans for each mess (Monthly / Quarterly / Half-Yearly) generated per mess price
INSERT INTO plans (mess_id, plan_name, duration_days, price, meals_per_day, menu_type, savings_note)
SELECT id, 'Monthly', 30, price_per_month, '2 Meals/day', veg_type, NULL FROM mess;

INSERT INTO plans (mess_id, plan_name, duration_days, price, meals_per_day, menu_type, savings_note)
SELECT id, 'Quarterly', 90, ROUND(price_per_month*3*0.90), '2 Meals/day', veg_type, 'Save 10%' FROM mess;

INSERT INTO plans (mess_id, plan_name, duration_days, price, meals_per_day, menu_type, savings_note)
SELECT id, 'Half-Yearly', 180, ROUND(price_per_month*6*0.85), '2 Meals/day', veg_type, 'Save 15% + 1 Week Free' FROM mess;

-- Trial plans (2-day and 3-day) for every mess, priced per-day at roughly monthly/22 days
INSERT INTO trial_plans (mess_id, trial_days, price)
SELECT id, 2, ROUND(price_per_month/22*2) FROM mess;

INSERT INTO trial_plans (mess_id, trial_days, price)
SELECT id, 3, ROUND(price_per_month/22*3) FROM mess;
