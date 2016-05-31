-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 31-Maio-2016 às 14:43
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
-- Estrutura da tabela `condicao`
--

CREATE TABLE IF NOT EXISTS `condicao` (
`id` int(11) NOT NULL,
  `regra` int(11) NOT NULL,
  `variavel` int(11) NOT NULL,
  `valor` varchar(100) DEFAULT NULL,
  `op` varchar(2) NOT NULL,
  `pai` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `consequencia`
--

CREATE TABLE IF NOT EXISTS `consequencia` (
`id` int(11) NOT NULL,
  `regra` int(11) NOT NULL,
  `variavel` int(11) NOT NULL,
  `valor` varchar(100) DEFAULT NULL,
  `certeza` float NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `opcao`
--

CREATE TABLE IF NOT EXISTS `opcao` (
`id` int(11) NOT NULL,
  `variavel` int(11) NOT NULL,
  `valor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `regra`
--

CREATE TABLE IF NOT EXISTS `regra` (
`id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `ordem` int(11) NOT NULL,
  `sistema` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `sistema`
--

CREATE TABLE IF NOT EXISTS `sistema` (
`id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
`id` int(11) NOT NULL,
  `login` varchar(30) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `nome` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `variavel`
--

CREATE TABLE IF NOT EXISTS `variavel` (
`id` int(11) NOT NULL,
  `sistema` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `tipo` varchar(8) NOT NULL,
  `pergunta` varchar(255) DEFAULT NULL,
  `descricao` text,
  `objetivo` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `condicao`
--
ALTER TABLE `condicao`
 ADD PRIMARY KEY (`id`), ADD KEY `regra` (`regra`), ADD KEY `variavel` (`variavel`), ADD KEY `pai` (`pai`);

--
-- Indexes for table `consequencia`
--
ALTER TABLE `consequencia`
 ADD PRIMARY KEY (`id`), ADD KEY `regra` (`regra`), ADD KEY `variavel` (`variavel`);

--
-- Indexes for table `opcao`
--
ALTER TABLE `opcao`
 ADD PRIMARY KEY (`id`), ADD KEY `variavel` (`variavel`);

--
-- Indexes for table `regra`
--
ALTER TABLE `regra`
 ADD PRIMARY KEY (`id`), ADD KEY `sistema` (`sistema`);

--
-- Indexes for table `sistema`
--
ALTER TABLE `sistema`
 ADD PRIMARY KEY (`id`), ADD KEY `usuario` (`usuario`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `variavel`
--
ALTER TABLE `variavel`
 ADD PRIMARY KEY (`id`), ADD KEY `sistema` (`sistema`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `condicao`
--
ALTER TABLE `condicao`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `consequencia`
--
ALTER TABLE `consequencia`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `opcao`
--
ALTER TABLE `opcao`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `regra`
--
ALTER TABLE `regra`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sistema`
--
ALTER TABLE `sistema`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `variavel`
--
ALTER TABLE `variavel`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `condicao`
--
ALTER TABLE `condicao`
ADD CONSTRAINT `paiFKcondicao` FOREIGN KEY (`pai`) REFERENCES `condicao` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `regraFKcondicao` FOREIGN KEY (`regra`) REFERENCES `regra` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `variavelFKcondicao` FOREIGN KEY (`variavel`) REFERENCES `variavel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `consequencia`
--
ALTER TABLE `consequencia`
ADD CONSTRAINT `regraFKconsequencia` FOREIGN KEY (`regra`) REFERENCES `regra` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `variavelFKconsequencia` FOREIGN KEY (`variavel`) REFERENCES `variavel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `opcao`
--
ALTER TABLE `opcao`
ADD CONSTRAINT `variavelFKopcao` FOREIGN KEY (`variavel`) REFERENCES `variavel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `regra`
--
ALTER TABLE `regra`
ADD CONSTRAINT `sistemaFKregra` FOREIGN KEY (`sistema`) REFERENCES `sistema` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `sistema`
--
ALTER TABLE `sistema`
ADD CONSTRAINT `usuarioFK` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limitadores para a tabela `variavel`
--
ALTER TABLE `variavel`
ADD CONSTRAINT `sistemaFK` FOREIGN KEY (`sistema`) REFERENCES `sistema` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
