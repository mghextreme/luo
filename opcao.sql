-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 14-Jun-2016 às 12:40
-- Versão do servidor: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `luo`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `opcao`
--

CREATE TABLE IF NOT EXISTS `opcao` (
`id` int(11) NOT NULL,
  `variavel` int(11) NOT NULL,
  `valor` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `opcao`
--

INSERT INTO `opcao` (`id`, `variavel`, `valor`) VALUES
(1, 1, 'SIM'),
(2, 1, 'NÃO'),
(3, 2, 'SIM'),
(4, 2, 'NÃO'),
(5, 3, 'SIM'),
(6, 3, 'NÃO'),
(7, 4, 'SIM'),
(8, 4, 'NÃO');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `opcao`
--
ALTER TABLE `opcao`
 ADD PRIMARY KEY (`id`), ADD KEY `variavel` (`variavel`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `opcao`
--
ALTER TABLE `opcao`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `opcao`
--
ALTER TABLE `opcao`
ADD CONSTRAINT `variavelFKopcao` FOREIGN KEY (`variavel`) REFERENCES `variavel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
