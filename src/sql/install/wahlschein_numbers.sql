delimiter //

create table if not exists `wahlschein_numbers`
(
    id bigint not null primary key ,
    used boolean default false not null,
    random integer default 0,
    index idx_wahlschein_numbers_used (used),
    index idx_wahlschein_numbers_random (random)
) //


CREATE OR REPLACE PROCEDURE `fill_wahlschein_numbers`()
begin
    declare i int default 10000000;
    declare listLength int default 99999999;
    declare randomInt int default 0;
    
    select max(id) into i from wahlschein_numbers;
    if i is null then
        set i = 10000000;
    end if;
    
    while i < listLength do
        set randomInt = floor(rand() * 1000000);
        set i = i + 1;
        insert  delayed ignore into wahlschein_numbers (id,random) values (i,randomInt);
    end while;
end //

call fill_wahlschein_numbers() //

