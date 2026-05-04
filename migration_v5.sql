-- Migration v5: Fix BK and Wali Kelas login passwords
-- passwordbk = 'passwordbk'
-- passwordwalas = 'passwordwalas'

-- Update all guru who are wali kelas (assigned in t_rombel.id_walikelas) with passwordwalas
UPDATE t_ptk SET password = '$2y$10$.Rbp90UyT9SQ4qEw0i9kheGleV4/kMWoDsueQG9OdE5IyLH.P5aeC'
WHERE id_ptk IN (SELECT DISTINCT id_walikelas FROM t_rombel WHERE id_walikelas IS NOT NULL);

-- Update all guru who are BK (assigned in t_rombel.id_guru_bk) with passwordbk
UPDATE t_ptk SET password = '$2y$10$gQza9pDowyErxmndJ0joK.jaUORw..kh1dZCzHk8Y2X5/DNrtLbkq'
WHERE id_ptk IN (SELECT DISTINCT id_guru_bk FROM t_rombel WHERE id_guru_bk IS NOT NULL);
