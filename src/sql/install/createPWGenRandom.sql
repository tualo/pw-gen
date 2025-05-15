DELIMITER //

CREATE OR REPLACE FUNCTION `randomPWGenString`(length SMALLINT(3),allowedChars varchar(255)) 
RETURNS varchar(100) CHARSET utf8
begin
    SET @returnStr = '';
    SET @i = 0;

    WHILE (@i < length) DO
        SET @returnStr = CONCAT(@returnStr, substring(allowedChars, FLOOR(RAND() * LENGTH(allowedChars) + 1), 1));
        SET @i = @i + 1;
    END WHILE;

    RETURN @returnStr;
END //

CREATE OR REPLACE PROCEDURE `createPWGenRandom`(
    in tableName varchar(255),
    in randomLength SMALLINT(3),
    in allowedChars varchar(255), 
    in listLength int(11),
    in field varchar(255),
    in uniqueValue boolean
)
begin

    declare sql_command longtext;
    declare c int;
    declare versioned varchar(255);
    set versioned = '';

    set versioned = (select distinct TABLE_TYPE from INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA = database() and TABLE_NAME = tableName);

    if (versioned='SYSTEM VERSIONED') then
        set versioned = 'FOR SYSTEM_TIME ALL';
    else
        set versioned = '';
    end if;

    set sql_command = concat('create temporary table if not exists temp_random_list (seq bigint , val varchar(',randomLength,') primary key)');

    PREPARE stmt FROM sql_command;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;


    set c=0;
    while (c< listLength) DO
        set sql_command = concat('insert ignore into temp_random_list (seq  , val )  select seq,randomPWGenString(',randomLength,',"',allowedChars,'") val from seq_1_to_3000');


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

call createPWGenRandom( 'muc_data' ,8,"1234567890", '1000' ,"pwgen_id",true)