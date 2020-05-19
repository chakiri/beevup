-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le :  mar. 19 mai 2020 à 16:31
-- Version du serveur :  5.6.35
-- Version de PHP :  7.1.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données :  `mvp`
--

-- --------------------------------------------------------

--
-- Structure de la table `abuse`
--

CREATE TABLE `abuse` (
  `id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `comment_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `status` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `company`
--

CREATE TABLE `company` (
  `id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `siret` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_street` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_post_code` int(11) DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(11,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_valid` tinyint(1) NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `introduction` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `is_completed` tinyint(1) NOT NULL,
  `bar_code` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `other_category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `company`
--

INSERT INTO `company` (`id`, `store_id`, `category_id`, `siret`, `email`, `phone`, `address_number`, `address_street`, `address_post_code`, `city`, `country`, `filename`, `video`, `name`, `description`, `website`, `latitude`, `longitude`, `is_valid`, `slug`, `introduction`, `updated_at`, `is_completed`, `bar_code`, `other_category`) VALUES
(1, 1, 4, 'feeee', 'compan@dev.fr', '+332147483647', '12', 'Vallé des entrepreneurs', 0, 'Les clayes sous bois', 'FR', '5ec14d75f0b35748654426.JPG', NULL, 'life groups', 'ma description', 'ww.monsite.fr', NULL, NULL, 0, 'life_groups', 'mon intro youha', '2020-05-17 16:43:01', 1, 'iVBORw0KGgoAAAANSUhEUgAAAM0AAAA/CAIAAADMhNssAAAATHRFWHRDb3B5cmlnaHQAR2VuZXJhdGVkIHdpdGggQmFyY29kZSBHZW5lcmF0b3IgZm9yIFBIUCBodHRwOi8vd3d3LmJhcmNvZGVwaHAuY29tWX9wuAAAAAlwSFlzAAAOxAAADsQBlSsOGwAAA4VJREFUeJzt3T9IMnEcx/GfIkbXWVwEDf0ZJW4WinBpk2pqCnVrCWkQiZCgoc05mkIomppCqCEkmm5pawkkGoJucrCQw0KSe4Yf9dzTZY+P3X0Uns9r0q93er+fb9ImA7Zti44FAgEhhDzFedv9qHMifX+Wk/uSOnmeTo7s/NW7O9J9u926+vPIf33v3M/W7vmDgsh/7IwQ2BkhsDNCYGeEwM4IgZ0RAjsjBHZGCOyMENgZIbAzQmBnhMDOCIGdEQI7IwR2RgjsjBDYGSGwM0JgZ4TAzgiBnRECOyMEdkYI7IwQ2BkhsDNCYGeEwM4IgZ0RAjsjBHZGCOyMENgZIbAzQmBnhMDOCIGdEQI7IwR2RgjsjBDYGSGwM0JgZ4TAzgiBnRECOyMEdkYIAQ9/T/j/xD1x4+8JU2+wM0JgZ4TwR2dnZ2fy0xTg5eVlbW1N07RIJLK0tPT4+PgxT6VSIyMj4+PjOzs7zuN9nfcDzBpvb2+HhoZarZa8G3EYHBwcGBjwZW32u1qtNjk56Zy4OU/5dPq/ymaz8/Pz1Wq10Wgkk8l4PC7nmUxmcXHRsizTNGdmZg4ODjDzrnm4J4A1lkqlsbExIcTb25v7AtLpdKFQ6Pr6P7j35PempNPpbDYL6yyTyVxcXMjbhmGEQiHbtpvNpqIohmHIebFYnJubA8x/wqs9AayxUCjour6/v/9lZ+VyORqNNpvN7q7fqW1npVIpkUjc3NzAOnPK5/OxWMy27fv7eyHE8/OznBuGoaoqYP4TXu0JYI2madq2Ld9ld2exWOzo6Ki7i//EvSchIcTT09PW1tbl5WWtVuv049Y7h4eHe3t7V1dXQohGoyGEUBRFPqQoipz4Pe8HgDVOTEy0e/Xr6+uHh4fV1VXP1yUFhRAbGxu5XG5qasqn1/jG7u5uLpc7Pz+fnZ0VQqiqKt53St6QE7/n/aC3azw5OVlZWfHrnwDZ2enpaT6f1zQtHo8LITRN0zTty6Pb/VXsQqvVSqVSx8fHhmEsLCzI4fT0tKqqlUpF3q1UKrquA+Y/4dWe9HaN5XJ5eXm5uyt3+2JPnB+rf/1+5qHNzc1oNFqtVj/N19fXE4lEvV43TVPX9WKxiJn3A8wa3d/PLMsKBoPy25tPetOZZVnhcDgcDqsOcuX1ej2ZTA4PD4+Ojm5vb3+c4ve8H2DW6O7s7u5OCPH6+urbyuxfmTxkvciFtzYAAAAASUVORK5CYII=', 'peinture'),
(2, 1, 1, 'sefsw', 'company1@dev.fr', '+332147483647', '12', '', 1212, 'Paris', 'France', NULL, NULL, 'hall', NULL, NULL, NULL, NULL, 0, 'hall', NULL, '0000-00-00 00:00:00', 0, '', NULL),
(11, 1, NULL, '2423', 'test@erf.fr', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'zdzedzed', NULL, NULL, NULL, NULL, 0, 'zdzedzed', NULL, '0000-00-00 00:00:00', 0, '', NULL),
(12, 1, NULL, '3545', 'test@dev.fr', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'zedzed', NULL, NULL, NULL, NULL, 0, 'zedzed', NULL, '0000-00-00 00:00:00', 0, '', NULL),
(13, 1, NULL, '234234', 'test2@dev.fr', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Mon entreprise 2', NULL, NULL, NULL, NULL, 0, 'mon-entreprise-2', NULL, '0000-00-00 00:00:00', 0, '', NULL),
(14, 3, NULL, '123456789', 'test3@dev.fr', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SNCF', NULL, NULL, NULL, NULL, 0, 'sncf', NULL, '2020-03-17 19:19:08', 0, '', NULL),
(15, 1, 3, '123456', 'test4@dev.fr', '781818181', '9', 'Rue St', 78250, 'Paris', 'FR', NULL, NULL, 'Auchan', 'ma description', 'www.bureau-vallee.com', NULL, NULL, 0, 'auchan', 'Mon introduction', '2020-03-18 11:49:56', 1, '', NULL),
(16, 3, NULL, '1234532567', 'test5@dev.fr', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'auchan', NULL, NULL, NULL, NULL, 0, 'auchan', NULL, '2020-03-27 18:51:41', 0, '', NULL),
(17, 1, NULL, '1234', 'test6@dev.fr', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Mon entreprise', NULL, NULL, NULL, NULL, 0, 'mon-entreprise', NULL, '2020-03-30 10:06:16', 0, '', NULL),
(18, 3, NULL, '12345', 'chakiri@dev.fr', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Station F', NULL, NULL, NULL, NULL, 0, 'station-f', NULL, '2020-03-30 11:04:18', 0, '', NULL),
(22, 1, NULL, '123324324', 'test123@dev.fr', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'idek', NULL, NULL, NULL, NULL, 0, 'idek', NULL, '2020-04-06 15:50:54', 0, '', NULL),
(23, 5, 4, '987654', 'decathelon1@dev.fr', '11111111', '9', 'azd', 123123, 'pa', 'DZ', NULL, NULL, 'decathelon', 'dsfsdfsdf', 'jhfjhv.vom', NULL, NULL, 0, 'decathelon', 'fsdfsdf', '2020-05-07 15:17:53', 1, 'iVBORw0KGgoAAAANSUhEUgAAAM0AAAA/CAIAAADMhNssAAAATHRFWHRDb3B5cmlnaHQAR2VuZXJhdGVkIHdpdGggQmFyY29kZSBHZW5lcmF0b3IgZm9yIFBIUCBodHRwOi8vd3d3LmJhcmNvZGVwaHAuY29tWX9wuAAAAAlwSFlzAAAOxAAADsQBlSsOGwAABFJJREFUeJzt3c9LMlsYB/CTVNAwViNEEBZFECER8RIVYWCtJFu11HZtwloMJiJBYKvcBRERIhT9ARHlolVECK2CCBcuxE22EVIRM+kH5y4Or3duWrdr4/MG9/tZxOmZMz+eh29OOxs45+zLGhoaGGPiFO268qi2Inx+llblI33lOl/Z+fW717azcv1RX5Q7P6pUnlXpK3f/PAlibah6dQB9IWdAATkDCsgZUEDOgAJyBhSQM6CAnAEF5AwoIGdAATkDCsgZUEDOgAJyBhSQM6CAnAEF5AwoIGdAATkDCsgZUEDOgAJyBhSQM6CAnAEF5AwoIGdAATkDCsgZUEDOgAJyBhSQM6CAnAEF5AwoIGdAATkDCsgZUEDOgAJyBhSQM6CAnAEF5AwoIGdAATkDCsgZUEDOgAJyBhSQM6CAnAEF5AwoNOj4fcL/T5hJJXyfMPwZyBlQQM6Awj9ydnp6Kt6mBJ6enhYXFxVFMRqNDofj7u6uXHe5XG1tbZ2dnevr69r9da3/BDr2WDnbl5eX1dXVjo4Oo9E4NTV1fX0tNt/f3zscDqPR2NfXt7W1Va/e+G+ZTMZsNmsrlbSnvDv9v1JVdXJyMp1OF4tFp9NptVpF3e12z87OFgqFVCo1ODgYCoVo6jXTcSZ69VJ1tsFgcGhoKJVKibXZbBabf/36NT8/n8vlMpmMzWbb3d2t+fnLKmfy91AWFhZUVSXLmdvtPjs7E+toNNrY2Mg5f35+liQpGo2KejgcnpiYIKh/h14z0bGXqrPlnBcKBfEzEAiMjIxwzpPJJGNMhI9zfn5+Pjw8XNvza32Ys+PjY7vdfnNzQ5YzLb/fPzo6yjlPJBKMsVwuJ+rRaFSWZYL6d+g1kzr1Up6tcHBwYDAYZFmOxWLli2QyGXH04uJCkqTanl+rciYGxlg2m/X5fKFQ6OtvWx3t7+9vb2/v7OwwxorFImNMkiRxSJIkUal3/SeoRy/a2QpOp7NQKMzMzHi9XsZYf3+/xWLx+XyPj48PDw/BYLBUKtWjOwNjbGVlxePxdHd31+MGn9vY2PB4PJFIZHx8nDEmyzL7PUGxEJV6138C3Xt5N1uhqamppaVFVdXLy0tROTk5SSaTZrN5bm7Obre3t7fXozsDY+zo6Mjv9yuKYrVaGWOKoiiKUnX3R5+KNXh7e3O5XIeHh9FodHp6WhR7enpkWY7H4+LXeDxusVgI6t+h10x07KXqbJeXlzc3N8X69fW1/EGYz+cjkUg2m726umKMjYyM1Pb8WlVmon2t/uv/Zzryer0DAwPpdPpdfWlpyW635/P5VCplsVjC4TBN/SfQq5eqs93b2+vq6kokErlczmazqaoq6sPDw4FAgHMei8XMZnMkEqlHa38mZ4VCobm5ubm5WdZ4fX3lnOfzeafT2draajKZ1tbWyqfUu/4T6NLLJ7NdW1szmUwmk8ntdpdKJbH/9vZ2bGxMkqTe3t76/dX9BYW7amT467PkAAAAAElFTkSuQmCC', NULL),
(24, 5, 3, '1992312313', 'malboro1@dev.fr', '111111111', '12', 'qsxqx', 122222, 'qsxqsx', 'AF', NULL, NULL, 'marlboro', 'qscqsc', 'qscqsc', NULL, NULL, 0, 'marlboro', 'qscqcs', '2020-05-07 18:52:52', 1, 'iVBORw0KGgoAAAANSUhEUgAAAM0AAAA/CAIAAADMhNssAAAATHRFWHRDb3B5cmlnaHQAR2VuZXJhdGVkIHdpdGggQmFyY29kZSBHZW5lcmF0b3IgZm9yIFBIUCBodHRwOi8vd3d3LmJhcmNvZGVwaHAuY29tWX9wuAAAAAlwSFlzAAAOxAAADsQBlSsOGwAAA9VJREFUeJzt3bFL61AUBvBbkVBiqjEgKujiICLiJCji4FgUFByjiKBICQ6liIiioJOzOJWCi39AQQcHEYcgjg4OIk61iJRaSwkRSiVvuNiXZ5r3+urteYX3/abr6U3ac+6HzdaA4zisaoFAgDHGL3Gvva+6K9zvr3LzfqRq7lPNzurfvbad3rVfX5Q7/Srfv79feLw7myruAxALOQMKyBlQQM6AAnIGFJAzoICcAQXkDCggZ0ABOQMKyBlQQM6AAnIGFJAzoICcAQXkDCggZ0ABOQMKyBlQQM6AAnIGFJAzoICcAQXkDCggZ0ABOQMKyBlQQM6AAnIGFJAzoICcAQXkDCggZ0ABOQMKyBlQQM6AAnIGFJAzoICcAQXkDCggZ0ABOQMKyBlQQM6AAnIGFJAzoICcAQXkDCggZ0AhIPD3hP9PmIkXfk8Y/g3kDCggZ0Dhl5ydnp7yb1MC7+/vy8vL7e3toVBoenr66empXJ+fn29ra+vs7NzZ2XHvr2u9EQjsseJsy7wH/fHxsb293d3dHQqFZmdnX19fBffmfMrlcj09Pe6Kl/uSL5f/rWg0Oj4+nslkbNvWdX1iYoLXDcOYmpqyLCudTg8MDMTjcZp6zQTORFQvfrPlKh70/v7+0NBQKpWyLGtmZmZxcbHmLpxKM/n5ZgsLC9FolCxnhmGcn5/ztWmazc3NjuMUi0VZlk3T5PVEIjE2NkZQ/w5RMxHYS8XZllU86K6ursvLS77OZrMPDw+1dcH55iyZTIbD4dvbW7KcuW1ubo6MjDiO8/j4yBjL5/O8bpqmoigE9e8QNZM69VKeLVfxoJ+fnxljJycnw8PDmqbpul4oFGrrgvPOpIkx9vb2trGxEY/Hq/2uFer4+Pjw8PDo6IgxZts2Y0yWZf6SLMu8Uu96I6hHL+7ZMv+DzuVyjLGrq6vr6+v7+/uXlxfDMMR218QYW1tbi8Vivb29Ym9djb29vVgsdnZ2Njo6yhhTFIV9TpAveKXe9UYgvJcvs2X+By1JEmPs4OCgpaWlo6Njd3c3mUwKbs9xnGAwqKqqqqr8g/L1d/5tVqNUKum63tfXd3d35y4qinJzc8P/LD9z1LveCAT2UnG2jv9BF4vFYDCYSqX4touLC03TxHb3y8PEH5/PBFpfX+/v789kMl/qkUgkHA4XCoV0Oj04OJhIJGjqjUBUL36zLfMe9MrKytzcXD6fz2azk5OTkUhEbGv/JmeWZUmSJEmS4lIqlRzHKRQKuq63trZqmra1tVW+pN71RiCkl9/Mtsx70LZtr66uapqmqurS0pJlWWJb+wEiy+fjg4sAJQAAAABJRU5ErkJggg==', NULL),
(25, 5, 2, '12974823GH', 'trello1@dev.fr', '1111111', '3', '10', 71111, 'sf', 'AG', NULL, NULL, 'trello', 'sdcsdc', 'sdfsf', NULL, NULL, 0, 'trello', 'sdcsdc', '2020-05-11 14:24:14', 1, 'iVBORw0KGgoAAAANSUhEUgAAAM0AAAA/CAIAAADMhNssAAAATHRFWHRDb3B5cmlnaHQAR2VuZXJhdGVkIHdpdGggQmFyY29kZSBHZW5lcmF0b3IgZm9yIFBIUCBodHRwOi8vd3d3LmJhcmNvZGVwaHAuY29tWX9wuAAAAAlwSFlzAAAOxAAADsQBlSsOGwAABChJREFUeJzt3bFLOn8cx/FPFiKHWidGIeIYIQ0NESEG1iS1NMbpVkTYYigiQhAtNUY0iAhFf0AELU0SdGNBRIPD0eJNDiVyWJnx+Q3H19991fr1tfP9DX6vxxD67i79vHlmY32cc/ZlfX19jDH9FuPj9u8aJ7rP7zJqf0tf+TlfufLrr97dle2PPzoX5ZUfTdrv+vpJ232+B0vHewDMhc6AAjoDCugMKKAzoIDOgAI6AwroDCigM6CAzoACOgMK6AwooDOggM6AAjoDCugMKKAzoIDOgAI6AwroDCigM6CAzoACOgMK6AwooDOggM6AAjoDCugMKKAzoIDOgAI6AwroDCigM6CAzoACOgMK6AwooDOggM6AAjoDCugMKKAzoIDOgAI6AwroDCigM6CAzoACOgMK6AwooDOg0Gfi/xP+f8JO2uH/CcPfgc6AAjoDCr91dn5+rv81JfD8/LyysiKKosPhWFxcLJVKzXkkEhkcHBwZGdna2jJe39P5T2DiGTvu9urqqr+/3/HL5uYmY+zt7S2RSAwPDzscjtnZ2Zubm56cjf/y+Pjo9XqNk3bGW1pu/1PxeDwQCJTL5VqtJklSMBjU57FYbGFhQdM0VVXHx8dzuRzNvGsm7sSss3y02/39/aWlpZYX3dvbm5iYUFVVf+z1ert+/03tO/l3KdFoNB6Pk3UWi8UuLi70x7IsDwwMcM7r9bogCLIs6/N8Pj8zM0Mw/w6zdmLiWTrulnMejUZ3dnbaX1rTNP3r9vb25ORkd+/f6MPOzs7OwuHw7e0tWWdG6XR6amqKc64oCmOsUqnoc1mW7XY7wfw7zNpJj87S3C3n3O/3h0Ihn8/ndrtXV1f1vHTHx8cWi8Vut9/f33f3/o3ad2JhjD09PaVSqVwu9yd/b01zdHR0cHBweHjIGKvVaowxQRD0bwmCoE96Pf8JenEW424ZYx6PZ3l5WVGUu7s7RVHW1taaV0qSpGna/Px8MpnsyfE455IkZbNZzjn959n29vbQ0FChUNCfPjw8sN9/R51OJ8H8O8zaielnadlti8vLS5vN1jIsFAqCIHT3/o3ad2JhjJ2enqbTaVEUg8EgY0wURVEUP4qy40/pwvv7eyQSOTk5kWV5bm5OH/p8PrvdXiwW9afFYtHv9xPMv8OsnZh4lo67LZVKiUTi9fVVf1qv1202G2NsY2Njd3dXHzYajeYH5Hd02Ikxw//8PDNRMpkcGxsrl8st8/X19XA4XK1WVVX1+/35fJ5m/hOYdZaOu9U0ze12p9Pper2uqur09HQymeScZ7NZj8ejKEqlUgmFQvF4vBdH+zudaZpmtVqtVqvdoNFocM6r1aokSU6n0+VyZTKZ5i29nv8Eppzlk91eX18HAgFBEEZHR1OplD7knGcyGZfL5XK5YrHYy8tLL472Dy0DDvK9ttWiAAAAAElFTkSuQmCC', NULL),
(26, 4, NULL, 'Lechakiri', 'yassir.chakiri12@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '99292912', NULL, NULL, NULL, NULL, 0, '99292912', NULL, '2020-05-12 15:17:35', 0, 'iVBORw0KGgoAAAANSUhEUgAAAM0AAAA/CAIAAADMhNssAAAATHRFWHRDb3B5cmlnaHQAR2VuZXJhdGVkIHdpdGggQmFyY29kZSBHZW5lcmF0b3IgZm9yIFBIUCBodHRwOi8vd3d3LmJhcmNvZGVwaHAuY29tWX9wuAAAAAlwSFlzAAAOxAAADsQBlSsOGwAAA89JREFUeJzt3T9IOn8cx/G3ERJ2VidEBRVNERJNQREOtUlBQeMZERQhR4NIhBQFNTVHkwhBNIdQQ1M0HM2NRzSVRIiZyHFBKPcbjm+/++afr9nH91f4vh7T9fbuY58PT7JNl2VZVDOXy0VE9iPO69JXnRNb9aecSn+lWtap5c7a372+O0uvK+2L885Kk0pPVUriu+/uvG4puyKAWOgMOKAz4IDOgAM6Aw7oDDigM+CAzoADOgMO6Aw4oDPggM6AAzoDDugMOKAz4IDOgAM6Aw7oDDigM+CAzoADOgMO6Aw4oDPggM6AAzoDDugMOKAz4IDOgAM6Aw7oDDigM+CAzoADOgMO6Aw4oDPggM6AAzoDDugMOKAz4IDOgAM6Aw7oDDigM+CAzoADOgMO6Aw4oDPggM6AAzoDDi6B3yf8b8KZlML3CcPfgc6AAzoDDr91dnFxYX+aMnh/f19dXZVl2ev1zs3NPT09fc5DoVBnZ2dPT8/u7q7z/obOm4HAPZY922KxuLOz09fX5/V6FxYWXl9fq68jkvVLNpvt7+93Tko5H/ny+HdFIpGpqal0Om2apqIogUDAnquqOjs7axhGKpUaGRmJx+M887oJPBNRe6l0tgcHB6Ojo4+Pj4ZhzM/PLy8vV1+nbqVn8v+hLC0tRSIRts5UVb26urKvNU1rbW21LOvj48Pj8WiaZs8TicTk5CTD/CdEnYnAvZQ9W8uyent7r6+v7etMJnN/f199nbpV7CyZTAaDwbu7O7bOnGKx2Pj4uGVZDw8PRJTL5ey5pmmSJDHMf0LUmTRoL59n+/z8TERnZ2djY2M+n09RlHw+X/s631J6Ji1E9Pb2trW1FY/Ha/+0Fejk5OTo6Oj4+JiITNMkIo/HY7/k8XjsSaPnzaARe3GebTabJaKbm5vb21td119eXlRVrXGdn2shoo2NjWg0OjAwIHz1P9rf349Go5eXlxMTE0QkSRL92rl9YU8aPW8Gwvfy5WzdbjcRHR4etre3d3d37+3tJZPJWtYRooWIzs/PY7GYLMuBQICIZFmWZbns3ZX+KtahWCyGQqHT01NN02ZmZuzh4OCgJEm6rts/6rru9/sZ5j8h6kwE7qXs2Q4NDbW1tX32VCgU7PKYzsT5sfrH/88E2tzcHB4eTqfTX+bhcDgYDObz+VQq5ff7E4kEz7wZiNpLpbNdW1tbXFzM5XKZTGZ6ejocDldfR6C/05lhGG632+12Sw6FQsGyrHw+ryhKR0eHz+fb3t7+fKTR82YgZC9VztY0zfX1dZ/P19XVtbKyYhhG9fUF+g/1mGUVB3o8EwAAAABJRU5ErkJggg==', NULL),
(27, 4, NULL, '21324234234', 'yassir.chakiri12@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Lechakiri', NULL, NULL, NULL, NULL, 0, 'lechakiri', NULL, '2020-05-12 15:20:12', 0, 'iVBORw0KGgoAAAANSUhEUgAAAM0AAAA/CAIAAADMhNssAAAATHRFWHRDb3B5cmlnaHQAR2VuZXJhdGVkIHdpdGggQmFyY29kZSBHZW5lcmF0b3IgZm9yIFBIUCBodHRwOi8vd3d3LmJhcmNvZGVwaHAuY29tWX9wuAAAAAlwSFlzAAAOxAAADsQBlSsOGwAAA4FJREFUeJzt3TFIMmEYB/BHEaPjLK6lpRolnIUiXNqkmppC3VpCGkQiJGhoa24MoWhqiqCGkHC6pa1RoiHoJgcLOSykeBuO/N7P8/xOe+9J+P6/6fr7vp3v05/OzZAQgnwLhUJE5GyRr92vyomj/y6Z+y35+T1+Vvq/+3Ar3dde5+Jc6ZW4d8n8/H39rHSuwwQQPPQMOKBnwAE9Aw7oGXBAz4ADegYc0DPggJ4BB/QMOKBnwAE9Aw7oGXBAz4ADegYc0DPggJ4BB/QMOKBnwAE9Aw7oGXBAz4ADegYc0DPggJ4BB/QMOKBnwAE9Aw7oGXBAz4ADegYc0DPggJ4BB/QMOKBnwAE9Aw7oGXBAz4ADegYc0DPggJ4BB/QMOKBnwAE9Aw7oGXBAz4ADegYc0DPggJ4Bh5DC7xP+P2Embvg+Yfgd6BlwQM+Aw189u7q6cp6mDN7e3jY3Nw3DiMViq6urz8/PnTybzU5OTk5PT+/v78vrA81HgcIz9pxtTDI+Pj42NtY/V0l8azQaMzMzcuImb+naPqhCobC0tFSv11utViaTSaVSTp7P51dWVmzbtixrfn7++PiYJx+awpmoOovXbGW5XO7w8NB/PhD3TP4MJZfLFQoFtp7l8/mbmxvn2jTNSCQihGi325qmmabp5OVyeXFxkSH/CVUzUXiWnrOVVSqVeDzebrd95oPy7Nnl5WU6nb6/v2frmaxUKiWTSSHE4+MjEb2+vjq5aZq6rjPkP6FqJgGdpTNbWTKZPD09dS/2ygflnkmEiF5eXnZ3d29vbxuNxiCPXDVOTk6Ojo6q1SoRtVotItI0zXlJ0zQnCTofBUGcRZ5tx93d3dPT08bGRtdir1yJMBFtb28Xi8XZ2dkgbtDfwcFBsVi8vr5eWFggIl3X6XuCzoWTBJ2PAuVn6Zptx/n5+fr6uvvDvleuRJiILi4uSqWSYRipVIqIDMMwDKPnaq//ikP4/PzMZrNnZ2emaS4vLzvh3Nycruu1Ws35sVarJRIJhvwnVM1E4Vl6zrajUqmsra2534BXPoQeM5Efq//8fKbQzs5OPB6v1+td+dbWVjqdbjablmUlEolyucyTjwJVZ/GarRDCtu1wOGxZls9cld/pmW3b0Wg0Go3qko+PDyFEs9nMZDITExNTU1N7e3udLUHno0DJWfrMVgjx8PBARO/v71239spV+QKy0nfgDwcD1QAAAABJRU5ErkJggg==', NULL),
(28, 4, NULL, '123123123', 'chakiri.mohamedyassir@dev.fr', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Chachak', NULL, NULL, NULL, NULL, 0, 'chachak', NULL, '2020-05-12 15:23:50', 0, 'iVBORw0KGgoAAAANSUhEUgAAAM0AAAA/CAIAAADMhNssAAAATHRFWHRDb3B5cmlnaHQAR2VuZXJhdGVkIHdpdGggQmFyY29kZSBHZW5lcmF0b3IgZm9yIFBIUCBodHRwOi8vd3d3LmJhcmNvZGVwaHAuY29tWX9wuAAAAAlwSFlzAAAOxAAADsQBlSsOGwAAA9tJREFUeJzt3b9LOnEcx/G3cjicl3IuUZhjhLQVFeFQmxU0NWlD0HJIg0iEhA2BY1M0iRA0RENEUEFjw/0BLYVDQZBESJjIcYFY9x2Ob9/7+uPLfevu/RW+r8dk7+7yPm+e5KjHMAyyzePxEJF5i/V1+2+tE9Of77JqfyQ7f8fOlfbf/WtXtr/udi7OK7tN2u/qNvn+k3gJwH3oDDigM+CAzoADOgMO6Aw4oDPggM6AAzoDDugMOKAz4IDOgAM6Aw7oDDigM+CAzoADOgMO6Aw4oDPggM6AAzoDDugMOKAz4IDOgAM6Aw7oDDigM+CAzoADOgMO6Aw4oDPggM6AAzoDDugMOKAz4IDOgAM6Aw7oDDigM+CAzoADOgMO6Aw4oDPggM6AAzoDDugMOKAz4IDOgAM6Aw4eB79P+P+EnbTD9wnDv4HOgAM6Aw6/dXZ2dmZ+mjJ4e3tbXV2VZbmvr29hYeHx8fFznkwmg8Fgf3//1taW9XpX573AwTN23O3FxcXY2Jjf7w8Gg3Nzczc3Ny0P4GIAxk/VajUcDlsn7ay3tNz+t9Lp9PT0dKVS0XU9kUjEYjFznkql5ufnNU0rl8sjIyOFQoFn/mUO7sSps3Tc7fPzsyAIR0dHzWZT1/VcLheJRKzvbicAm9p38uuPLi8vp9Npts5SqdTl5aX5WlVVQRAMw2g0GqIoqqpqzovF4tTUFMP8O5zaiYNn6bjbarUaCAQODw8bjYamablcbnR01PoAdgKwqWtnp6en8Xj8+vqarTOrbDY7Pj5uGMbd3R0R1Wo1c66qqiRJDPPvcGonLp3lc7eGYVxdXYmiKAiC1+sdHBx8eHj4vMxmADa178RLRK+vrxsbG4VCwfaHrZP29/d3d3f39vaISNd1IhJF0fyVKIrmxO15L3DjLNbdElEqlVpaWqrVauVyORwO5/N5c84QgJeI1tbWMpnM0NCQe2/Tzfb2diaTOT8/n5ycJCJJkujnBs0X5sTteS9w/Cwtu72/v7+9vd3Z2fH7/QMDA/l8/vj42LySIQAvEZ2cnGSzWVmWY7EYEcmyLMtyx6u7/Vf8gvf392QyeXBwoKrq7OysOYxEIpIklUol88dSqRSNRhnm3+HUThw8S8fdCoJARB8fH9YfTfYDsKnDTqwfq059PNuxvr4+PDxcqVRa5oqixOPxer1eLpej0WixWOSZ9wKnztJttxMTEysrK7quv7y8zMzMKIrScoF7AfybzjRN8/l8Pp9Psmg2m4Zh1Ov1RCIRCARCodDm5ubnLW7Pe4EjZ/nDbp+enhYXFyVJCoVCiqLout7yAO4F8APFUV/MoZ071gAAAABJRU5ErkJggg==', NULL),
(29, 3, NULL, '12312342376', 'chakiri.mohamedyassir@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Chakchak', NULL, NULL, NULL, NULL, 0, 'chakchak', NULL, '2020-05-12 15:25:40', 0, 'iVBORw0KGgoAAAANSUhEUgAAAM0AAAA/CAIAAADMhNssAAAATHRFWHRDb3B5cmlnaHQAR2VuZXJhdGVkIHdpdGggQmFyY29kZSBHZW5lcmF0b3IgZm9yIFBIUCBodHRwOi8vd3d3LmJhcmNvZGVwaHAuY29tWX9wuAAAAAlwSFlzAAAOxAAADsQBlSsOGwAAA+tJREFUeJzt3b9Lan8cx/G3EQ6nY3UiaMiiCCLOIBFREQ61STY1altLSINIiARBbW5FREMIQX9ASDk0hDQcaI0ocAiXbHE4iohJJOcOH27f8/XHvV47532D+3pMp7fHq593z65tOQzDoLY5HA4iEk8xXzc+ap4Iv36WWeNbauffaefO9l+9szsbr1udi/POVpOvbKD977K47iIA+6Ez4IDOgAM6Aw7oDDigM+CAzoADOgMO6Aw4oDPggM6AAzoDDugMOKAz4IDOgAM6Aw7oDDigM+CAzoADOgMO6Aw4oDPggM6AAzoDDugMOKAz4IDOgAM6Aw7oDDigM+CAzoADOgMO6Aw4oDPggM6AAzoDDugMOKAz4IDOgAM6Aw7oDDigM+CAzoADOgMO6Aw4oDPggM6AAzoDDugMODgs/HvC/ybspBH+njD8HegMOKAz4PC/zq6ursSnKYO3t7eNjQ1FUVwul9/vf3l5+ZwHg8G+vr6hoaHd3V3z/bbOvwMLz9h0t6+vr36/3+VyjY+PHxwc1L3609NTT09PrVaz5WzGT7quu91u86SR+Sl1T/9T4XB4cXExn89XKpVAIOD1esU8FAqtrKyUy+VcLjc1NXV6esoz75iFO7HqLK12OzMzs7a2ViwWdV1fWlo6OTn5fOlkMjk4OEhEHx8fHb//T407+W8p6+vr4XCYrbNQKHR9fS2uNU3r7u42DOP9/V2SJE3TxDyRSCwsLDDMv8KqnVh4lqa7zWazRJTL5cQ8nU57PB5xHY/HVVU9Pj62vbNkMunz+e7v79k6M4vFYrOzs4ZhPD8/E1GxWBRzTdNkWWaYf4VVO7HpLHW71XVdzG9vbyVJEtciPvHdt6mzbiIqFArRaPTm5kbX9TY/bS10dnZ2dHSUTqeJqFKpEJEkSeIhSZLExO75d2DHWcy7nZiYUFU1Go0eHh5Wq9V4PF6tVsVtw8PDNh+Ouohoa2srEomMjIzY/WKN9vf3I5FIKpWan58nIlmW6ecGxYWY2D3/Diw/S91uiejy8jKbzbrd7tXVVZ/P19/fz3AuoYuILi4uYrGYoiher5eIFEVRFKXp3a3+V+xArVYLBoPn5+eapi0vL4vh6OioLMuZTEZ8mclkVFVlmH+FVTux8CxNd0tEpVIplUoVCoW7uzsimp6e7uyt/laTnZg/Vn/7+5mFtre3Jycn8/l83Xxzc9Pn85VKpVwup6pqIpHgmX8HVp2l1W49Hs/e3p5hGI+Pj263O5VKmR+18PezRn+ns3K57HQ6nU6nbCJOWCqVAoFAb2/vwMDAzs7O51Psnn8HlpzlF7t9eHiYm5uTJGlsbKzxB8zWzn4AeFhnLUi7Lv8AAAAASUVORK5CYII=', NULL),
(30, 4, NULL, '123123004', 'chakiri.mohamedyassir@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'chakiriieie', NULL, NULL, NULL, NULL, 0, 'chakiriieie', NULL, '2020-05-12 15:31:03', 0, 'iVBORw0KGgoAAAANSUhEUgAAAM0AAAA/CAIAAADMhNssAAAATHRFWHRDb3B5cmlnaHQAR2VuZXJhdGVkIHdpdGggQmFyY29kZSBHZW5lcmF0b3IgZm9yIFBIUCBodHRwOi8vd3d3LmJhcmNvZGVwaHAuY29tWX9wuAAAAAlwSFlzAAAOxAAADsQBlSsOGwAAA/FJREFUeJzt3TFIam0cx/FHKcPDsThCBGGNEtLQIBRhUE1STU2hbi1hDQeTECGoKedoCBGSpqYIagjHOEtD0NAg4RDk5FAiBwsznnd4uL7nZmnXzvlf4f4+Q5z77/F0nqcveZuycc7Zt9lsNsaYeInxuvmzxonQ+lVGzY/0nft8Z+X3v3pnK5uvv9oXzcqvvrnm3rN1CeLa/ulzAJgLnQEFdAYU0BlQQGdAAZ0BBXQGFNAZUEBnQAGdAQV0BhTQGVBAZ0ABnQEFdAYU0BlQQGdAAZ0BBXQGFNAZUEBnQAGdAQV0BhTQGVBAZ0ABnQEFdAYU0BlQQGdAAZ0BBXQGFNAZUEBnQAGdAQV0BhTQGVBAZ0ABnQEFdAYU0BlQQGdAAZ0BBXQGFNAZUEBnQAGdAQV0BhTQGVBAZ0DBZuLfE/434Uya4e8Jw9+BzoACOgMKv3V2fn4u3k0JvLy8rK6uKoricrkWFxcfHx8b83A4PDAwMDQ0tL29bVxv6bwbmLKXt7e3zc3NwcFBl8s1MzNzc3PT+iYuA6fT2dfXZ8ne+C9PT08ej8c4aWZ8yYeX/ylVVaenp0ulUrVaDYVCgUBAzKPR6MLCgq7rxWJxbGwsnU7TzDtm4pmYspdUKjU+Pl4sFsW1x+NpfROjSCSSSqU6fv6G5jP5/1AikYiqqmSdRaPRy8tLca1pWk9PD+e8VqtJkqRpmphnMpmpqSmC+U+YdSYm7kXXdfFxZ2dnYmKi9eKGXC7n9XprtVpnz2/0ZWdnZ2fBYPD29pasM6NEIuH3+znnhUKBMVYul8Vc0zRZlgnmP2HWmZi7l2w2a7fbZVm+u7tru1jw+/3ZbLazh/+g+UzsjLHn5+etra10Ov0Hb7fmOTo62t/fPzg4YIxVq1XGmCRJ4lOSJImJ1fNuYO5eQqGQruvz8/PxeLztYsbY9fX1w8PDysqKRbuzM8Y2NjZisdjIyIhFX6OF3d3dWCx2cXExOTnJGJNlmf06FHEhJlbPu4G5e+nt7XU6naqqXl1dtV3MGDs5OVleXrbqlwDR2enpaSKRUBQlEAgwxhRFURTl09Vf/VTswPv7ezgcPj4+1jRtbm5ODEdHR2VZzufz4p/5fN7n8xHMf8KsMzFrL+vr63t7e2JYr9fFz7C2G8/lcktLS509ebNPzsT4ttr2/2cmisfjXq+3VCp9mK+trQWDwUqlUiwWfT5fJpOhmXcDU/ZyeHg4PDxcKBTK5fLs7Kyqqq1vwjnXdd1ut4tfUS3ydzrTdd3hcDgcDtmgXq9zziuVSigU6u/vd7vdyWSy8RKr593ArL0kk0m32+12u6PR6Ovra+vFnPP7+3vGWGOlFf4DivhnZKDcfoQAAAAASUVORK5CYII=', NULL),
(31, 4, NULL, '10292932', 'yassir.chakiri12@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'chakisis', NULL, NULL, NULL, NULL, 0, 'chakisis', NULL, '2020-05-12 15:33:08', 0, 'iVBORw0KGgoAAAANSUhEUgAAAM0AAAA/CAIAAADMhNssAAAATHRFWHRDb3B5cmlnaHQAR2VuZXJhdGVkIHdpdGggQmFyY29kZSBHZW5lcmF0b3IgZm9yIFBIUCBodHRwOi8vd3d3LmJhcmNvZGVwaHAuY29tWX9wuAAAAAlwSFlzAAAOxAAADsQBlSsOGwAAA89JREFUeJzt3TFL61AcBfDbogVjqiQoglZHkeDgIChSQZ2KfoPUzUWiQ6hFSkHQyc7iIKWg+AGkoIOzZBUcFDp0M1MHLSVU0Za84WJfnmkl1fRv4Z3fIPE0ye2999i4NWDbNvMsEAgwxvglzmP3q86E+/oqJ/db8nIfL2d6H/17Z7qPW82L5sxWm9vqnt73rt13Emz6PgD8hZ4BBfQMKKBnQAE9AwroGVBAz4ACegYU0DOggJ4BBfQMKKBnQAE9AwroGVBAz4ACegYU0DOggJ4BBfQMKKBnQAE9AwroGVBAz4ACegYU0DOggJ4BBfQMKKBnQAE9AwroGVBAz4ACegYU0DOggJ4BBfQMKKBnQAE9AwroGVBAz4ACegYU0DOggJ4BBfQMKKBnQAE9AwroGVBAz4ACegYU0DOgEPDx+4T/T1gTN3yfMPwO9AwooGdA4Z+eXV5e8qcpgZeXl42NDUmSwuHw2tra4+NjI4/H44ODgyMjI3t7e87zO5p3A1/m8v7+vrOzMzw8HA6HFxcXb29vnUM8PDz09/fX6/VPQ3d86+0PT09PkUjEmbg5L/l0ebt0XV9YWCiVStVqVVXVaDTKc03TVldXLcsyTXNqaiqbzdLk3+bjmvgyl0wmMz09bZomP45EIo375/P5oaEhxlitVnOO62Xr2+Jek7+3Xl9f13WdrGeapl1fX/NjwzB6enps2357exMEwTAMnudyufn5eYL8J/xaEx/nYlkW/7m/vz8zM8PDTCajKMrx8bG7Z162vi0te5bP52Ox2N3dHVnPnFKp1OzsrG3bxWKRMVYul3luGIYoigT5T/i1Jv7O5ezsLBgMiqJ4f3/PE/4Jx7fY2TOPW98W95oEGWPPz8+7u7vZbNbbk9Znp6enR0dH/O+sWq0yxgRB4C8JgsCTTufdwN+5qKpqWdbKykoymeTJ2NiYe1CyrQ8yxra3txOJxPj4eKcHczs4OEgkEldXV3Nzc4wxURTZxwryA550Ou8G/s6lt7e3r69P1/Wbm5svBiXb+iBj7OLiIpVKSZIUjUYZY5IkSZLU9OxWn4rfUK/X4/H4+fm5YRjLy8s8nJiYEEWxUCjwXwuFgqIoBPlP+LUmfs1la2vr8PCQh7VarfGB15T3rW9LkzVxPlb9fUh/LZlMTk5OlkqlT/nm5mYsFqtUKqZpKoqSy+Vo8m7gy1xOTk5GR0eLxWK5XF5aWtJ13TmE+/8zZ965qf1OzyzLCoVCoVBIdOCTr1QqqqoODAzIspxOpxuXdDrvBn7NJZ1Oy7Isy7Kmaa+vr84hfqtnfwA3i1zblUkwhgAAAABJRU5ErkJggg==', NULL),
(32, 4, NULL, '9020292CS', 'chakiri.mohamedyassir@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'chakchak', NULL, NULL, NULL, NULL, 0, 'chakchak', NULL, '2020-05-12 15:39:48', 0, 'iVBORw0KGgoAAAANSUhEUgAAAM0AAAA/CAIAAADMhNssAAAATHRFWHRDb3B5cmlnaHQAR2VuZXJhdGVkIHdpdGggQmFyY29kZSBHZW5lcmF0b3IgZm9yIFBIUCBodHRwOi8vd3d3LmJhcmNvZGVwaHAuY29tWX9wuAAAAAlwSFlzAAAOxAAADsQBlSsOGwAAA9pJREFUeJzt3TFL62wUB/CnRQvGVElQBK2OIsHBQVCkgjoV/Qapm4tEh1CLlIKgk53FQUpB8QNIQQdnySo4KHToZqYOWkqooi3PHR5ub96mvW+vpucW7v83SDx90uSc/Jt0a4BzzjoWCAQYY2IX97b3VXdF+P1ebt5T6uR9OlnZ+dG/ttK73a4vmpXtLm6792xX8U6g86sstoMtzwPAX8gZUEDOgAJyBhSQM6CAnAEF5AwoIGdAATkDCsgZUEDOgAJyBhSQM6CAnAEF5AwoIGdAATkDCsgZUEDOgAJyBhSQM6CAnAEF5AwoIGdAATkDCsgZUEDOgAJyBhSQM6CAnAEF5AwoIGdAATkDCsgZUEDOgAJyBhSQM6CAnAEF5AwoIGdAATkDCsgZUEDOgAJyBhSQM6CAnAEF5AwoIGdAATkDCgEff0/434SZeOH3hOHvQM6AAnIGFP6Ts+vra/E0JfD29ra1taUoSjgc3tjYeH5+btTj8fjw8PDY2NjBwYF7fVfrvcCXXj4/P/f29kZHR8Ph8PLy8v39fWNxy4ELT09Pg4OD9Xq9W73xn15eXiKRiLvi5d6lafc/ZZrm0tJSqVSqVqu6rkejUVE3DGN9fd1xHNu2Z2ZmstksTf3LfJyJL71kMpnZ2VnbtsV2JBIRi9sNnHOez+dHRkYYY7Va7csn7+adya+hbG5umqZJljPDMG5vb8W2ZVl9fX2c84+PD0mSLMsS9Vwut7i4SFD/Dr9m4mMvjuOIv4eHh3Nzc6LYcuCc80wmo2na6ekpRc7y+XwsFnt4eCDLmVsqlZqfn+ecF4tFxli5XBZ1y7JkWSaof4dfM/G3l4uLi2AwKMvy4+Oj91iNgXPOxZ1PXPru5SzIGHt9fd3f389msx0/bP10fn5+cnIiPk/VapUxJkmSeEmSJFHpdr0X+NuLruuO46ytrSWTyaYDuQfOGJuYmOhaT78EGWO7u7uJRGJycpLgeE2Ojo4SicTNzc3CwgJjTJZl9nOCYkNUul3vBf720t/fPzAwYJrm3d2d+yhNAycTZIxdXV2lUilFUaLRKGNMURRFUVqubndX/IJ6vR6Pxy8vLy3LWl1dFcWpqSlZlguFgvi3UChomkZQ/w6/ZuJXLzs7O8fHx6JYq9UaN7yWA++SFjNxP1b/9/uZj5LJ5PT0dKlUaqpvb2/HYrFKpWLbtqZpuVyOpt4LfOnl7OxsfHy8WCyWy+WVlRXTNMXidgMX/P1+5vV3cuY4TigUCoVCsotoslKp6Lo+NDSkqmo6nW7s0u16L/Crl3Q6raqqqqqGYby/v/PfDlzods5+AHBTWn1ohKRFAAAAAElFTkSuQmCC', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `company_category`
--

CREATE TABLE `company_category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `company_category`
--

INSERT INTO `company_category` (`id`, `name`) VALUES
(1, 'categorie 1'),
(2, 'fourniture'),
(3, 'distribution'),
(4, 'peinture et décor');

-- --------------------------------------------------------

--
-- Structure de la table `company_service`
--

CREATE TABLE `company_service` (
  `company_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `company_service`
--

INSERT INTO `company_service` (`company_id`, `service_id`) VALUES
(1, 62),
(1, 63),
(1, 64),
(1, 65),
(1, 66),
(1, 67),
(1, 68),
(1, 69),
(1, 70),
(1, 71),
(1, 72),
(1, 73),
(1, 74),
(1, 75),
(1, 76),
(1, 77),
(1, 78),
(1, 79),
(1, 80),
(1, 81),
(15, 39),
(15, 40),
(15, 43),
(15, 56),
(15, 61);

-- --------------------------------------------------------

--
-- Structure de la table `dashboard_notification`
--

CREATE TABLE `dashboard_notification` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `owner_id` int(11) NOT NULL,
  `comment_id` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `seen` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `favorit`
--

CREATE TABLE `favorit` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `favorit_user_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `is_private` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`id`, `user_id`, `content`, `created_at`, `topic_id`, `receiver_id`, `is_private`) VALUES
(802, 5, 'salut', '2020-04-09 16:20:13', NULL, 19, 1),
(803, 5, 'coucou', '2020-04-09 16:20:27', 1, NULL, 0),
(804, 19, 'salut yass', '2020-04-09 16:20:48', NULL, 5, 1),
(805, 5, 'coucou tt le monde', '2020-04-09 16:21:36', 1, NULL, 0),
(806, 19, 'hey', '2020-04-09 16:21:49', NULL, 5, 1),
(807, 5, 'lol', '2020-04-09 16:26:10', NULL, 19, 1),
(808, 19, 'oui', '2020-04-09 16:26:14', NULL, 5, 1),
(809, 19, 'dd', '2020-04-09 16:26:19', 1, NULL, 0),
(810, 5, 'nico', '2020-04-09 16:26:36', NULL, 6, 1),
(811, 5, 'oui je dis oui', '2020-04-09 16:27:43', NULL, 6, 1),
(812, 19, 'moi aussi', '2020-04-09 16:27:50', NULL, 6, 1),
(813, 19, 'heeey', '2020-04-09 16:27:54', 1, NULL, 0),
(814, 6, 'roro', '2020-04-09 16:28:33', NULL, 19, 1),
(815, 6, 'mon roro', '2020-04-09 16:28:41', NULL, 19, 1),
(816, 6, '?', '2020-04-09 16:28:45', NULL, 19, 1),
(817, 6, 'bb', '2020-04-09 16:28:55', NULL, 19, 1),
(818, 5, 'allaaaah', '2020-04-09 16:29:37', 1, NULL, 0),
(826, 19, 'coucou', '2020-04-09 16:43:54', 1, NULL, 0),
(827, 19, 'hey', '2020-04-09 16:43:56', 1, NULL, 0),
(828, 5, 'dd', '2020-04-09 16:44:01', NULL, 6, 1),
(829, 5, 'dd', '2020-04-09 16:44:09', NULL, 19, 1),
(830, 5, 'zed', '2020-04-09 16:44:11', NULL, 19, 1),
(831, 5, 'zed', '2020-04-09 16:44:12', NULL, 19, 1),
(832, 19, 'agg', '2020-04-09 16:44:23', NULL, 5, 1),
(833, 19, 'ah oui', '2020-04-09 16:44:38', NULL, 5, 1),
(834, 19, 'lol', '2020-04-09 16:44:41', NULL, 5, 1),
(835, 5, 'tu fais peur', '2020-04-09 16:44:52', 1, NULL, 0),
(836, 5, 'ah oui', '2020-04-09 16:44:59', NULL, 19, 1),
(837, 5, 'oui ', '2020-04-09 16:45:07', NULL, 19, 1),
(838, 5, 'oui', '2020-04-09 16:45:08', NULL, 19, 1),
(839, 5, 'c\'est ca', '2020-04-09 16:45:21', NULL, 19, 1),
(840, 5, 'cool', '2020-04-09 16:45:24', NULL, 19, 1),
(841, 5, 'lol', '2020-04-09 16:45:29', 1, NULL, 0),
(842, 5, 'coucou nico', '2020-04-09 17:06:37', NULL, 6, 1),
(843, 5, 'coucou nico', '2020-04-09 17:06:45', NULL, 6, 1),
(844, 5, 'coucou nico', '2020-04-09 17:06:48', NULL, 6, 1),
(845, 5, 'nn', '2020-04-09 17:07:10', NULL, 6, 1),
(846, 5, 'ahh', '2020-04-09 17:07:22', NULL, 6, 1),
(847, 5, 'hey you !', '2020-04-21 12:39:58', 1, NULL, 0),
(848, 5, 'coucou', '2020-04-21 12:41:32', 13, NULL, 0),
(849, 5, 'hey', '2020-04-21 13:27:46', NULL, 5, 1),
(850, 5, 'coucou les général !', '2020-04-21 13:34:42', 1, NULL, 0),
(851, 6, 'coucou gégé', '2020-04-21 17:47:23', 1, NULL, 0),
(852, 6, 'hey', '2020-04-21 17:47:28', 1, NULL, 0),
(853, 5, 'lol', '2020-04-21 17:47:36', NULL, 6, 1),
(854, 5, 'ro ror roroor oror', '2020-04-21 17:49:04', NULL, 19, 1),
(855, 5, 'la la la la ', '2020-04-21 17:49:13', NULL, 19, 1),
(856, 5, 'hey toi là !', '2020-04-21 17:53:54', NULL, 19, 1),
(857, 5, 'coucou g', '2020-04-24 14:53:25', 1, NULL, 0),
(858, 5, 'heey', '2020-04-24 14:53:57', 1, NULL, 0),
(859, 6, 'llo', '2020-04-24 14:54:11', 1, NULL, 0),
(860, 5, 'dd', '2020-04-24 14:54:19', NULL, 6, 1),
(861, 6, 'bb', '2020-04-24 14:54:25', 1, NULL, 0),
(862, 5, 'ah', '2020-04-24 14:54:34', 1, NULL, 0),
(863, 6, 'cucouc j\'ai bien vu ton message la zoz', '2020-04-24 15:49:09', NULL, 5, 1),
(864, 6, 'hay l\'aziz', '2020-04-24 15:49:26', 1, NULL, 0),
(865, 6, 'comment vas tu ?', '2020-04-24 15:49:30', 1, NULL, 0),
(866, 6, 'bine ou bien ?', '2020-04-24 15:50:02', 1, NULL, 0),
(867, 6, 'lulu', '2020-04-24 15:50:36', 1, NULL, 0),
(868, 6, 'lolo', '2020-04-24 15:50:51', 1, NULL, 0),
(869, 6, 'kgkhb', '2020-04-24 15:51:01', 1, NULL, 0),
(870, 6, 'lolo', '2020-04-24 15:51:21', 1, NULL, 0),
(871, 6, 'dd', '2020-04-24 15:51:28', 1, NULL, 0),
(872, 6, 'coucou la zoz', '2020-04-24 15:51:49', 1, NULL, 0),
(873, 6, 'heus l\'znf', '2020-04-24 15:52:15', NULL, 5, 1),
(874, 6, 'jhv,v', '2020-04-24 15:52:26', NULL, 5, 1),
(875, 6, 'jkhvjhv', '2020-04-24 15:52:43', 1, NULL, 0),
(876, 6, 'lolozzad', '2020-04-24 16:04:11', NULL, 5, 1),
(877, 5, 'la la la la ', '2020-04-25 14:23:58', NULL, 6, 1),
(878, 5, 'couc ouc', '2020-04-25 14:24:09', NULL, 6, 1),
(879, 6, 'heeey tout le monde', '2020-04-25 14:24:43', 1, NULL, 0),
(880, 6, 'hey tout le monde ', '2020-04-25 14:25:05', 1, NULL, 0),
(881, 6, 'ah ', '2020-04-25 14:25:09', 1, NULL, 0),
(882, 6, 'ah oui ?', '2020-04-25 14:25:17', 1, NULL, 0),
(883, 6, 'didou', '2020-04-25 14:25:28', NULL, 5, 1),
(884, 6, 'dah', '2020-04-25 14:25:30', NULL, 5, 1),
(885, 6, 'keuh', '2020-04-25 14:25:32', NULL, 5, 1),
(886, 6, '1', '2020-04-25 14:26:04', NULL, 5, 1),
(887, 6, 'Z', '2020-04-25 14:26:08', NULL, 5, 1),
(888, 6, 'E', '2020-04-25 14:26:09', NULL, 5, 1),
(889, 5, 'hey', '2020-04-27 13:56:33', NULL, 6, 1),
(890, 5, 'hey you', '2020-04-27 13:58:00', NULL, 6, 1),
(891, 5, 're', '2020-04-27 13:58:25', 1, NULL, 0),
(892, 5, '2', '2020-04-27 13:58:39', 1, NULL, 0),
(893, 5, '3', '2020-04-27 13:59:01', 1, NULL, 0),
(894, 5, 'coucou', '2020-04-27 13:59:35', 1, NULL, 0),
(895, 5, 'coucou nico', '2020-04-27 14:05:45', NULL, 6, 1),
(896, 5, 'comment ca va ?', '2020-04-27 14:05:49', NULL, 6, 1),
(897, 5, 'salut tout le monde !', '2020-04-27 14:06:12', 1, NULL, 0),
(898, 24, 'hey deca', '2020-05-07 18:08:59', 21, NULL, 0),
(899, 24, 'dey deca deca', '2020-05-07 18:09:10', 21, NULL, 0),
(900, 24, 'ah ouais ?', '2020-05-07 18:09:33', 21, NULL, 0),
(901, 33, 'uoui', '2020-05-07 18:10:53', 21, NULL, 0),
(902, 24, 'oui', '2020-05-07 18:16:01', 1, NULL, 0),
(903, 24, 'lol', '2020-05-07 18:16:14', 21, NULL, 0),
(904, 33, 'moi je suis le nouveau', '2020-05-07 18:18:54', 21, NULL, 0),
(905, 33, 'taraaa', '2020-05-07 18:18:56', 21, NULL, 0),
(906, 5, 'coucou', '2020-05-17 20:50:07', 1, NULL, 0),
(907, 5, 'hey', '2020-05-17 20:51:22', 1, NULL, 0),
(917, 5, 'coucou test 2 comment tu vas ?', '2020-05-18 15:01:56', NULL, 17, 1),
(918, 5, 'hey', '2020-05-18 15:06:14', NULL, 17, 1),
(919, 5, 'coucou nico', '2020-05-18 18:44:34', NULL, 6, 1),
(920, 5, 'roro', '2020-05-18 18:44:45', NULL, 19, 1),
(921, 5, 'coucou teillon', '2020-05-18 18:45:49', NULL, 24, 1),
(922, 5, 'salut', '2020-05-19 15:08:21', NULL, 6, 1),
(923, 5, 'hey', '2020-05-19 15:09:01', NULL, 23, 1),
(924, 5, 'coucou', '2020-05-19 15:09:34', NULL, 23, 1);

-- --------------------------------------------------------

--
-- Structure de la table `migration_versions`
--

CREATE TABLE `migration_versions` (
  `version` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migration_versions`
--

INSERT INTO `migration_versions` (`version`, `executed_at`) VALUES
('20200415211359', '2020-04-15 21:14:09'),
('20200415212650', '2020-04-15 21:27:05'),
('20200415213355', '2020-04-15 21:34:07'),
('20200415235026', '2020-04-15 23:50:45'),
('20200416094005', '2020-04-16 09:40:48'),
('20200416160817', '2020-04-16 16:08:43'),
('20200416161709', '2020-04-16 16:17:33'),
('20200416162709', '2020-04-16 16:27:26'),
('20200416171059', '2020-04-16 17:11:13'),
('20200417233124', '2020-04-17 23:31:37'),
('20200423083406', '2020-04-23 08:34:30'),
('20200423162712', '2020-04-23 16:27:28'),
('20200427162548', '2020-04-27 16:26:09'),
('20200428151452', '2020-04-28 15:15:35'),
('20200429142015', '2020-04-29 14:20:44'),
('20200429155151', '2020-04-29 15:52:03'),
('20200504135204', '2020-05-04 13:52:20'),
('20200505140403', '2020-05-05 14:04:20'),
('20200505142038', '2020-05-05 14:20:57'),
('20200505150912', '2020-05-05 15:10:37'),
('20200506150145', '2020-05-06 15:02:10'),
('20200506155220', '2020-05-06 15:52:45'),
('20200506155628', '2020-05-06 15:56:58'),
('20200512104506', '2020-05-12 10:45:21'),
('20200512123728', '2020-05-12 12:37:46'),
('20200514121013', '2020-05-14 12:10:43'),
('20200517132906', '2020-05-17 13:29:27');

-- --------------------------------------------------------

--
-- Structure de la table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nb_messages` int(11) DEFAULT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notification`
--

INSERT INTO `notification` (`id`, `user_id`, `nb_messages`, `topic_id`, `receiver_id`) VALUES
(51, 19, 0, 1, NULL),
(52, 5, 0, NULL, 19),
(53, 5, 0, 1, NULL),
(54, 6, 2, NULL, 5),
(55, 6, 0, NULL, 19),
(56, 19, 0, NULL, 6),
(57, 6, 0, 1, NULL),
(58, 17, 2, NULL, 5),
(59, 17, 0, NULL, 19),
(60, 19, 1, NULL, 5),
(61, 5, 0, NULL, 6),
(62, 33, 0, 21, NULL),
(63, 24, 1, NULL, 5),
(64, 23, 2, NULL, 5);

-- --------------------------------------------------------

--
-- Structure de la table `opportunity_notification`
--

CREATE TABLE `opportunity_notification` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `seen` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `modified_at` datetime DEFAULT NULL,
  `likes_number` int(11) DEFAULT NULL,
  `comments_number` int(11) DEFAULT NULL,
  `distinct_user_comment_number` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `to_company_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `post`
--

INSERT INTO `post` (`id`, `user_id`, `title`, `description`, `category`, `created_at`, `modified_at`, `likes_number`, `comments_number`, `distinct_user_comment_number`, `status`, `to_company_id`) VALUES
(1, 19, 'hey', 'azert', 'Informations', '2020-04-06 11:44:03', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 5, 'Mon post trop génial', 'C\'est la folie ici', 'Informations', '2020-04-14 00:42:54', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 5, 'Mon post trop cool', 'c\'est super', 'Informations', '2020-04-14 01:30:40', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 5, 'mon offre d\'emploi', 'descrip', 'job', '2020-05-13 14:58:45', NULL, NULL, NULL, NULL, NULL, NULL),
(7, 5, 'opportunité', 'dzdz', 'opportunité', '2020-05-13 15:00:21', NULL, NULL, NULL, NULL, NULL, NULL),
(8, 5, 'mon oppo', 'zedzed', 'opportunite', '2020-05-13 15:01:26', NULL, NULL, NULL, NULL, NULL, NULL),
(9, 5, 'lknlk', 'kjbkjb', 'opportunite', '2020-05-13 18:46:56', NULL, NULL, NULL, NULL, NULL, NULL),
(10, 5, 'zedzed', 'zedzed', 'opportunite', '2020-05-14 14:56:59', NULL, NULL, NULL, NULL, NULL, NULL),
(12, 5, 'mon offre', 'description', 'opportunite', '2020-05-18 15:07:06', NULL, NULL, NULL, NULL, NULL, NULL),
(13, 6, 'offre nico', 'scsdcdc', 'opportunite', '2020-05-19 15:25:45', NULL, NULL, NULL, NULL, NULL, NULL),
(14, 5, 'qsxqsx', 'sqxqsx', 'opportunite', '2020-05-19 15:27:37', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `post_like`
--

CREATE TABLE `post_like` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `profile`
--

CREATE TABLE `profile` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `gender` int(11) DEFAULT NULL,
  `mobile_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firstname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `introduction` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `function_id` int(11) DEFAULT NULL,
  `is_completed` tinyint(1) NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `profile`
--

INSERT INTO `profile` (`id`, `user_id`, `gender`, `mobile_number`, `phone_number`, `lastname`, `firstname`, `filename`, `introduction`, `function_id`, `is_completed`, `updated_at`) VALUES
(1, 5, 1, '0111111111', '0111111111', 'chakiri', 'yassir', '5ea84ae81fe5c332921976.JPG', 'mon introduction !! ouii', 4, 1, '2020-04-28 17:25:28'),
(2, 6, 1, '07 11 11 11 11', NULL, 'strugarek', 'nicolas', '5e9ae81626b86921898724.JPG', NULL, 3, 1, '2020-04-18 13:44:22'),
(3, 15, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(4, 16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(5, 17, 0, '07 77 77 77 77', '07 77 77 77 77', 'test2', 'test2', NULL, NULL, 1, 1, NULL),
(6, 18, 1, '07 81 77 25 83', '07 81 77 25 83', 'test3', 'test3', NULL, NULL, 1, 1, NULL),
(7, 19, 0, '07 81 77 25 83', NULL, 'douglass', 'robert', NULL, NULL, 2, 1, NULL),
(8, 20, 0, '07 81 77 11 11', NULL, 'test4', 'test4', NULL, NULL, 2, 1, NULL),
(14, 23, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(15, 24, 1, '0111111111', NULL, 'deca', 'teillon', NULL, 'dscsc', 1, 1, NULL),
(16, 25, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(17, 26, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(18, 27, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(21, 33, 0, '01010101001', NULL, 'deca', 'lon', NULL, 'zeded', 2, 1, NULL),
(22, 34, 0, '0111111111', NULL, 'marl', 'boro', NULL, 'ZDZADAD', 3, 1, NULL),
(23, 35, 0, '01010101001', NULL, 'mal', 'boro2', NULL, 'sdfsdf', 1, 1, NULL),
(24, 36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(25, 37, 0, '07 77 77 77 77', NULL, 'ezdze', 'zedzed', NULL, 'zed', 3, 1, NULL),
(26, 38, 0, '01010101001', NULL, 'mag3', 'clay', NULL, 'ezdzd', 2, 1, NULL),
(27, 39, 0, '01010101001', NULL, 'user', 'mag 3', NULL, 'azs', 1, 1, NULL),
(28, 40, 1, '01 11 11 11 11', NULL, 'admin', 'trello', NULL, 'feff', 2, 1, NULL),
(29, 41, 0, '01010101001', NULL, 'sdcsdc', 'sdcsdc', NULL, 'sdcsdc', 3, 1, NULL),
(30, 42, 0, '01010101001', NULL, 'admin', 'mag4', NULL, 'zerzer', 1, 1, NULL),
(31, 43, 0, '0777777777', NULL, 'user', 'mag 4', NULL, 'sddfs', 3, 1, NULL),
(32, 44, 0, '01010101001', NULL, 'adminn', 'mags 5', NULL, 'sdcsdce', 1, 1, NULL),
(33, 45, 0, '07 77 77 77 77', NULL, 'admin', 'mag 6', NULL, 'zeezed', 1, 1, NULL),
(34, 46, 0, '01010101001', NULL, 'user', 'mag6 u', NULL, 'zedzed', 1, 1, NULL),
(35, 47, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(36, 48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(37, 49, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(38, 50, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(39, 51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(40, 52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(41, 53, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `publicity`
--

CREATE TABLE `publicity` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `modified_at` datetime DEFAULT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `recommandation`
--

CREATE TABLE `recommandation` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `create_at` datetime NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `recommandation`
--

INSERT INTO `recommandation` (`id`, `user_id`, `service_id`, `company_id`, `message`, `create_at`, `status`) VALUES
(1, 5, 33, 2, 'ffefer', '2020-04-16 13:46:34', 'Validated'),
(2, 5, 33, 2, 'ma recooo', '2020-04-16 13:57:09', 'Open'),
(3, 5, 37, 2, 'qdsds', '2020-04-16 15:50:38', 'Open'),
(4, 5, 37, 2, 'qqqq', '2020-04-16 15:52:57', 'Open'),
(5, 5, 37, 2, 'sdcsdc', '2020-04-16 15:53:53', 'Open'),
(6, 5, 37, 2, 'aaaa', '2020-04-16 15:54:59', 'Open'),
(7, 5, 37, 2, 'jeee teee tiens', '2020-04-16 16:26:51', 'Validated'),
(8, 5, 37, 2, 'il est piqué !', '2020-04-16 16:33:15', 'Validated'),
(9, 5, 37, 2, 'ma reco', '2020-04-16 16:59:37', 'Open'),
(10, 5, NULL, 2, 'lol', '2020-04-16 17:02:11', 'Validated'),
(11, 6, 36, 1, 'Très bonne société Life groups \r\nje recommande !', '2020-04-16 19:29:09', 'Validated');

-- --------------------------------------------------------

--
-- Structure de la table `score`
--

CREATE TABLE `score` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `points` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `score`
--

INSERT INTO `score` (`id`, `user_id`, `points`) VALUES
(4, 5, 430),
(5, 19, 40),
(6, 6, 50);

-- --------------------------------------------------------

--
-- Structure de la table `service`
--

CREATE TABLE `service` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(1500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(5,2) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `introduction` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filename1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filename2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filename3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_quote` tinyint(1) NOT NULL,
  `is_discovery` tinyint(1) NOT NULL,
  `discovery_content` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `service`
--

INSERT INTO `service` (`id`, `user_id`, `title`, `description`, `price`, `created_at`, `introduction`, `type_id`, `updated_at`, `category_id`, `filename`, `filename1`, `filename2`, `filename3`, `is_quote`, `is_discovery`, `discovery_content`) VALUES
(1, 5, 'Eos et hic voluptas vero dolorum', '<p>Omnis aut qui eius perspiciatis tenetur. Aut nobis molestiae possimus. Aut doloremque iure rem.</p><p>Quaerat corporis dolores accusamus et nulla est. Nisi porro voluptatem consequatur recusandae omnis temporibus qui suscipit. Nemo minima enim autem ut velit qui. Omnis vitae sint quo non enim officiis ut sint.</p><p>Et earum qui libero eum consequatur. Quia nisi sed consequuntur voluptatem vero. Qui omnis magni natus quia sit culpa repellat. Laudantium aut sed repellat.</p><p>Repellendus exce', '77.00', '2020-03-09 21:41:02', 'Et vitae et quia doloremque itaque ut.', 4, '2020-04-16 01:57:26', 1, '5e978ba3a43a8973951022.JPG', '5e979f66bde20980560282.JPG', '5e979f66c2293077239560.JPG', '5e979f66c270f288501339.JPG', 0, 0, NULL),
(2, 19, 'Ipsa quis et placeat et minima tenetur.ee', '<p>Rem commodi voluptatem voluptas laudantium. Incidunt distinctio illo facilis aut. Libero repellat tempore eum sed quas. Eum consequatur porro quaerat tempora enim velit.</p><p>Ipsa soluta cupiditate voluptas reiciendis labore. Distinctio odio sint velit sequi nihil perferendis.</p><p>In aliquam cum eos aut eaque est libero. Omnis aliquid error qui quia voluptatum necessitatibus. Rerum architecto ratione atque.</p><p>Voluptates eum molestiae error enim ea. Non voluptates et alias ut sunt sed a', '78.00', '2020-03-09 21:41:02', 'Consectetur et perspiciatis porro corporis quae illo rerum quia asperiores in ipsam quas fugit.', 2, '2020-04-16 01:59:51', 2, '5e979ff769de0795748505.JPG', '5e979ff771d5d076128841.JPG', '', '', 0, 0, NULL),
(3, 6, 'Est modi tenetur magnam.', '<p>Minus delectus rem quod officia delectus nesciunt voluptatem. Ut corporis fugiat porro doloremque laboriosam quas. Qui impedit voluptas enim et.</p><p>Sit ex vitae ea officia rem non. Molestias perferendis deleniti ut adipisci alias voluptatibus omnis voluptas. Quibusdam distinctio ex ut. Temporibus aut voluptas numquam ut repellat in eaque et.</p><p>Ullam quisquam qui accusantium sint et. Eum dolor nihil ullam in. Quis nesciunt atque enim.</p><p>Rerum est a eveniet quis aliquam sit. Cum quia', '75.00', '2020-03-09 21:41:02', 'Est voluptate voluptates temporibus officiis quos sit magnam voluptate blanditiis.', 5, NULL, 3, '', '', '', '', 0, 0, NULL),
(4, 17, 'Dolorum iusto perferendis est qui.', '<p>Optio laborum molestias autem voluptatem qui natus. Et et non sit enim. Earum fuga quo nam ipsum. Laudantium iusto maiores est autem vel veritatis id.</p><p>Earum architecto ut praesentium laborum eius quam rerum. Corporis modi perferendis ratione corporis ut temporibus. Sit vitae tempore temporibus aliquam eum hic rerum blanditiis. Soluta iusto sint quia.</p><p>Officiis magni non tempore doloremque quo. Quo repellat voluptas ab eveniet delectus. Cum doloremque voluptatibus doloremque nemo vo', '2.00', '2020-03-09 21:41:02', 'Vero et nemo omnis inventore vel autem quaerat sapiente ab est sequi.', NULL, '2020-04-16 00:41:02', 4, '5e978d7e9cf28713970467.JPG', '', '', '', 0, 0, NULL),
(6, 5, 'Dolore ullam omnis id veniam optio.', '<p>Consequuntur quibusdam doloremque ea dignissimos quis magni voluptate perferendis. Ipsa perferendis tenetur inventore eius. Dignissimos reprehenderit aut voluptates. Quos illum culpa enim sit earum vitae. Sed voluptatum modi dolor eum voluptate vero.</p><p>Qui et tempora similique debitis. Aut officia et reiciendis non consequatur magnam ratione. Recusandae ut nisi qui recusandae. Et autem libero quos velit enim totam. Laudantium quia eius consectetur odio nihil voluptatibus laboriosam.</p><p', '73.00', '2020-03-09 21:41:02', 'Ut sit repellat nihil totam quas iure perspiciatis hic tempora.', NULL, NULL, 2, '', '', '', '', 0, 0, NULL),
(7, 5, 'Labore explicabo voluptas eaque repudiandae atque.', '<p>Consequatur nesciunt quo animi illo ea nesciunt suscipit deserunt. Tenetur sint animi rerum. Magnam ratione iste consequatur natus. Sed dolorem sint qui beatae voluptatem quia atque.</p><p>Quibusdam sed consectetur sit asperiores. Mollitia voluptas ratione itaque ducimus. Aut dolore molestiae vel minima dolores quam consequatur molestiae.</p><p>Totam at accusantium facilis debitis. Ex hic suscipit non est. Corrupti iusto sunt sunt rerum ab voluptas.</p><p>Atque quos deserunt quia suscipit. To', '99.00', '2020-03-09 21:41:02', 'Voluptate aut et rerum ducimus atque est magnam fuga sint quis enim ipsam.', NULL, '2020-04-16 11:29:35', 3, '', '5e98256cf08bf296213006.JPG', '5e98257fcc3d2358816269.JPG', '5e97a068b9ddc443177476.JPG', 0, 0, NULL),
(8, 5, 'Aut quia veritatis natus nulla.', '<p>Incidunt expedita aut deserunt sit repellat. Et qui odit sapiente amet enim quo. Totam ducimus voluptatem necessitatibus nisi hic vel.</p><p>Et saepe sed doloribus accusamus vitae. Eos sint vero modi tempore modi et. Perspiciatis mollitia cumque corporis asperiores. Voluptas debitis unde fuga consequuntur id quis sit.</p><p>Placeat voluptatum vel quaerat vitae quia ea maiores dolores. Soluta debitis consequatur repellendus ipsa quo aliquam atque. Ex animi et iure reprehenderit odio impedit vo', '12.00', '2020-03-09 21:41:02', 'Iste quia perferendis ab iure id nobis aut ut harum dolores voluptatem ut libero.', NULL, NULL, 4, '', '', '', '', 0, 0, NULL),
(9, 5, 'Et ut delectus enim dicta minima et.', '<p>Dolor autem sequi ratione vitae. Unde ipsa laborum quo quis vero beatae. Culpa cumque accusamus aut deserunt.</p><p>Officia dicta odio ad qui. Distinctio vitae accusamus aperiam tenetur esse debitis nostrum vitae. Omnis doloremque dolorum tempora est quia qui excepturi. Aspernatur aliquid ipsam inventore ut non est consequatur velit.</p><p>Nobis fuga necessitatibus consectetur. Labore saepe consequatur sint. Sequi illo velit explicabo beatae.</p><p>Et atque dolorem totam id sit at. Dolorem de', '60.00', '2020-03-09 21:41:02', 'Praesentium maiores eos quam dicta aut error qui velit omnis.', NULL, NULL, 1, '', '', '', '', 0, 0, NULL),
(10, 5, 'Autem quasi aliquam occaecati.', '<p>Est minima incidunt quas eum est et quis. Praesentium tenetur laudantium rerum est magnam. Beatae tenetur culpa ducimus voluptatem vero.</p><p>Natus voluptas cum ut ullam. Neque ut repudiandae quis rerum. Facilis tempora voluptas hic aperiam suscipit veniam minus.</p><p>Excepturi quia earum quia pariatur excepturi. Nihil ipsam qui velit similique. Voluptatum et omnis exercitationem dolorum dolore assumenda debitis.</p><p>Ex sed esse odit aliquam repudiandae labore. Nemo ullam et dolorem totam', '13.00', '2020-03-09 21:41:02', 'Quidem est et architecto voluptatem et dolor et repudiandae quidem omnis enim.', NULL, NULL, 2, '', '', '', '', 0, 0, NULL),
(11, 5, 'Nostrum quia nemo ipsa dolores.', '<p>Quidem harum facilis non enim repudiandae vel quod. Ratione incidunt aut odio commodi vitae doloribus est aspernatur. Eos et omnis optio et.</p><p>Vero praesentium suscipit et ad velit voluptatibus. Facere similique aut consequatur recusandae qui non quisquam consequuntur. Id dolorem laboriosam sunt.</p><p>Eos voluptate et dicta fugit non. Eos enim repellendus sed voluptatem. Tempore et necessitatibus veniam accusamus velit officiis architecto. Distinctio aliquid eos quisquam ex vero quaerat.', '6.00', '2020-03-09 21:41:02', 'Ducimus omnis dolorem soluta quia ipsa vel natus debitis quia illo et et.', NULL, NULL, 3, '', '', '', '', 0, 0, NULL),
(12, 5, 'Quia rerum illum voluptas sunt.', '<p>Atque autem distinctio qui soluta et iure. Quae nemo dolores pariatur eligendi quidem aut unde. Non fugiat in exercitationem illo.</p><p>Qui explicabo eveniet itaque. Sapiente nihil corrupti officia facilis et aut. Debitis fugit et voluptas ab quibusdam praesentium tempore.</p><p>Quod sapiente error aut et dolores accusamus. Ab animi aliquam sunt aut harum. Magni occaecati quia maxime quaerat unde. Explicabo atque sapiente minus. Temporibus et minus sint et reprehenderit aut ut.</p><p>Debitis', '78.00', '2020-03-09 21:41:02', 'Molestias ea magni quo eius quidem et facilis iusto et ut sed.', NULL, NULL, 4, '', '', '', '', 0, 0, NULL),
(13, 5, 'Et sit praesentium repellendus possimus iusto fugiat.', '<p>Ipsum soluta inventore repudiandae maiores molestias tenetur et tempore. Ratione ut quam maiores odio voluptates. Dolor pariatur qui adipisci assumenda ex est. Consequatur velit nesciunt labore qui corporis quas.</p><p>Enim cupiditate quibusdam maiores beatae at atque. Quidem minima est eos deserunt aut vitae. Reiciendis non voluptatem quos illo. Pariatur placeat similique enim aut.</p><p>Autem omnis eaque eum nisi. Eos dolorum asperiores est autem in. Temporibus dolor commodi quaerat vero. V', '18.00', '2020-03-09 21:41:02', 'Ducimus nesciunt nisi aut necessitatibus sit dolorem qui et voluptates amet a excepturi rerum.', NULL, NULL, 1, '', '', '', '', 0, 0, NULL),
(14, 5, 'Quam necessitatibus ut suscipit atque.', '<p>Voluptas quam reprehenderit sunt error pariatur esse. Voluptatem molestiae aliquid recusandae voluptas ut molestiae. Enim dolore repellendus inventore sapiente sunt et saepe. Et debitis mollitia et nam hic.</p><p>Sint excepturi sunt velit officiis explicabo. Iste dolor enim dolores earum sapiente cumque. Voluptas magni alias dolores quas aut. Molestiae asperiores magni cumque ab molestiae molestiae necessitatibus.</p><p>Et ut debitis est molestiae commodi dolore a deserunt. Perferendis repreh', '3.00', '2020-03-09 21:41:02', 'Cum consequatur quia quaerat velit soluta sapiente maxime et velit enim assumenda velit et.', NULL, NULL, 2, '', '', '', '', 0, 0, NULL),
(15, 5, 'Quam iure ipsa inventore modi.', '<p>Magni voluptate qui doloremque nulla sed est quia. Quo vero temporibus nihil dolor quia. Esse nam dolores et impedit aut a ab. Repellendus eius impedit et quaerat sint.</p><p>Minima repellendus dolorem odit. Consequatur molestias repellendus dolores dolores ut.</p><p>Aut ut quia atque laborum quaerat nihil illum nulla. Delectus aut voluptatem recusandae impedit labore maiores sit molestiae. Distinctio error aut eveniet hic quis minus molestiae. Rem eum soluta aut maxime esse.</p><p>Aliquam iu', '52.00', '2020-03-09 21:41:02', 'Molestiae quam id labore perspiciatis nesciunt repudiandae culpa excepturi perspiciatis tempora facere.', NULL, NULL, 3, '', '', '', '', 0, 0, NULL),
(16, 5, 'Necessitatibus impedit dolores ut assumenda saepe.', '<p>Eum distinctio voluptatibus labore libero quia possimus. Placeat ut dolorem eaque quos aut. Eos deserunt aut eligendi libero laudantium. Maiores quis nihil provident sequi aut non.</p><p>Autem debitis a necessitatibus assumenda reprehenderit dolorem. Modi repellat aut eum ad veritatis sit. Consequatur nulla illo ex ut temporibus fugiat. Rerum iste dolore magni repudiandae mollitia ratione.</p><p>Nihil delectus perferendis quo soluta perferendis voluptatem sit. Est voluptatem officiis eaque di', '26.00', '2020-03-09 21:41:02', 'Aspernatur fuga ut voluptas recusandae ad eligendi inventore est est.', NULL, NULL, 4, '', '', '', '', 0, 0, NULL),
(17, 5, 'Magni nihil rerum dolore.', '<p>Voluptatem vitae quasi quis odit. Modi eaque voluptatum praesentium aut ducimus et sapiente. Dolorum quidem reprehenderit qui vitae molestias. Omnis at repellendus eos placeat illo tempora nesciunt.</p><p>Quis fuga id officiis quo. Et omnis repellendus accusamus dolor in ea. Sint nostrum quia sapiente et.</p><p>Dolores ut nulla omnis distinctio eum voluptate numquam. Eaque dolores quod deserunt ut dignissimos. Maiores eius in amet suscipit sed. Ipsum eos quis repellat qui.</p><p>Ullam officia', '82.00', '2020-03-09 21:41:02', 'Suscipit quia aspernatur dolor doloremque voluptatem quod.', NULL, NULL, 1, '', '', '', '', 0, 0, NULL),
(18, 5, 'Natus quis temporibus ea cupiditate.', '<p>Praesentium et tenetur qui voluptatibus. Dolore in officiis ut porro. Et dignissimos illum perspiciatis sit quo doloribus laudantium.</p><p>Qui magnam sint nemo nihil consequatur. Vel quisquam laborum qui laborum adipisci atque laudantium sed. Consequatur corporis molestias et omnis sed reprehenderit quo sapiente. Dignissimos perferendis dolorem unde occaecati nihil.</p><p>Sit nulla odit libero non porro. Alias expedita nulla reiciendis incidunt et voluptates consequatur. Nisi nisi dolorum ip', '56.00', '2020-03-09 21:41:02', 'Veritatis velit quisquam dolorem consequatur nemo et sequi nisi nulla minima dolores.', NULL, NULL, 2, '', '', '', '', 0, 0, NULL),
(19, 18, 'mon service', 'ma description de service', NULL, '2020-03-17 23:06:16', 'mon titre de service', 1, NULL, 3, '', '', '', '', 0, 0, NULL),
(24, 5, 'Aide à domicile', 'Si la transaction est validée, le vendeur et l’acheteur reçoivent un mail de confirmation (quel service, informations du client et du vendeur, confirmation de paiement, date choisie si calendrier associé)', '99.00', '2020-04-16 11:49:55', 'Je propose en ces temps difficile mon aide pour les personnes fragiles et dans le besoin', NULL, '2020-04-16 11:50:56', NULL, '5e982a80d6dbb481871797.JPG', NULL, NULL, NULL, 0, 0, NULL),
(27, 5, 'mon type', 'type typetype typetype typetype typetype type', NULL, '2020-04-16 12:27:32', 'type type', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL),
(32, 6, 'Mon service externe', 'descririririr', NULL, '2020-04-16 12:53:05', 'mon intro', 6, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL),
(33, 6, 'mon service plateform', 'desc', '12.00', '2020-04-16 12:54:14', 'into', 3, NULL, 2, NULL, NULL, NULL, NULL, 0, 1, 'Je vous propose 20min de démonstration pour les fidèles'),
(34, 5, 'mon service adminmag', 'mag', NULL, '2020-04-16 12:54:58', 'mag', 4, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL),
(36, 5, 'mon service test 1', 'trop coool', '1.00', '2020-04-16 14:24:57', 'test test', 4, '2020-04-16 14:24:57', 2, '5e984e9961d49993163921.JPG', NULL, NULL, NULL, 1, 1, 'Je vous propose 20min de démonstration pour découvrir notre service'),
(37, 6, 'mon service generique', 'description', '100.00', '2020-04-16 15:01:58', 'intro', 3, '2020-04-16 15:03:06', 4, '5e985747687a1806960615.JPG', '5e98578aaaa8b516809769.JPG', '5e98578ab33ca881764768.JPG', '5e98578ab696a485800602.JPG', 0, 0, NULL),
(38, 19, 'Mon service bilingue !', 'But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. \r\n\r\nNo one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical', '15.00', '2020-04-16 18:58:48', 'intro', 4, '2020-04-18 15:11:22', 3, '5e9afc7a31097030177657.JPG', NULL, NULL, NULL, 0, 1, 'Je vous mets plein la tête avec mon super service !'),
(39, 19, 'mon service', 'desc', NULL, '2020-04-20 11:50:53', 'intro', 5, NULL, 2, NULL, NULL, NULL, NULL, 0, 0, NULL),
(40, 19, 'mon service de company', 'introduction ouais', NULL, '2020-04-27 20:22:31', 'introduction ouais', 5, NULL, 2, NULL, NULL, NULL, NULL, 0, 0, NULL),
(41, 5, 'service 12', 'desc', NULL, '2020-04-27 22:45:41', 'intro', 4, NULL, 3, NULL, NULL, NULL, NULL, 0, 0, NULL),
(42, 5, 'Service life', 'description', NULL, '2020-04-27 22:58:16', 'intro', 4, NULL, 1, NULL, NULL, NULL, NULL, 0, 0, NULL),
(43, 19, 'service auchan', 'description', NULL, '2020-04-27 23:01:14', 'intro', 5, NULL, 3, NULL, NULL, NULL, NULL, 0, 0, NULL),
(44, 5, 'mon service test 1', 'desc', '10.00', '2020-04-29 13:55:32', 'intro', 4, '2020-04-29 13:55:33', 2, '5ea96b35d81b2791529482.JPG', NULL, NULL, NULL, 0, 0, NULL),
(49, 5, 'mon service spécial', 'descri', '10.00', '2020-04-29 17:16:03', 'intro', 4, '2020-04-29 17:16:03', 2, '5ea99a33b429f056306767.JPG', NULL, NULL, NULL, 0, 1, 'hey'),
(53, 5, '2 eme service pécial', 'desc', '10.00', '2020-04-29 17:27:40', 'intro', 4, NULL, 2, NULL, NULL, NULL, NULL, 0, 1, 'ouuiiiii'),
(54, 5, 'mon ser', 'azdazd', '1.00', '2020-04-29 17:30:48', 'zadazd', 4, '2020-04-29 17:30:49', 2, '5ea99da929299204032115.JPG', NULL, NULL, NULL, 0, 1, 'lololilo'),
(55, 5, 'mon ser 2', 'csdcsdc', '1.00', '2020-04-29 17:31:20', 'sd', 4, NULL, 3, NULL, NULL, NULL, NULL, 0, 1, 'scsdcsdc'),
(56, 19, 'mon service loyal spé', 'zedzede', '12.00', '2020-04-29 17:56:36', 'zedzed', 5, NULL, 2, NULL, NULL, NULL, NULL, 0, 1, 'looazdd'),
(57, 6, 'service offre spécial', 'ma description', NULL, '2020-04-30 14:20:33', 'introduction', 3, NULL, 2, NULL, NULL, NULL, NULL, 1, 1, 'Mon offre super géniale'),
(58, 5, 'mon service test', 'zedzed', '10.00', '2020-05-05 16:42:30', 'zedzed', 4, NULL, 2, NULL, NULL, NULL, NULL, 0, 0, NULL),
(59, 5, 'mon service test 2', 'zedzedzed', '101.00', '2020-05-05 16:42:58', 'dzzed', 4, NULL, 2, NULL, NULL, NULL, NULL, 0, 0, NULL),
(60, 5, 'mon service local', 'azdazd', NULL, '2020-05-05 18:13:55', 'zadazd', 4, NULL, 2, NULL, NULL, NULL, NULL, 0, 0, NULL),
(61, 19, 'serser', 'sdfsdf', '0.00', '2020-05-05 18:38:51', 'sdfsdf', 5, NULL, 3, NULL, NULL, NULL, NULL, 0, 1, NULL),
(62, 5, 'mon test toast', 'zedzed', '10.00', '2020-05-12 19:43:49', 'zdzd', 5, NULL, 3, NULL, NULL, NULL, NULL, 0, 1, 'zedzed'),
(63, 5, 'zedzed', 'zedzed', '11.00', '2020-05-12 19:45:02', 'zedzed', 5, NULL, 2, NULL, NULL, NULL, NULL, 0, 1, 'zedzed'),
(64, 5, 'zedzed', 'zedzed', '11.00', '2020-05-12 19:45:43', 'zedzed', 5, NULL, 2, NULL, NULL, NULL, NULL, 0, 1, 'zedzed'),
(65, 5, 'zedzed', 'zdzedze', '11.00', '2020-05-12 19:48:42', 'zdzed', 5, NULL, 2, NULL, NULL, NULL, NULL, 0, 1, 'zedzed'),
(66, 5, 'ezdzed', 'zedzd', '10.00', '2020-05-13 10:02:46', 'zedzed', 5, NULL, 2, NULL, NULL, NULL, NULL, 0, 1, 'zdzedezd'),
(67, 5, 'dazdz', 'zedzd', '10.00', '2020-05-13 10:07:35', 'dzedzd', 5, NULL, 2, NULL, NULL, NULL, NULL, 0, 1, 'sdfsdf'),
(68, 5, 'scddc', 'sdcsdcs', '10.00', '2020-05-13 10:27:25', 'sdcsdc', 5, NULL, 3, NULL, NULL, NULL, NULL, 0, 0, NULL),
(69, 5, 'zezed', 'dzedzed', '10.00', '2020-05-13 10:38:51', 'ezdze', 5, NULL, 1, NULL, NULL, NULL, NULL, 0, 0, NULL),
(70, 5, 'zedze', 'zedzed', NULL, '2020-05-13 10:49:27', 'dzez', 5, NULL, 2, NULL, NULL, NULL, NULL, 1, 0, NULL),
(71, 5, 'dssdc', 'sdcsdc', '10.00', '2020-05-13 10:51:30', 'sdcsc', 5, NULL, 2, NULL, NULL, NULL, NULL, 0, 0, NULL),
(72, 5, 'dssdc', 'sdcsdc', '10.00', '2020-05-13 10:52:16', 'sdcsc', 5, NULL, 2, NULL, NULL, NULL, NULL, 0, 0, NULL),
(73, 5, 'dssdc', 'sdcsdc', '10.00', '2020-05-13 10:56:57', 'sdcsc', 5, NULL, 2, NULL, NULL, NULL, NULL, 0, 0, NULL),
(74, 5, 'dssdc', 'sdcsdc', '10.00', '2020-05-13 10:57:33', 'sdcsc', 5, NULL, 2, NULL, NULL, NULL, NULL, 0, 0, NULL),
(75, 5, 'zedezd', 'zdzd', '1.00', '2020-05-13 11:05:24', 'zedzd', 5, NULL, 3, NULL, NULL, NULL, NULL, 0, 0, NULL),
(76, 5, 'zedezd', 'zdzd', '1.00', '2020-05-13 11:06:11', 'zedzd', 5, NULL, 3, NULL, NULL, NULL, NULL, 0, 0, NULL),
(77, 5, 'zedzed', 'zedzed', '10.00', '2020-05-13 11:07:46', 'zedzed', 5, NULL, 3, NULL, NULL, NULL, NULL, 0, 1, 'zdzed'),
(78, 5, 'dsfsdf', 'sdfsdf', '10.00', '2020-05-13 11:11:11', 'sdfsfd', 5, NULL, 3, NULL, NULL, NULL, NULL, 0, 1, 'sdcsdcsc'),
(79, 5, 'sds', 'dsfsdf', '10.00', '2020-05-13 11:14:34', 'sdfsdf', 5, NULL, 3, NULL, NULL, NULL, NULL, 0, 1, 'sdfsfd'),
(80, 5, 'sdcsdc', 'sdcsdc', '19.00', '2020-05-13 11:21:42', 'sdcsdc', 5, NULL, 3, NULL, NULL, NULL, NULL, 0, 1, 'zedzd'),
(81, 5, 'dzzed', 'zedzed', '10.00', '2020-05-13 11:23:00', 'zedz', 5, '2020-05-18 13:58:47', 3, '5ec278771dba3875849345.JPG', NULL, NULL, NULL, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `service_category`
--

CREATE TABLE `service_category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `service_category`
--

INSERT INTO `service_category` (`id`, `name`) VALUES
(1, 'Décor & Peinture'),
(2, 'Papeterie'),
(3, 'Menuiserie'),
(4, 'Comptabilité');

-- --------------------------------------------------------

--
-- Structure de la table `store`
--

CREATE TABLE `store` (
  `id` int(11) NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_number` int(11) NOT NULL,
  `address_street` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_post_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(11,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `introduction` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `default_adviser_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `store`
--

INSERT INTO `store` (`id`, `reference`, `name`, `email`, `phone`, `address_number`, `address_street`, `address_post_code`, `city`, `country`, `filename`, `latitude`, `longitude`, `slug`, `introduction`, `description`, `modified_at`, `default_adviser_id`) VALUES
(1, '111111', 'Mon magasin 12', 'magasin@dev.fr', '234234324234', 25, 'Avenue des entreprneurs', '78250', 'Les Clayes sous bois', 'AD', NULL, NULL, NULL, 'mon_magasin_1', 'mon intro', 'description', '0000-00-00 00:00:00', NULL),
(3, '', 'mon mag 2', 'mag2@dev.fr', '', 0, '', '', '', '', NULL, NULL, NULL, 'mon_magasin_2', NULL, NULL, '0000-00-00 00:00:00', NULL),
(4, '12323434', 'magasin bv les clayes', 'magasin@dev.fr', '0111111111', 9, 'ZEFEf', '19110', 'les clayes', 'AF', NULL, NULL, NULL, 'magasin-bv-les-clayes', 'magasin', 'magasin', NULL, NULL),
(5, '12323434', 'mon mag paris', 'magas@dev.fr', '0101111111', 11, 'azdzd', '11234', 'Paris', 'AF', NULL, NULL, NULL, 'mon-mag-paris', 'magasin', 'magason', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `store_service`
--

CREATE TABLE `store_service` (
  `id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `store_service`
--

INSERT INTO `store_service` (`id`, `store_id`, `service_id`, `price`) VALUES
(4, 5, 60, NULL),
(8, 5, 61, NULL),
(12, 5, 33, '15.00');

-- --------------------------------------------------------

--
-- Structure de la table `topic`
--

CREATE TABLE `topic` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `topic`
--

INSERT INTO `topic` (`id`, `name`, `type_id`) VALUES
(1, 'general', 1),
(19, 'peinture-et-decor', 4),
(20, 'categorie-1', 4),
(21, 'decathelon', 5),
(22, 'distribution', 4),
(23, 'marlboro', 5),
(24, 'magasin-bv-les-clayes', 6),
(25, 'trello', 5),
(26, 'fourniture', 4),
(27, 'responsable_marketing', 6),
(28, 'chef_de_projet', 6),
(29, 'mon-mag-paris', 7),
(30, 'developpeur', 6),
(31, 'responsable_digital', 6),
(32, 'mon_magasin_2', 7),
(33, 'Franchisé', 8),
(34, 'magasin-bv-les-clayes', 7),
(35, '99292912', 5),
(36, 'lechakiri', 5),
(37, 'chachak', 5),
(38, 'chakchak', 5),
(39, 'chakiriieie', 5),
(40, 'chakisis', 5);

-- --------------------------------------------------------

--
-- Structure de la table `topic_type`
--

CREATE TABLE `topic_type` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `topic_type`
--

INSERT INTO `topic_type` (`id`, `name`) VALUES
(1, 'admin'),
(4, 'categoryCompany'),
(5, 'company'),
(6, 'store'),
(7, 'function'),
(8, 'admin_store');

-- --------------------------------------------------------

--
-- Structure de la table `type_service`
--

CREATE TABLE `type_service` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `type_service`
--

INSERT INTO `type_service` (`id`, `name`) VALUES
(3, 'plateform'),
(4, 'store'),
(5, 'company'),
(6, 'foreign');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `store_id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` tinytext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `created_at` datetime NOT NULL,
  `modified_at` datetime DEFAULT NULL,
  `is_valid` tinyint(1) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT NULL,
  `reset_token` longtext COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `company_id`, `store_id`, `type_id`, `email`, `password`, `roles`, `created_at`, `modified_at`, `is_valid`, `is_deleted`, `reset_token`) VALUES
(5, 1, 5, 1, 'yassir@dev.fr', '$2y$13$jvUPmP9p6CQn1RCxMAT91.xZNhdPVp/NM7MWRboVnaA.Pzz80C6f2', 'a:1:{i:0;s:18:\"ROLE_ADMIN_COMPANY\";}', '2020-03-12 00:00:00', NULL, 1, 0, NULL),
(6, 2, 1, NULL, 'nicolas@dev.fr', '$2y$13$jvUPmP9p6CQn1RCxMAT91.xZNhdPVp/NM7MWRboVnaA.Pzz80C6f2', 'a:1:{i:0;s:20:\"ROLE_ADMIN_PLATEFORM\";}', '2020-03-12 00:00:00', NULL, 1, 0, NULL),
(17, 1, 4, NULL, 'yassir.chakiri12@gmail.com', '$2y$13$wloNxI9uZqnoNPtoqbZgvu4lPzr1djss3vZGsUQKRR/RnbQQMYTUa', 'a:1:{i:0;s:16:\"ROLE_ADMIN_STORE\";}', '2020-03-13 11:36:05', NULL, 1, NULL, NULL),
(18, 14, 3, NULL, 'test3@dev.fr', '$2y$13$oApVFVWdgrPKrdws9iNeT.TYmRNDOgMUBb7EB/Zl3OjCQ3SQOSE4C', 'a:1:{i:0;s:9:\"ROLE_USER\";}', '2020-03-17 19:19:08', NULL, 0, NULL, NULL),
(19, 15, 1, NULL, 'test4@dev.fr', '$2y$13$bJNaHZtg2IkWkYPZ2yFWO.1kknMbfgbor5zb/7el1oYmihM7wkY72', 'a:1:{i:0;s:18:\"ROLE_ADMIN_COMPANY\";}', '2020-03-18 11:49:53', NULL, 0, NULL, NULL),
(23, 22, 1, NULL, 'test123@dev.fr', '$2y$13$uMrLL.X9/S45Ptv39EiFae8ILhm4ehIcCl8Jqnd4HuUeuOKZ3WC3G', 'a:2:{i:0;s:9:\"ROLE_USER\";i:1;s:18:\"ROLE_ADMIN_COMPANY\";}', '2020-04-06 15:50:54', NULL, 0, NULL, NULL),
(24, 23, 5, NULL, 'decathelon1@dev.fr', '$2y$13$ovUCrCv8R72Td2QZqoqF2ups0CfPm8Vf6llBLqPH0877XoSYD5hle', 'a:1:{i:0;s:18:\"ROLE_ADMIN_COMPANY\";}', '2020-05-07 15:17:52', NULL, 0, 0, NULL),
(27, 23, 5, NULL, 'decathelon2@dev.fr', '$2y$13$jvUPmP9p6CQn1RCxMAT91.xZNhdPVp/NM7MWRboVnaA.Pzz80C6f2', 'a:1:{i:0;s:9:\"ROLE_USER\";}', '2020-05-07 17:46:04', NULL, 0, 0, NULL),
(33, 23, 5, NULL, 'decathelon3@dev.fr', '$2y$13$jvUPmP9p6CQn1RCxMAT91.xZNhdPVp/NM7MWRboVnaA.Pzz80C6f2', 'a:1:{i:0;s:9:\"ROLE_USER\";}', '2020-05-07 18:06:37', NULL, 0, 0, NULL),
(34, 24, 5, NULL, 'malboro1@dev.fr', '$2y$13$26Rfh7pqY8azScifxDdhcu/CekTLqtsr.hQA7Fd2QIUWhgL/Lo2gu', 'a:1:{i:0;s:18:\"ROLE_ADMIN_COMPANY\";}', '2020-05-07 18:52:51', NULL, 0, 0, NULL),
(35, 24, 5, NULL, 'malboro2@dev.fr', '$2y$13$jvUPmP9p6CQn1RCxMAT91.xZNhdPVp/NM7MWRboVnaA.Pzz80C6f2', 'a:1:{i:0;s:9:\"ROLE_USER\";}', '2020-05-11 11:39:02', NULL, 0, 0, NULL),
(36, NULL, 4, 4, 'adminmag1@dev.fr', '$2y$13$jvUPmP9p6CQn1RCxMAT91.xZNhdPVp/NM7MWRboVnaA.Pzz80C6f2', 'a:2:{i:0;s:16:\"ROLE_ADMIN_STORE\";i:1;s:9:\"ROLE_USER\";}', '2020-05-11 12:28:14', NULL, 1, 0, NULL),
(37, NULL, 3, 4, 'adminmag2@dev.fr', '$2y$13$jvUPmP9p6CQn1RCxMAT91.xZNhdPVp/NM7MWRboVnaA.Pzz80C6f2', 'a:2:{i:0;s:16:\"ROLE_ADMIN_STORE\";i:1;s:9:\"ROLE_USER\";}', '2020-05-11 12:32:49', NULL, 1, 0, NULL),
(38, NULL, 4, 1, 'adminmag3@dev.fr', '$2y$13$jvUPmP9p6CQn1RCxMAT91.xZNhdPVp/NM7MWRboVnaA.Pzz80C6f2', 'a:2:{i:0;s:16:\"ROLE_ADMIN_STORE\";i:1;s:9:\"ROLE_USER\";}', '2020-05-11 13:08:23', NULL, 1, 0, NULL),
(39, NULL, 4, 2, 'usermag3@dev.fr', '$2y$13$jvUPmP9p6CQn1RCxMAT91.xZNhdPVp/NM7MWRboVnaA.Pzz80C6f2', 'a:1:{i:0;s:9:\"ROLE_USER\";}', '2020-05-11 13:18:29', NULL, 0, 0, NULL),
(40, 25, 5, 3, 'trello1@dev.fr', '$2y$13$BKhJ7mcWbZ5jCiG9df8Tqe8fWSQYPSfwLQyqaWRu7EfNGA1f3yAfS', 'a:1:{i:0;s:18:\"ROLE_ADMIN_COMPANY\";}', '2020-05-11 14:24:14', NULL, 1, 0, NULL),
(41, 25, 5, 6, 'trello2@dev.fr', '$2y$13$jvUPmP9p6CQn1RCxMAT91.xZNhdPVp/NM7MWRboVnaA.Pzz80C6f2', 'a:1:{i:0;s:9:\"ROLE_USER\";}', '2020-05-11 14:33:22', NULL, 1, 0, NULL),
(42, NULL, 5, 1, 'adminmag4@dev.fr', '$2y$13$jvUPmP9p6CQn1RCxMAT91.xZNhdPVp/NM7MWRboVnaA.Pzz80C6f2', 'a:2:{i:0;s:16:\"ROLE_ADMIN_STORE\";i:1;s:9:\"ROLE_USER\";}', '2020-05-12 12:57:00', NULL, 1, 0, NULL),
(43, NULL, 5, 2, 'usermag4@dev.fr', '$2y$13$jvUPmP9p6CQn1RCxMAT91.xZNhdPVp/NM7MWRboVnaA.Pzz80C6f2', 'a:1:{i:0;s:9:\"ROLE_USER\";}', '2020-05-12 13:00:10', NULL, 0, 0, NULL),
(44, NULL, 3, 1, 'adminmag5@dev.fr', '$2y$13$jvUPmP9p6CQn1RCxMAT91.xZNhdPVp/NM7MWRboVnaA.Pzz80C6f2', 'a:2:{i:0;s:16:\"ROLE_ADMIN_STORE\";i:1;s:9:\"ROLE_USER\";}', '2020-05-12 13:19:12', NULL, 1, 0, NULL),
(45, NULL, 4, 1, 'adminmag6@dev.fr', '$2y$13$jvUPmP9p6CQn1RCxMAT91.xZNhdPVp/NM7MWRboVnaA.Pzz80C6f2', 'a:2:{i:0;s:16:\"ROLE_ADMIN_STORE\";i:1;s:9:\"ROLE_USER\";}', '2020-05-12 14:41:05', NULL, 1, 0, NULL),
(46, NULL, 4, 2, 'usermag6@dev.fr', '$2y$13$jvUPmP9p6CQn1RCxMAT91.xZNhdPVp/NM7MWRboVnaA.Pzz80C6f2', 'a:1:{i:0;s:9:\"ROLE_USER\";}', '2020-05-12 14:44:46', NULL, 1, 0, NULL),
(53, 32, 4, 3, 'chakiri.mohamedyassir@gmail.com', '$2y$13$jo8kQA2QfvNgIsdLRWEzC.rHFz7l0WFhwSxJsyqpUVkC7MGcI..yG', 'a:1:{i:0;s:18:\"ROLE_ADMIN_COMPANY\";}', '2020-05-12 15:39:47', NULL, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user_function`
--

CREATE TABLE `user_function` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user_function`
--

INSERT INTO `user_function` (`id`, `name`, `slug`) VALUES
(1, 'chef de projet', 'chef_de_projet'),
(2, 'développeur', 'developpeur'),
(3, 'responsable digital', 'responsable_digital'),
(4, 'responsable marketing', 'responsable_marketing');

-- --------------------------------------------------------

--
-- Structure de la table `user_topic`
--

CREATE TABLE `user_topic` (
  `user_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user_topic`
--

INSERT INTO `user_topic` (`user_id`, `topic_id`) VALUES
(5, 1),
(5, 19),
(6, 1),
(19, 1),
(20, 1),
(23, 1),
(24, 1),
(24, 19),
(24, 21),
(25, 21),
(26, 1),
(26, 21),
(27, 1),
(27, 21),
(33, 1),
(33, 19),
(33, 21),
(34, 1),
(34, 22),
(34, 23),
(35, 1),
(35, 22),
(35, 23),
(38, 24),
(39, 28),
(40, 1),
(40, 25),
(40, 26),
(41, 1),
(41, 25),
(41, 26),
(42, 29),
(43, 29),
(43, 31),
(44, 32),
(44, 33),
(45, 33),
(45, 34),
(46, 28),
(46, 34),
(47, 1),
(47, 35),
(48, 1),
(48, 36),
(49, 1),
(49, 37),
(50, 1),
(50, 38),
(51, 1),
(51, 39),
(52, 1),
(52, 40),
(53, 1),
(53, 38);

-- --------------------------------------------------------

--
-- Structure de la table `user_type`
--

CREATE TABLE `user_type` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user_type`
--

INSERT INTO `user_type` (`id`, `name`) VALUES
(1, 'admin_magasin'),
(2, 'conseiller_magasin'),
(3, 'admin_entreprise'),
(4, 'patron_magasin'),
(5, 'admin_Platforme'),
(6, 'collaborateur_entreprise');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `abuse`
--
ALTER TABLE `abuse`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_98AF83314B89032C` (`post_id`),
  ADD KEY `IDX_98AF8331F8697D13` (`comment_id`),
  ADD KEY `IDX_98AF8331A76ED395` (`user_id`);

--
-- Index pour la table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9474526C4B89032C` (`post_id`),
  ADD KEY `IDX_9474526CA76ED395` (`user_id`);

--
-- Index pour la table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_4FBF094F26E94372` (`siret`),
  ADD KEY `IDX_4FBF094FB092A811` (`store_id`),
  ADD KEY `IDX_4FBF094F12469DE2` (`category_id`);

--
-- Index pour la table `company_category`
--
ALTER TABLE `company_category`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `company_service`
--
ALTER TABLE `company_service`
  ADD PRIMARY KEY (`company_id`,`service_id`),
  ADD KEY `IDX_C1CF8005979B1AD6` (`company_id`),
  ADD KEY `IDX_C1CF8005ED5CA9E6` (`service_id`);

--
-- Index pour la table `dashboard_notification`
--
ALTER TABLE `dashboard_notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_596D0BE8A76ED395` (`user_id`),
  ADD KEY `IDX_596D0BE84B89032C` (`post_id`),
  ADD KEY `IDX_596D0BE87E3C61F9` (`owner_id`),
  ADD KEY `IDX_596D0BE8F8697D13` (`comment_id`);

--
-- Index pour la table `favorit`
--
ALTER TABLE `favorit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_17575191A76ED395` (`user_id`),
  ADD KEY `IDX_1757519176E51759` (`favorit_user_id`),
  ADD KEY `IDX_17575191979B1AD6` (`company_id`);

--
-- Index pour la table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_B6BD307FA76ED395` (`user_id`),
  ADD KEY `IDX_B6BD307F1F55203D` (`topic_id`),
  ADD KEY `IDX_B6BD307FCD53EDB6` (`receiver_id`);

--
-- Index pour la table `migration_versions`
--
ALTER TABLE `migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_BF5476CAA76ED395` (`user_id`),
  ADD KEY `IDX_BF5476CA1F55203D` (`topic_id`),
  ADD KEY `IDX_BF5476CACD53EDB6` (`receiver_id`);

--
-- Index pour la table `opportunity_notification`
--
ALTER TABLE `opportunity_notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_1738B3A54B89032C` (`post_id`),
  ADD KEY `IDX_1738B3A5A76ED395` (`user_id`);

--
-- Index pour la table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_5A8A6C8DB0A8F8AB` (`to_company_id`),
  ADD KEY `IDX_5A8A6C8DA76ED395` (`user_id`);

--
-- Index pour la table `post_like`
--
ALTER TABLE `post_like`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_653627B8A76ED395` (`user_id`),
  ADD KEY `IDX_653627B84B89032C` (`post_id`);

--
-- Index pour la table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8157AA0FA76ED395` (`user_id`),
  ADD KEY `IDX_8157AA0F67048801` (`function_id`);

--
-- Index pour la table `publicity`
--
ALTER TABLE `publicity`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `recommandation`
--
ALTER TABLE `recommandation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C7782A28A76ED395` (`user_id`),
  ADD KEY `IDX_C7782A28ED5CA9E6` (`service_id`),
  ADD KEY `IDX_C7782A28979B1AD6` (`company_id`);

--
-- Index pour la table `score`
--
ALTER TABLE `score`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_32993751A76ED395` (`user_id`);

--
-- Index pour la table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_E19D9AD2A76ED395` (`user_id`),
  ADD KEY `IDX_E19D9AD2C54C8C93` (`type_id`),
  ADD KEY `IDX_E19D9AD212469DE2` (`category_id`);

--
-- Index pour la table `service_category`
--
ALTER TABLE `service_category`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `store`
--
ALTER TABLE `store`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_FF5758776440E1B5` (`default_adviser_id`);

--
-- Index pour la table `store_service`
--
ALTER TABLE `store_service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F895BB35B092A811` (`store_id`),
  ADD KEY `IDX_F895BB35ED5CA9E6` (`service_id`);

--
-- Index pour la table `topic`
--
ALTER TABLE `topic`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9D40DE1BC54C8C93` (`type_id`);

--
-- Index pour la table `topic_type`
--
ALTER TABLE `topic_type`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `type_service`
--
ALTER TABLE `type_service`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_8D93D649979B1AD6` (`company_id`),
  ADD KEY `IDX_8D93D649B092A811` (`store_id`),
  ADD KEY `IDX_8D93D649C54C8C93` (`type_id`);

--
-- Index pour la table `user_function`
--
ALTER TABLE `user_function`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user_topic`
--
ALTER TABLE `user_topic`
  ADD PRIMARY KEY (`user_id`,`topic_id`),
  ADD KEY `IDX_7F822543A76ED395` (`user_id`),
  ADD KEY `IDX_7F8225431F55203D` (`topic_id`);

--
-- Index pour la table `user_type`
--
ALTER TABLE `user_type`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `abuse`
--
ALTER TABLE `abuse`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `company`
--
ALTER TABLE `company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT pour la table `company_category`
--
ALTER TABLE `company_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `dashboard_notification`
--
ALTER TABLE `dashboard_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `favorit`
--
ALTER TABLE `favorit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=925;
--
-- AUTO_INCREMENT pour la table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;
--
-- AUTO_INCREMENT pour la table `opportunity_notification`
--
ALTER TABLE `opportunity_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT pour la table `post_like`
--
ALTER TABLE `post_like`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `profile`
--
ALTER TABLE `profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;
--
-- AUTO_INCREMENT pour la table `publicity`
--
ALTER TABLE `publicity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `recommandation`
--
ALTER TABLE `recommandation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT pour la table `score`
--
ALTER TABLE `score`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `service`
--
ALTER TABLE `service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;
--
-- AUTO_INCREMENT pour la table `service_category`
--
ALTER TABLE `service_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `store`
--
ALTER TABLE `store`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `store_service`
--
ALTER TABLE `store_service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT pour la table `topic`
--
ALTER TABLE `topic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
--
-- AUTO_INCREMENT pour la table `topic_type`
--
ALTER TABLE `topic_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT pour la table `type_service`
--
ALTER TABLE `type_service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;
--
-- AUTO_INCREMENT pour la table `user_function`
--
ALTER TABLE `user_function`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `user_type`
--
ALTER TABLE `user_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `abuse`
--
ALTER TABLE `abuse`
  ADD CONSTRAINT `FK_98AF83314B89032C` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`),
  ADD CONSTRAINT `FK_98AF8331A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_98AF8331F8697D13` FOREIGN KEY (`comment_id`) REFERENCES `comment` (`id`);

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `FK_9474526C4B89032C` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`),
  ADD CONSTRAINT `FK_9474526CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `company`
--
ALTER TABLE `company`
  ADD CONSTRAINT `FK_4FBF094F12469DE2` FOREIGN KEY (`category_id`) REFERENCES `company_category` (`id`),
  ADD CONSTRAINT `FK_4FBF094FB092A811` FOREIGN KEY (`store_id`) REFERENCES `store` (`id`);

--
-- Contraintes pour la table `company_service`
--
ALTER TABLE `company_service`
  ADD CONSTRAINT `FK_C1CF8005979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_C1CF8005ED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `dashboard_notification`
--
ALTER TABLE `dashboard_notification`
  ADD CONSTRAINT `FK_596D0BE84B89032C` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`),
  ADD CONSTRAINT `FK_596D0BE87E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_596D0BE8A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_596D0BE8F8697D13` FOREIGN KEY (`comment_id`) REFERENCES `comment` (`id`);

--
-- Contraintes pour la table `favorit`
--
ALTER TABLE `favorit`
  ADD CONSTRAINT `FK_1757519176E51759` FOREIGN KEY (`favorit_user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_17575191979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`),
  ADD CONSTRAINT `FK_17575191A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `FK_B6BD307F1F55203D` FOREIGN KEY (`topic_id`) REFERENCES `topic` (`id`),
  ADD CONSTRAINT `FK_B6BD307FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_B6BD307FCD53EDB6` FOREIGN KEY (`receiver_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `FK_BF5476CA1F55203D` FOREIGN KEY (`topic_id`) REFERENCES `topic` (`id`),
  ADD CONSTRAINT `FK_BF5476CAA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_BF5476CACD53EDB6` FOREIGN KEY (`receiver_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `opportunity_notification`
--
ALTER TABLE `opportunity_notification`
  ADD CONSTRAINT `FK_1738B3A54B89032C` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`),
  ADD CONSTRAINT `FK_1738B3A5A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `FK_5A8A6C8DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_5A8A6C8DB0A8F8AB` FOREIGN KEY (`to_company_id`) REFERENCES `company` (`id`);

--
-- Contraintes pour la table `post_like`
--
ALTER TABLE `post_like`
  ADD CONSTRAINT `FK_653627B84B89032C` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`),
  ADD CONSTRAINT `FK_653627B8A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `profile`
--
ALTER TABLE `profile`
  ADD CONSTRAINT `FK_8157AA0F67048801` FOREIGN KEY (`function_id`) REFERENCES `user_function` (`id`),
  ADD CONSTRAINT `FK_8157AA0FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `recommandation`
--
ALTER TABLE `recommandation`
  ADD CONSTRAINT `FK_C7782A28979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`),
  ADD CONSTRAINT `FK_C7782A28A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_C7782A28ED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`);

--
-- Contraintes pour la table `score`
--
ALTER TABLE `score`
  ADD CONSTRAINT `FK_32993751A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `FK_E19D9AD212469DE2` FOREIGN KEY (`category_id`) REFERENCES `service_category` (`id`),
  ADD CONSTRAINT `FK_E19D9AD2A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_E19D9AD2C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `type_service` (`id`);

--
-- Contraintes pour la table `store`
--
ALTER TABLE `store`
  ADD CONSTRAINT `FK_FF5758776440E1B5` FOREIGN KEY (`default_adviser_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `store_service`
--
ALTER TABLE `store_service`
  ADD CONSTRAINT `FK_F895BB35B092A811` FOREIGN KEY (`store_id`) REFERENCES `store` (`id`),
  ADD CONSTRAINT `FK_F895BB35ED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`);

--
-- Contraintes pour la table `topic`
--
ALTER TABLE `topic`
  ADD CONSTRAINT `FK_9D40DE1BC54C8C93` FOREIGN KEY (`type_id`) REFERENCES `topic_type` (`id`);

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_8D93D649979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`),
  ADD CONSTRAINT `FK_8D93D649B092A811` FOREIGN KEY (`store_id`) REFERENCES `store` (`id`),
  ADD CONSTRAINT `FK_8D93D649C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `user_type` (`id`);

--
-- Contraintes pour la table `user_topic`
--
ALTER TABLE `user_topic`
  ADD CONSTRAINT `FK_7F8225431F55203D` FOREIGN KEY (`topic_id`) REFERENCES `topic` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_7F822543A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;
