create table if not exists review
(
	id int auto_increment
		primary key,
	reviewer_id int not null,
	guide_id int not null,
	efficiency_score int default 5 not null,
	info_score int default 5 not null,
	complexity_score int default 5 not null,
	quality_score int default 5 not null,
	overall_score int default 5 not null,
	notes mediumtext null,
	is_finished tinyint(1) default 0 not null,
	constraint guide_id
		foreign key (guide_id) references guide (id)
			on delete cascade,
	constraint reviewer_id
		foreign key (reviewer_id) references user (id)
);

