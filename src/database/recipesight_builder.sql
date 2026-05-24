-- ============================================================
-- RecipeSight Database Schema
-- Changes from original:
--   1. quantity fields: int -> decimal(8,2)
--   2. UNIQUE constraint on user_inventory(user_id, ingredient_id)
--   3. category moved to lookup table with FK on recipe
--   4. Added created_at / updated_at to recipe
--   5. Added image_url to recipe
--   6. Added FK constraint on nutrition_info(recipe_id)
--   7. ingredient is a lookup table with per-100g nutrition columns
--   8. unit lookup table — canonical allowed units
--   9. unit_conversions table — (ingredient, unit) -> grams_equivalent
--      weight units (g, kg) need no conversion row; app handles directly.
--      recipe_ingredient and user_inventory now FK to unit.
-- ============================================================

-- --------------------------------------------------------
-- Table: category
-- --------------------------------------------------------

CREATE TABLE `category` (
  `category_id`   int(11)     NOT NULL AUTO_INCREMENT,
  `category_name` varchar(50) NOT NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `category_name` (`category_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `category` (`category_name`) VALUES
('Main Dish'), ('Breakfast'), ('Dessert'), ('Baking'),
('Soup'), ('Salad'), ('Snack'), ('Beverage'), ('Sauce'), ('Healthy');

-- --------------------------------------------------------
-- Table: unit
--   is_weight = 1 means the app converts quantity directly to grams
--   (g -> *1, kg -> *1000) without needing a unit_conversions row.
--   is_weight = 0 means a unit_conversions row is required per ingredient.
-- --------------------------------------------------------

CREATE TABLE `unit` (
  `unit_id`    int(11)    NOT NULL AUTO_INCREMENT,
  `unit_name`  varchar(50) NOT NULL,
  `is_weight`  tinyint(1)  NOT NULL DEFAULT 0,
  PRIMARY KEY (`unit_id`),
  UNIQUE KEY `unit_name` (`unit_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `unit` (`unit_name`, `is_weight`) VALUES
-- weight units (no conversion row needed)
('g',       1),
('kg',      1),
-- volume units (conversion row required per ingredient)
('tsp',     0),
('tbsp',    0),
('cup',     0),
('ml',      0),
('liter',   0),
-- count units (conversion row required per ingredient)
('piece',   0),
('clove',   0),
('slice',   0),
('pack',    0),
('bottle',  0);

-- --------------------------------------------------------
-- Table: unit_conversions
--   grams_equivalent = how many grams one of this unit weighs
--   for this specific ingredient.
--   Weight units (g, kg) never need a row here.
--   If a row is missing for a (ingredient, unit) pair, nutrition
--   calculation is skipped for that ingredient (treat as NULL).
-- --------------------------------------------------------

CREATE TABLE `unit_conversions` (
  `conversion_id`    int(11)      NOT NULL AUTO_INCREMENT,
  `ingredient_id`    int(11)      NOT NULL,
  `unit_id`          int(11)      NOT NULL,
  `grams_equivalent` decimal(10,4) NOT NULL,
  PRIMARY KEY (`conversion_id`),
  UNIQUE KEY `uq_ingredient_unit` (`ingredient_id`, `unit_id`),
  KEY `fk_uc_ingredient` (`ingredient_id`),
  KEY `fk_uc_unit`       (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: ingredient
--   Nutrition values are per 100g (USDA FoodData Central, approximate).
--   NULL = not yet populated.
-- --------------------------------------------------------

CREATE TABLE `ingredient` (
  `ingredient_id`    int(11)      NOT NULL AUTO_INCREMENT,
  `ingredient_name`  varchar(100) NOT NULL,
  `calories_per_100g` decimal(8,2) DEFAULT NULL,
  `protein_per_100g`  decimal(8,2) DEFAULT NULL,
  `carbs_per_100g`    decimal(8,2) DEFAULT NULL,
  `fats_per_100g`     decimal(8,2) DEFAULT NULL,
  PRIMARY KEY (`ingredient_id`),
  UNIQUE KEY `ingredient_name` (`ingredient_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ingredient`
  (`ingredient_name`, `calories_per_100g`, `protein_per_100g`, `carbs_per_100g`, `fats_per_100g`)
VALUES
('Baking Powder',    53.00,  0.00,  28.00,  0.00),
('Baking Soda',       0.00,  0.00,   0.00,  0.00),
('Banana',           89.00,  1.09,  22.84,  0.33),
('Bay Leaf',        313.00,  7.61,  74.97,  8.36),
('Beef',            250.00, 26.00,   0.00, 17.00),
('Bell Pepper',      31.00,  1.00,   6.00,  0.30),
('Butter',          717.00,  0.85,   0.06, 81.11),
('Carrot',           41.00,  0.93,   9.58,  0.24),
('Cheddar Cheese',  402.00, 24.90,   1.28, 33.14),
('Chicken',         165.00, 31.00,   0.00,  3.60),
('Chili Powder',    282.00, 13.46,  49.70,  9.68),
('Cinnamon',        247.00,  3.99,  80.59,  1.24),
('Cocoa Powder',    228.00, 19.60,  57.90, 13.70),
('Coconut Milk',    197.00,  2.02,   2.81, 21.33),
('Cumin',           375.00, 17.81,  44.24, 22.27),
('Egg',             143.00, 13.00,   1.00, 10.00),
('Flour',           364.00, 10.33,  76.31,  0.98),
('Garlic',          149.00,  6.36,  33.06,  0.50),
('Ginger',           80.00,  1.82,  17.77,  0.75),
('Ground Beef',     254.00, 17.17,   0.00, 20.00),
('Honey',           304.00,  0.30,  82.40,  0.00),
('Lemon',            29.00,  1.10,   9.32,  0.30),
('Milk',             61.00,  3.20,   4.80,  3.25),
('Mozzarella',      280.00, 28.00,   2.19, 17.12),
('Olive Oil',       884.00,  0.00,   0.00,100.00),
('Onion',            40.00,  1.10,   9.34,  0.10),
('Paprika',         282.00, 14.14,  53.99, 12.89),
('Parmesan',        431.00, 38.46,   4.06, 28.61),
('Pasta',           371.00, 13.04,  74.67,  1.51),
('Pepper',          251.00, 10.39,  63.95,  3.26),
('Pork',            242.00, 27.32,   0.00, 13.92),
('Potato',           77.00,  2.02,  17.49,  0.09),
('Rice',            130.00,  2.69,  28.17,  0.28),
('Salt',              0.00,  0.00,   0.00,  0.00),
('Soy Sauce',        53.00,  8.14,   4.93,  0.57),
('Sugar',           387.00,  0.00, 100.00,  0.00),
('Tomato',           18.00,  0.88,   3.89,  0.20),
('Tomato Sauce',     29.00,  1.50,   5.76,  0.39),
('Vanilla Extract', 288.00,  0.06,  12.65,  0.06),
('Vinegar',          18.00,  0.00,   0.04,  0.00),
('Yeast',           325.00, 40.44,  40.74,  7.61),
('Yogurt',           59.00,  3.47,   4.66,  3.25);

-- --------------------------------------------------------
-- unit_conversions seed data
-- Only volume and count units need rows here.
-- grams_equivalent = grams per 1 of that unit for that ingredient.
-- --------------------------------------------------------

-- Each row is a SELECT that resolves ingredient_id and unit_id by name,
-- compatible with MariaDB (no VALUES ROW() syntax needed).
INSERT INTO `unit_conversions` (`ingredient_id`, `unit_id`, `grams_equivalent`)
-- Flour
SELECT i.ingredient_id, u.unit_id,   2.60 FROM ingredient i, unit u WHERE i.ingredient_name = 'Flour'           AND u.unit_name = 'tsp'    UNION ALL
SELECT i.ingredient_id, u.unit_id,   7.80 FROM ingredient i, unit u WHERE i.ingredient_name = 'Flour'           AND u.unit_name = 'tbsp'   UNION ALL
SELECT i.ingredient_id, u.unit_id, 125.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Flour'           AND u.unit_name = 'cup'    UNION ALL
-- Sugar
SELECT i.ingredient_id, u.unit_id,   4.20 FROM ingredient i, unit u WHERE i.ingredient_name = 'Sugar'           AND u.unit_name = 'tsp'    UNION ALL
SELECT i.ingredient_id, u.unit_id,  12.50 FROM ingredient i, unit u WHERE i.ingredient_name = 'Sugar'           AND u.unit_name = 'tbsp'   UNION ALL
SELECT i.ingredient_id, u.unit_id, 200.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Sugar'           AND u.unit_name = 'cup'    UNION ALL
-- Butter
SELECT i.ingredient_id, u.unit_id,   4.73 FROM ingredient i, unit u WHERE i.ingredient_name = 'Butter'          AND u.unit_name = 'tsp'    UNION ALL
SELECT i.ingredient_id, u.unit_id,  14.18 FROM ingredient i, unit u WHERE i.ingredient_name = 'Butter'          AND u.unit_name = 'tbsp'   UNION ALL
SELECT i.ingredient_id, u.unit_id, 227.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Butter'          AND u.unit_name = 'cup'    UNION ALL
SELECT i.ingredient_id, u.unit_id, 227.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Butter'          AND u.unit_name = 'pack'   UNION ALL
-- Milk
SELECT i.ingredient_id, u.unit_id,    5.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Milk'           AND u.unit_name = 'tsp'    UNION ALL
SELECT i.ingredient_id, u.unit_id,   15.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Milk'           AND u.unit_name = 'tbsp'   UNION ALL
SELECT i.ingredient_id, u.unit_id,  244.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Milk'           AND u.unit_name = 'cup'    UNION ALL
SELECT i.ingredient_id, u.unit_id,    1.03 FROM ingredient i, unit u WHERE i.ingredient_name = 'Milk'           AND u.unit_name = 'ml'     UNION ALL
SELECT i.ingredient_id, u.unit_id, 1030.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Milk'           AND u.unit_name = 'liter'  UNION ALL
-- Egg (~50g per large egg)
SELECT i.ingredient_id, u.unit_id,  50.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Egg'             AND u.unit_name = 'piece'  UNION ALL
-- Chicken (~200g per bone-in piece)
SELECT i.ingredient_id, u.unit_id, 200.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Chicken'         AND u.unit_name = 'piece'  UNION ALL
-- Soy Sauce
SELECT i.ingredient_id, u.unit_id,   5.69 FROM ingredient i, unit u WHERE i.ingredient_name = 'Soy Sauce'       AND u.unit_name = 'tsp'    UNION ALL
SELECT i.ingredient_id, u.unit_id,  17.07 FROM ingredient i, unit u WHERE i.ingredient_name = 'Soy Sauce'       AND u.unit_name = 'tbsp'   UNION ALL
SELECT i.ingredient_id, u.unit_id, 272.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Soy Sauce'       AND u.unit_name = 'cup'    UNION ALL
SELECT i.ingredient_id, u.unit_id, 240.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Soy Sauce'       AND u.unit_name = 'bottle' UNION ALL
-- Vinegar
SELECT i.ingredient_id, u.unit_id,   5.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Vinegar'         AND u.unit_name = 'tsp'    UNION ALL
SELECT i.ingredient_id, u.unit_id,  15.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Vinegar'         AND u.unit_name = 'tbsp'   UNION ALL
SELECT i.ingredient_id, u.unit_id, 239.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Vinegar'         AND u.unit_name = 'cup'    UNION ALL
-- Garlic (~3g per clove)
SELECT i.ingredient_id, u.unit_id,   3.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Garlic'          AND u.unit_name = 'clove'  UNION ALL
SELECT i.ingredient_id, u.unit_id,   2.80 FROM ingredient i, unit u WHERE i.ingredient_name = 'Garlic'          AND u.unit_name = 'tsp'    UNION ALL
SELECT i.ingredient_id, u.unit_id,   8.40 FROM ingredient i, unit u WHERE i.ingredient_name = 'Garlic'          AND u.unit_name = 'tbsp'   UNION ALL
-- Bay Leaf (~0.6g per piece)
SELECT i.ingredient_id, u.unit_id,   0.60 FROM ingredient i, unit u WHERE i.ingredient_name = 'Bay Leaf'        AND u.unit_name = 'piece'  UNION ALL
-- Pepper
SELECT i.ingredient_id, u.unit_id,   2.30 FROM ingredient i, unit u WHERE i.ingredient_name = 'Pepper'          AND u.unit_name = 'tsp'    UNION ALL
SELECT i.ingredient_id, u.unit_id,   6.90 FROM ingredient i, unit u WHERE i.ingredient_name = 'Pepper'          AND u.unit_name = 'tbsp'   UNION ALL
-- Rice (dry)
SELECT i.ingredient_id, u.unit_id, 185.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Rice'            AND u.unit_name = 'cup'    UNION ALL
-- Baking Powder
SELECT i.ingredient_id, u.unit_id,   4.60 FROM ingredient i, unit u WHERE i.ingredient_name = 'Baking Powder'   AND u.unit_name = 'tsp'    UNION ALL
SELECT i.ingredient_id, u.unit_id,  13.80 FROM ingredient i, unit u WHERE i.ingredient_name = 'Baking Powder'   AND u.unit_name = 'tbsp'   UNION ALL
-- Baking Soda
SELECT i.ingredient_id, u.unit_id,   6.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Baking Soda'     AND u.unit_name = 'tsp'    UNION ALL
SELECT i.ingredient_id, u.unit_id,  18.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Baking Soda'     AND u.unit_name = 'tbsp'   UNION ALL
-- Vanilla Extract
SELECT i.ingredient_id, u.unit_id,   4.20 FROM ingredient i, unit u WHERE i.ingredient_name = 'Vanilla Extract' AND u.unit_name = 'tsp'    UNION ALL
SELECT i.ingredient_id, u.unit_id,  12.60 FROM ingredient i, unit u WHERE i.ingredient_name = 'Vanilla Extract' AND u.unit_name = 'tbsp'   UNION ALL
-- Olive Oil
SELECT i.ingredient_id, u.unit_id,   4.50 FROM ingredient i, unit u WHERE i.ingredient_name = 'Olive Oil'       AND u.unit_name = 'tsp'    UNION ALL
SELECT i.ingredient_id, u.unit_id,  13.50 FROM ingredient i, unit u WHERE i.ingredient_name = 'Olive Oil'       AND u.unit_name = 'tbsp'   UNION ALL
SELECT i.ingredient_id, u.unit_id, 216.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Olive Oil'       AND u.unit_name = 'cup'    UNION ALL
-- Honey
SELECT i.ingredient_id, u.unit_id,   7.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Honey'           AND u.unit_name = 'tsp'    UNION ALL
SELECT i.ingredient_id, u.unit_id,  21.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Honey'           AND u.unit_name = 'tbsp'   UNION ALL
SELECT i.ingredient_id, u.unit_id, 340.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Honey'           AND u.unit_name = 'cup'    UNION ALL
-- Tomato (~120g per piece)
SELECT i.ingredient_id, u.unit_id, 120.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Tomato'          AND u.unit_name = 'piece'  UNION ALL
-- Onion (~110g per piece)
SELECT i.ingredient_id, u.unit_id, 110.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Onion'           AND u.unit_name = 'piece'  UNION ALL
-- Banana (~120g per piece)
SELECT i.ingredient_id, u.unit_id, 120.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Banana'          AND u.unit_name = 'piece'  UNION ALL
-- Yogurt
SELECT i.ingredient_id, u.unit_id, 245.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Yogurt'          AND u.unit_name = 'cup'    UNION ALL
SELECT i.ingredient_id, u.unit_id,  15.30 FROM ingredient i, unit u WHERE i.ingredient_name = 'Yogurt'          AND u.unit_name = 'tbsp'   UNION ALL
-- Parmesan
SELECT i.ingredient_id, u.unit_id, 100.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Parmesan'        AND u.unit_name = 'cup'    UNION ALL
SELECT i.ingredient_id, u.unit_id,   6.25 FROM ingredient i, unit u WHERE i.ingredient_name = 'Parmesan'        AND u.unit_name = 'tbsp'   UNION ALL
-- Coconut Milk
SELECT i.ingredient_id, u.unit_id, 240.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Coconut Milk'    AND u.unit_name = 'cup'    UNION ALL
SELECT i.ingredient_id, u.unit_id,  15.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Coconut Milk'    AND u.unit_name = 'tbsp'   UNION ALL
-- Carrot (~60g per piece)
SELECT i.ingredient_id, u.unit_id,  60.00 FROM ingredient i, unit u WHERE i.ingredient_name = 'Carrot'          AND u.unit_name = 'piece';

-- --------------------------------------------------------
-- Table: user
-- --------------------------------------------------------

CREATE TABLE `user` (
  `user_id`       int(11)      NOT NULL AUTO_INCREMENT,
  `username`      varchar(100) NOT NULL,
  `email`         varchar(255) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user` (`user_id`, `username`, `email`, `password_hash`) VALUES
(1, 'recipe_admin', 'admin@recipesight.com', '$2y$10$5COwReVhdE9V1dKdnDzcQOf7XwgtF3HpaBvi9OSf2cJztjPkJJXMe'); --password-hash password is: juan123

-- --------------------------------------------------------
-- Table: recipe
-- --------------------------------------------------------

CREATE TABLE `recipe` (
  `recipe_id`   int(11)      NOT NULL AUTO_INCREMENT,
  `user_id`     int(11)      DEFAULT NULL,
  `category_id` int(11)      DEFAULT NULL,
  `title`       varchar(200) NOT NULL,
  `description` text         DEFAULT NULL,
  `instructions` text        DEFAULT NULL,
  `image_url`   varchar(500) DEFAULT NULL,
  `created_at`  datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`recipe_id`),
  KEY `fk_recipe_user`     (`user_id`),
  KEY `fk_recipe_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: recipe_ingredient
-- --------------------------------------------------------

CREATE TABLE `recipe_ingredient` (
  `recipe_id`     int(11)      NOT NULL,
  `ingredient_id` int(11)      NOT NULL,
  `quantity`      decimal(8,2) DEFAULT NULL,
  `unit_id`       int(11)      DEFAULT NULL,
  PRIMARY KEY (`recipe_id`, `ingredient_id`),
  KEY `fk_ri_ingredient` (`ingredient_id`),
  KEY `fk_ri_unit`       (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: nutrition_info
--   Stores the computed nutrition totals per recipe.
--   Recalculate and overwrite this row whenever recipe_ingredient changes.
-- --------------------------------------------------------

CREATE TABLE `nutrition_info` (
  `nutrition_id` int(11)      NOT NULL AUTO_INCREMENT,
  `recipe_id`    int(11)      NOT NULL,
  `calories`     decimal(8,2) DEFAULT NULL,
  `protein`      decimal(8,2) DEFAULT NULL,
  `carbs`        decimal(8,2) DEFAULT NULL,
  `fats`         decimal(8,2) DEFAULT NULL,
  PRIMARY KEY (`nutrition_id`),
  UNIQUE KEY `recipe_id` (`recipe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: user_inventory
-- --------------------------------------------------------

CREATE TABLE `user_inventory` (
  `inventory_id`  int(11)      NOT NULL AUTO_INCREMENT,
  `user_id`       int(11)      NOT NULL,
  `ingredient_id` int(11)      NOT NULL,
  `quantity`      decimal(8,2) DEFAULT NULL,
  `unit_id`       int(11)      DEFAULT NULL,
  PRIMARY KEY (`inventory_id`),
  UNIQUE KEY `uq_user_ingredient` (`user_id`, `ingredient_id`),
  KEY `fk_inv_user`       (`user_id`),
  KEY `fk_inv_ingredient` (`ingredient_id`),
  KEY `fk_inv_unit`       (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Foreign key constraints
-- --------------------------------------------------------

ALTER TABLE `recipe`
  ADD CONSTRAINT `fk_recipe_user`
    FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_recipe_category`
    FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE SET NULL;

ALTER TABLE `recipe_ingredient`
  ADD CONSTRAINT `fk_ri_recipe`
    FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`recipe_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ri_ingredient`
    FOREIGN KEY (`ingredient_id`) REFERENCES `ingredient` (`ingredient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ri_unit`
    FOREIGN KEY (`unit_id`) REFERENCES `unit` (`unit_id`) ON DELETE SET NULL;

ALTER TABLE `nutrition_info`
  ADD CONSTRAINT `fk_nutrition_recipe`
    FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`recipe_id`) ON DELETE CASCADE;

ALTER TABLE `user_inventory`
  ADD CONSTRAINT `fk_inv_user`
    FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_inv_ingredient`
    FOREIGN KEY (`ingredient_id`) REFERENCES `ingredient` (`ingredient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_inv_unit`
    FOREIGN KEY (`unit_id`) REFERENCES `unit` (`unit_id`) ON DELETE SET NULL;

ALTER TABLE `unit_conversions`
  ADD CONSTRAINT `fk_uc_ingredient`
    FOREIGN KEY (`ingredient_id`) REFERENCES `ingredient` (`ingredient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_uc_unit`
    FOREIGN KEY (`unit_id`) REFERENCES `unit` (`unit_id`) ON DELETE CASCADE;

-- ============================================================
-- SEED DATA
-- ============================================================

INSERT INTO `recipe` (`user_id`, `category_id`, `title`, `description`, `instructions`) VALUES
(1, (SELECT category_id FROM category WHERE category_name = 'Main Dish'),
 'Chicken Adobo',
 'Classic Filipino chicken stew simmered in soy sauce and vinegar.',
 'Marinate chicken, simmer with vinegar and bay leaves, serve with rice.'),
(1, (SELECT category_id FROM category WHERE category_name = 'Breakfast'),
 'Fluffy Pancakes',
 'Light and fluffy breakfast pancakes.',
 'Mix dry and wet ingredients, cook on griddle until bubbles form.'),
(1, (SELECT category_id FROM category WHERE category_name = 'Dessert'),
 'Chocolate Chip Cookies',
 'Classic homemade chocolate chip cookies.',
 'Cream butter and sugar, add eggs and flour, fold in chips, bake.');

-- Chicken Adobo ingredients
INSERT INTO `recipe_ingredient` (`recipe_id`, `ingredient_id`, `quantity`, `unit_id`) VALUES
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Chicken'),   500.00, (SELECT unit_id FROM unit WHERE unit_name = 'g')),
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Soy Sauce'),   0.50, (SELECT unit_id FROM unit WHERE unit_name = 'cup')),
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Vinegar'),     0.25, (SELECT unit_id FROM unit WHERE unit_name = 'cup')),
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Garlic'),      6.00, (SELECT unit_id FROM unit WHERE unit_name = 'clove')),
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Bay Leaf'),    2.00, (SELECT unit_id FROM unit WHERE unit_name = 'piece')),
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Pepper'),      1.00, (SELECT unit_id FROM unit WHERE unit_name = 'tsp')),
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Rice'),        2.00, (SELECT unit_id FROM unit WHERE unit_name = 'cup'));

-- Fluffy Pancakes ingredients
INSERT INTO `recipe_ingredient` (`recipe_id`, `ingredient_id`, `quantity`, `unit_id`) VALUES
(2, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Flour'),         1.50, (SELECT unit_id FROM unit WHERE unit_name = 'cup')),
(2, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Milk'),          1.25, (SELECT unit_id FROM unit WHERE unit_name = 'cup')),
(2, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Egg'),           1.00, (SELECT unit_id FROM unit WHERE unit_name = 'piece')),
(2, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Butter'),        2.00, (SELECT unit_id FROM unit WHERE unit_name = 'tbsp')),
(2, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Sugar'),         2.00, (SELECT unit_id FROM unit WHERE unit_name = 'tbsp')),
(2, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Baking Powder'), 1.00, (SELECT unit_id FROM unit WHERE unit_name = 'tbsp'));

-- Chocolate Chip Cookies ingredients
INSERT INTO `recipe_ingredient` (`recipe_id`, `ingredient_id`, `quantity`, `unit_id`) VALUES
(3, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Flour'),           2.25, (SELECT unit_id FROM unit WHERE unit_name = 'cup')),
(3, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Butter'),          1.00, (SELECT unit_id FROM unit WHERE unit_name = 'cup')),
(3, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Sugar'),           0.75, (SELECT unit_id FROM unit WHERE unit_name = 'cup')),
(3, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Egg'),             2.00, (SELECT unit_id FROM unit WHERE unit_name = 'piece')),
(3, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Vanilla Extract'), 1.00, (SELECT unit_id FROM unit WHERE unit_name = 'tsp')),
(3, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Baking Soda'),    1.00, (SELECT unit_id FROM unit WHERE unit_name = 'tsp'));

-- Nutrition (pre-computed totals)
INSERT INTO `nutrition_info` (`recipe_id`, `calories`, `protein`, `carbs`, `fats`) VALUES
(1, 550.00, 38.00, 45.00, 22.00),
(2, 350.00, 10.00, 55.00, 12.00),
(3, 480.00,  6.00, 60.00, 25.00);

-- Admin inventory
INSERT INTO `user_inventory` (`user_id`, `ingredient_id`, `quantity`, `unit_id`) VALUES
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Chicken'),         2.00, (SELECT unit_id FROM unit WHERE unit_name = 'piece')),
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Soy Sauce'),       1.00, (SELECT unit_id FROM unit WHERE unit_name = 'bottle')),
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Vinegar'),         1.00, (SELECT unit_id FROM unit WHERE unit_name = 'cup')),
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Garlic'),         10.00, (SELECT unit_id FROM unit WHERE unit_name = 'clove')),
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Bay Leaf'),        5.00, (SELECT unit_id FROM unit WHERE unit_name = 'piece')),
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Pepper'),          1.00, (SELECT unit_id FROM unit WHERE unit_name = 'tsp')),
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Flour'),           2.00, (SELECT unit_id FROM unit WHERE unit_name = 'kg')),
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Milk'),            1.00, (SELECT unit_id FROM unit WHERE unit_name = 'liter')),
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Egg'),            12.00, (SELECT unit_id FROM unit WHERE unit_name = 'piece')),
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Butter'),          1.00, (SELECT unit_id FROM unit WHERE unit_name = 'pack')),
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Sugar'),           1.00, (SELECT unit_id FROM unit WHERE unit_name = 'kg')),
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Baking Powder'),   1.00, (SELECT unit_id FROM unit WHERE unit_name = 'tbsp')),
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Vanilla Extract'), 1.00, (SELECT unit_id FROM unit WHERE unit_name = 'tbsp')),
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Baking Soda'),     1.00, (SELECT unit_id FROM unit WHERE unit_name = 'tsp')),
(1, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Rice'),            5.00, (SELECT unit_id FROM unit WHERE unit_name = 'cup'));

-- ============================================================
-- COMMENTED-OUT RECIPES (uncomment to activate)
-- ============================================================

/*
-- Baking: Banana Bread
INSERT INTO `recipe` (`user_id`, `category_id`, `title`, `description`, `instructions`) VALUES
(1, (SELECT category_id FROM category WHERE category_name = 'Baking'),
 'Banana Bread', 'Moist banana bread with walnuts.',
 'Mash bananas, mix with butter, sugar, egg, flour, bake at 350°F for 60 min.');
SET @last_id = LAST_INSERT_ID();
INSERT INTO `recipe_ingredient` (`recipe_id`, `ingredient_id`, `quantity`, `unit_id`) VALUES
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Flour'),  2.00, (SELECT unit_id FROM unit WHERE unit_name = 'cup')),
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Banana'), 3.00, (SELECT unit_id FROM unit WHERE unit_name = 'piece')),
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Butter'), 0.50, (SELECT unit_id FROM unit WHERE unit_name = 'cup')),
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Sugar'),  1.00, (SELECT unit_id FROM unit WHERE unit_name = 'cup')),
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Egg'),    2.00, (SELECT unit_id FROM unit WHERE unit_name = 'piece'));
INSERT INTO `nutrition_info` (`recipe_id`, `calories`, `protein`, `carbs`, `fats`) VALUES
(@last_id, 420.00, 6.00, 65.00, 18.00);

-- Soup: Tomato Basil Soup
INSERT INTO `recipe` (`user_id`, `category_id`, `title`, `description`, `instructions`) VALUES
(1, (SELECT category_id FROM category WHERE category_name = 'Soup'),
 'Tomato Basil Soup', 'Creamy tomato soup with fresh basil.',
 'Sauté onions and garlic, add tomatoes and broth, simmer and blend, stir in basil.');
SET @last_id = LAST_INSERT_ID();
INSERT INTO `recipe_ingredient` (`recipe_id`, `ingredient_id`, `quantity`, `unit_id`) VALUES
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Tomato'),    6.00, (SELECT unit_id FROM unit WHERE unit_name = 'piece')),
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Onion'),     1.00, (SELECT unit_id FROM unit WHERE unit_name = 'piece')),
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Garlic'),    3.00, (SELECT unit_id FROM unit WHERE unit_name = 'clove')),
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Olive Oil'), 2.00, (SELECT unit_id FROM unit WHERE unit_name = 'tbsp'));
INSERT INTO `nutrition_info` (`recipe_id`, `calories`, `protein`, `carbs`, `fats`) VALUES
(@last_id, 180.00, 5.00, 25.00, 8.00);

-- Salad: Caesar Salad
INSERT INTO `recipe` (`user_id`, `category_id`, `title`, `description`, `instructions`) VALUES
(1, (SELECT category_id FROM category WHERE category_name = 'Salad'),
 'Caesar Salad', 'Classic Caesar salad with croutons and Parmesan.',
 'Toss lettuce with dressing, top with croutons and Parmesan.');
SET @last_id = LAST_INSERT_ID();
INSERT INTO `recipe_ingredient` (`recipe_id`, `ingredient_id`, `quantity`, `unit_id`) VALUES
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Parmesan'),  0.50, (SELECT unit_id FROM unit WHERE unit_name = 'cup')),
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Olive Oil'), 2.00, (SELECT unit_id FROM unit WHERE unit_name = 'tbsp'));
INSERT INTO `nutrition_info` (`recipe_id`, `calories`, `protein`, `carbs`, `fats`) VALUES
(@last_id, 350.00, 12.00, 20.00, 25.00);

-- Snack: Yogurt Parfait
INSERT INTO `recipe` (`user_id`, `category_id`, `title`, `description`, `instructions`) VALUES
(1, (SELECT category_id FROM category WHERE category_name = 'Snack'),
 'Yogurt Parfait', 'Layered Greek yogurt with granola and berries.',
 'Layer yogurt, granola, and berries. Drizzle with honey.');
SET @last_id = LAST_INSERT_ID();
INSERT INTO `recipe_ingredient` (`recipe_id`, `ingredient_id`, `quantity`, `unit_id`) VALUES
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Yogurt'), 1.00, (SELECT unit_id FROM unit WHERE unit_name = 'cup')),
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Honey'),  1.00, (SELECT unit_id FROM unit WHERE unit_name = 'tbsp'));
INSERT INTO `nutrition_info` (`recipe_id`, `calories`, `protein`, `carbs`, `fats`) VALUES
(@last_id, 250.00, 15.00, 30.00, 8.00);

-- Beverage: Banana Smoothie
INSERT INTO `recipe` (`user_id`, `category_id`, `title`, `description`, `instructions`) VALUES
(1, (SELECT category_id FROM category WHERE category_name = 'Beverage'),
 'Banana Smoothie', 'Creamy banana smoothie with yogurt.',
 'Blend all ingredients until smooth.');
SET @last_id = LAST_INSERT_ID();
INSERT INTO `recipe_ingredient` (`recipe_id`, `ingredient_id`, `quantity`, `unit_id`) VALUES
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Banana'), 2.00, (SELECT unit_id FROM unit WHERE unit_name = 'piece')),
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Yogurt'), 0.50, (SELECT unit_id FROM unit WHERE unit_name = 'cup')),
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Milk'),   1.00, (SELECT unit_id FROM unit WHERE unit_name = 'cup')),
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Honey'),  1.00, (SELECT unit_id FROM unit WHERE unit_name = 'tbsp'));
INSERT INTO `nutrition_info` (`recipe_id`, `calories`, `protein`, `carbs`, `fats`) VALUES
(@last_id, 280.00, 10.00, 55.00, 5.00);

-- Sauce: Marinara Sauce
INSERT INTO `recipe` (`user_id`, `category_id`, `title`, `description`, `instructions`) VALUES
(1, (SELECT category_id FROM category WHERE category_name = 'Sauce'),
 'Marinara Sauce', 'Classic tomato sauce for pasta.',
 'Sauté garlic in olive oil, add tomatoes and herbs, simmer for 20 minutes.');
SET @last_id = LAST_INSERT_ID();
INSERT INTO `recipe_ingredient` (`recipe_id`, `ingredient_id`, `quantity`, `unit_id`) VALUES
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Tomato'),    4.00, (SELECT unit_id FROM unit WHERE unit_name = 'piece')),
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Garlic'),    3.00, (SELECT unit_id FROM unit WHERE unit_name = 'clove')),
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Olive Oil'), 3.00, (SELECT unit_id FROM unit WHERE unit_name = 'tbsp'));
INSERT INTO `nutrition_info` (`recipe_id`, `calories`, `protein`, `carbs`, `fats`) VALUES
(@last_id, 120.00, 3.00, 15.00, 7.00);

-- Healthy: Quinoa Salad
INSERT INTO `recipe` (`user_id`, `category_id`, `title`, `description`, `instructions`) VALUES
(1, (SELECT category_id FROM category WHERE category_name = 'Healthy'),
 'Quinoa Salad', 'Healthy quinoa salad with vegetables.',
 'Cook quinoa, chop vegetables, mix with olive oil and lemon juice.');
SET @last_id = LAST_INSERT_ID();
INSERT INTO `recipe_ingredient` (`recipe_id`, `ingredient_id`, `quantity`, `unit_id`) VALUES
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Rice'),      1.00, (SELECT unit_id FROM unit WHERE unit_name = 'cup')),
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Tomato'),    2.00, (SELECT unit_id FROM unit WHERE unit_name = 'piece')),
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Carrot'),    1.00, (SELECT unit_id FROM unit WHERE unit_name = 'piece')),
(@last_id, (SELECT ingredient_id FROM ingredient WHERE ingredient_name = 'Olive Oil'), 2.00, (SELECT unit_id FROM unit WHERE unit_name = 'tbsp'));
INSERT INTO `nutrition_info` (`recipe_id`, `calories`, `protein`, `carbs`, `fats`) VALUES
(@last_id, 320.00, 10.00, 45.00, 12.00);
*/

COMMIT;