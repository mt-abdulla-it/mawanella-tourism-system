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
(1, 'Cultural / Heritage Places'),
(2, 'Nature / Sightseeing Places'),
(3, 'Religious / Heritage Places'),
(4, 'Heritage / Monument Places'),
(5, 'Heritage / Infrastructure Places'),
(6, 'Nature / Waterfall Places'),
(7, 'Nature / Wildlife Places');

CREATE TABLE `places` (
  `place_id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL UNIQUE,
  `category_id` INT NOT NULL,
  `description` TEXT NOT NULL,
  `opening_hours` VARCHAR(255) DEFAULT '8:00 AM - 5:00 PM',
  `travel_tips` TEXT,
  `local_cuisine` TEXT,
  `travel_routes` TEXT,
  `travel_time` VARCHAR(50) DEFAULT '30 mins',
  `distance` DECIMAL(10,2) NOT NULL,
  `estimated_time` VARCHAR(50) DEFAULT '1-2 hours',
  `latitude` DECIMAL(10,8) DEFAULT NULL,
  `longitude` DECIMAL(11,8) DEFAULT NULL,
  `facilities` TEXT,
  `entrance_fees` VARCHAR(100) DEFAULT 'Free',
  `safe_visit_time` VARCHAR(100) DEFAULT '8:00 AM - 4:00 PM',
  `last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`category_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `places` (`place_id`, `name`, `category_id`, `description`, `opening_hours`, `travel_tips`, `local_cuisine`, `travel_routes`, `travel_time`, `distance`, `estimated_time`, `latitude`, `longitude`, `facilities`, `entrance_fees`) VALUES
(1, 'Uthuwankanda Saradiel Village', 1, 'Cultural village and resort that pays homage to the great Sri Lankan hero, Saradiel, with historical significance.', '8:30 AM - 5:30 PM', 'Wear comfortable walking shoes. Good to get local guide for history details.', 'Try the traditional village rice and curry served on banana leaves.', 'Take the Colombo-Kandy road from Mawanella and turn left at Uthuwankanda junction.', '15 mins', 5.70, '2 - 3 hours', 7.25150000, 80.44910000, 'Parking, Restrooms, Local Food Stall, Guided Tours', 'LKR 500'),

(2, 'Bo Ella Waterfall', 2, 'A local waterfall that can be photographed as it is surrounded by lush greenery and is easily accessible.', '6:00 AM - 6:00 PM', 'Path can get slippery when raining. Be careful near rocks.', 'Local tea and pol roti available at small shops nearby.', 'Head towards Uthuwankanda and follow the rural roads leading to the falls.', '15 mins', 5.80, '1 - 2 hours', 7.24310000, 80.46120000, 'Parking nearby, Viewing Spot'),

(3, 'Mawela Walagamba Cave Temple', 3, 'Ancient cave temple with Buddhist heritage.', '6:00 AM - 7:00 PM', 'Dress properly covering shoulders and knees when visiting shrine.', 'Purchase sweetmeats like Kavum and Kokis from nearby vendors.', 'Take the Rambukkana road from Mawanella town and turn at Mawela.', '20 mins', 5.90, '1 hour', 7.23890000, 80.43850000, 'Restrooms, Shoe Keeping Place'),

(4, 'Kadugannawa Tunnel', 4, 'Historic rock tunnel constructed during the British rule and is one of the landmarks along the Colombo – Kandy Road.', 'Open 24 Hours', 'Park vehicle safely on side bay before taking photos.', 'Great spot to grab king coconut or roasted corn on the cob.', 'Drive directly along the Colombo-Kandy (A1) highway.', '25 mins', 7.20, '30 mins', 7.25540000, 80.52210000, 'Parking, Small Shops'),

(5, 'Kadugannawa Train Station', 5, 'Colonial styled railway station with scenic views of the surroundings.', '5:00 AM - 9:00 PM', 'Good place for rail photos. Do not walk on live train lines.', 'Wade and milk tea from the station canteen.', 'Drive up the Kadugannawa pass on A1 and turn right into the station road.', '30 mins', 8.70, '45 mins', 7.25410000, 80.52840000, 'Ticket Counter, Waiting Area, Restrooms, Parking'),

(6, 'Meeyan Ella Waterfall', 6, 'Scenic waterfall that can be visited through a trek, which is located near the historic railway line.', '7:00 AM - 5:30 PM', 'Trek path near railway line needs extra care. Go early morning.', 'Bring your own snacks and water as shops are limited.', 'Drive towards Ihalakotte via Rambukkana road and trek along the railway line.', '40 mins', 9.90, '2 hours', 7.26110000, 80.51420000, 'Walking Trail, Viewpoint'),

(7, 'Pinnawala Zoo', 7, 'Zoological garden located near the Elephant Orphanage, houses a variety of endemic and exotic animal species.', '8:30 AM - 5:30 PM', 'Can visit both zoo and elephant orphanage on same day.', 'Various food options inside including local rice meals and snacks.', 'Take the Rambukkana road from Mawanella, straight to Pinnawala.', '35 mins', 16.00, '2 - 3 hours', 7.30210000, 80.38720000, 'Parking, Restaurants, Souvenir Shop, Restrooms', 'LKR 2000'),

(8, 'Pinnawala Elephant Orphanage', 7, 'World renowned center for taking care of orphaned elephants which includes their feeding and allowing them to bathe in the river', '8:30 AM - 5:30 PM', 'River bath times are 10:00 AM and 2:00 PM. Go early to get good place.', 'Enjoy local buffet lunches at the restaurants overlooking the river.', 'Located on Rambukkana road, right opposite the Pinnawala Zoo.', '35 mins', 17.00, '3 - 4 hours', 7.30110000, 80.38610000, 'Cafeteria, Restrooms, Parking, Shops', 'LKR 3000'),

(9, 'Asupini Ella Waterfall', 6, 'Majestic water fall with local mythology and legend associated with it, provides a challenge and great natural sight.', '7:00 AM - 5:00 PM', 'Need to trek down path. Take water bottle and wear good shoes.', 'Small village shops offer local sweets and hot tea.', 'Take the Aranayaka road from Mawanella and follow signs to the falls.', '45 mins', 18.00, '2 - 3 hours', 7.18950000, 80.47980000, 'Viewing Deck, Local Snacks'),

(10, 'Ambuluwawa Biodiversity Complex', 2, 'Multireligious Tower and Biodiversity Complex located atop a mountain that offers a 360 degree view of the surroundings.', '7:30 AM - 6:00 PM', 'Tower steps get narrow at top, take care if afraid of heights.', 'Snack bar at the entrance offers rolls, patties, and soft drinks.', 'Drive towards Gampola and take the steep road up to Ambuluwawa peak.', '55 mins', 24.80, '3 - 4 hours', 7.16810000, 80.54890000, 'Parking, Snack Bar, Restrooms, Tower View');

CREATE TABLE `place_images` (
  `image_id` INT AUTO_INCREMENT PRIMARY KEY,
  `place_id` INT NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `is_primary` TINYINT(1) DEFAULT 0,
  FOREIGN KEY (`place_id`) REFERENCES `places`(`place_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `place_images` (`place_id`, `image_path`, `is_primary`) VALUES
(1, 'assets/images/places/6ab2ba464e2a7_0.jpeg', 1),
(1, 'assets/images/places/6ab2bacb25437_0.jpeg', 0),
(1, 'assets/images/places/6ab2bacb25cbd_1.jpeg', 0),
(2, 'assets/images/places/6ab2bc81ee3a6_0.jpeg', 1),
(3, 'assets/images/places/6ab2bd6e73940_0.jpeg', 1),
(4, 'assets/images/places/6ab2bdffef41a_0.jpeg', 1),
(4, 'assets/images/places/6ab2bf823c6d2_0.jpg', 0),
(5, 'assets/images/places/6ab2c05f9c7f4_0.png', 1),
(5, 'assets/images/places/6ab2c0e586444_0.jpg', 0),
(6, 'assets/images/places/6ab2c194412c3_0.jpeg', 1),
(6, 'assets/images/places/6ab2c2d2b9019_0.jpg', 0),
(7, 'assets/images/places/6ab2c312d0d13_0.jpeg', 1),
(7, 'assets/images/places/6ab2c4271c095_0.jpg', 0),
(8, 'assets/images/places/6ab3fee7a4d86_0.jpg', 1),
(8, 'assets/images/places/6ab3fee7a526e_1.jpeg', 0),
(8, 'assets/images/places/6ab3fee7a5c11_2.jpg', 0),
(8, 'assets/images/places/6ab3fee7a627f_3.jpg', 0),
(9, 'assets/images/places/6ab4000f7ade7_0.jpg', 1),
(9, 'assets/images/places/6ab4000f7b2db_1.jpg', 0),
(9, 'assets/images/places/6ab4000f7b879_2.jpeg', 0),
(10, 'assets/images/places/6ab401b6dca80_0.jpg', 1),
(10, 'assets/images/places/6ab401b6dd0d8_1.jpeg', 0),
(10, 'assets/images/places/6ab401b6dd699_2.jpg', 0);

CREATE TABLE `admins` (
  `admin_id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) DEFAULT 'System Administrator',
  `email` VARCHAR(100) DEFAULT 'admin@mawanella.lk',
  `role` VARCHAR(50) DEFAULT 'Super Admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- default admin record (username: admin, password: password)
INSERT INTO `admins` (`admin_id`, `username`, `password_hash`, `full_name`, `email`, `role`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Abdulla MT (Super Admin)', 'admin@mawanella.lk', 'Super Admin');

-- day plans table (reserved for future database itinerary saving feature)
CREATE TABLE `day_plans` (
  `plan_id` INT AUTO_INCREMENT PRIMARY KEY,
  `place_ids` JSON NOT NULL,
  `total_distance` DECIMAL(10,2) DEFAULT 0.00,
  `total_estimated_time` VARCHAR(50) DEFAULT '0 hours',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
