ALTER TABLE mengajar
    ADD COLUMN guru_id INT NULL AFTER mapel_id;

UPDATE mengajar
JOIN mapel ON mapel.id = mengajar.mapel_id
SET mengajar.guru_id = mapel.guru_id;

ALTER TABLE mengajar
    ADD CONSTRAINT fk_mengajar_guru
    FOREIGN KEY (guru_id) REFERENCES guru(id)
    ON DELETE SET NULL
    ON UPDATE CASCADE;
