# --- these contraint keys were added wrong in the original sql
ALTER TABLE `ms_specimens_x_bibliography`
DROP FOREIGN KEY `fk_ms_media_x_bibliography_ms_media10`,
ADD CONSTRAINT `fk_ms_specimens_x_bibliography_ms_specimens1`
	FOREIGN KEY (`specimen_id` )
    REFERENCES `ms_specimens` (`specimen_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
DROP FOREIGN KEY `fk_ms_media_x_bibliography_ms_bibliography10`,
ADD CONSTRAINT `fk_ms_specimens_x_bibliography_ms_bibliography1`
	FOREIGN KEY (`bibref_id` )
    REFERENCES `ms_bibliography` (`bibref_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
DROP FOREIGN KEY `fk_ms_media_x_bibliography_ms_users10`,
ADD CONSTRAINT `fk_ms_specimens_x_bibliography_ms_users1`
	FOREIGN KEY (`user_id` )
    REFERENCES `ca_users` (`user_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT;
    
    
DROP INDEX `fk_ms_specimens_x_bibliography_ms_bibliography1_idx` ON ms_specimens_x_bibliography;
CREATE INDEX `fk_ms_specimens_x_bibliography_ms_bibliography1_idx` ON ms_specimens_x_bibliography (`bibref_id` ASC);