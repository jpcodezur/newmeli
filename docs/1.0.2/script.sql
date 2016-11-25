ALTER TABLE `articulos` CHANGE `estado` `estado` VARCHAR(255) NULL DEFAULT NULL;

UPDATE articulos set estado = 'not_published' WHERE status = '1';
UPDATE articulos set estado = 'active' WHERE status = '2';
UPDATE articulos set estado = 'closed' WHERE status = '3';
UPDATE articulos set estado = 'paused' WHERE status = '4';

ALTER TABLE `estados_publicacion` CHANGE `id` `id` VARCHAR(255) NULL DEFAULT NULL;

truncate table estados_publicacion;

INSERT INTO `estados_publicacion` (`id`, `estado`) VALUES
('active', 'PUBLICADO'),
('closed', 'FINALIZADO'),
('not_published', 'SIN PUBLICAR'),
('paused', 'PAUSADO');

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `id_articulo` varchar(255) NOT NULL,
  `accion` varchar(255) NOT NULL,
  `respuesta` text NOT NULL,
  `fecha` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `logs_cron` (
  `id` int(11) NOT NULL,
  `id_articulo` varchar(255) NOT NULL,
  `accion` varchar(255) NOT NULL,
  `respuesta` text NOT NULL,
  `fecha` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

