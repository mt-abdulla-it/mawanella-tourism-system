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
  `map_embed` TEXT DEFAULT NULL,
  `facilities` TEXT,
  `entrance_fees` VARCHAR(100) DEFAULT 'Free',
  `safe_visit_time` VARCHAR(100) DEFAULT '8:00 AM - 4:00 PM',
  `last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`category_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `places` (`place_id`, `name`, `category_id`, `description`, `opening_hours`, `travel_tips`, `local_cuisine`, `travel_routes`, `travel_time`, `distance`, `estimated_time`, `latitude`, `longitude`, `map_embed`, `facilities`, `entrance_fees`) VALUES
(1, 'Uthuwankanda Saradiel Village', 1, 'Cultural village and resort that pays homage to the great Sri Lankan hero, Saradiel, with historical significance.', '8:30 AM - 5:30 PM', 'Wear comfortable walking shoes. Good to get local guide for history details.', 'Try the traditional village rice and curry served on banana leaves.', 'Take the Colombo-Kandy road from Mawanella and turn left at Uthuwankanda junction.', '15 mins', 5.70, '2 - 3 hours', 7.26090350, 80.41994050, '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.823631303603!2d80.41994048977666!3d7.260903500287627!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae3149aa1a6e73d%3A0xcd5efe099ce2339a!2sSaradiel%20Resort!5e0!3m2!1sen!2slk!4v1790530350253!5m2!1sen!2slk" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>', 'Parking, Restrooms, Local Food Stall, Guided Tours', 'LKR 160'),

(2, 'Bo Ella Waterfall', 2, 'A local waterfall that can be photographed as it is surrounded by lush greenery and is easily accessible.', '6:00 AM - 6:00 PM', 'Path can get slippery when raining. Be careful near rocks.', 'Local tea and pol roti available at small shops nearby.', 'Head towards Uthuwankanda and follow the rural roads leading to the falls.', '15 mins', 5.80, '1 - 2 hours', 7.27586590, 80.43787650, '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.6918112287754!2d80.4378765!3d7.2758658999999986!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae314c147ce1ad1%3A0x2e6aa8faa08ade6e!2sBo%20Ella!5e0!3m2!1sen!2slk!4v1790532300198!5m2!1sen!2slk" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>', 'Parking nearby, Viewing Spot', 'Free'),

(3, 'Mawela Walagamba Cave Temple', 3, 'Ancient cave temple with Buddhist heritage.', '6:00 AM - 7:00 PM', 'Dress properly covering shoulders and knees when visiting shrine.', 'Purchase sweetmeats like Kavum and Kokis from nearby vendors.', 'Take the Rambukkana road from Mawanella town and turn at Mawela.', '20 mins', 5.90, '1 hour', 7.25197093, 80.49913615, '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d572.5318164046174!2d80.49913614849224!3d7.25197093447644!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae36b2aeda1cdd7%3A0x6cc65af288566da3!2sMawela%20rajamaha%20viharaya!5e0!3m2!1sen!2slk!4v1790533631850!5m2!1sen!2slk" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>', 'Restrooms, Shoe Keeping Place', 'Free'),

(4, 'Kadugannawa Tunnel', 4, 'Historic rock tunnel constructed during the British rule and is one of the landmarks along the Colombo – Kandy Road.', 'Open 24 Hours', 'Park vehicle safely on side bay before taking photos.', 'Great spot to grab king coconut or roasted corn on the cob.', 'Drive directly along the Colombo-Kandy (A1) highway.', '25 mins', 7.20, '30 mins', 7.25733179, 80.44777150, '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63325.68093522701!2d80.447771496197!3d7.257331793752407!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae36c780b2311d5%3A0xb4eb84c60d8aacc5!2zS2FkdWdhbm5hd2EgVHVubmVsIHwg4Laa4Lap4LeU4Lac4Lax4LeK4Lax4LeP4LeAIOC2i-C2uOC2nw!5e0!3m2!1sen!2slk!4v1790533939429!5m2!1sen!2slk" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>', 'Parking, Small Shops', 'Free'),(5, 'Kadugannawa Train Station', 5, 'Colonial styled railway station with scenic views of the surroundings.', '5:00 AM - 9:00 PM', 'Good place for rail photos. Do not walk on live train lines.', 'Vadai and milk tea from the station canteen.', 'Drive up the Kadugannawa pass on A1 and turn right into the station road.', '30 mins', 8.70, '45 mins', 7.25542544, 80.44131605, '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63325.94921547175!2d80.4413160461926!3d7.255425444229544!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae36b8ca7e9f571%3A0xb65ee15931c748af!2sKadugannawa!5e0!3m2!1sen!2slk!4v1790534677468!5m2!1sen!2slk" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>', 'Ticket Counter, Waiting Area, Restrooms, Parking', 'Free'),

(6, 'Meeyan Ella Waterfall', 6, 'Scenic waterfall that can be visited through a trek, which is located near the historic railway line.', '7:00 AM - 5:30 PM', 'Trek path near railway line needs extra care. Go early morning.', 'Bring your own snacks and water as shops are limited.', 'Drive towards Ihalakotte via Rambukkana road and trek along the railway line.', '40 mins', 9.90, '2 hours', 7.27135274, 80.43348941, '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31661.85280833187!2d80.43348940625259!3d7.271352735071465!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae36b28faa54de1%3A0xec363027dc428652!2sMeeyan%20Ella%20Waterfall%20(Walagoda)!5e0!3m2!1sen!2slk!4v1790534783743!5m2!1sen!2slk" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>', 'Walking Trail, Viewpoint', 'Free'),

(7, 'Pinnawala Zoo', 7, 'Zoological garden located near the Elephant Orphanage, houses a variety of endemic and exotic animal species.', '8:30 AM - 5:30 PM', 'Can visit both zoo and elephant orphanage on same day.', 'Various food options inside including local rice meals and snacks.', 'Take the Rambukkana road from Mawanella, straight to Pinnawala.', '35 mins', 16.00, '2 - 3 hours', 7.29653418, 80.31482220, '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63320.14844650705!2d80.31482219696042!3d7.296534182838259!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae315ca19d4f40d%3A0x3d3bdad28c9b3625!2sPinnawala%20Zoo!5e0!3m2!1sen!2slk!4v1790534903839!5m2!1sen!2slk" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>', 'Parking, Restaurants, Souvenir Shop, Restrooms', 'LKR 300'),

(8, 'Pinnawala Elephant Orphanage', 7, 'World renowned center for taking care of orphaned elephants which includes their feeding and allowing them to bathe in the river', '8:30 AM - 5:30 PM', 'River bath times are 10:00 AM and 2:00 PM. Go early to get good place.', 'Enjoy local buffet lunches at the restaurants overlooking the river.', 'Located on Rambukkana road, right opposite the Pinnawala Zoo.', '35 mins', 17.00, '3 - 4 hours', 7.30083351, 80.31675339, '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63319.539894220754!2d80.31675338745116!3d7.300833512124203!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae315c83a2c0273%3A0xa5744b525ada1e6c!2sPinnawala%20Elephant%20Orphanage!5e0!3m2!1sen!2slk!4v1790535001087!5m2!1sen!2slk" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>', 'Cafeteria, Restrooms, Parking, Shops', 'LKR 300'),

(9, 'Asupini Ella Waterfall', 6, 'Majestic waterfall with local mythology and legend associated with it, provides a challenge and great natural sight.', '7:00 AM - 5:00 PM', 'Need to trek down path. Take water bottle and wear good shoes.', 'Small village shops offer local sweets and hot tea.', 'Take the Aranayaka road from Mawanella and follow signs to the falls.', '45 mins', 18.00, '2 - 3 hours', 7.11775649, 80.34147263, '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126690.27598695076!2d80.34147262573246!3d7.1177564881039865!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae3731b770bc943%3A0x6b11b25d846475bb!2sAsupini%20Ella%20Falls!5e0!3m2!1sen!2slk!4v1790535084512!5m2!1sen!2slk" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>', 'Viewing Deck, Local Snacks', 'Free'),

(10, 'Ambuluwawa Biodiversity Complex', 2, 'Multireligious Tower and Biodiversity Complex located atop a mountain that offers a 360 degree view of the surroundings.', '7:30 AM - 6:00 PM', 'Tower steps get narrow at top, take care if afraid of heights.', 'Snack bar at the entrance offers rolls, patties, and soft drinks.', 'Drive towards Gampola and take the steep road up to Ambuluwawa peak.', '55 mins', 24.80, '3 - 4 hours', 7.20543706, 80.45450645, '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63332.959051200065!2d80.45450644607732!3d7.205437056743686!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae371f92e975b5d%3A0xa383ec30d0ad2ef6!2sAmbuluwawa%20Biodiversity%20Complex!5e0!3m2!1sen!2slk!4v1790535195015!5m2!1sen!2slk" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>', 'Parking, Snack Bar, Restrooms, Tower View', 'LKR 150');

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
  `full_name` VARCHAR(100) DEFAULT 'Abdulla MT',
  `email` VARCHAR(100) DEFAULT 'abdulla@mawanella.lk',
  `role` VARCHAR(50) DEFAULT 'Super Admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- default admin record (username: admin, password: password)
INSERT INTO `admins` (`admin_id`, `username`, `password_hash`, `full_name`, `email`, `role`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Abdulla MT', 'abdulla@mawanella.lk', 'Super Admin');

-- day plans table (reserved for future database itinerary saving feature)
CREATE TABLE `day_plans` (
  `plan_id` INT AUTO_INCREMENT PRIMARY KEY,
  `place_ids` JSON NOT NULL,
  `total_distance` DECIMAL(10,2) DEFAULT 0.00,
  `total_estimated_time` VARCHAR(50) DEFAULT '0 hours',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
