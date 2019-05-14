-- Adminer 4.7.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `condicao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `regra` int(11) NOT NULL,
  `variavel` int(11) NOT NULL,
  `valor` varchar(100) DEFAULT NULL,
  `op` varchar(2) NOT NULL,
  `pai` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `regra` (`regra`),
  KEY `variavel` (`variavel`),
  KEY `pai` (`pai`),
  CONSTRAINT `paiFKcondicao` FOREIGN KEY (`pai`) REFERENCES `condicao` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `regraFKcondicao` FOREIGN KEY (`regra`) REFERENCES `regra` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `variavelFKcondicao` FOREIGN KEY (`variavel`) REFERENCES `variavel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT INTO `condicao` (`id`, `regra`, `variavel`, `valor`, `op`, `pai`) VALUES
(1,	1,	2,	'SIM',	'=',	NULL),
(2,	2,	2,	'NÃO',	'=',	NULL),
(3,	3,	3,	'SIM',	'=',	NULL),
(4,	4,	3,	'NÃO',	'=',	NULL),
(5,	5,	4,	'SIM',	'=',	NULL),
(6,	6,	4,	'NÃO',	'=',	NULL);

CREATE TABLE `consequencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `regra` int(11) NOT NULL,
  `variavel` int(11) NOT NULL,
  `valor` varchar(100) DEFAULT NULL,
  `certeza` float NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `regra` (`regra`),
  KEY `variavel` (`variavel`),
  CONSTRAINT `regraFKconsequencia` FOREIGN KEY (`regra`) REFERENCES `regra` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `variavelFKconsequencia` FOREIGN KEY (`variavel`) REFERENCES `variavel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT INTO `consequencia` (`id`, `regra`, `variavel`, `valor`, `certeza`) VALUES
(1,	1,	1,	'2',	1),
(2,	2,	1,	'1',	1),
(3,	3,	2,	'3',	1),
(4,	4,	2,	'4',	1),
(5,	5,	1,	'2',	1),
(6,	6,	1,	'1',	1);

CREATE TABLE `opcao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variavel` int(11) NOT NULL,
  `valor` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `variavel` (`variavel`),
  CONSTRAINT `variavelFKopcao` FOREIGN KEY (`variavel`) REFERENCES `variavel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

INSERT INTO `opcao` (`id`, `variavel`, `valor`) VALUES
(1,	1,	'SIM'),
(2,	1,	'NÃO'),
(3,	2,	'SIM'),
(4,	2,	'NÃO'),
(5,	3,	'SIM'),
(6,	3,	'NÃO'),
(7,	4,	'SIM'),
(8,	4,	'NÃO');

CREATE TABLE `regra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) DEFAULT NULL,
  `ordem` int(11) NOT NULL,
  `sistema` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sistema` (`sistema`),
  CONSTRAINT `sistemaFKregra` FOREIGN KEY (`sistema`) REFERENCES `sistema` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT INTO `regra` (`id`, `nome`, `ordem`, `sistema`) VALUES
(1,	'chove',	1,	1),
(2,	'choveNao',	2,	1),
(3,	'previsão',	3,	1),
(4,	'previsãoNao',	4,	1),
(5,	'namorada',	5,	1),
(6,	'namoradaNão',	6,	1);

CREATE TABLE `sistema` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text NOT NULL,
  `usuario` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario` (`usuario`),
  CONSTRAINT `usuarioFK` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `sistema` (`id`, `nome`, `descricao`, `usuario`, `datetime`) VALUES
(1,	'IrPraia',	'',	1,	'2019-05-13 15:00:00');

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(30) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `nome` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `usuario` (`id`, `login`, `senha`, `nome`) VALUES
(1,	'matias',	'$1$Oj..9U0.$CTAGpwqJabr5WDLKUhe0F1',	'Matias'),
(2,	'evandro',	'123',	'Evandro');

CREATE TABLE `variavel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sistema` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `tipo` varchar(8) CHARACTER SET utf32 NOT NULL,
  `pergunta` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `objetivo` tinyint(1) NOT NULL DEFAULT 0,
  `questionavel` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `sistema` (`sistema`),
  CONSTRAINT `sistemaFK` FOREIGN KEY (`sistema`) REFERENCES `sistema` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO `variavel` (`id`, `sistema`, `nome`, `tipo`, `pergunta`, `descricao`, `objetivo`, `questionavel`) VALUES
(1,	1,	'ir',	'OPCAO',	NULL,	NULL,	1,	0),
(2,	1,	'chove',	'OPCAO',	NULL,	NULL,	0,	0),
(3,	1,	'previsão',	'OPCAO',	NULL,	NULL,	0,	1),
(4,	1,	'namorada',	'OPCAO',	NULL,	NULL,	0,	1);