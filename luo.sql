-- Adminer 4.7.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `condicao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `regra` int(11) NOT NULL,
  `variavel` int(11) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

INSERT INTO `condicao` (`id`, `regra`, `variavel`, `valor`, `op`, `pai`) VALUES
(1,	1,	2,	'3',	'=',	NULL),
(2,	2,	2,	'4',	'=',	NULL),
(3,	3,	3,	'5',	'=',	NULL),
(4,	4,	3,	'6',	'=',	NULL),
(5,	5,	4,	'7',	'=',	NULL),
(6,	6,	4,	'8',	'=',	NULL),
(7,	7,	NULL,	NULL,	'||',	NULL),
(8,	7,	6,	'11',	'=',	7),
(9,	7,	8,	'15',	'=',	7),
(10,	8,	NULL,	NULL,	'&&',	NULL),
(11,	8,	6,	'12',	'=',	10),
(12,	8,	8,	'16',	'=',	10),
(13,	9,	7,	'13',	'=',	NULL),
(14,	10,	7,	'14',	'=',	NULL),
(15,	11,	NULL,	NULL,	'&&',	NULL),
(16,	11,	9,	'0',	'=',	15),
(17,	11,	15,	'1',	'=',	15),
(18,	11,	16,	'1',	'=',	15),
(19,	12,	NULL,	NULL,	'&&',	NULL),
(20,	12,	9,	'1',	'=',	19),
(21,	12,	15,	'0',	'=',	19),
(22,	12,	16,	'0',	'=',	19),
(23,	13,	11,	'1',	'=',	NULL),
(24,	14,	11,	'0',	'=',	NULL),
(25,	15,	NULL,	NULL,	'&&',	NULL),
(26,	15,	17,	'0',	'=',	25),
(27,	15,	13,	'0',	'=',	25),
(28,	16,	NULL,	NULL,	'||',	NULL),
(29,	16,	17,	'1',	'=',	28),
(30,	16,	13,	'1',	'=',	28),
(31,	17,	12,	'1',	'=',	NULL),
(32,	18,	12,	'0',	'=',	NULL),
(33,	19,	14,	'1',	'=',	NULL),
(34,	20,	14,	'0',	'=',	NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

INSERT INTO `consequencia` (`id`, `regra`, `variavel`, `valor`, `certeza`) VALUES
(1,	1,	1,	'2',	1),
(2,	2,	1,	'1',	1),
(3,	3,	2,	'3',	1),
(4,	4,	2,	'4',	1),
(5,	5,	1,	'2',	1),
(6,	6,	1,	'1',	1),
(7,	7,	5,	'10',	1),
(8,	8,	5,	'9',	1),
(9,	9,	6,	'11',	1),
(10,	10,	6,	'12',	1),
(11,	11,	10,	'1',	1),
(12,	12,	10,	'1',	1),
(13,	13,	9,	'0',	0),
(14,	14,	9,	'1',	0),
(15,	15,	15,	'1',	0),
(16,	16,	15,	'0',	0),
(17,	17,	17,	'1',	1),
(18,	18,	17,	'0',	0),
(19,	19,	16,	'0',	0),
(20,	20,	16,	'1',	0);

CREATE TABLE `opcao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variavel` int(11) NOT NULL,
  `valor` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `variavel` (`variavel`),
  CONSTRAINT `variavelFKopcao` FOREIGN KEY (`variavel`) REFERENCES `variavel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

INSERT INTO `opcao` (`id`, `variavel`, `valor`) VALUES
(1,	1,	'SIM'),
(2,	1,	'NÃO'),
(3,	2,	'SIM'),
(4,	2,	'NÃO'),
(5,	3,	'SIM'),
(6,	3,	'NÃO'),
(7,	4,	'SIM'),
(8,	4,	'NÃO'),
(9,	5,	'SIM'),
(10,	5,	'NÃO'),
(11,	6,	'SIM'),
(12,	6,	'NÃO'),
(13,	7,	'SIM'),
(14,	7,	'NÃO'),
(15,	8,	'SIM'),
(16,	8,	'NÃO');

CREATE TABLE `regra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) DEFAULT NULL,
  `ordem` int(11) NOT NULL,
  `sistema` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sistema` (`sistema`),
  CONSTRAINT `sistemaFKregra` FOREIGN KEY (`sistema`) REFERENCES `sistema` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

INSERT INTO `regra` (`id`, `nome`, `ordem`, `sistema`) VALUES
(1,	'chove',	1,	1),
(2,	'choveNao',	2,	1),
(3,	'previsão',	3,	1),
(4,	'previsãoNao',	4,	1),
(5,	'namorada',	5,	1),
(6,	'namoradaNão',	6,	1),
(7,	'Não vai',	1,	2),
(8,	'Vai',	2,	2),
(9,	'previsão',	3,	2),
(10,	'previsãoNao',	4,	2),
(11,	'R1',	1,	3),
(12,	'R2',	2,	3),
(13,	'R3',	3,	3),
(14,	'R3.2',	4,	3),
(15,	'R4',	5,	3),
(16,	'R4.2',	6,	3),
(17,	'R5',	7,	3),
(18,	'R5.2',	8,	3),
(19,	'R6',	9,	3),
(20,	'R6.2',	10,	3);

CREATE TABLE `sistema` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `usuario` int(11) NOT NULL,
  `datetime` datetime DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario` (`usuario`),
  CONSTRAINT `usuarioFK` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `sistema` (`id`, `nome`, `usuario`, `datetime`, `descricao`) VALUES
(1,	'IrPraia',	1,	NULL,	'Aqui está um descrição em Latim enquanto o sistema não está pronto:\r\n\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus eget metus egestas, porttitor odio vitae, pellentesque massa. Aenean a urna mattis tortor vestibulum hendrerit at at erat. In vel leo mollis dui suscipit dignissim.\r\n\r\nNunc bibendum, lorem nec feugiat gravida, libero augue dictum arcu, vel tincidunt sem neque eget nibh. Donec mollis nisi at elit malesuada, sed iaculis urna ornare. Fusce quis arcu in felis aliquam iaculis quis a urna. Praesent at augue sem.\r\n\r\nSuspendisse potenti. Vestibulum sit amet diam aliquam, scelerisque quam quis, auctor leo. Curabitur consequat lorem ullamcorper tortor pharetra egestas. '),
(2,	'IrPraiaLogico',	1,	'2016-06-21 08:02:37',	NULL),
(3,	'IrPraia - versão ExpertSinta',	1,	'2016-06-22 07:59:46',	NULL);

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(30) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `nome` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `usuario` (`id`, `login`, `senha`, `nome`) VALUES
(1,	'matias',	'$1$Oj..9U0.$CTAGpwqJabr5WDLKUhe0F1',	'Matias G H');

CREATE TABLE `variavel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sistema` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `tipo` varchar(8) NOT NULL,
  `pergunta` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `objetivo` tinyint(1) NOT NULL DEFAULT 0,
  `questionavel` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `sistema` (`sistema`),
  CONSTRAINT `sistemaFK` FOREIGN KEY (`sistema`) REFERENCES `sistema` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

INSERT INTO `variavel` (`id`, `sistema`, `nome`, `tipo`, `pergunta`, `descricao`, `objetivo`, `questionavel`) VALUES
(1,	1,	'ir',	'OPCAO',	NULL,	NULL,	1,	0),
(2,	1,	'chove',	'OPCAO',	NULL,	NULL,	0,	0),
(3,	1,	'previsão',	'OPCAO',	'A previsão disse que hoje chove',	NULL,	0,	1),
(4,	1,	'namorada',	'OPCAO',	'A namorada ligou',	NULL,	0,	1),
(5,	2,	'ir',	'OPCAO',	'',	'',	1,	0),
(6,	2,	'chove',	'OPCAO',	'',	'',	0,	0),
(7,	2,	'previsão',	'OPCAO',	'',	'',	0,	1),
(8,	2,	'namorada',	'OPCAO',	'',	'',	0,	1),
(9,	3,	'Chover amanhã',	'OPCAO',	'Vai chover amanhã?',	'',	0,	1),
(10,	3,	'Ir pra praia',	'OPCAO',	'',	'',	1,	0),
(11,	3,	'Metereologia diz que chove',	'OPCAO',	'',	'',	0,	1),
(12,	3,	'Namorada ligar',	'OPCAO',	'',	'',	0,	1),
(13,	3,	'Ocorre emergência',	'OPCAO',	'',	'',	0,	1),
(14,	3,	'Orientador passa trabalho',	'OPCAO',	'',	'',	0,	1),
(15,	3,	'Tenho dinheiro',	'OPCAO',	'',	'',	0,	1),
(16,	3,	'Tenho tempo',	'OPCAO',	'',	'',	0,	1),
(17,	3,	'Vou sair hoje',	'OPCAO',	'',	'',	0,	1);
