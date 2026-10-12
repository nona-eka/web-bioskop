-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 05, 2026 at 03:53 PM
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
-- Database: `bioskop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_ticket_price` decimal(10,2) NOT NULL,
  `total_fnv_price` decimal(10,2) DEFAULT 0.00,
  `grand_total` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `schedule_id`, `booking_date`, `total_ticket_price`, `total_fnv_price`, `grand_total`, `payment_status`, `payment_method`) VALUES
(1, 5, 17, '2026-09-30 06:55:14', 60000.00, 100000.00, 160000.00, 'paid', 'e_wallet');

-- --------------------------------------------------------

--
-- Table structure for table `booking_products`
--

CREATE TABLE `booking_products` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking_seats`
--

CREATE TABLE `booking_seats` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `seat_number` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_seats`
--

INSERT INTO `booking_seats` (`id`, `booking_id`, `seat_number`) VALUES
(1, 1, 'B8');

-- --------------------------------------------------------

--
-- Table structure for table `cinemas`
--

CREATE TABLE `cinemas` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `city` varchar(50) NOT NULL,
  `address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cinemas`
--

INSERT INTO `cinemas` (`id`, `name`, `city`, `address`) VALUES
(1, 'Grand Indonesia XXI', 'Jakarta Pusat', 'Jl. M.H. Thamrin No.1, Jakarta Pusat'),
(2, 'Plaza Senayan XXI', 'Jakarta Selatan', 'Jl. Asia Afrika No.8, Jakarta Selatan'),
(3, 'Paris Van Java XXI', 'Bandung', 'Jl. Sukajadi No.131-139, Bandung'),
(4, 'Tunjungan Plaza 5 XXI', 'Surabaya', 'Jl. Basuki Rahmat No.8-12, Surabaya'),
(5, 'Ambarrukmo Plaza XXI', 'Yogyakarta', 'Jl. Laksda Adisucipto Km. 6.5, Yogyakarta');

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `genre` varchar(100) NOT NULL,
  `duration` int(11) NOT NULL,
  `synopsis` text NOT NULL,
  `poster` varchar(255) NOT NULL,
  `release_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `title`, `genre`, `duration`, `synopsis`, `poster`, `release_date`) VALUES
(1, 'The Lord of the Rings: The Fellowship of the Ring', 'Fantasy', 178, 'Seorang hobbit muda bernama Frodo Baggins mewarisi sebuah Cincin Ajaib yang kuat dan harus menghancurkannya di Mount Doom.', 'https://m.media-amazon.com/images/M/MV5BN2EyZjM3NzUtNWUzMi00MTgxLWI0NTctMzY4M2VlOTdjZWRiXkEyXkFqcGdeQXVyNDg4NjY5OTQ@._V1_SX300.jpg', '2001-12-19'),
(2, 'The Lord of the Rings: The Two Towers', 'Fantasy', 179, 'Kelompok persahabatan Cincin terpecah saat mereka berjuang melawan kekuatan Sauron dari berbagai arah.', 'https://m.media-amazon.com/images/M/MV5BZGMxZTdjZmYtMmE2Ni00ZTdkLWI5NTgtNjlmMjBiNzU2MmI5XkEyXkFqcGdeQXVyNjU0OTQ0OTY@._V1_SX300.jpg', '2002-12-18'),
(3, 'The Lord of the Rings: The Return of the King', 'Fantasy', 201, 'Pertempuran akhir untuk Middle-earth dimulai saat Frodo dan Sam mendekati Mount Doom.', 'https://m.media-amazon.com/images/M/MV5BNzA5ZDNlZWMtM2NhNS00NDJjLTk4NDItYTRmY2EwMWZlMTY3XkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_SX300.jpg', '2003-12-17'),
(4, 'Harry Potter and the Sorcerer\'s Stone', 'Fantasy', 152, 'Harry Potter mengetahui bahwa ia adalah seorang penyihir dan memulai tahun pertamanya di Hogwarts.', 'https://m.media-amazon.com/images/M/MV5BNmQ0ODBhMjUtNDRhOC00MGQzLThkMTAtZmFlODU2MWMyY2JiXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '2001-11-16'),
(5, 'Harry Potter and the Chamber of Secrets', 'Fantasy', 161, 'Harry kembali ke Hogwarts dan menyelidiki misteri Kamar Rahasia yang berbahaya.', 'https://m.media-amazon.com/images/M/MV5BMjE0YjUzNDUtMjc5OS00MTU3LTgxMmUtODhkOThkMzdjNWI4XkEyXkFqcGdeQXVyMTA3MzQ4MTc0._V1_SX300.jpg', '2002-11-15'),
(6, 'Harry Potter and the Prisoner of Azkaban', 'Fantasy', 142, 'Pelarian narapidana berbahaya Sirius Black mengancam keselamatan Harry di tahun ketiga sekolahnya.', 'https://m.media-amazon.com/images/M/MV5BMTY4NTIwODg0N15BMl5BanBnXkFtZTcwOTc0MjEzMw@@._V1_SX300.jpg', '2004-06-04'),
(7, 'Harry Potter and the Goblet of Fire', 'Fantasy', 157, 'Harry terpilih secara misterius untuk mengikuti Turnamen Triwizard yang mematikan.', 'https://m.media-amazon.com/images/M/MV5BMTI1NDMyMjExOF5BMl5BanBnXkFtZTcwOTc4MjEzMw@@._V1_SX300.jpg', '2005-11-18'),
(8, 'Harry Potter and the Order of the Phoenix', 'Fantasy', 138, 'Harry membentuk kelompok rahasia untuk melawan kebangkitan kembali Lord Voldemort.', 'https://m.media-amazon.com/images/M/MV5BMTM0NTczMTUzOV5BMl5BanBnXkFtZTY1OTE5NDQ3._V1_SX300.jpg', '2007-07-11'),
(9, 'Harry Potter and the Half-Blood Prince', 'Fantasy', 153, 'Harry menemukan buku tua misterius dan membantu Dumbledore mengungkap masa lalu Voldemort.', 'https://m.media-amazon.com/images/M/MV5BNzU3OWIyNDAtNmIyOC00NTJjLTc1OWEtZTc3MmJkOWY3ZjQ2XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '2009-07-15'),
(10, 'Harry Potter and the Deathly Hallows: Part 1', 'Fantasy', 146, 'Harry, Ron, dan Hermione meninggalkan Hogwarts untuk memburu dan menghancurkan sisa Horcrux.', 'https://m.media-amazon.com/images/M/MV5BMTQ2OTE1Mjk0N15BMl5BanBnXkFtZTcwODE3MDAwNA@@._V1_SX300.jpg', '2010-11-19'),
(11, 'Harry Potter and the Deathly Hallows: Part 2', 'Fantasy', 130, 'Pertempuran puncak pecah di halaman Hogwarts antara pasukan kebaikan dan kejahatan mutlak.', 'https://m.media-amazon.com/images/M/MV5BMGVmMWNiMDktYjQ0Mi00MWFlLTk0N2UtY2ZlNDdkNzhjNjg0XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '2011-07-15'),
(12, 'Avatar', 'Fantasy', 162, 'Seorang marinir lumpuh dikirim ke bulan Pandora dan terjebak dalam konflik kaum pribumi Na\'vi.', 'https://m.media-amazon.com/images/M/MV5BZDA0OGQxNTItMDZkMC00N2UyLWI1MjktYTdhMDhiOGY5NWYyXkEyXkFqcGdeQXVyNDUzOTQ5MjY@._V1_SX300.jpg', '2009-12-18'),
(13, 'Avatar: The Way of Water', 'Fantasy', 192, 'Jake Sully dan keluarganya harus mencari perlindungan di klan air Pandora demi bertahan hidup.', 'https://m.media-amazon.com/images/M/MV5BYjhiNjBlODctY2ZiOC00YjVlLWFlNzAtNTVhNzM2YjIxZW00XkEyXkFqcGdeQXVyMTQxNzMzNDI@._V1_SX300.jpg', '2022-12-16'),
(14, 'The Chronicles of Narnia: The Lion, the Witch and the Wardrobe', 'Fantasy', 143, 'Empat anak bersaudara menemukan dunia magis Narnia melalui sebuah lemari pakaian.', 'https://m.media-amazon.com/images/M/MV5BMTc0NTcxODcyM15BMl5BanBnXkFtZTcwMzM1NDk1MQ@@._V1_SX300.jpg', '2005-12-09'),
(15, 'Pan\'s Labyrinth', 'Fantasy', 118, 'Seorang gadis imajinatif di Spanyol pasca-perang melarikan diri ke dunia labirin mitologis yang gelap.', 'https://m.media-amazon.com/images/M/MV5BMjFhODGzMjctM2FlYy00NThjLWE3ZDktOTc0MjEzMw@@._V1_SX300.jpg', '2006-12-29'),
(16, 'Alice in Wonderland', 'Fantasy', 108, 'Alice yang kini remaja kembali secara tidak sengaja ke dunia bawah tanah ajaib penuh keanehan.', 'https://m.media-amazon.com/images/M/MV5BMTMwNjAxMTc0OF5BMl5BanBnXkFtZTcwODc0MjEzMw@@._V1_SX300.jpg', '2010-03-05'),
(17, 'Stardust', 'Fantasy', 127, 'Seorang pemuda menjelajahi alam magis demi menangkap bintang jatuh untuk wanitapujaannya.', 'https://m.media-amazon.com/images/M/MV5BMjMxOTIwNzExNl5BMl5BanBnXkFtZTcwNzE5MzAyMQ@@._V1_SX300.jpg', '2007-08-10'),
(18, 'Pirates of the Caribbean: On Stranger Tides', 'Fantasy', 136, 'Kapten Jack Sparrow berpetualang mencari Mata Air Keabadian yang legendaris.', 'https://m.media-amazon.com/images/M/MV5BMjE5MjkwODI3Nl5BMl5BanBnXkFtZTcwNjcwNzQ4NA@@._V1_SX300.jpg', '2011-05-20'),
(19, 'Willy Wonka & the Chocolate Factory', 'Fantasy', 100, 'Seorang anak miskin memenangkan tiket emas untuk mengunjungi pabrik cokelat ajaib yang eksentrik.', 'https://m.media-amazon.com/images/M/MV5BNjExMjk0NTQ1N15BMl5BanBnXkFtZTcwNTI2Mjg3OA@@._V1_SX300.jpg', '1971-06-30'),
(20, 'The Wizard of Oz', 'Fantasy', 102, 'Dorothy terbawa angin tornado ke tanah ajaib Oz dan mencari jalan pulang bersama teman-temannya.', 'https://m.media-amazon.com/images/M/MV5BNjU3OWIyNDAtNmIyOC00NTJjLTc1OWEtZTc3MmJkOWY3ZjQ2XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '1939-08-25'),
(21, 'Exhuma', 'Horror', 134, 'Dua dukun, ahli feng shui, dan petugas pemakaman menggali kuburan misterius yang berujung malapetaka.', 'https://m.media-amazon.com/images/M/MV5BYzBjYmU2YmEtYWY0MC00MjE2LTg1NTEtYjYxNzg5MjAyMzBlXkEyXkFqcGdeQXVyMTUzMTg2ODMz._V1_SX300.jpg', '2024-02-22'),
(22, 'Train to Busan', 'Horror', 118, 'Seorang ayah dan putrinya terjebak di dalam kereta cepat saat wabah zombie misterius melanda Korea.', 'https://m.media-amazon.com/images/M/MV5BMTQwNTgyNTMzNF5BMl5BanBnXkFtZTgwNDA2MzI1OTE@._V1_SX300.jpg', '2016-07-20'),
(23, 'The Conjuring', 'Horror', 112, 'Pasangan penyelidik paranormal Ed dan Lorraine Warren membantu keluarga yang diteror makhluk gaib.', 'https://m.media-amazon.com/images/M/MV5BOTk1MjEwNjA4NF5BMl5BanBnXkFtZTcwMTMyOTk3OQ@@._V1_SX300.jpg', '2013-07-19'),
(24, 'The Conjuring 2', 'Horror', 134, 'Warren bersaudara pergi ke Inggris untuk membantu keluarga yang mengalami poltergeist parah di Enfield.', 'https://m.media-amazon.com/images/M/MV5BZjU5OWIyNDAtNmIyOC00NTJjLTc1OWEtZTc3MmJkOWY3ZjQ2XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '2016-06-10'),
(25, 'Hereditary', 'Horror', 127, 'Sebuah keluarga berduka mulai mengungkap rahasia leluhur mereka yang mengerikan setelah kematian sang nenek.', 'https://m.media-amazon.com/images/M/MV5BOTU5MDg3OGItZWQ1Ny00ZGY0LTg0YTAtYjA0Nzg5ZjIzZjJkXkEyXkFqcGdeQXVyMTQxNzMzNDI@._V1_SX300.jpg', '2018-06-08'),
(26, 'The Exorcist', 'Horror', 122, 'Dua pendeta berusaha melakukan eksorsisme pada seorang gadis muda yang dirasuki iblis kuno.', 'https://m.media-amazon.com/images/M/MV5BYjU5OWIyNDAtNmIyOC00NTJjLTc1OWEtZTc3MmJkOWY3ZjQ2XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '1973-12-26'),
(27, 'Hagazussa', 'Horror', 102, 'Seorang gembala muda di pegunungan Alpen terisolasi dan perlahan terjerumus kedalam kegelapan.', 'https://m.media-amazon.com/images/M/MV5BZjU5OWIyNDAtNmIyOC00NTJjLTc1OWEtZTc3MmJkOWY3ZjQ2XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '2017-02-13'),
(28, 'The Shining', 'Horror', 144, 'Seorang penulis mengambil pekerjaan sebagai penjaga musim dingin di hotel terpencil yang memicu kegilaan.', 'https://m.media-amazon.com/images/M/MV5BMWY5ZjY0MzAtNDk3MC00Njg4LTk5N2EtNWVkYjYyZjQ2XkEyXkFqcGdeQXVyMTQxNzMzNDI@._V1_SX300.jpg', '1980-05-23'),
(29, 'Get Out', 'Horror', 104, 'Seorang pemuda kulit hitam mengungkap rahasia mengerikan saat berkunjung ke rumah keluarga pacarnya.', 'https://m.media-amazon.com/images/M/MV5BMjA2OTczNTQwOV5BMl5BanBnXkFtZTgwNzAzMzI0OTE@._V1_SX300.jpg', '2017-02-24'),
(30, 'A Quiet Place', 'Horror', 90, 'Sebuah keluarga harus hidup dalam keheningan total untuk menghindari makhluk buta yang berburu dengan suara.', 'https://m.media-amazon.com/images/M/MV5BMjI0MDMzNTQ0M15BMl5BanBnXkFtZTgwMDUxMTg1NDM@._V1_SX300.jpg', '2018-04-06'),
(31, 'It', 'Horror', 135, 'Sekelompok anak-anak di sebuah kota kecil harus menghadapi badut iblis pemangsa rasa takut.', 'https://m.media-amazon.com/images/M/MV5BZDVkZmE0ZmEtZDMzYS00NTFhLTlmNDMtY2I3NWIwNmEwMmE2XkEyXkFqcGdeQXVyMTMzNzIyNDc1._V1_SX300.jpg', '2017-09-08'),
(32, 'The Witch', 'Horror', 92, 'Sebuah keluarga Puritan di Selandia Baru abad ke-17 diuji oleh kekuatan sihir dan kejahatan hutan.', 'https://m.media-amazon.com/images/M/MV5BMTUzMTQ4NDk1MV5BMl5BanBnXkFtZTgwMjQwMzcxNzE@._V1_SX300.jpg', '2015-02-19'),
(33, 'Midsommar', 'Horror', 148, 'Seorang wanita mengunjungi festival musim panas pagan di Swedia yang berujung pada ritual mengerikan.', 'https://m.media-amazon.com/images/M/MV5BMzQxNzQzOTQwM15BMl5BanBnXkFtZTgwMDQ2NTcwODM@._V1_SX300.jpg', '2019-07-03'),
(34, 'Insidious', 'Horror', 103, 'Pasangan suami istri berjuang menyelamatkan putra mereka yang koma dan terjebak di dimensi lain.', 'https://m.media-amazon.com/images/M/MV5BMTYyOTAxMDA0OF5BMl5BanBnXkFtZTcwNzYwMDczNQ@@._V1_SX300.jpg', '2010-09-14'),
(35, 'Sinister', 'Horror', 110, 'Seorang penulis true-crime menemukan kaset video pembunuhan misterius di loteng rumah barunya.', 'https://m.media-amazon.com/images/M/MV5BMjI5MjM0MjAwMV5BMl5BanBnXkFtZTcwMjcxMTU1OQ@@._V1_SX300.jpg', '2012-10-12'),
(36, 'The Babadook', 'Horror', 94, 'Seorang ibu tunggal dan anaknya diteror oleh makhluk misterius dari buku cerita anak-anak.', 'https://m.media-amazon.com/images/M/MV5BMTk3MzM4ODk1NV5BMl5BanBnXkFtZTgwODQxODg3MDE@._V1_SX300.jpg', '2014-05-22'),
(37, 'Scream', 'Horror', 111, 'Seorang pembunuh bertopeng misterius mengincar sekelompok remaja SMA di sebuah kota kecil.', 'https://m.media-amazon.com/images/M/MV5BMjA2NjU5MTg5Nl5BMl5BanBnXkFtZTgwOTkyODQzMTE@._V1_SX300.jpg', '1996-12-20'),
(38, 'The Cabin in the Woods', 'Horror', 95, 'Lima mahasiswa pergi berlibur ke kabin terpencil tanpa menyadari mereka diawasi oleh ilmuwan.', 'https://m.media-amazon.com/images/M/MV5BNTUyNzMzODQ1OV5BMl5BanBnXkFtZTcwMzUxMjcxNw@@._V1_SX300.jpg', '2012-04-13'),
(39, 'Evil Dead Rise', 'Horror', 96, 'Reuni dua saudari terganggu oleh kemunculan iblis purba yang merasuki penghuni apartemen.', 'https://m.media-amazon.com/images/M/MV5BMmZiN2VmMzEtZGY3OC00YjAxLWE1YTctYTJkY2Y2N2RjOTdkXkEyXkFqcGdeQXVyMTUzMTg2ODMz._V1_SX300.jpg', '2023-04-21'),
(40, 'Talk to Me', 'Horror', 95, 'Sekelompok remaja menemukan cara memanggil arwah menggunakan tangan keramik yang diawetkan.', 'https://m.media-amazon.com/images/M/MV5BMDE1OTJlYTMtNGZiMy00NDlkLWE2YjktNjYyZmNhYTNmZDlkXkEyXkFqcGdeQXVyMTU1MDM3NDk0._V1_SX300.jpg', '2023-07-28'),
(41, 'Mad Max: Fury Road', 'Action', 120, 'Di padang gurun post-apokaliptik, Max bekerja sama dengan Imperator Furiosa untuk melarikan diri dari gembong lalim.', 'https://m.media-amazon.com/images/M/MV5BN2EwM2I5OWMtMGQyYS00ZTk1LThjM2EtYTZkY2Y2N2RjOTdkXkEyXkFqcGdeQXVyMTM1MTE1NDAx._V1_SX300.jpg', '2015-05-15'),
(42, 'John Wick', 'Action', 101, 'Seorang mantan pembunuh bayaran ulung kembali beraksi demi membalas dendam atas kematian anjing mendiang istrinya.', 'https://m.media-amazon.com/images/M/MV5BMTU2NjA1ODgzMF5BMl5BanBnXkFtZTgwMTM2MTI4MjE@._V1_SX300.jpg', '2014-10-24'),
(43, 'John Wick: Chapter 4', 'Action', 169, 'John Wick mengungkap jalan untuk mengalahkan High Table demi mendapatkan kebebasan sepenuhnya.', 'https://m.media-amazon.com/images/M/MV5BMDExZGMyOTMtOTgyOS00NzQ0LWFmNTktYmUyYTJiY2Y2N2RjOTdkXkEyXkFqcGdeQXVyMTM1MTE1NDAx._V1_SX300.jpg', '2023-03-24'),
(44, 'The Dark Knight', 'Action', 152, 'Batman harus menghadapi ancaman psikologis dari dalang kriminal anarkis bernama Joker di Gotham City.', 'https://m.media-amazon.com/images/M/MV5BMTMxNTMwODM0NF5BMl5BanBnXkFtZTcwODAyMTk2Mw@@._V1_SX300.jpg', '2008-07-18'),
(45, 'Gladiator', 'Action', 155, 'Seorang jenderal Romawi yang dikhianati kembali ke Colosseum sebagai gladiator untuk membalas dendam.', 'https://m.media-amazon.com/images/M/MV5BMDliMmNhNDEtODUyOS00MjNlLTgxODEtN2U3NzIxMGVkZTA1XkEyXkFqcGdeQXVyNjU0OTQ0OTY@._V1_SX300.jpg', '2000-05-05'),
(46, 'Mission: Impossible - Fallout', 'Action', 147, 'Ethan Hunt dan tim IMF berpacu dengan waktu setelah misi penyelamatan plutonium mengalami kegagalan.', 'https://m.media-amazon.com/images/M/MV5BNjRlZmM0ODktY2RjNS00ZDdjLWJhZGYtNDljNWZkYjYyZjQ2XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '2018-07-27'),
(47, 'Top Gun: Maverick', 'Action', 130, 'Pete Mitchell kembali melatih lulusan elit Top Gun untuk misi berbahaya yang menuntut presisi tinggi.', 'https://m.media-amazon.com/images/M/MV5BZWYzOGEwNTgtNWU3NS00ZTQ0LWJkODUtMmVhMjIwMjA1ZmQwXkEyXkFqcGdeQXVyMTUwMjEzMzUz._V1_SX300.jpg', '2022-05-27'),
(48, 'Avengers: Endgame', 'Action', 181, 'Tim Avengers yang tersisa bersatu untuk membalikkan tindakan Thanos dan memulihkan kedamaian alam semesta.', 'https://m.media-amazon.com/images/M/MV5BMTc5MDE2ODcwNV5BMl5BanBnXkFtZTgwMzI2NzQ2NzM@._V1_SX300.jpg', '2019-04-26'),
(49, 'The Raid: Redemption', 'Action', 101, 'Pasukan khusus kepolisian terjebak di dalam gedung apartemen kumuh yang dikuasai gembong narkoba kejam.', 'https://m.media-amazon.com/images/M/MV5BMjA5OTg1MjY0NV5BMl5BanBnXkFtZTcwMjE0ODQ2Nw@@._V1_SX300.jpg', '2011-09-08'),
(50, 'The Raid 2: Berandal', 'Action', 150, 'Rama menyamar sebagai narapidana untuk menyusup dan membongkar sindikat kriminal korup di Jakarta.', 'https://m.media-amazon.com/images/M/MV5BODgzMjEwOTM5NV5BMl5BanBnXkFtZTgwOTcwMzQ0MTE@._V1_SX300.jpg', '2014-03-28'),
(51, 'Die Hard', 'Action', 132, 'Seorang detektif NYPD mencoba menyelamatkan sandera, termasuk istrinya, dari kelompok teroris di gedung pencakar langit.', 'https://m.media-amazon.com/images/M/MV5BZjU5OWIyNDAtNmIyOC00NTJjLTc1OWEtZTc3MmJkOWY3ZjQ2XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '1988-07-15'),
(52, 'Matrix', 'Action', 136, 'Seorang hacker komputer menyadari bahwa realitas dunia sehari-hari adalah simulasi buatan mesin.', 'https://m.media-amazon.com/images/M/MV5BNzQzOTk3OTAtNDQ0OS00Njg4LTk5N2EtNWVkYjYyZjQ2XkEyXkFqcGdeQXVyMTQxNzMzNDI@._V1_SX300.jpg', '1999-03-31'),
(53, 'Inception', 'Action', 148, 'Seorang pencuri ulung yang meretas mimpi orang lain diberi tugas menanamkan ide ke alam bawah sadar target.', 'https://m.media-amazon.com/images/M/MV5BMjAxMzY3NjcxNF5BMl5BanBnXkFtZTcwNTI5OTM0Mw@@._V1_SX300.jpg', '2010-07-16'),
(54, 'Casino Royale', 'Action', 144, 'Agen 007 James Bond harus mengalahkan seorang bankir teroris dalam turnamen poker taruhan tinggi.', 'https://m.media-amazon.com/images/M/MV5BMDI5ZWJhOWItYTlhOC00YWNhLTlkNzctNDc5YjYyZjQ2XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '2006-11-17'),
(55, 'Logan', 'Action', 137, 'Wolverine yang menua harus melindungi mutan muda bernama Laura dari kejaran pasukan berbahaya.', 'https://m.media-amazon.com/images/M/MV5BYzc5MTU4N2EtYTkyMi00NjhhLThjNTktMjY3N2Y2N2RjOTdkXkEyXkFqcGdeQXVyNTAyODkwOQ@@._V1_SX300.jpg', '2017-03-03'),
(56, 'Terminator 2: Judgment Day', 'Action', 137, 'Sebuah robot pelindung dikirim dari masa depan untuk melindungi Sarah Connor dan putranya dari mesin pembunuh.', 'https://m.media-amazon.com/images/M/MV5BMGU2NzRmZjUtOGUxYS00ZjdjLWEwZWItY2N2Y2N2RjOTdkXkEyXkFqcGdeQXVyMTQxNzMzNDI@._V1_SX300.jpg', '1991-07-03'),
(57, 'Spider-Man: Across the Spider-Verse', 'Action', 140, 'Miles Morales melintasi berbagai dimensi multiverse dan berhadapan dengan konflik para manusia laba-laba.', 'https://m.media-amazon.com/images/M/MV5BMzI0NmVkMjEtYmY4MS00ZDMxLTlkZmEtMzU0ZjQxYjYyZjQ2XkEyXkFqcGdeQXVyMTM1MTE1NDAx._V1_SX300.jpg', '2023-06-02'),
(58, 'Gladiator II', 'Action', 148, 'Lucius memasuki arena Colosseum setelah rumahnya ditaklukkan oleh kaisar tiran yang memimpin Roma.', 'https://m.media-amazon.com/images/M/MV5BN2EwM2I5OWMtMGQyYS00ZTk1LThjM2EtYTZkY2Y2N2RjOTdkXkEyXkFqcGdeQXVyMTM1MTE1NDAx._V1_SX300.jpg', '2024-11-22'),
(59, 'Bullet Train', 'Action', 126, 'Lima pembunuh bayaran mendapati misi mereka saling berkaitan di dalam kereta cepat berkecepatan tinggi.', 'https://m.media-amazon.com/images/M/MV5BMDU2ZmM2ODktY2RjNS00ZDdjLWJhZGYtNDljNWZkYjYyZjQ2XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '2022-08-05'),
(60, 'Fast Five', 'Action', 130, 'Dom Toretto dan krunya merencanakan perampokan besar di Brasil sambil menghindari kejaran agen federal.', 'https://m.media-amazon.com/images/M/MV5BMTMxNTMwODM0NF5BMl5BanBnXkFtZTcwODAyMTk2Mw@@._V1_SX300.jpg', '2011-04-29'),
(61, 'Lord of the Mysteries: Special / First Chapter', 'Animation', 120, 'Adaptasi animasi populer dari dunia era victoria yang penuh misteri, kekuatan gaib, dan intrik konspirasi.', 'https://m.media-amazon.com/images/M/MV5BMjA5OTg1MjY0NV5BMl5BanBnXkFtZTcwMjE0ODQ2Nw@@._V1_SX300.jpg', '2025-06-28'),
(62, 'Spider-Man: Into the Spider-Verse', 'Animation', 117, 'Miles Morales menjadi Spider-Man baru di dimensinya dan bekerja sama dengan pahlawan dari dunia lain.', 'https://m.media-amazon.com/images/M/MV5BMjMwNDkxMTgzOF5BMl5BanBnXkFtZTgwNTkwNTQ3NjM@._V1_SX300.jpg', '2018-12-14'),
(63, 'Spirited Away', 'Animation', 125, 'Chihiro tersesat ke dunia roh ajaib dan harus bekerja di pemandian umum demi menyelamatkan orang tuanya.', 'https://m.media-amazon.com/images/M/MV5BMjlmZmI5MDctNDE2YS00YWE0LWE5ZWItZDBhYWQ0NTcxNWRhXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '2001-07-20'),
(64, 'The Boy and the Heron', 'Animation', 124, 'Seorang anak laki-laki menemukan menara terbengkalai dan memasuki dunia fantastis bersama burung bangau abu-abu.', 'https://m.media-amazon.com/images/M/MV5BZjU5OWIyNDAtNmIyOC00NTJjLTc1OWEtZTc3MmJkOWY3ZjQ2XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '2023-07-14'),
(65, 'Your Name', 'Animation', 106, 'Dua remaja secara misterius bertukar tubuh dan mencoba mencari satu sama lain melintasi ruang dan waktu.', 'https://m.media-amazon.com/images/M/MV5BODRmZDVmNzUtZGY4Zi00OWFiLThlNDItNmZiMjYxNDIxZjcyXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '2016-08-26'),
(66, 'WALL-E', 'Animation', 98, 'Sebuah robot pembersih sampah kecil di bumi yang kosong tanpa sengaja memulai petualangan kosmik.', 'https://m.media-amazon.com/images/M/MV5BMjExMTg5OTU0NF5BMl5BanBnXkFtZTcwMjMxMzMzMw@@._V1_SX300.jpg', '2008-06-27'),
(67, 'Toy Story', 'Animation', 81, 'Mainan koboi cemburu ketika mainan astronot canggih baru datang dan merebut perhatian pemiliknya.', 'https://m.media-amazon.com/images/M/MV5BMDU2ZmM2ODktY2RjNS00ZDdjLWJhZGYtNDljNWZkYjYyZjQ2XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '1995-11-22'),
(68, 'Toy Story 3', 'Animation', 103, 'Andy bersiap pergi ke universitas, membuat para mainan tak sengaja berakhir di tempat penampungan penitipan anak.', 'https://m.media-amazon.com/images/M/MV5BMTgxOTY4Mjc0MF5BMl5BanBnXkFtZTcwNTA4MDQyMw@@._V1_SX300.jpg', '2010-06-18'),
(69, 'Up', 'Animation', 96, 'Seorang kakek pemarah menerbangkan rumahnya menggunakan ribuan balon untuk memenuhi janji mendiang istrinya.', 'https://m.media-amazon.com/images/M/MV5BMTk3NDE2NzI4NF5BMl5BanBnXkFtZTgwNzE1MzEyMTE@._V1_SX300.jpg', '2009-05-29'),
(70, 'Coco', 'Animation', 105, 'Seorang anak laki-laki pecinta musik secara tidak sengaja masuk ke Land of the Dead demi mencari leluhurnya.', 'https://m.media-amazon.com/images/M/MV5BYjQ5NjM0Y2YtNjZkNC00ZDhkLWJjMWItN2QyNzFkMDJjY2YyXkEyXkFqcGdeQXVyMTM1MTE1NDAx._V1_SX300.jpg', '2017-11-22'),
(71, 'Inside Out', 'Animation', 95, 'Emosi dalam pikiran seorang gadis muda mengalami kekacauan saat keluarganya harus pindah ke kota baru.', 'https://m.media-amazon.com/images/M/MV5BOTgxMDQwMDk0OV5BMl5BanBnXkFtZTgwNzU5MTQ2NDE@._V1_SX300.jpg', '2015-06-19'),
(72, 'Inside Out 2', 'Animation', 96, 'Memori remaja Riley kedatangan emosi-emosi baru yang kompleks seperti Ansietas, Envy, dan Ennui.', 'https://m.media-amazon.com/images/M/MV5BY2NkZmM2ODktY2RjNS00ZDdjLWJhZGYtNDljNWZkYjYyZjQ2XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '2024-06-14'),
(73, 'Princess Mononoke', 'Animation', 134, 'Seorang pangeran muda terlibat dalam pertarungan sengit antara dewa hutan dan koloni penambang manusia.', 'https://m.media-amazon.com/images/M/MV5BNGIzY2JiODktMWJhMC00MzhlLThjMGYtZjVkNWY2ZjQ2XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '1997-07-12'),
(74, 'Howl\'s Moving Castle', 'Animation', 119, 'Seorang gadis muda yang dikutuk menjadi tua mencari perlindungan di istana berjalan milik penyihir eksentrik.', 'https://m.media-amazon.com/images/M/MV5BNGIzY2JiODktMWJhMC00MzhlLThjMGYtZjVkNWY2ZjQ2XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '2004-11-20'),
(75, 'Finding Nemo', 'Animation', 100, 'Seekor ikan badut penakut melintasi lautan luas demi mencari putranya yang diculik penyelam.', 'https://m.media-amazon.com/images/M/MV5BZTAzNWZlYWMtZjQ2ZS00ODk3LWE3ZDktOTc0MjEzMw@@._V1_SX300.jpg', '2003-05-30'),
(76, 'The Lion King', 'Animation', 88, 'Seekor singa muda pangeran hutan melarikan diri setelah tragedi keluarganya, lalu kembali merebut takhta.', 'https://m.media-amazon.com/images/M/MV5BY2UyODc3OGItZDNhZi00NDliLThmNzctYTMzZWY4NTg2MjBkXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '1994-06-24'),
(77, 'Demon Slayer: Kimetsu no Yaiba - The Movie: Mugen Train', 'Animation', 117, 'Tanjiro dan kawan-kawan membantu Hashira Api menyelidiki kasus hilangnya penumpang di dalam kereta.', 'https://m.media-amazon.com/images/M/MV5BZjU5OWIyNDAtNmIyOC00NTJjLTc1OWEtZTc3MmJkOWY3ZjQ2XkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '2020-10-16'),
(78, 'The Incredibles', 'Animation', 115, 'Sebuah keluarga mantan pahlawan super terpaksa kembali beraksi untuk menyelamatkan dunia.', 'https://m.media-amazon.com/images/M/MV5BMTY5OTU0N2EtYTkyMi00NjhhLThjNTktMjY3N2Y2N2RjOTdkXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '2004-11-05'),
(79, 'Kung Fu Panda', 'Animation', 92, 'Seekor panda gemuk dan kikuk yang menyukai seni bela diri tak terduga terpilih sebagai Pendekar Naga.', 'https://m.media-amazon.com/images/M/MV5BODJkZTZhMWItMDI3MS00ZjM5LThhMDktYTlhNWY4NTYzN2YyXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg', '2008-06-06'),
(80, 'Zootopia', 'Animation', 108, 'Seekor kelinci polisi optimis dan rubah penipu harus bekerja sama memecahkan kasus konspirasi di kota hewan.', 'https://m.media-amazon.com/images/M/MV5BOTMyMjEyNzIzMV5BMl5BanBnXkFtZTgwNzIyNjU0OTE@._V1_SX300.jpg', '2016-03-04');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','cancelled') NOT NULL DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `payment_method`, `amount`, `status`, `paid_at`, `created_at`) VALUES
(1, 1, 'e_wallet', 160000.00, 'paid', '2026-09-30 06:55:14', '2026-10-05 13:23:03');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category` enum('food','beverage','combo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`, `category`) VALUES
(1, 'cireng', 50000.00, '', 'food'),
(2, 'Classic Salted Popcorn (Regular)', 35000.00, 'https://m.media-amazon.com/images/I/71XmU68wWOL._SX679_.jpg', 'food'),
(3, 'Classic Salted Popcorn (Large)', 50000.00, 'https://m.media-amazon.com/images/I/71XmU68wWOL._SX679_.jpg', 'food'),
(4, 'Caramel Popcorn (Regular)', 40000.00, 'https://m.media-amazon.com/images/I/81k3yW5kL+L._SX679_.jpg', 'food'),
(5, 'Caramel Popcorn (Large)', 55000.00, 'https://m.media-amazon.com/images/I/81k3yW5kL+L._SX679_.jpg', 'food'),
(6, 'French Fries', 30000.00, 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=500&q=80', 'food'),
(7, 'Sausage Roll', 32000.00, 'https://images.unsplash.com/photo-1627308595229-7830a5c91f9f?auto=format&fit=crop&w=500&q=80', 'food'),
(8, 'Nachos with Cheese Dip', 45000.00, 'https://images.unsplash.com/photo-1513456852971-30c0b8199d4d?auto=format&fit=crop&w=500&q=80', 'food'),
(9, 'Churros with Chocolate Sauce', 38000.00, 'https://images.unsplash.com/photo-1624371414361-e670ff6d6fca?auto=format&fit=crop&w=500&q=80', 'food'),
(10, 'Coca-Cola (Regular)', 25000.00, 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?auto=format&fit=crop&w=500&q=80', 'beverage'),
(11, 'Coca-Cola (Large)', 35000.00, 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?auto=format&fit=crop&w=500&q=80', 'beverage'),
(12, 'Sprite (Regular)', 25000.00, 'https://images.unsplash.com/photo-1625772299848-391b6a87d7b3?auto=format&fit=crop&w=500&q=80', 'beverage'),
(13, 'Fanta Strawberry (Regular)', 25000.00, 'https://images.unsplash.com/photo-1624552184280-9e9632bbeee9?auto=format&fit=crop&w=500&q=80', 'beverage'),
(14, 'Mineral Water 600ml', 15000.00, 'https://images.unsplash.com/photo-1548839140-29a749e1cf4d?auto=format&fit=crop&w=500&q=80', 'beverage'),
(15, 'Iced Lemon Tea', 28000.00, 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=500&q=80', 'beverage'),
(16, 'Sweet Green Tea', 28000.00, 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?auto=format&fit=crop&w=500&q=80', 'beverage'),
(17, 'Combo Couple (1 Large Popcorn + 2 Coca-Cola Large)', 105000.00, 'https://images.unsplash.com/photo-1585647347385-257981095f84?auto=format&fit=crop&w=500&q=80', 'combo'),
(18, 'Combo Solo (1 Regular Popcorn + 1 Mineral Water)', 45000.00, 'https://images.unsplash.com/photo-1578849278619-e73505e9610f?auto=format&fit=crop&w=500&q=80', 'combo'),
(19, 'Combo Movie Night (1 Large Caramel Popcorn + 2 Iced Lemon Tea + 1 French Fries)', 115000.00, 'https://images.unsplash.com/photo-1527960669563-bbf725c1c74d?auto=format&fit=crop&w=500&q=80', 'combo'),
(20, 'Combo Snack Box (Nachos + Sausage Roll + 2 Coca-Cola Regular)', 95000.00, 'https://images.unsplash.com/photo-1541592106381-b31e9677c0e5?auto=format&fit=crop&w=500&q=80', 'combo'),
(21, 'Combo Ultimate (2 Large Popcorn Mixed + 4 Coca-Cola Large + 1 Nachos)', 190000.00, 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?auto=format&fit=crop&w=500&q=80', 'combo');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `studio_id` int(11) NOT NULL,
  `show_date` date NOT NULL,
  `show_time` time NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `movie_id`, `studio_id`, `show_date`, `show_time`, `price`) VALUES
(1, 1, 1, '2026-10-01', '13:00:00', 50000.00),
(2, 1, 1, '2026-10-01', '17:00:00', 65000.00),
(3, 1, 2, '2026-10-01', '20:30:00', 65000.00),
(4, 4, 2, '2026-10-01', '12:30:00', 45000.00),
(5, 4, 3, '2026-10-01', '15:45:00', 75000.00),
(6, 21, 3, '2026-10-01', '14:00:00', 55000.00),
(7, 21, 4, '2026-10-01', '18:15:00', 60000.00),
(8, 21, 5, '2026-10-01', '21:00:00', 60000.00),
(9, 22, 5, '2026-10-01', '13:30:00', 45000.00),
(10, 22, 6, '2026-10-01', '16:30:00', 50000.00),
(11, 61, 7, '2026-10-01', '13:15:00', 70000.00),
(12, 61, 7, '2026-10-01', '16:00:00', 85000.00),
(13, 61, 8, '2026-10-01', '19:00:00', 85000.00),
(14, 41, 8, '2026-10-01', '14:30:00', 50000.00),
(15, 41, 9, '2026-10-01', '17:30:00', 60000.00),
(16, 57, 10, '2026-10-01', '12:00:00', 45000.00),
(17, 57, 10, '2026-10-01', '15:15:00', 60000.00),
(18, 57, 1, '2026-10-01', '20:00:00', 75000.00),
(19, 1, 1, '2026-10-06', '10:30:00', 40000.00),
(20, 4, 2, '2026-10-06', '12:30:00', 45000.00),
(21, 21, 3, '2026-10-06', '14:30:00', 50000.00),
(22, 22, 4, '2026-10-06', '16:30:00', 50000.00),
(23, 41, 5, '2026-10-06', '18:00:00', 55000.00),
(24, 44, 6, '2026-10-06', '19:30:00', 60000.00),
(25, 57, 7, '2026-10-06', '21:00:00', 65000.00),
(26, 61, 8, '2026-10-06', '11:15:00', 45000.00),
(27, 63, 9, '2026-10-06', '15:15:00', 50000.00),
(28, 72, 10, '2026-10-06', '20:15:00', 60000.00),
(29, 1, 1, '2026-10-07', '10:30:00', 40000.00),
(30, 4, 2, '2026-10-07', '12:30:00', 45000.00),
(31, 21, 3, '2026-10-07', '14:30:00', 50000.00),
(32, 22, 4, '2026-10-07', '16:30:00', 50000.00),
(33, 41, 5, '2026-10-07', '18:00:00', 55000.00),
(34, 44, 6, '2026-10-07', '19:30:00', 60000.00),
(35, 57, 7, '2026-10-07', '21:00:00', 65000.00),
(36, 61, 8, '2026-10-07', '11:15:00', 45000.00),
(37, 63, 9, '2026-10-07', '15:15:00', 50000.00),
(38, 72, 10, '2026-10-07', '20:15:00', 60000.00),
(39, 1, 1, '2026-10-08', '10:30:00', 40000.00),
(40, 4, 2, '2026-10-08', '12:30:00', 45000.00),
(41, 21, 3, '2026-10-08', '14:30:00', 50000.00),
(42, 22, 4, '2026-10-08', '16:30:00', 50000.00),
(43, 41, 5, '2026-10-08', '18:00:00', 55000.00),
(44, 44, 6, '2026-10-08', '19:30:00', 60000.00),
(45, 57, 7, '2026-10-08', '21:00:00', 65000.00),
(46, 61, 8, '2026-10-08', '11:15:00', 45000.00),
(47, 63, 9, '2026-10-08', '15:15:00', 50000.00),
(48, 72, 10, '2026-10-08', '20:15:00', 60000.00),
(49, 1, 1, '2026-10-09', '10:30:00', 40000.00),
(50, 4, 2, '2026-10-09', '12:30:00', 45000.00),
(51, 21, 3, '2026-10-09', '14:30:00', 50000.00),
(52, 22, 4, '2026-10-09', '16:30:00', 50000.00),
(53, 41, 5, '2026-10-09', '18:00:00', 55000.00),
(54, 44, 6, '2026-10-09', '19:30:00', 60000.00),
(55, 57, 7, '2026-10-09', '21:00:00', 65000.00),
(56, 61, 8, '2026-10-09', '11:15:00', 45000.00),
(57, 63, 9, '2026-10-09', '15:15:00', 50000.00),
(58, 72, 10, '2026-10-09', '20:15:00', 60000.00),
(59, 1, 1, '2026-10-10', '10:30:00', 40000.00),
(60, 4, 2, '2026-10-10', '12:30:00', 45000.00),
(61, 21, 3, '2026-10-10', '14:30:00', 50000.00),
(62, 22, 4, '2026-10-10', '16:30:00', 50000.00),
(63, 41, 5, '2026-10-10', '18:00:00', 55000.00),
(64, 44, 6, '2026-10-10', '19:30:00', 60000.00),
(65, 57, 7, '2026-10-10', '21:00:00', 65000.00),
(66, 61, 8, '2026-10-10', '11:15:00', 45000.00),
(67, 63, 9, '2026-10-10', '15:15:00', 50000.00),
(68, 72, 10, '2026-10-10', '20:15:00', 60000.00),
(69, 1, 1, '2026-10-11', '10:30:00', 40000.00),
(70, 4, 2, '2026-10-11', '12:30:00', 45000.00),
(71, 21, 3, '2026-10-11', '14:30:00', 50000.00),
(72, 22, 4, '2026-10-11', '16:30:00', 50000.00),
(73, 41, 5, '2026-10-11', '18:00:00', 55000.00),
(74, 44, 6, '2026-10-11', '19:30:00', 60000.00),
(75, 57, 7, '2026-10-11', '21:00:00', 65000.00),
(76, 61, 8, '2026-10-11', '11:15:00', 45000.00),
(77, 63, 9, '2026-10-11', '15:15:00', 50000.00),
(78, 72, 10, '2026-10-11', '20:15:00', 60000.00),
(79, 1, 1, '2026-10-12', '10:30:00', 40000.00),
(80, 4, 2, '2026-10-12', '12:30:00', 45000.00),
(81, 21, 3, '2026-10-12', '14:30:00', 50000.00),
(82, 22, 4, '2026-10-12', '16:30:00', 50000.00),
(83, 41, 5, '2026-10-12', '18:00:00', 55000.00),
(84, 44, 6, '2026-10-12', '19:30:00', 60000.00),
(85, 57, 7, '2026-10-12', '21:00:00', 65000.00),
(86, 61, 8, '2026-10-12', '11:15:00', 45000.00),
(87, 63, 9, '2026-10-12', '15:15:00', 50000.00),
(88, 72, 10, '2026-10-12', '20:15:00', 60000.00);

-- --------------------------------------------------------

--
-- Table structure for table `studios`
--

CREATE TABLE `studios` (
  `id` int(11) NOT NULL,
  `cinema_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studios`
--

INSERT INTO `studios` (`id`, `cinema_id`, `name`, `capacity`) VALUES
(1, 1, 'Studio 1 (IMAX)', 50),
(2, 1, 'Studio 2', 40),
(3, 2, 'Studio 1 (Premiere)', 30),
(4, 2, 'Studio 2', 40),
(5, 3, 'Studio 1', 40),
(6, 3, 'Studio 2', 50),
(7, 4, 'Studio 1 (IMAX)', 50),
(8, 4, 'Studio 2', 40),
(9, 5, 'Studio 1', 40),
(10, 5, 'Studio 2', 40);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','client') DEFAULT 'client',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Super Administrator', 'admin@gmail.com', '$2y$12$JjNFEthQJan6HZ1q/LkMHum1k4YT/s5Cz2u119Yb/guGR/J1Q7So.', 'admin', '2026-09-30 06:01:42'),
(2, 'Kasir Staff Loket', 'staf@gmail.com', '$2y$12$70o0e0T6S3sOzS2z0xSYJuJVeBXzB8vQxAGB91/MOrfnfTQqu5y.e', 'staff', '2026-09-30 06:01:42'),
(3, 'Budi Pelanggan', 'budi@client.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', '2026-09-30 06:01:42'),
(4, 'Siti Klien', 'siti@client.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', '2026-09-30 06:01:42'),
(5, 'Kurnia Eka Pratiwi', 'akkasia818@gmail.com', '$2y$10$YvV/nHGzcxolCBD6IBEu2.iMh7/Ep2x5xrEFE1HOcRRm2nCmswvIC', 'client', '2026-09-30 06:30:40'),
(6, 'pa fitri', 'fitri@gmail.com', '$2y$10$/dpNQYwQOC8J/nqNzrytsel.L61ZSR3fU5Cz85ag1o4Hg9880lrxa', 'client', '2026-09-30 07:05:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `booking_products`
--
ALTER TABLE `booking_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `booking_seats`
--
ALTER TABLE `booking_seats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `cinemas`
--
ALTER TABLE `cinemas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `studio_id` (`studio_id`);

--
-- Indexes for table `studios`
--
ALTER TABLE `studios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cinema_id` (`cinema_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `booking_products`
--
ALTER TABLE `booking_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `booking_seats`
--
ALTER TABLE `booking_seats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cinemas`
--
ALTER TABLE `cinemas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `studios`
--
ALTER TABLE `studios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_products`
--
ALTER TABLE `booking_products`
  ADD CONSTRAINT `booking_products_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_seats`
--
ALTER TABLE `booking_seats`
  ADD CONSTRAINT `booking_seats_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`studio_id`) REFERENCES `studios` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `studios`
--
ALTER TABLE `studios`
  ADD CONSTRAINT `studios_ibfk_1` FOREIGN KEY (`cinema_id`) REFERENCES `cinemas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
