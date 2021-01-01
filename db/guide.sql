create table if not exists guide
(
	id int auto_increment
		primary key,
	name varchar(60) not null,
	abstract text null,
	filename varchar(60) not null,
	user_id int not null,
	guide_state int default 0 not null,
	constraint guide_filename_uindex
		unique (filename),
	constraint guide_name_uindex
		unique (name),
	constraint guide_state
		foreign key (guide_state) references guide_state_lov (id)
			on delete cascade,
	constraint user_id
		foreign key (user_id) references user (id)
			on delete cascade
);

