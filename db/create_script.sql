create schema code_guides collate utf8mb4_general_ci;

create table if not exists guide_state_lov
(
    id    int auto_increment
        primary key,
    state varchar(20) not null,
    constraint guide_state_lov_state_uindex
        unique (state)
);

create table if not exists role_lov
(
    id   int auto_increment
        primary key,
    role varchar(255) null,
    constraint role_lov_role_uindex
        unique (role)
);

create table if not exists user
(
    id       int auto_increment
        primary key,
    username varchar(30)          not null,
    password varchar(255)         not null,
    email    varchar(255)         not null,
    role_id  int                  not null,
    banned   tinyint(1) default 0 not null,
    constraint user_email_uindex
        unique (email),
    constraint user_username_uindex
        unique (username),
    constraint user_role_lov_id_fk
        foreign key (role_id) references role_lov (id)
);

create table if not exists guide
(
    id          int auto_increment
        primary key,
    name        varchar(60)   not null,
    abstract    text          null,
    filename    varchar(60)   not null,
    user_id     int           not null,
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

create table if not exists review
(
    id               int auto_increment
        primary key,
    reviewer_id      int                  not null,
    guide_id         int                  not null,
    efficiency_score int        default 5 not null,
    info_score       int        default 5 not null,
    complexity_score int        default 5 not null,
    quality_score    int        default 5 not null,
    overall_score    int        default 5 not null,
    notes            mediumtext           null,
    is_finished      tinyint(1) default 0 not null,
    constraint guide_id
        foreign key (guide_id) references guide (id)
            on delete cascade,
    constraint reviewer_id
        foreign key (reviewer_id) references user (id)
);