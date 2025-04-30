-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2025 at 09:10 AM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `php_project`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `order_cost` decimal(6,2) NOT NULL,
  `order_status` varchar(100) NOT NULL DEFAULT 'on_hold',
  `user_id` int(11) NOT NULL,
  `user_phone` int(11) NOT NULL,
  `user_city` varchar(255) NOT NULL,
  `user_address` varchar(255) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_cost`, `order_status`, `user_id`, `user_phone`, `user_city`, `user_address`, `order_date`) VALUES
(1, 1299.99, 'On Hold', 1, 123456789, 'Warszawa', 'ul. Marsza?kowska 10', '2025-04-29 15:29:34'),
(2, 199.99, 'On Hold', 2, 987654321, 'Kraków', 'ul. Floria?ska 5', '2025-04-29 15:29:34'),
(3, 249.99, 'Shipped', 1, 123456789, 'Warszawa', 'ul. Marsza?kowska 10', '2025-04-29 15:29:34'),
(4, 2399.00, 'Delivered', 4, 1, '1', '1', '2025-04-29 16:05:07'),
(5, 2399.00, 'Not Paid', 4, 0, '1', 'youraverageroomfangame 1/2', '2025-04-29 16:09:33'),
(6, 58.00, 'Shipped', 4, 2147483647, 'Kwidzyn', 'Ko?cielna 2/1', '2025-04-29 19:48:23'),
(7, 76.00, 'Paid', 4, 2147483647, '1234', 'Ko?cielna 2/1', '2025-04-29 19:52:08'),
(8, 76.00, 'On Hold', 4, 2147483647, 'Gda?sk', 'Ko?cielna 2/1', '2025-04-29 19:57:19'),
(9, 76.00, 'Not Paid', 4, 2147483647, 'Kwidzyn', 'youraverageroomfangame 1/2', '2025-04-29 19:57:55'),
(10, 76.00, 'Not Paid', 4, 2147483647, 'Kwidzyn', 'youraverageroomfangame 1/2', '2025-04-29 19:58:53'),
(11, 76.00, 'Not Paid', 4, 2147483647, 'Kwidzyn', 'youraverageroomfangame 1/2', '2025-04-29 19:59:40'),
(12, 76.00, 'Not Paid', 4, 2147483647, 'Kwidzyn', 'youraverageroomfangame 1/2', '2025-04-29 20:01:10');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `product_price` decimal(6,2) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `product_name`, `product_image`, `product_price`, `product_quantity`, `user_id`, `order_date`) VALUES
(1, 1, '2', 'Laptop Pro 15', 'laptop15.jpg', 0.00, 0, 1, '2025-04-29 15:29:34'),
(2, 2, '3', 'S?uchawki Bluetooth', 'sluchawki.jpg', 0.00, 0, 2, '2025-04-29 15:29:34'),
(3, 3, '4', 'Zegarek sportowy', 'zegarek.jpg', 0.00, 0, 1, '2025-04-29 15:29:34'),
(4, 5, '1', 'Smartphone XYZ', 'xyz.jpg', 799.00, 3, 4, '2025-04-29 16:09:33'),
(5, 6, '7', 'Oti awa', 'little gniot.png', 29.00, 2, 4, '2025-04-29 19:48:23'),
(6, 7, '8', 'Wiku? gniotek', 'wik (4).png', 19.00, 4, 4, '2025-04-29 19:52:08'),
(7, 8, '8', 'Wiku? gniotek', 'wik (4).png', 19.00, 4, 4, '2025-04-29 19:57:19'),
(8, 9, '8', 'Wiku? gniotek', 'wik (4).png', 19.00, 4, 4, '2025-04-29 19:57:55'),
(9, 10, '8', 'Wiku? gniotek', 'wik (4).png', 19.00, 4, 4, '2025-04-29 19:58:53'),
(10, 11, '8', 'Wiku? gniotek', 'wik (4).png', 19.00, 4, 4, '2025-04-29 19:59:40'),
(11, 12, '8', 'Wiku? gniotek', 'wik (4).png', 19.00, 4, 4, '2025-04-29 20:01:10'),
(12, 13, '8', 'Wiku? gniotek', 'wik (4).png', 19.00, 1, 1, '2025-04-29 20:02:32'),
(13, 14, '8', 'Wiku? gniotek', 'wik (4).png', 19.00, 2, 1, '2025-04-29 20:55:31'),
(14, 14, '7', 'Oti awa', 'little gniot.png', 29.00, 1, 1, '2025-04-29 20:55:31'),
(15, 15, '8', 'Wiku? gniotek', 'wik (4).png', 19.00, 1, 1, '2025-04-30 00:53:46');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_category` varchar(100) NOT NULL,
  `product_description` varchar(255) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `product_price` decimal(6,2) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `product_amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_category`, `product_description`, `product_image`, `product_price`, `product_quantity`, `product_amount`) VALUES
(7, 'Oti awa', 'beauty', 'awawa', 'little gniot.png', 29.00, 0, 4),
(9, 'wiktorw', 'books', '123123', '78.png', 59.00, 0, 55);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `ratings`
--

CREATE TABLE `ratings` (
  `Rating_ID` int(11) NOT NULL,
  `Users_ID` int(11) NOT NULL,
  `Products_ID` int(11) NOT NULL,
  `rating` varchar(1) NOT NULL,
  `comment` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`Rating_ID`, `Users_ID`, `Products_ID`, `rating`, `comment`) VALUES
(1, 0, 2, '5', '?wietny laptop, bardzo szybki!'),
(2, 0, 3, '4', 'Dobre s?uchawki, ale mog?yby mie? lepszy bass.'),
(3, 0, 1, '3', 'Smartfon ok, ale bateria mog?aby by? lepsza.'),
(4, 1, 1, '2', 'Bardzo dobry produkt'),
(5, 1, 1, '4', 'Bardzo dobry produkt :333'),
(6, 4, 7, '4', 'Bardzo dobry produkt, polecam'),
(7, 4, 7, '4', 'Bardzo dobry produkt, polecam'),
(8, 1, 7, '1', '?mieszne'),
(9, 1, 7, '1', 'Bardzo dobry produkt'),
(10, 1, 7, '1', 'testuje'),
(11, 1, 7, '1', 'testuje dalej'),
(12, 1, 7, '1', 'nie no ?mie?nie'),
(13, 1, 7, '1', 'co nie?');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `account_type` varchar(255) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`, `account_type`) VALUES
(1, 'wiktorw', 's30897@pjwstk.edu.pl', '123123123', 'user'),
(4, 'Admin Adminowski', 'admin@example.com', '124124124', 'admin');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indeksy dla tabeli `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indeksy dla tabeli `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indeksy dla tabeli `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`Rating_ID`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `UX_Constraint` (`user_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `Rating_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
