create table if not exists user
(
	id int auto_increment
		primary key,
	username varchar(30) not null,
	password varchar(255) not null,
	email varchar(255) not null,
	role_id int not null,
	banned tinyint(1) default 0 not null,
	constraint user_email_uindex
		unique (email),
	constraint user_username_uindex
		unique (username),
	constraint user_role_lov_id_fk
		foreign key (role_id) references role_lov (id)
);

