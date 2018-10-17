CREATE TRIGGER `add_author_2_post_logs_on_update` AFTER UPDATE ON `gwr_posts`
 FOR EACH ROW BEGIN
        declare LogID integer;
        declare ModTime varchar(100);
        declare AuthLog text;
        declare OldAuthLog text;
        declare StatusLog text;
        declare OldStatusLog text;
        declare ContentLog text;
        declare OldContentLog text;
        declare LastEdit varchar(25);
        declare LastEditLog text;
        declare OldLastEditLog text;
        declare PostID integer;
        
        SET @PostID :=  Old.ID;
        SELECT `meta_value` FROM `freerang_directory`.`gwr_postmeta` WHERE `meta_key` = '_edit_last' AND `post_id` = Old.ID INTO @LastEdit;
        
        SELECT DATE_FORMAT(now(), '%d-%m-%Y %r') INTO @ModTime;
        
        SELECT CONCAT(New.post_author, '#', @ModTime) INTO @AuthLog;
        SELECT CONCAT(New.post_status, '#', '!') INTO @StatusLog;
        SELECT CONCAT(New.post_modified_gmt, '#', '!') INTO @ContentLog;
        SELECT CONCAT(@LastEdit, '#', '!') INTO @LastEditLog;
        
        SELECT `id`, `author_log`,`status_log`, `content_log`, `post_edit_log`  FROM `freerang_directory`.`frc_post_logs` WHERE `post_id` = Old.ID LIMIT 1 INTO @LogID, @OldAuthLog, @OldStatusLog, @OldContentLog, @OldLastEditLog;
        
        IF(New.post_type = 'ait-dir-item' || New.post_type = 'ait-item') THEN
            IF(!ISNULL(@LogID)) THEN
                SET @StatusLog  =   CONCAT(@OldStatusLog,   "|", @StatusLog);
                SET @AuthLog    =   CONCAT(@OldAuthLog,     "|", @AuthLog);
                SET @ContentLog =   CONCAT(@OldContentLog,  "|", @ContentLog);
                SET @LastEdit   =   CONCAT(@OldLastEditLog, "|", @LastEditLog);
                
                UPDATE `freerang_directory`.`frc_post_logs` SET `author_log` = @AuthLog, `status_log` = @StatusLog, `content_log` = @ContentLog, `post_edit_log`= @LastEdit WHERE `post_id` = New.ID ;
            ELSE
                INSERT INTO `freerang_directory`.`frc_post_logs` (`post_id`, `post_title`, `author_log`, `status_log`, `content_log`, `post_edit_log`) VALUES (New.ID, New.post_title, concat(@AuthLog, "|"), concat(@StatusLog, "|"), concat(@ContentLog, "|"), @LastEditLog);
            END IF;
        END IF;
    END

CREATE TRIGGER `sync_on_delete_4_users` AFTER DELETE ON `gwr_users`
 FOR EACH ROW BEGIN
    declare UserID integer;
    SET @UserID := (SELECT ID FROM `freerang_premium`.`gwr_users` WHERE `user_login` = Old.user_login ORDER BY `ID` DESC LIMIT 1);
    IF (!ISNULL(@UserID))
    THEN
        DELETE FROM `freerang_premium`.`gwr_users` WHERE `ID` = @UserID;
    end if;
    SET @UserID := (SELECT ID FROM `freerang_shop`.`gwr_users` WHERE `user_login` = Old.user_login ORDER BY `ID` DESC LIMIT 1);
    IF (!ISNULL(@UserID))
    THEN
        DELETE FROM `freerang_shop`.`gwr_users` WHERE `ID` = @UserID;
    END IF;
    SET @UserID := (SELECT ID FROM `freerang_classified`.`gwr_users` WHERE `user_login` = Old.user_login ORDER BY `ID` DESC LIMIT 1);
    IF (!ISNULL(@UserID))
    THEN
        DELETE FROM `freerang_classified`.`gwr_users` WHERE `ID` = @UserID;
    END IF;
END

CREATE TRIGGER `sync_on_add_4_users` AFTER INSERT ON `gwr_users`
 FOR EACH ROW BEGIN
    declare UserID integer;
    SET @UserID := (SELECT ID FROM `freerang_premium`.`gwr_users` WHERE `user_login` = New.user_login ORDER BY `ID` DESC LIMIT 1);
    IF (ISNULL(@UserID))
    THEN
        INSERT INTO `freerang_premium`.`gwr_users` (`ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_url`, `user_registered`, `user_activation_key`, `user_status`, `display_name`) VALUES (New.ID, New.user_login, New.user_pass, New.user_nicename, New.user_email, New.user_url, New.user_registered, New.user_activation_key, New.user_status, New.display_name);
    else
        if @UserID <> New.ID
        THEN
            INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES ('premium_IF_all_bad', New.ID);
        ELSE
            INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES ('premium_ELSE_all_good', New.ID);
        end if;
    end if;
    SET @UserID := (SELECT ID FROM `freerang_shop`.`gwr_users` WHERE `user_login` = New.user_login ORDER BY `ID` DESC LIMIT 1);
    IF (ISNULL(@UserID))
    THEN
        INSERT INTO `freerang_shop`.`gwr_users` (`ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_url`, `user_registered`, `user_activation_key`, `user_status`, `display_name`) VALUES (New.ID, New.user_login, New.user_pass, New.user_nicename, New.user_email, New.user_url, New.user_registered, New.user_activation_key, New.user_status, New.display_name);
    ELSE
        if @UserID <> New.ID
        THEN
            INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES ('classified_IF_all_bad', New.ID);
        ELSE
            INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES ('classified_ELSE_all_good', New.ID);
        end if;
    END IF;
    SET @UserID := (SELECT ID FROM `freerang_classified`.`gwr_users` WHERE `user_login` = New.user_login ORDER BY `ID` DESC LIMIT 1);
    IF (ISNULL(@UserID))
    THEN
        INSERT INTO `freerang_classified`.`gwr_users` (`ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_url`, `user_registered`, `user_activation_key`, `user_status`, `display_name`) VALUES (New.ID, New.user_login, New.user_pass, New.user_nicename, New.user_email, New.user_url, New.user_registered, New.user_activation_key, New.user_status, New.display_name);
    ELSE
        if @UserID <> New.ID
        THEN
            INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES ('directory_IF_all_bad', New.ID);
        ELSE
            INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES ('directory_ELSE_all_good', New.ID);
        end if;
    END IF;
END

CREATE TRIGGER `sync_on_add_4_WLM_level_opts` AFTER INSERT ON `gwr_wlm_userlevel_options`
 FOR EACH ROW BEGIN
    declare LvlOptID integer;
    SET @LvlOptID := (SELECT ID FROM `freerang_classified`.`classified_wlm_userlevel_options` WHERE `userlevel_id` = New.userlevel_id AND `option_name` = New.option_name ORDER BY `ID` DESC LIMIT 1);
    IF (ISNULL(@LvlOptID)) THEN
        INSERT INTO `freerang_classified`.`classified_wlm_userlevel_options` (`ID`, `userlevel_id`, `option_name`, `option_value`) VALUES (New.ID, New.userlevel_id, New.option_name, New.option_value);
    END IF;
    SET @LvlOptID := (SELECT ID FROM `freerang_premium`.`wp_wlm_userlevel_options` WHERE `userlevel_id` = New.userlevel_id AND `option_name` = New.option_name ORDER BY `ID` DESC LIMIT 1);
    IF (ISNULL(@LvlOptID)) THEN
        INSERT INTO `freerang_premium`.`wp_wlm_userlevel_options` (`ID`, `userlevel_id`, `option_name`, `option_value`) VALUES (New.ID, New.userlevel_id, New.option_name, New.option_value);
    END IF;
    SET @LvlOptID := (SELECT ID FROM `freerang_shop`.`shop_wlm_userlevel_options` WHERE `userlevel_id` = New.userlevel_id AND `option_name` = New.option_name ORDER BY `ID` DESC LIMIT 1);
    IF (ISNULL(@LvlOptID)) THEN
        INSERT INTO `freerang_shop`.`shop_wlm_userlevel_options` (`ID`, `userlevel_id`, `option_name`, `option_value`) VALUES (New.ID, New.userlevel_id, New.option_name, New.option_value);
    END IF;
END

CREATE TRIGGER `sync_on_add_4_WLM_levels` AFTER INSERT ON `gwr_wlm_userlevels`
 FOR EACH ROW BEGIN
    declare LvlID integer;
    SET @LvlID := (SELECT `ID` FROM `freerang_classified`.`classified_wlm_userlevels` WHERE `user_id` = New.user_id AND `level_id` = New.level_id ORDER BY `ID` DESC LIMIT 1);
    IF (ISNULL(@LvlID)) THEN
        INSERT INTO `freerang_classified`.`classified_wlm_userlevels` (`ID`, `user_id`, `level_id`) VALUES (New.ID, New.user_id, New.level_id);
    END IF;
    SET @LvlID := (SELECT ID FROM `freerang_premium`.`wp_wlm_userlevels` WHERE `user_id` = New.user_id AND `level_id` = New.level_id ORDER BY `ID` DESC LIMIT 1);
    IF (ISNULL(@LvlID)) THEN
        INSERT INTO `freerang_premium`.`wp_wlm_userlevels` (`ID`, `user_id`, `level_id`) VALUES (New.ID, New.user_id, New.level_id);
    END IF;
    SET @LvlID := (SELECT ID FROM `freerang_shop`.`shop_wlm_userlevels` WHERE `user_id` = New.user_id AND `level_id` = New.level_id ORDER BY `ID` DESC LIMIT 1);
    IF (ISNULL(@LvlID)) THEN
        INSERT INTO `freerang_shop`.`shop_wlm_userlevels` (`ID`, `user_id`, `level_id`) VALUES (New.ID, New.user_id, New.level_id);
    END IF;
END

CREATE TRIGGER `sync_on_update_4_WLM_user_opts` AFTER UPDATE ON `gwr_wlm_user_options`
 FOR EACH ROW BEGIN
    declare uMetaID integer;
    IF (New.option_name = 'wlm_password_hint') THEN
        SET @uMetaID := (SELECT `umeta_id` FROM `freerang_directory`.`gwr_usermeta` WHERE `user_id` = New.user_id AND `meta_key` = 'frc_password_hint' ORDER BY `umeta_id` DESC LIMIT 1);
        IF (!ISNULL(@uMetaID)) THEN
            UPDATE `freerang_directory`.`gwr_usermeta` SET `meta_value` = New.option_value WHERE `umeta_id` = @uMetaID;
        END IF;
        SET @uMetaID := (SELECT `umeta_id` FROM `freerang_premium`.`gwr_usermeta` WHERE `user_id` = New.user_id AND `meta_key` = 'frc_password_hint' ORDER BY `umeta_id` DESC LIMIT 1);
        IF (!ISNULL(@uMetaID)) THEN
            UPDATE `freerang_premium`.`gwr_usermeta` SET `meta_value` = New.option_value WHERE `umeta_id` = @uMetaID;
        END IF;
    END IF;
END

CREATE TRIGGER `sync_on_update_4_users` AFTER UPDATE ON `gwr_users`
 FOR EACH ROW BEGIN
    declare UserID integer;
    declare UserLogin varchar(60);
    declare UserPass  varchar(255);
    declare UserNicename varchar(50);
    declare UserEmail varchar(100);
    declare UserUrl varchar(100);
    declare UserActivationKey varchar(255);
    declare UserStatus integer;
    declare DisplayName varchar(250);
    SELECT ID, user_login, user_pass, user_nicename, user_email, user_url, user_activation_key, user_status, display_name FROM `freerang_premium`.`gwr_users` WHERE `user_login` = Old.user_login ORDER BY `ID` DESC LIMIT 1 INTO @UserID, @UserLogin, @UserPass, @UserNicename, @UserEmail, @UserUrl, @UserActivationKey, @UserStatus, @DisplayName;
    IF (ISNULL(@UserID))
    THEN
        INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES ('classified_IF_all_bad', New.ID);
    else
        IF(@UserPass <> NEW.user_pass || @UserNicename <> New.user_nicename || @UserEmail <> New.user_email || @UserUrl <> New.user_url || @UserActivationKey <> New.user_activation_key || @UserStatus <> New.user_status || @DisplayName <> New.display_name)
        THEN
            UPDATE `freerang_premium`.`gwr_users` SET `user_pass` = New.user_pass, `user_nicename` = New.user_nicename, `user_email` = New.user_email, `user_url` = New.user_url, `user_activation_key` = New.user_activation_key, `user_status` = New.user_status, `display_name` = New.display_name WHERE `user_login` = Old.user_login;
            INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES (concat('UPDATED', '-',@UserLogin), New.ID);
        ELSE
            INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES (concat('!UPDATED', '-',@UserLogin), New.ID);
        END IF;
        if @UserID <> New.ID
        THEN
            INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES ('premium_IF_all_bad', New.ID);
        ELSE
            INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES (@DisplayName, New.ID);
        end if;
    end if;
    SELECT ID, user_login, user_pass, user_nicename, user_email, user_url, user_activation_key, user_status, display_name FROM `freerang_classified`.`gwr_users` WHERE `user_login` = Old.user_login ORDER BY `ID` DESC LIMIT 1 INTO @UserID, @UserLogin, @UserPass, @UserNicename, @UserEmail, @UserUrl, @UserActivationKey, @UserStatus, @DisplayName;
    IF (ISNULL(@UserID))
    THEN
        INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES ('classified_IF_all_bad', New.ID);
    else
        IF(@UserPass <> NEW.user_pass || @UserNicename <> New.user_nicename || @UserEmail <> New.user_email || @UserUrl <> New.user_url || @UserActivationKey <> New.user_activation_key || @UserStatus <> New.user_status || @DisplayName <> New.display_name)
        THEN
            UPDATE `freerang_classified`.`gwr_users` SET `user_pass` = New.user_pass, `user_nicename` = New.user_nicename, `user_email` = New.user_email, `user_url` = New.user_url, `user_activation_key` = New.user_activation_key, `user_status` = New.user_status, `display_name` = New.display_name WHERE `user_login` = Old.user_login;
            INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES (concat('UPDATED', '-',@UserLogin), New.ID);
        ELSE
            INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES (concat('!UPDATED', '-',@UserLogin), New.ID);
        END IF;
        if @UserID <> New.ID
        THEN
            INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES ('premium_IF_all_bad', New.ID);
        ELSE
            INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES (@DisplayName, New.ID);
        end if;
    end if;
    SELECT ID, user_login, user_pass, user_nicename, user_email, user_url, user_activation_key, user_status, display_name FROM `freerang_shop`.`gwr_users` WHERE `user_login` = Old.user_login ORDER BY `ID` DESC LIMIT 1 INTO @UserID, @UserLogin, @UserPass, @UserNicename, @UserEmail, @UserUrl, @UserActivationKey, @UserStatus, @DisplayName;
    IF (ISNULL(@UserID))
    THEN
        INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES ('classified_IF_all_bad', New.ID);
    else
        IF(@UserPass <> NEW.user_pass || @UserNicename <> New.user_nicename || @UserEmail <> New.user_email || @UserUrl <> New.user_url || @UserActivationKey <> New.user_activation_key || @UserStatus <> New.user_status || @DisplayName <> New.display_name)
        THEN
            UPDATE `freerang_shop`.`gwr_users` SET `user_pass` = New.user_pass, `user_nicename` = New.user_nicename, `user_email` = New.user_email, `user_url` = New.user_url, `user_activation_key` = New.user_activation_key, `user_status` = New.user_status, `display_name` = New.display_name WHERE `user_login` = Old.user_login;
            INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES (concat('UPDATED', '-',@UserLogin), New.ID);
        ELSE
            INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES (concat('!UPDATED', '-',@UserLogin), New.ID);
        END IF;
        if @UserID <> New.ID
        THEN
            INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES ('premium_IF_all_bad', New.ID);
        ELSE
            INSERT INTO `freerang_shop`.`synch_user_error_log` (`sub_directory`, `user_id`) VALUES (@DisplayName, New.ID);
        end if;
    end if;
END

CREATE TRIGGER `update_session_4_directory_bak` AFTER UPDATE ON `gwr_usermeta_bak`
 FOR EACH ROW BEGIN
    declare UserMetaID integer;
    declare MetaVal text;
    declare MetaCap varchar(255);
    declare sessionKey varchar(255);
    SET @sessionKey :=  'session_tokens';
    SET @MetaCap :=  Old.meta_key;
    IF ( Old.meta_key = @sessionKey ) THEN
        IF (New.user_id = 58839) THEN
            SELECT `umeta_id`, `meta_value` FROM `freerang_premium`.`gwr_usermeta_bak` WHERE `user_id` = Old.user_id AND `meta_key`= @sessionKey ORDER BY `umeta_id` DESC LIMIT 1 INTO @UserMetaID, @MetaVal;
            IF(ISNULL(@UserMetaID)) THEN
                INSERT INTO `freerang_premium`.`gwr_usermeta_bak` (`user_id`, `meta_key`, `meta_value`, `modified`) VALUES (Old.user_id, @sessionKey, New.meta_value, '0000-00-00 00:00:00');
            ELSE
                if(@MetaVal <> New.meta_value ) THEN
                    UPDATE `freerang_premium`.`gwr_usermeta_bak` SET `meta_value` = New.meta_value, `modified` = '0000-00-00 00:00:00' WHERE `user_id` = NEW.user_id AND `meta_key` = @MetaCap;
                END IF;
            END IF;
            SELECT `umeta_id`, `meta_value` FROM `freerang_shop`.`gwr_usermeta_bak` WHERE `user_id` = Old.user_id AND `meta_key`= @sessionKey ORDER BY `umeta_id` DESC LIMIT 1 INTO @UserMetaID, @MetaVal;
            IF(ISNULL(@UserMetaID)) THEN
                INSERT INTO `freerang_shop`.`gwr_usermeta_bak` (`user_id`, `meta_key`, `meta_value`, `modified`) VALUES (Old.user_id, @sessionKey, New.meta_value, '0000-00-00 00:00:00');
            ELSE
                if(@MetaVal <> New.meta_value ) THEN
                    UPDATE `freerang_shop`.`gwr_usermeta_bak` SET `meta_value` = New.meta_value, `modified` = '0000-00-00 00:00:00' WHERE `user_id` = NEW.user_id AND `meta_key` = @MetaCap;
                END IF;
            END IF;
            SELECT `umeta_id`, `meta_value` FROM `freerang_classified`.`gwr_usermeta_bak` WHERE `user_id` = Old.user_id AND `meta_key`= @sessionKey ORDER BY `umeta_id` DESC LIMIT 1 INTO @UserMetaID, @MetaVal;
            IF(ISNULL(@UserMetaID)) THEN
                INSERT INTO `freerang_classified`.`gwr_usermeta_bak` (`user_id`, `meta_key`, `meta_value`, `modified`) VALUES (Old.user_id, @sessionKey, New.meta_value, '0000-00-00 00:00:00');
            ELSE
                if(@MetaVal <> New.meta_value ) THEN
                    UPDATE `freerang_classified`.`gwr_usermeta_bak` SET `meta_value` = New.meta_value, `modified` = '0000-00-00 00:00:00' WHERE `user_id` = NEW.user_id AND `meta_key` = @MetaCap;
                END IF;
            END IF;
        END IF;
    END IF;
END

CREATE TRIGGER `sync_on_delete_4_WLM_levels` AFTER DELETE ON `gwr_wlm_userlevels`
 FOR EACH ROW BEGIN
    declare LvlID integer;
    SET @LvlID := (SELECT ID FROM `freerang_classified`.`classified_wlm_userlevels` WHERE `user_id` = Old.user_id AND `level_id` = Old.level_id ORDER BY `ID` DESC LIMIT 1);
    IF (!ISNULL(@LvlID)) THEN
        DELETE FROM `freerang_classified`.`classified_wlm_userlevels` WHERE `ID` = @LvlID;
    END IF;
    SET @LvlID := (SELECT ID FROM `freerang_premium`.`wp_wlm_userlevels` WHERE `user_id` = Old.user_id AND `level_id` = Old.level_id ORDER BY `ID` DESC LIMIT 1);
    IF (!ISNULL(@LvlID)) THEN
        DELETE FROM `freerang_premium`.`wp_wlm_userlevels` WHERE `ID` = @LvlID;
    END IF;
    SET @LvlID := (SELECT ID FROM `freerang_shop`.`shop_wlm_userlevels` WHERE `user_id` = Old.user_id AND `level_id` = Old.level_id ORDER BY `ID` DESC LIMIT 1);
    IF (!ISNULL(@LvlID))
    THEN
        DELETE FROM `freerang_shop`.`shop_wlm_userlevels` WHERE `ID` = @LvlID;
    END IF;
END

CREATE TRIGGER `sync_on_add_4_WLM_user_opts` AFTER INSERT ON `gwr_wlm_user_options`
 FOR EACH ROW BEGIN
    declare uMetaID integer;
    IF (New.option_name = 'wlm_password_hint') THEN
        SET @uMetaID := (SELECT `umeta_id` FROM `freerang_directory`.`gwr_usermeta` WHERE `user_id` = New.user_id AND `meta_key` = 'frc_password_hint' ORDER BY `umeta_id` DESC LIMIT 1);
        IF (ISNULL(@uMetaID)) THEN
            INSERT INTO `freerang_directory`.`gwr_usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES (New.user_id, 'frc_password_hint', New.option_value);
        END IF;
        SET @uMetaID := (SELECT `umeta_id` FROM `freerang_premium`.`gwr_usermeta` WHERE `user_id` = New.user_id AND `meta_key` = 'frc_password_hint' ORDER BY `umeta_id` DESC LIMIT 1);
        IF (ISNULL(@uMetaID)) THEN
            INSERT INTO `freerang_premium`.`gwr_usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES (New.user_id, 'frc_password_hint', New.option_value);
        END IF;
    END IF;
END

CREATE TRIGGER `update_login_usermeta_directory` AFTER UPDATE ON `gwr_usermeta`
 FOR EACH ROW BEGIN
    declare UserMetaID integer;
    declare MetaVal text;
    declare MetaKey varchar(255);
    declare MetaCap varchar(255);
    SET @MetaKey :=  'session_tokens';
    SET @MetaCap :=  Old.meta_key;
    IF(Old.meta_key = @MetaKey) THEN
        SELECT `umeta_id`, `meta_value` FROM `freerang_premium`.`gwr_usermeta` WHERE `user_id` = Old.user_id AND `meta_key`= @MetaCap ORDER BY `umeta_id` DESC LIMIT 1 INTO @UserMetaID, @MetaVal;
        IF(!ISNULL(@UserMetaID)) THEN
            IF @MetaVal <> New.meta_value THEN
                UPDATE `freerang_premium`.`gwr_usermeta` SET `meta_value` = New.meta_value, `processed_by_script` = 21, `modified`='0000-00-00 00:00:00' WHERE `umeta_id` = @UserMetaID;
            END IF;
        ELSE
            INSERT INTO `freerang_premium`.`gwr_usermeta` (`user_id`, `meta_key`, `meta_value`, `processed_by_script`, `modified`) VALUES (Old.user_id, @MetaCap, New.meta_value, 22, '0000-00-00 00:00:00');
        END IF;
    END IF;
    IF(Old.meta_key = @MetaKey) THEN
        SELECT `umeta_id`, `meta_value` FROM `freerang_shop`.`gwr_usermeta` WHERE `user_id` = Old.user_id AND `meta_key`= @MetaCap ORDER BY `umeta_id` DESC LIMIT 1 INTO @UserMetaID, @MetaVal;
        IF(!ISNULL(@UserMetaID)) THEN
            IF @MetaVal <> New.meta_value THEN
                UPDATE `freerang_shop`.`gwr_usermeta` SET `meta_value` = New.meta_value, `processed_by_script` = 21, `modified`='0000-00-00 00:00:00' WHERE `umeta_id` = @UserMetaID;
            END IF;
        ELSE
            INSERT INTO `freerang_shop`.`gwr_usermeta` (`user_id`, `meta_key`, `meta_value`, `processed_by_script`, `modified`) VALUES (Old.user_id, @MetaCap, New.meta_value, 22, '0000-00-00 00:00:00');
        END IF;
    END IF;
    IF(Old.meta_key = @MetaKey) THEN
        SELECT `umeta_id`, `meta_value` FROM `freerang_classified`.`gwr_usermeta` WHERE `user_id` = Old.user_id AND `meta_key`= @MetaCap ORDER BY `umeta_id` DESC LIMIT 1 INTO @UserMetaID, @MetaVal;
        IF(!ISNULL(@UserMetaID)) THEN
            IF @MetaVal <> New.meta_value THEN
                UPDATE `freerang_classified`.`gwr_usermeta` SET `meta_value` = New.meta_value, `processed_by_script` = 21, `modified`='0000-00-00 00:00:00' WHERE `umeta_id` = @UserMetaID;
            END IF;
        ELSE
            INSERT INTO `freerang_classified`.`gwr_usermeta` (`user_id`, `meta_key`, `meta_value`, `processed_by_script`, `modified`) VALUES (Old.user_id, @MetaCap, New.meta_value, 22, '0000-00-00 00:00:00');
        END IF;
    END IF;
END

CREATE TRIGGER `create_meta_log` AFTER INSERT ON `gwr_postmeta`
 FOR EACH ROW BEGIN
  declare MetaKey varchar(255);
  SET @MetaKey :=  '_ait-dir-item';
  IF(NEW.meta_key = '_ait-dir-item' || NEW.meta_key = '_ait_dir_gallery' || NEW.meta_key = '_ait-item_item-data') THEN
   
    INSERT INTO `freerang_directory`.`frc_postmeta_log` (`post_id`, `meta_key`, `meta_value`) VALUES (New.post_id, New.meta_key, New.meta_value);
   END IF;
 END

CREATE TRIGGER `sync_on_update_4_WLM_level_opts` AFTER UPDATE ON `gwr_wlm_userlevel_options`
 FOR EACH ROW BEGIN
    declare LvlOptID integer;
    declare LvlOptValue varchar(255);
    SELECT `ID`, `option_value` FROM `freerang_classified`.`classified_wlm_userlevel_options` WHERE `userlevel_id` = Old.userlevel_id AND `option_name` = New.option_name LIMIT 1 INTO @LvlOptID, @LvlOptValue;
    IF (!ISNULL(@LvlOptID) AND NEW.`option_value` <> @LvlOptValue) THEN
        UPDATE `freerang_classified`.`classified_wlm_userlevel_options` SET `option_value` = NEW.`option_value` WHERE `ID` = @LvlOptID;
    END IF;
    SELECT `ID`, `option_value` FROM `freerang_premium`.`wp_wlm_userlevel_options` WHERE `userlevel_id` = Old.userlevel_id AND `option_name` = New.option_name LIMIT 1 INTO @LvlOptID, @LvlOptValue;
    IF (!ISNULL(@LvlOptID) AND NEW.`option_value` <> @LvlOptValue) THEN
        UPDATE `freerang_premium`.`wp_wlm_userlevel_options` SET `option_value` = NEW.`option_value` WHERE `ID` = @LvlOptID;
    END IF;
    SELECT `ID`, `option_value` FROM `freerang_shop`.`shop_wlm_userlevel_options` WHERE `userlevel_id` = Old.userlevel_id AND `option_name` = New.option_name LIMIT 1 INTO @LvlOptID, @LvlOptValue;
    IF (!ISNULL(@LvlOptID) AND NEW.`option_value` <> @LvlOptValue) THEN
        UPDATE `freerang_shop`.`shop_wlm_userlevel_options` SET `option_value` = NEW.`option_value` WHERE `ID` = @LvlOptID;
    END IF;
END

CREATE TRIGGER `save_meta_log` AFTER UPDATE ON `gwr_postmeta`
 FOR EACH ROW BEGIN
  declare MetaKey varchar(255);
  SET @MetaKey :=  '_ait-dir-item';
  IF(Old.meta_key = '_ait-dir-item' || Old.meta_key = '_ait_dir_gallery') THEN
   IF(New.meta_value <> Old.meta_value) THEN
    INSERT INTO `freerang_directory`.`frc_postmeta_log` (`post_id`, `meta_key`, `meta_value`) VALUES (New.post_id, New.meta_key, New.meta_value);
   END IF;
  END IF;
 END

CREATE TRIGGER `sync_on_delete_4_WLM_level_opts` AFTER DELETE ON `gwr_wlm_userlevel_options`
 FOR EACH ROW BEGIN
    declare LvlOptID integer;
    SET @LvlOptID := (SELECT ID FROM `freerang_classified`.`classified_wlm_userlevel_options` WHERE `userlevel_id` = Old.userlevel_id AND `option_name` = Old.option_name ORDER BY `ID` DESC LIMIT 1);
    IF (!ISNULL(@LvlOptID)) THEN
        DELETE FROM `freerang_classified`.`classified_wlm_userlevel_options` WHERE `ID` = @LvlOptID;
    END IF;
    SET @LvlOptID := (SELECT ID FROM `freerang_premium`.`wp_wlm_userlevel_options` WHERE `userlevel_id` = Old.userlevel_id AND `option_name` = Old.option_name ORDER BY `ID` DESC LIMIT 1);
    IF (!ISNULL(@LvlOptID)) THEN
        DELETE FROM `freerang_premium`.`wp_wlm_userlevel_options` WHERE `ID` = @LvlOptID;
    END IF;
    SET @LvlOptID := (SELECT ID FROM `freerang_shop`.`shop_wlm_userlevel_options` WHERE `userlevel_id` = Old.userlevel_id AND `option_name` = Old.option_name ORDER BY `ID` DESC LIMIT 1);
    IF (!ISNULL(@LvlOptID)) THEN
        DELETE FROM `freerang_shop`.`shop_wlm_userlevel_options` WHERE `ID` = @LvlOptID;
    END IF;
END
