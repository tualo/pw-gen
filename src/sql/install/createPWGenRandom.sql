DELIMITER //

CREATE OR REPLACE PROCEDURE `createPWGenRandom`(
    in tableName varchar(255),
    in randomLength SMALLINT(3),
    in allowedChars varchar(255), 
    in listLength int(11),
    in field varchar(255)
    in uniqueValue bool DEFAULT false
)
begin

    declare sql_command longtext;
    declare c int;
    declare versioned varchar(255);
    set versioned = '';

    SELECT 
        TABLE_TYPE
        INTO versioned
    FROM 
        INFORMATION_SCHEMA.TABLES
    WHERE 
        TABLE_SCHEMA = database_name
        AND TABLE_NAME = table_name;

    if (versioned='SYSTEM VERSIONED') then
        set versioned = 'FOR SYSTEM_TIME ALL';
    end if;

    set sql_command = concat('create temporary table if not exists temp_random_list (seq bigint , val varchar(',randomLength,') primary key)');
    PREPARE stmt FROM sql_command;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;


    set c=0;
    while (c< listLength) DO
        set sql_command = concat('insert ignore into temp_random_list (seq  , val )  select seq,randomString(',randomLength,',"',allowedChars,'") val from seq_1_to_3000');
        PREPARE stmt FROM sql_command;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;

        if (uniqueValue=true) then
            set sql_command = concat('delete from temp_random_list where val in (select `',field,'` from `',tableName,'` ',versioned,')');
            PREPARE stmt FROM sql_command;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
        end if;

        set c = (select count(*)x from temp_random_list);
    END WHILE;

end //
