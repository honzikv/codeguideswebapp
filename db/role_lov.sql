create table if not exists role_lov
(
	id int auto_increment
		primary key,
	role varchar(255) null,
	constraint role_lov_role_uindex
		unique (role)
);

