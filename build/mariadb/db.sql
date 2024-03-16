-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Creato il: Feb 03, 2024 alle 14:33
-- Versione del server: 11.2.2-MariaDB-1:11.2.2+maria~ubu2204
-- Versione PHP: 8.2.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `SWBD-database`
--
CREATE DATABASE IF NOT EXISTS `SWBD-database` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `SWBD-database`;

-- --------------------------------------------------------

--
-- Struttura della tabella `Attivita`
--

CREATE TABLE `Attivita` (
  `ID` int(10) UNSIGNED NOT NULL,
  `Titolo` varchar(255) CHARACTER SET utf16 COLLATE utf16_general_ci NOT NULL,
  `Descrizione` varchar(255) CHARACTER SET utf16 COLLATE utf16_general_ci NOT NULL,
  `Stato` enum('Da fare','In corso','Fatto') NOT NULL,
  `Scadenza` date DEFAULT NULL,
  `Assegnato` varchar(33) CHARACTER SET utf16 COLLATE utf16_general_ci DEFAULT NULL,
  `FattoIl` date DEFAULT NULL,
  `FK_TeamID` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `Attivita`
--

INSERT INTO `Attivita` (`ID`, `Titolo`, `Descrizione`, `Stato`, `Scadenza`, `Assegnato`, `FattoIl`, `FK_TeamID`) VALUES
(28, 'Controllo finanziario', 'Verifica dei bilanci finanziari e dei report per assicurare l\'accuratezza e la conformità alle normative contabili', 'Da fare', '2024-02-29', NULL, NULL, 38),
(29, 'Preparazione dei report finanziari', 'Creazione di report finanziari per i soci o per le autorità fiscali', 'Fatto', '2024-02-04', 'Pino', '2024-01-15', 38),
(30, 'Consulenza fiscale', 'Consulenza su questioni fiscali per minimizzare l\'imposta sul reddito dell\'azienda', 'In corso', '2024-02-12', 'Giacomo', '2024-01-21', 38),
(31, 'Gestione delle obbligazioni', 'Gestione delle obbligazioni finanziarie dell azienda come i prestiti o le linee di credito', 'Fatto', '2024-02-03', 'Giacomo', '2024-01-24', 38),
(32, 'Controllo del costo', 'Monitoraggio dei costi per assicurare che l\'azienda rimanga entro il budget', 'Fatto', '2024-02-04', 'Ago', '2024-01-21', 38),
(33, 'Organizzazione cena aziendale', 'None', 'Da fare', '2024-02-26', 'Pino', NULL, 38),
(34, 'Gestione della liquidita', 'Monitoraggio dei flussi di cassa per assicurare che l\'azienda abbia sempre abbastanza denaro a disposizione', 'Fatto', NULL, 'Giacomo', '2024-02-01', 38),
(37, 'Organizzazione viaggio', 'None', 'Da fare', '2024-04-30', NULL, NULL, 34),
(38, 'Organizzazione festa a sorpresa Pino', 'None', 'In corso', '2024-02-16', NULL, NULL, 34),
(39, 'ribilanciamento wallet', 'None', 'Da fare', '2024-03-01', NULL, NULL, 34);

-- --------------------------------------------------------

--
-- Struttura della tabella `Team`
--

CREATE TABLE `Team` (
  `ID` int(10) UNSIGNED NOT NULL,
  `Nome` varchar(33) CHARACTER SET utf16 COLLATE utf16_general_ci NOT NULL,
  `Descrizione` varchar(255) CHARACTER SET utf16 COLLATE utf16_unicode_ci NOT NULL DEFAULT 'None',
  `CodiceInvito` varchar(12) CHARACTER SET armscii8 COLLATE armscii8_general_ci DEFAULT NULL,
  `FK_UsernameProprietario` varchar(33) CHARACTER SET utf16 COLLATE utf16_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `Team`
--

INSERT INTO `Team` (`ID`, `Nome`, `Descrizione`, `CodiceInvito`, `FK_UsernameProprietario`) VALUES
(34, 'Privato', 'Questo è il tuo Team privato, che non sara visibbila da nessun altro', NULL, 'Pino'),
(35, 'Privato', 'Questo è il tuo Team privato, che non sara visibbila da nessun altro', NULL, 'Giacomo'),
(37, 'Privato', 'Questo è il tuo Team privato, che non sara visibbila da nessun altro', NULL, 'Ago'),
(38, 'Contabilita', 'Team di contabili dedicati che gestisce la contabilita e la situazione fiscale del un azienda', 'ZTqoQ7_!kLX0', 'Ago');

--
-- Trigger `Team`
--
DELIMITER $$
CREATE TRIGGER `DeleteTeamActivity` BEFORE DELETE ON `Team` FOR EACH ROW BEGIN
   DELETE FROM Attivita WHERE FK_TeamID = OLD.ID;
   DELETE FROM UserInTeam WHERE TeamID = OLD.ID;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `NewTeam` AFTER INSERT ON `Team` FOR EACH ROW BEGIN 
    DECLARE user_id INT;
    SELECT ID INTO user_id FROM User WHERE Username = NEW.FK_UsernameProprietario;
    
    INSERT INTO UserInTeam (UserID, TeamID) VALUES (user_id, NEW.ID);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `User`
--

CREATE TABLE `User` (
  `ID` int(10) UNSIGNED NOT NULL,
  `Username` varchar(33) CHARACTER SET utf16 COLLATE utf16_general_ci NOT NULL,
  `Email` varchar(51) CHARACTER SET utf16 COLLATE utf16_general_ci NOT NULL,
  `HashPW` varchar(512) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `User`
--

INSERT INTO `User` (`ID`, `Username`, `Email`, `HashPW`) VALUES
(23, 'Pino', 'Vito@email.com', '$2y$10$wZ5trEzJu3vrFJMa2UW.tOrytUyCyOQgi1x5WRmNsF7SMVAHgGB2.'),
(24, 'Giacomo', 'Aldo@gmail.com', '$2y$10$42FqSJJXoiH5LAUJ4gnPoeQXr1D3UYX7874fnslvdo6OND5/NPQx6'),
(26, 'Ago', 'valle@ago.com', '$2y$10$t9a9b9OWxz4BdFzytz.6Heg8Kl6JjEnWHFHrOedmE7L8Wlc3ZGzRq');

--
-- Trigger `User`
--
DELIMITER $$
CREATE TRIGGER `DeleteAllConnected` BEFORE DELETE ON `User` FOR EACH ROW BEGIN    
    DELETE FROM UserInTeam WHERE UserID = OLD.ID;
    DELETE FROM Team WHERE FK_UsernameProprietario = OLD.Username;
    UPDATE Attivita SET Attivita.Assegnato = null WHERE Assegnato = OLD.Username;
   END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `NewUserPrivateTeam` AFTER INSERT ON `User` FOR EACH ROW BEGIN
    DECLARE last_id INT;
    INSERT INTO Team (Nome, Descrizione, CodiceInvito,FK_UsernameProprietario) VALUES ('Privato', "Questo è il tuo Team privato, che non sara visibbila da nessun altro", Null, NEW.Username);
    SET last_id = LAST_INSERT_ID();
    END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `UserInTeam`
--

CREATE TABLE `UserInTeam` (
  `ID` int(10) UNSIGNED NOT NULL,
  `UserID` int(10) UNSIGNED NOT NULL,
  `TeamID` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `UserInTeam`
--

INSERT INTO `UserInTeam` (`ID`, `UserID`, `TeamID`) VALUES
(47, 23, 34),
(48, 24, 35),
(50, 26, 37),
(51, 26, 38),
(54, 24, 38),
(55, 23, 38);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `Attivita`
--
ALTER TABLE `Attivita`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FK_TeamID` (`FK_TeamID`),
  ADD KEY `Assegnato` (`Assegnato`);

--
-- Indici per le tabelle `Team`
--
ALTER TABLE `Team`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FK_UsernameProprietario` (`FK_UsernameProprietario`);

--
-- Indici per le tabelle `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indici per le tabelle `UserInTeam`
--
ALTER TABLE `UserInTeam`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `TeamID` (`TeamID`),
  ADD KEY `UserInTeam_ibfk_2` (`UserID`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `Attivita`
--
ALTER TABLE `Attivita`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT per la tabella `Team`
--
ALTER TABLE `Team`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT per la tabella `User`
--
ALTER TABLE `User`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT per la tabella `UserInTeam`
--
ALTER TABLE `UserInTeam`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `Attivita`
--
ALTER TABLE `Attivita`
  ADD CONSTRAINT `Attivita_ibfk_1` FOREIGN KEY (`FK_TeamID`) REFERENCES `Team` (`ID`),
  ADD CONSTRAINT `Attivita_ibfk_2` FOREIGN KEY (`FK_TeamID`) REFERENCES `Team` (`ID`),
  ADD CONSTRAINT `Attivita_ibfk_3` FOREIGN KEY (`Assegnato`) REFERENCES `User` (`Username`);

--
-- Limiti per la tabella `Team`
--
ALTER TABLE `Team`
  ADD CONSTRAINT `Team_ibfk_1` FOREIGN KEY (`FK_UsernameProprietario`) REFERENCES `User` (`Username`);

--
-- Limiti per la tabella `UserInTeam`
--
ALTER TABLE `UserInTeam`
  ADD CONSTRAINT `UserInTeam_ibfk_1` FOREIGN KEY (`TeamID`) REFERENCES `Team` (`ID`),
  ADD CONSTRAINT `UserInTeam_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `User` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
