drop database if exists gamedb;
create database if not exists gamedb;
use gamedb;

create table `user`(
	`id` int unsigned auto_increment,
	`username` varchar(255) not null,
    `password` char(60) not null,
    primary key(`id`),
    unique(`username`)
);

create table system(
	system_id int unsigned auto_increment,
    system_name varchar(50) not null,
    primary key(system_id)
);

create table developer(
	developer_id int unsigned auto_increment primary key,
    developer_name varchar(50) not null
);

create table game(
	game_id int unsigned auto_increment primary key,
    beaten bool default false,
    release_year year not null,
    title varchar(255) not null,
    system_id int unsigned,
    developer_id int unsigned,
    # foreign key (column in this table) 
    #	references other_table(primary_key)
    foreign key (developer_id) 
		references developer(developer_id),
    foreign key (system_id) 
		references system(system_id),
	index title_idx (title)
        
);

create table category(
	category_id int unsigned auto_increment primary key,
	category_desc varchar(50)
);

create table game_category(
	game_id int unsigned,
    category_id int unsigned,
    foreign key (game_id) 
		references game(game_id),
	foreign key (category_id)
		references category(category_id),
	primary key (game_id, category_id)
);

#show tables; # gives list of tables in db
#show columns from system; # show column defs of table

# create
insert into system(system_name) 
values('NES'),('SNES'),('N64'); # insert 3 records

# read
select * from system;

select system_name as `name` 
from system;

select system_name as `name`
from system
where system_name like '%NES';
# = <= >= < > and or not

insert developer (developer_name)
values ('Nintendo'),('Capcom'),('Konami');

select * from developer;

insert into category (category_desc)
values ('Platorm'),('Action'),('Adventure'),('RPG');

select * from category;

insert into game(
	title, 
    release_year, 
    beaten,
	system_id, 
    developer_id
) values
	('Super Mario Bros', '1985', true, 1, 1),
    ('Castlevania', '1987', true, 1, 3),
    ('Mega Man X', '1994', false, 2, 2);
    

insert into game (title, release_year)
values ('Faxanadu', 1988);
    
select g.title, 
	g.release_year, 
    s.system_name,
    d.developer_name
from game g
join system s using(system_id)
join developer d on g.developer_id = d.developer_id;

# update (CRUD)
update game 
set beaten = true
where title = 'Mega Man X';

# delete (CRUD)

select
	count(title) as `# of games`, 
    s.system_name
from game g
right join system s using(system_id)
group by (s.system_name)
order by `# of games` desc;

select * from game;

explain game;

select * from game_category;

select g.game_id, g.beaten, g.release_year, g.title, d.developer_id, d.developer_name, s.system_id, s.system_name,
	group_concat(c.category_desc, ', ') as categories
from game g
left join developer d on g.developer_id = d.developer_id
left join system s on g.system_id = s.system_id
left join game_category gc on g.game_id = gc.game_id
left join category c on gc.category_id = c.category_id
group by g.game_id, g.beaten, g.release_year, g.title, d.developer_id, d.developer_name, s.system_id, s.system_name
order by g.title;