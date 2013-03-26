# --- making specimen_id NULL so specimen can be added on second step of media form
ALTER TABLE ms_media CHANGE COLUMN specimen_id specimen_id INT UNSIGNED NULL;