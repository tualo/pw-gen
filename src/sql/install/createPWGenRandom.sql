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


CREATE OR REPLACE PROCEDURE `setPWGenRandom`(
    in tableName varchar(255),
    in randomName varchar(255),
    in randomLength SMALLINT(3),
    in field varchar(255)
)
begin

    declare sql_command longtext;


    drop table if exists temp_random_update_list;
    set sql_command = concat('
    create temporary table if not exists temp_random_update_list 
    with d as (
        select row_number() over (order by r) `rank`, id, pwgen_user, r from (
                select id, pwgen_user,rand() r from `',tableName,'`  where `',field,'` = ',quote(""),' or `',field,'` is null order by r
        ) sub
    ),
    p as (
        select row_number() over (order by r) `rank`, `random`, r from (
            select `random`,rand() r from pwgen_precalc where `length` = ',randomLength,' and `name`=',quote(randomName),' and `used`=false order by r
        ) sub
    )
    select d.rank,d.id,d.pwgen_user,d.r, p.rank p_rank, p.random from d join p on d.`rank` = p.`rank`
    ');
    select sql_command;
    PREPARE stmt FROM sql_command;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    for rec in (select id, pwgen_user, random from temp_random_update_list) do
        drop table if exists temp_random_update_list_exists;

        set sql_command = concat('create temporary table temp_random_update_list_exists select id from `',tableName,'` where `',field,'` = ',quote(rec.random),'  ');
        PREPARE stmt FROM sql_command;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;

        if not exists (select id from temp_random_update_list_exists) then
            set sql_command = concat('update `',tableName,'` set `',field,'` = ',quote(rec.random),' where id = ',quote(rec.id),' ');
            PREPARE stmt FROM sql_command;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;

        end if;
        set sql_command = concat('update pwgen_precalc set used=true where random = ',quote(rec.random),'');
        PREPARE stmt FROM sql_command;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    end for;

end //