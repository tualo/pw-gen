delimiter //
create table if not exists pwgen_precalc(
    random varchar(20) not null primary key,
    allowedChars varchar(255) not null,
    length SMALLINT(3) not null,
    used boolean default false,
    name varchar(255) default null,
    created_at datetime NOT NULL default current_timestamp,
    updated_at datetime NOT NULL default current_timestamp on update current_timestamp,
    key idx_pwgen_precalc_name (name),
    key idx_pwgen_precalc_used (used),
    key idx_pwgen_precalc_length (length),
    key idx_pwgen_precalc_allowedChars (allowedChars),
    key idx_pwgen_precalc_used_name (used,name)
) //



CREATE OR REPLACE PROCEDURE `fill_pwgen_precalc`(
    in name varchar(255),
    in randomLength SMALLINT(3),
    in allowedChars varchar(255), 
    in listLength int(11)
)
begin

    declare sql_command longtext;

    drop table if exists temp_random_list;
    set sql_command = concat('create temporary table if not exists temp_random_list  select seq,randomPWGenString(',randomLength,',',quote(allowedChars),') val from seq_1_to_',listLength);
    PREPARE stmt FROM sql_command;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    
    set sql_command = concat('insert ignore into pwgen_precalc (random,length,name,allowedChars) select val,',randomLength,',',quote(name),',',quote(allowedChars),' from temp_random_list ');

    PREPARE stmt FROM sql_command;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;


end //

-- call fill_pwgen_precalc('pass5',5,'ABCDEFGHJKLMNPRSTUVXYZ123456789',40000) //
-- call fill_pwgen_precalc('username7',7,'ABCDEFGHJKLMNPRSTUVXYZ123456789',2000000) //
-- call fill_pwgen_precalc('username8',8,'ABCDEFGHJKLMNPRSTUVXYZ123456789',2000000) //


-- call fill_pwgen_precalc('mucnames',8,'ABCDEFGHJKLMNPRSTUVXYZ123456789',2000000) //