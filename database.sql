-- database setup for mawanella tourism project
-- student: abdulla mt (E2410182)

CREATE DATABASE IF NOT EXISTS `mawanella_tourism_db`;
USE `mawanella_tourism_db`;

DROP TABLE IF EXISTS `day_plans`;
DROP TABLE IF EXISTS `place_images`;
DROP TABLE IF EXISTS `places`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `admins`;

CREATE TABLE `categories` (
  `category_id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_name` VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Cultural / Heritage'),
(2, 'Nature / Sightseeing'),
(3, 'Religious / Heritage'),
(4, 'Heritage / Monument'),
(5, 'Nature / Wildlife'),
(6, 'Nature / Waterfall');

CREATE TABLE `places` (
  `place_id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL UNIQUE,
  `category_id` INT NOT NULL,
  `description` TEXT NOT NULL,
  `opening_hours` VARCHAR(255) DEFAULT '8:00 AM - 5:00 PM',
  `travel_tips` TEXT,
  `distance` DECIMAL(10,2) NOT NULL,
  `estimated_time` VARCHAR(50) DEFAULT '1-2 hours',
  `latitude` DECIMAL(10,8) DEFAULT NULL,
  `longitude` DECIMAL(11,8) DEFAULT NULL,
  `facilities` TEXT,
  `last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`category_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `places` (`place_id`, `name`, `category_id`, `description`, `opening_hours`, `travel_tips`, `distance`, `estimated_time`, `latitude`, `longitude`, `facilities`) VALUES
(1, 'Uthuwankanda Saradiel Village', 1, 'Theme village setup about Saradiel hero with statues and history details about old Mawanella area.', '8:30 AM - 5:30 PM', 'Wear comfortable walking shoes. Good to get local guide for history details.', 5.70, '2 - 3 hours', 7.25150000, 80.44910000, 'Parking, Restrooms, Local Food Stall, Guided Tours'),

(2, 'Bo Ella Waterfall', 2, 'Small hidden waterfall in Uthuwankanda area with peaceful rocks and trees. Good spot for relaxing and pictures.', '6:00 AM - 6:00 PM', 'Path can get slippery when raining. Be careful near rocks.', 5.80, '1 - 2 hours', 7.24310000, 80.46120000, 'Parking nearby, Viewing Spot'),

(3, 'Mawela Walagamba Cave Temple', 3, 'Old cave temple linked to King Walagamba period with rock paintings and peaceful temple surrounding.', '6:00 AM - 7:00 PM', 'Dress properly covering shoulders and knees when visiting shrine.', 5.90, '1 hour', 7.23890000, 80.43850000, 'Restrooms, Shoe Keeping Place'),

(4, 'Kadugannawa Tunnel', 4, 'Famous rock tunnel made during British time along Colombo Kandy main road.', 'Open 24 Hours', 'Park vehicle safely on side bay before taking photos.', 7.20, '30 mins', 7.25540000, 80.52210000, 'Parking, Small Shops'),

(5, 'Kadugannawa Train Station', 4, 'Historic railway station built during British era with nice mountain and tea estate view.', '5:00 AM - 9:00 PM', 'Good place for rail photos. Do not walk on live train lines.', 8.70, '45 mins', 7.25410000, 80.52840000, 'Ticket Counter, Waiting Area, Restrooms, Parking'),

(6, 'Meeyan Ella Waterfall', 6, 'Waterfall near old railway line. Popular place for trekking and nature photography.', '7:00 AM - 5:30 PM', 'Trek path near railway line needs extra care. Go early morning.', 9.90, '2 hours', 7.26110000, 80.51420000, 'Walking Trail, Viewpoint'),

(7, 'Pinnawala Zoo', 5, 'Open layout zoo near Pinnawala featuring native animals and birds of Sri Lanka.', '8:30 AM - 5:30 PM', 'Can visit both zoo and elephant orphanage on same day.', 16.00, '2 - 3 hours', 7.30210000, 80.38720000, 'Parking, Restaurants, Souvenir Shop, Restrooms'),

(8, 'Pinnawala Elephant Orphanage', 5, 'Famous elephant center where you can watch elephants bathing in river and feeding.', '8:30 AM - 5:30 PM', 'River bath times are 10:00 AM and 2:00 PM. Go early to get good place.', 17.00, '3 - 4 hours', 7.30110000, 80.38610000, 'Cafeteria, Restrooms, Parking, Shops'),

(9, 'Asupini Ella Waterfall', 6, 'Large waterfall near Aranayaka side with high views and scenic countryside walk.', '7:00 AM - 5:00 PM', 'Need to trek down path. Take water bottle and wear good shoes.', 18.00, '2 - 3 hours', 7.18950000, 80.47980000, 'Viewing Deck, Local Snacks'),

(10, 'Ambuluwawa Biodiversity Complex', 2, 'Multi-religious tower built on top of hill with 360 degree panoramic view of central hills.', '7:30 AM - 6:00 PM', 'Tower steps get narrow at top, take care if afraid of heights.', 24.80, '3 - 4 hours', 7.16810000, 80.54890000, 'Parking, Snack Bar, Restrooms, Tower View');

CREATE TABLE `place_images` (
  `image_id` INT AUTO_INCREMENT PRIMARY KEY,
  `place_id` INT NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `is_primary` TINYINT(1) DEFAULT 0,
  FOREIGN KEY (`place_id`) REFERENCES `places`(`place_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `admins` (
  `admin_id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- default admin record (username: admin, password: password)
INSERT INTO `admins` (`admin_id`, `username`, `password_hash`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- day plans table (reserved for future database itinerary saving feature)
CREATE TABLE `day_plans` (
  `plan_id` INT AUTO_INCREMENT PRIMARY KEY,
  `place_ids` JSON NOT NULL,
  `total_distance` DECIMAL(10,2) DEFAULT 0.00,
  `total_estimated_time` VARCHAR(50) DEFAULT '0 hours',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
