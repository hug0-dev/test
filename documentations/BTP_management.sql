-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : jeu. 06 mars 2025 à 17:29
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `BTP_management`
--

-- --------------------------------------------------------

--
-- Structure de la table `affectation`
--

CREATE TABLE `affectation` (
  `id` int(11) NOT NULL,
  `equipe_id` int(11) NOT NULL,
  `chantier_id` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `nom` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `affectation`
--

INSERT INTO `affectation` (`id`, `equipe_id`, `chantier_id`, `date_debut`, `date_fin`, `nom`) VALUES
(5, 10, 8, '2025-03-01', '2025-03-09', 'les maçons'),
(6, 11, 9, '2025-03-01', '2025-03-09', 'les plombiers');

-- --------------------------------------------------------

--
-- Structure de la table `chantier`
--

CREATE TABLE `chantier` (
  `id` int(11) NOT NULL,
  `chef_chantier_id` int(11) DEFAULT NULL,
  `nom` varchar(255) NOT NULL,
  `chantier_prerequis` longtext DEFAULT NULL,
  `effectif_requis` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `chantier`
--

INSERT INTO `chantier` (`id`, `chef_chantier_id`, `nom`, `chantier_prerequis`, `effectif_requis`, `date_debut`, `date_fin`, `image`) VALUES
(8, 21, 'renovation maçonnerie', '[\"Ma\\u00e7on\"]', 3, '2025-03-01', '2025-03-09', 'istockphoto-177318607-612x612-67c9ccaf98c63.jpg'),
(9, 23, 'Fuite a paris', '[\"Plombier\"]', 3, '2025-03-01', '2025-03-09', 'gettyimages-157376761-612x612-67c9ccee97e77.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250305114057', '2025-03-05 11:41:05', 138),
('DoctrineMigrations\\Version20250305132954', '2025-03-05 13:30:09', 16),
('DoctrineMigrations\\Version20250305141310', '2025-03-05 14:13:19', 19);

-- --------------------------------------------------------

--
-- Structure de la table `equipe`
--

CREATE TABLE `equipe` (
  `id` int(11) NOT NULL,
  `chef_equipe_id` int(11) DEFAULT NULL,
  `nom_equipe` varchar(255) NOT NULL,
  `competance_equipe` varchar(255) NOT NULL,
  `nombre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `equipe`
--

INSERT INTO `equipe` (`id`, `chef_equipe_id`, `nom_equipe`, `competance_equipe`, `nombre`) VALUES
(10, 21, 'equipe maçon', '[\"Ma\\u00e7on\"]', 3),
(11, 23, 'plombier team', '[\"Plombier\"]', 3);

-- --------------------------------------------------------

--
-- Structure de la table `ouvrier`
--

CREATE TABLE `ouvrier` (
  `id` int(11) NOT NULL,
  `equipe_id` int(11) DEFAULT NULL,
  `nom_ouvrier` varchar(100) NOT NULL,
  `competences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`competences`)),
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ouvrier`
--

INSERT INTO `ouvrier` (`id`, `equipe_id`, `nom_ouvrier`, `competences`, `role`) VALUES
(19, 10, 'bob', '[\"Ma\\u00e7on\"]', 'Ouvrier'),
(20, 10, 'carl', '[\"Ma\\u00e7on\"]', 'Ouvrier'),
(21, 10, 'mario', '[\"Ma\\u00e7on\"]', 'Chef'),
(22, 10, 'martin', '[\"Ma\\u00e7on\"]', 'Ouvrier'),
(23, 11, 'luigie', '[\"Plombier\"]', 'Chef'),
(24, 11, 'pedro', '[\"Plombier\"]', 'Ouvrier'),
(25, 11, 'jose', '[\"Plombier\"]', 'Ouvrier'),
(26, 11, 'antonio', '[\"Plombier\"]', 'Ouvrier');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`) VALUES
(5, 'a@a', '[]', '$2y$13$pzZ3B3L2iaBpakPxxdUCp.oqA0e9r0o7plxj.JD7351E5E6FtQHu2');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `affectation`
--
ALTER TABLE `affectation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F4DD61D36D861B89` (`equipe_id`),
  ADD KEY `IDX_F4DD61D3D0C0049D` (`chantier_id`);

--
-- Index pour la table `chantier`
--
ALTER TABLE `chantier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_636F27F622456F8F` (`chef_chantier_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `equipe`
--
ALTER TABLE `equipe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_2449BA15BEF74F87` (`chef_equipe_id`);

--
-- Index pour la table `ouvrier`
--
ALTER TABLE `ouvrier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_ED5E7D256D861B89` (`equipe_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `affectation`
--
ALTER TABLE `affectation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `chantier`
--
ALTER TABLE `chantier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `equipe`
--
ALTER TABLE `equipe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `ouvrier`
--
ALTER TABLE `ouvrier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `affectation`
--
ALTER TABLE `affectation`
  ADD CONSTRAINT `FK_F4DD61D36D861B89` FOREIGN KEY (`equipe_id`) REFERENCES `equipe` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_F4DD61D3D0C0049D` FOREIGN KEY (`chantier_id`) REFERENCES `chantier` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `chantier`
--
ALTER TABLE `chantier`
  ADD CONSTRAINT `FK_636F27F622456F8F` FOREIGN KEY (`chef_chantier_id`) REFERENCES `ouvrier` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `equipe`
--
ALTER TABLE `equipe`
  ADD CONSTRAINT `FK_2449BA15BEF74F87` FOREIGN KEY (`chef_equipe_id`) REFERENCES `ouvrier` (`id`);

--
-- Contraintes pour la table `ouvrier`
--
ALTER TABLE `ouvrier`
  ADD CONSTRAINT `FK_ED5E7D256D861B89` FOREIGN KEY (`equipe_id`) REFERENCES `equipe` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
