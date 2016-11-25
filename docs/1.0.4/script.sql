ALTER TABLE `mapeo_categorias` ADD `template` VARCHAR(255) NOT NULL AFTER `categorias_ml`;
ALTER TABLE `logs` ADD `id_evento` INT(11) NOT NULL AFTER `id_evento`;
ALTER TABLE `logs_cron` ADD `id_evento` INT(11) NOT NULL AFTER `id_evento`;

CREATE TABLE `articulo_proceso` (
  `id_articulo` int(11) NOT NULL,
  `id_proceso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;