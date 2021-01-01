create table if not exists guide_state_lov
(
	id int auto_increment
		primary key,
	state varchar(20) not null,
	constraint guide_state_lov_state_uindex
		unique (state)
);

