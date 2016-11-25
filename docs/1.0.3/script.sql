BEGIN
        IF Old.price <> New.price || Old.special_price <> New.special_price || Old.short_description <> New.short_description || Old.categories <> New.categories || Old.name <> New.name THEN
                 INSERT IGNORE INTO alertas (`id`,estado) VALUES(Old.`id`, 'M');
         END IF;
  IF Old.visibility <> New.visibility THEN
    IF Old.visibility = 1 AND New.visibility = 4 THEN
      INSERT INTO alertas (`id`,estado) VALUES(Old.`id`, 'A') ON DUPLICATE KEY UPDATE estado = 'A';
    ELSE
      INSERT INTO alertas (`id`,estado) VALUES(Old.`id`, 'B') ON DUPLICATE KEY UPDATE estado = 'B';
    END IF;
  END IF;
END