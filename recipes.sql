-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2025 at 06:49 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ceylon_cuisine`
--

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ingredients` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`ingredients`)),
  `instructions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`instructions`)),
  `average_rating` decimal(3,2) DEFAULT 0.00,
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `user_id`, `title`, `description`, `image_url`, `created_at`, `updated_at`, `ingredients`, `instructions`, `average_rating`, `status`) VALUES
(23, 50, 'Fish Ambul Thiyal', 'A sour, dry fish curry made with goraka and bold spices. This tangy classic is a coastal favorite with rich flavor and intense aroma in every bite.', '682208f3621e6_336919407_5806908546074945_1170120185552864888_n.jpg', '2025-05-12 14:42:59', '2025-06-08 04:25:42', '{\"0\":\"500g firm fish (e.g., tuna), cut into chunks\",\"2\":\"1 tsp turmeric powder\",\"4\":\"1 tsp black pepper powder\",\"6\":\"Salt to taste\",\"8\":\"1 tbsp unroasted curry powder (optional)\",\"10\":\"2 cloves garlic, sliced\",\"12\":\"1-inch piece of ginger, sliced\",\"14\":\"3-4 dried red chilies, broken\",\"16\":\"2-inch piece of cinnamon stick\",\"18\":\"1 tsp fenugreek seeds\",\"20\":\"A few curry leaves\",\"22\":\"1 tbsp black peppercorns (lightly crushed)\",\"24\":\"3 pieces of goraka (dried garcinia), soaked in warm water\",\"26\":\"1 tbsp oil (preferably coconut oil)\",\"28\":\"\\u00bd cup water (as needed)\"}', '{\"0\":\"Marinate the fish with turmeric, pepper, salt, and optionally curry powder. Set aside for 15\\u201330 minutes.\",\"2\":\"In a clay pot or heavy-bottomed pan, heat oil. Add garlic, ginger, red chilies, cinnamon, curry leaves, and fenugreek seeds. Saut\\u00e9 for a minute.\",\"4\":\"Add the soaked goraka pieces along with the soaking water. Let it simmer for 5 minutes until it becomes slightly thick and tangy.\",\"6\":\"Place the marinated fish pieces gently into the pot in a single layer.\",\"8\":\"Sprinkle crushed peppercorns over the fish. Add just enough water to barely cover the bottom of the pot.\",\"10\":\"Simmer uncovered on low heat for 30\\u201340 minutes until the fish is cooked and the sauce has thickened and darkened.\",\"12\":\"Avoid stirring; instead, gently shake the pot to mix if needed.\",\"14\":\"Let it cool \\u2014 Ambul Thiyal tastes even better the next day!\"}', 3.00, 'approved'),
(24, 2, 'Hoppers', 'Crispy-edged, bowl-shaped pancakes made from fermented rice flour. Perfect with a spicy sambol or a runny egg in the center for breakfast or dinner.', '68220b9831070_istockphoto-543978354-612x612.jpg', '2025-05-12 14:54:16', '2025-06-08 04:25:40', '{\"0\":\"2 cups white raw rice (soaked for 4\\u20136 hours)\",\"2\":\"\\u00bd cup cooked rice\",\"4\":\"\\u00bd tsp sugar\",\"6\":\"\\u00bd tsp salt\",\"8\":\"\\u00bd tsp instant yeast\",\"10\":\"1 cup thick coconut milk (or more, as needed)\",\"12\":\"Water (as needed, to adjust consistency)\",\"14\":\"Oil (for greasing the pan)\"}', '{\"0\":\"Grind the soaked raw rice and cooked rice together with some water to form a smooth batter.\",\"2\":\"Transfer to a bowl, then mix in yeast, sugar, and salt. Cover and let it ferment in a warm place for about 6\\u20138 hours or overnight.\",\"4\":\"Once fermented, stir in the thick coconut milk to get a pourable, pancake-like consistency. Rest the batter for 15\\u201320 minutes before cooking.\",\"6\":\"Heat a small hopper pan or small wok. Grease it lightly with oil.\",\"8\":\"Pour a ladleful of batter into the hot pan, quickly swirl it around to coat the sides in a thin layer, leaving more batter in the center.\",\"10\":\"Cover with a lid and cook on medium heat for about 3\\u20134 minutes until the edges are crispy and the center is soft and cooked.\",\"12\":\"Remove carefully and serve hot.\"}', 0.00, 'approved'),
(25, 2, 'Cashew Curry', 'Creamy and mildly spiced curry made with soaked cashews and coconut milk. A luxurious vegetarian dish often served at festive occasions.\r\n\r\n', '682f490f7da8f_451436608_122211276014008560_2715113146278636003_n.jpg', '2025-05-12 14:58:09', '2025-06-08 04:25:38', '[\"1 cup raw cashew nuts (soaked in water overnight or for at least 4\\u20136 hours)\",\"\\u00bd cup green peas (optional, fresh or frozen)\",\"1 medium onion, finely sliced\",\"2\\u20133 green chilies, slit\",\"2 cloves garlic, minced\",\"1-inch piece of ginger, minced\",\"A few curry leaves\",\"\\u00bd tsp mustard seeds\",\"\\u00bd tsp turmeric powder\",\"1 tsp unroasted curry powder\",\"\\u00bd tsp chili powder (optional, for heat)\",\"Salt to taste\",\"1 cup thick coconut milk\",\"\\u00bd cup thin coconut milk\",\"1 tbsp coconut oil (or any cooking oil)\"]', '[\"Boil the soaked cashews in thin coconut milk (or water) until soft \\u2014 about 15\\u201320 minutes. If using green peas, boil them along with the cashews during the last 5 minutes.\",\"Heat oil in a pan. Add mustard seeds and let them splutter. Then add onion, green chilies, garlic, ginger, and curry leaves. Saut\\u00e9 until onions are soft.\",\"Add turmeric, curry powder, and chili powder (if using). Stir for a few seconds to release the aroma.\",\"Add the boiled cashews and peas along with the remaining cooking liquid. Mix well.\",\"Pour in the thick coconut milk and simmer gently for 5\\u20137 minutes until the curry thickens and flavors blend.\",\"Season with salt. Adjust thickness with a bit more coconut milk or water if needed.\",\"Serve hot with steamed rice or string hoppers.\"]', 0.00, 'approved'),
(26, 2, 'Konda Kavum', 'Golden oil cakes made from rice flour and treacle, deep-fried to perfection. A New Year treat that&#039;s crispy outside and soft, sweet inside.', '68220d9471243_Konda_Kavum_02.JPG', '2025-05-12 15:02:44', '2025-06-08 04:25:35', '{\"0\":\"1 cup raw rice (soaked in water for 3\\u20134 hours)\",\"2\":\"\\u00be cup grated jaggery (kithul jaggery preferred)\",\"4\":\"\\u00bd tsp salt\",\"6\":\"\\u00bd tsp turmeric powder (for color \\u2013 optional)\",\"8\":\"\\u00bd cup thick coconut milk\",\"10\":\"1\\u20132 tbsp water (if needed, for consistency)\",\"12\":\"Oil for deep frying\"}', '{\"0\":\"Grind the soaked raw rice to a smooth, thick paste using very little water. This forms the base of your kavum batter.\",\"2\":\"In a bowl, combine the ground rice flour with grated jaggery, salt, coconut milk, and turmeric powder (if using).\",\"4\":\"Mix until the jaggery dissolves completely and the batter is smooth and thick. The consistency should be similar to thick pancake batter.\",\"6\":\"Let the batter rest for at least 30 minutes.\",\"8\":\"Heat oil in a small wok or deep frying pan on medium heat.\",\"10\":\"To make the konda (hair\\/knot) on top:\",\"12\":\"Use a ladle to scoop some batter and gently pour it into the hot oil.\",\"14\":\"As it puffs up, spoon a bit more batter over the center to create the \\u201ckonda\\u201d (a topknot-like shape).\",\"16\":\"Fry until golden brown, flipping once, to ensure it cooks evenly inside.\",\"18\":\"Drain on paper towels and let cool slightly before serving.\"}', 0.00, 'approved'),
(27, 2, 'Asmi', 'A lacy, crispy sweet made from rice flour and coconut milk, drizzled with sugar syrup. A must-have for Avurudu with its unique look and taste.', '682e1e784a1fc_Asmi-1-768x590.jpg', '2025-05-12 15:07:26', '2025-06-08 04:25:33', '[\"2 cups raw white rice (soaked for 4\\u20136 hours)\",\"\\u00bc cup cooked rice\",\"1\\u20132 tbsp coconut milk (to help blend)\",\"A handful of davul kurundu (wild cinnamon leaves) or a pinch of baking soda (as a substitute)\",\"A pinch of salt\",\"Oil for deep frying\",\"For Sugar Syrup (Pani):\",\"\\u00bd cup sugar\",\"\\u00bc cup water\",\"A few drops of rose essence or a small piece of cinnamon (optional)\",\"A few drops of food coloring (usually pink or green \\u2013 optional)\"]', '[\"Grind the soaked raw rice and cooked rice together into a very smooth, thick batter using just enough coconut milk or water to blend.\",\"Add wild cinnamon leaf juice (by pounding and extracting juice) or a pinch of baking soda to help the batter become slightly airy.\",\"Add a pinch of salt and mix well. The batter should be thick but pourable \\u2014 like a loose pancake batter.\",\"Heat oil in a small wok or deep frying pan.\",\"Pour the batter into a coconut shell spoon with holes (or use a regular perforated ladle or funnel with multiple thin streams).\",\"Let the batter fall in thin streams into the oil in a circular, lacy pattern. Move your hand in spirals or zig-zags to create the design.\",\"Fry for 1\\u20132 minutes, then carefully fold the Asmi in half and press lightly with a spoon. Fry until crisp but not browned. Remove and drain.\"]', 0.00, 'approved'),
(28, 50, 'Achcharu (Pickle)', 'A tangy pickle made with mixed vegetables, mustard, vinegar, and spice. It adds a flavorful punch to rice and curry or street food snacks', '68221144f3a36_s-l1200.jpg', '2025-05-12 15:18:29', '2025-06-08 04:25:20', '{\"0\":\"1 cup carrot (cut into sticks)\",\"2\":\"1 cup green beans (cut into 2-inch pieces)\",\"4\":\"1 cup green chilies (cut lengthwise or into halves)\",\"6\":\"1 cup small onions (shallots, peeled)\",\"8\":\"\\u00bd cup raw mango (peeled and sliced, optional)\",\"10\":\"10\\u201312 garlic cloves (peeled)\",\"12\":\"1-inch piece of ginger (julienned)\",\"14\":\"For the Pickling Mixture:\",\"16\":\"\\u00bd cup white vinegar\",\"18\":\"1 tbsp mustard seeds\",\"20\":\"1 tbsp roasted curry powder\",\"22\":\"1 tbsp chili powder\",\"24\":\"1 tsp turmeric powder\",\"26\":\"1 tbsp sugar\",\"28\":\"1 tsp salt (adjust to taste)\",\"30\":\"\\u00bc cup water\",\"32\":\"1 tbsp oil\"}', '{\"0\":\"Blanch the vegetables (except mango and onion) by dipping them in boiling water for 30 seconds, then immediately transferring to cold water. Drain well.\",\"2\":\"Heat oil in a small pan. Add mustard seeds and let them splutter.\",\"4\":\"Add ginger and garlic. Saut\\u00e9 until fragrant.\",\"6\":\"Lower the heat and stir in turmeric, chili powder, curry powder, salt, and sugar. Cook for about 1 minute, then add vinegar and water. Bring to a gentle boil, then turn off the heat.\",\"8\":\"In a large bowl, combine all the vegetables and fruits (onion, carrot, beans, green chilies, mango).\",\"10\":\"Pour the warm vinegar-spice mixture over the vegetables and toss well to coat.\",\"12\":\"Let it cool completely, then store in a clean, dry glass jar.\",\"14\":\"Leave for at least 24 hours before consuming for best flavor.\"}', 0.00, 'approved'),
(29, 50, 'Watalappan', 'A creamy steamed coconut custard made with jaggery, eggs, and cardamom. Rich, silky, and full of island flavor—perfect for festive endings.', '682211c9b210e_d1d53dac007b62e5396b076e96fcc8ef.jpg', '2025-05-12 15:20:41', '2025-06-07 14:27:48', '{\"0\":\"1 cup grated jaggery (preferably kithul jaggery)\",\"2\":\"4 large eggs\",\"4\":\"1 cup thick coconut milk\",\"6\":\"\\u00bc tsp ground cardamom\",\"8\":\"A pinch of grated nutmeg (optional)\",\"10\":\"1 tsp vanilla extract (optional)\",\"12\":\"A small pinch of salt\",\"14\":\"1 tbsp chopped cashew nuts (for topping)\"}', '{\"0\":\"Melt the jaggery gently with 2\\u20133 tablespoons of water in a pan over low heat until it forms a thick syrup. Let it cool slightly.\",\"2\":\"In a bowl, beat the eggs lightly (do not overbeat; avoid foaming).\",\"4\":\"Add the cooled jaggery syrup to the eggs gradually while stirring continuously.\",\"6\":\"Stir in the coconut milk, cardamom, nutmeg, vanilla, and salt. Mix well until smooth.\",\"8\":\"Strain the mixture through a sieve to ensure a silky texture.\",\"10\":\"Pour the mixture into a heatproof bowl or small ramekins.\",\"12\":\"Sprinkle chopped cashew nuts on top.\",\"14\":\"Steam for about 30\\u201340 minutes on medium-low heat until the custard sets (a skewer inserted should come out clean).\",\"16\":\"Alternatively, you can bake in a water bath at 160\\u00b0C (320\\u00b0F) for 30\\u201340 minutes.\",\"18\":\"Let it cool, then refrigerate before serving for best flavor and texture.\"}', 5.00, 'approved');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
