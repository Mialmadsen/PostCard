USE postcard;


-- USERS

INSERT INTO users
(last_name, first_name, username, password_hash, profile_image, bio, email, birthdate, user_status, is_operator, email_verified_at, created_at)
VALUES
('Nielsen', 'Mia', 'mia_nielsen', 'test123', 'mia.jpg', 'Travel lover and photographer', 'mia@example.com', '2001-05-12', 'active', 0, '2026-09-01 10:00:00', '2026-09-01 09:30:00'),
('Jensen', 'Sofie', 'sofie_jensen', 'test123', 'sofie.jpg', 'Love travelling with friends and family', 'sofie@example.com', '2000-08-21', 'active', 0, '2026-09-02 11:00:00', '2026-09-02 10:30:00'),
('Hansen', 'Oliver', 'oliver_hansen', 'test123', 'oliver.jpg', 'Always looking for the next adventure', 'oliver@example.com', '1999-03-15', 'active', 0, '2026-09-03 12:00:00', '2026-09-03 11:30:00'),
('Madsen', 'Emma', 'emma_madsen', 'test123', 'emma.jpg', 'Beach, sunshine and good memories', 'emma@example.com', '2002-11-02', 'active', 0, '2026-09-04 13:00:00', '2026-09-04 12:30:00'),
('Pedersen', 'Lucas', 'lucas_pedersen', 'test123', 'lucas.jpg', 'Exploring new places', 'lucas@example.com', '2001-06-18', 'active', 0, '2026-09-05 10:00:00', '2026-09-05 09:30:00'),
('Andersen', 'Clara', 'clara_andersen', 'test123', 'clara.jpg', 'Coffee, beaches and city trips', 'clara@example.com', '2000-02-14', 'active', 0, '2026-09-06 11:00:00', '2026-09-06 10:30:00'),
('Christensen', 'William', 'william_christensen', 'test123', 'william.jpg', 'Always ready for a road trip', 'william@example.com', '1998-09-23', 'active', 0, '2026-09-07 12:00:00', '2026-09-07 11:30:00'),
('Larsen', 'Freja', 'freja_larsen', 'test123', 'freja.jpg', 'Travel memories are the best memories', 'freja@example.com', '2002-01-30', 'active', 0, '2026-09-08 13:00:00', '2026-09-08 12:30:00'),
('Thomsen', 'Noah', 'noah_thomsen', 'test123', 'noah.jpg', 'Mountain and nature lover', 'noah@example.com', '2001-10-11', 'active', 0, '2026-09-09 10:00:00', '2026-09-09 09:30:00'),
('Poulsen', 'Alma', 'alma_poulsen', 'test123', 'alma.jpg', 'Loving sunny destinations', 'alma@example.com', '2003-04-19', 'active', 0, '2026-09-10 11:00:00', '2026-09-10 10:30:00'),
('Mortensen', 'Emil', 'emil_mortensen', 'test123', 'emil.jpg', 'Photography and travel', 'emil@example.com', '1999-12-05', 'active', 0, '2026-09-11 12:00:00', '2026-09-11 11:30:00'),
('Rasmussen', 'Ida', 'ida_rasmussen', 'test123', 'ida.jpg', 'City breaks and good food', 'ida@example.com', '2002-07-07', 'active', 0, '2026-09-12 13:00:00', '2026-09-12 12:30:00'),
('Knudsen', 'Oscar', 'oscar_knudsen', 'test123', 'oscar.jpg', 'Adventure seeker', 'oscar@example.com', '2000-05-25', 'active', 0, '2026-09-13 10:00:00', '2026-09-13 09:30:00'),
('Møller', 'Anna', 'anna_moller', 'test123', 'anna.jpg', 'I love discovering new cultures', 'anna@example.com', '2001-11-16', 'active', 0, '2026-09-14 11:00:00', '2026-09-14 10:30:00'),
('Sørensen', 'Elias', 'elias_sorensen', 'test123', 'elias.jpg', 'Backpacking and photography', 'elias@example.com', '1999-08-09', 'active', 0, '2026-09-15 12:00:00', '2026-09-15 11:30:00'),
('Christiansen', 'Laura', 'laura_christiansen', 'test123', 'laura.jpg', 'Always planning the next trip', 'laura@example.com', '2003-03-28', 'active', 0, '2026-09-16 13:00:00', '2026-09-16 12:30:00'),
('Jørgensen', 'Alexander', 'alexander_jorgensen', 'test123', 'alexander.jpg', 'Nature, hiking and travel', 'alexander@example.com', '1998-06-03', 'active', 0, '2026-09-17 10:00:00', '2026-09-17 09:30:00'),
('Mikkelsen', 'Ella', 'ella_mikkelsen', 'test123', 'ella.jpg', 'Beach trips and sunsets', 'ella@example.com', '2002-09-12', 'active', 0, '2026-09-18 11:00:00', '2026-09-18 10:30:00'),
('Kjær', 'Malthe', 'malthe_kjaer', 'test123', 'malthe.jpg', 'Weekend trips with friends', 'malthe@example.com', '2000-01-22', 'active', 0, '2026-09-19 12:00:00', '2026-09-19 11:30:00'),
('Friis', 'Nora', 'nora_friis', 'test123', 'nora.jpg', 'Collecting memories around the world', 'nora@example.com', '2001-04-06', 'active', 0, '2026-09-20 13:00:00', '2026-09-20 12:30:00');


-- USER GROUPS

INSERT INTO user_groups
(name, description, posting_mode, default_post_limit, style, created_at)
VALUES
('Familien Nielsen', 'Private group for the Nielsen family', 'open', 20, 'postcard', '2026-09-01 10:00:00'),
('Vennegruppen', 'Travel group for close friends', 'permission', 15, 'summer', '2026-09-02 10:00:00'),
('Studievenner', 'Group for classmates and study friends', 'open', 10, 'classic', '2026-09-03 10:00:00'),
('Sommerholdet', 'Group for friends who travel together', 'open', 15, 'summer', '2026-09-04 10:00:00'),
('Rejseklubben', 'Group for people who love travelling', 'permission', 20, 'postcard', '2026-09-05 10:00:00'),
('Adventure Crew', 'Group for outdoor trips and adventures', 'open', 12, 'classic', '2026-09-06 10:00:00');


-- GROUP MEMBERS

INSERT INTO group_members
(user_id, group_id, member_role, member_status, joined_at)
VALUES
(1, 1, 'group_admin', 'active', '2026-09-01 10:15:00'),
(2, 1, 'member', 'active', '2026-09-01 10:20:00'),
(3, 1, 'member', 'active', '2026-09-01 10:25:00'),
(4, 1, 'member', 'active', '2026-09-01 10:30:00'),
(5, 1, 'member', 'active', '2026-09-01 10:35:00'),

(2, 2, 'group_admin', 'active', '2026-09-02 10:15:00'),
(4, 2, 'member', 'active', '2026-09-02 10:20:00'),
(6, 2, 'member', 'active', '2026-09-02 10:25:00'),
(7, 2, 'member', 'active', '2026-09-02 10:30:00'),
(8, 2, 'member', 'active', '2026-09-02 10:35:00'),

(3, 3, 'group_admin', 'active', '2026-09-03 10:15:00'),
(9, 3, 'member', 'active', '2026-09-03 10:20:00'),
(11, 3, 'member', 'active', '2026-09-03 10:25:00'),
(12, 3, 'member', 'active', '2026-09-03 10:30:00'),
(16, 3, 'member', 'active', '2026-09-03 10:35:00'),

(4, 4, 'group_admin', 'active', '2026-09-04 10:15:00'),
(8, 4, 'member', 'active', '2026-09-04 10:20:00'),
(10, 4, 'member', 'active', '2026-09-04 10:25:00'),
(14, 4, 'member', 'active', '2026-09-04 10:30:00'),
(18, 4, 'member', 'active', '2026-09-04 10:35:00'),

(5, 5, 'group_admin', 'active', '2026-09-05 10:15:00'),
(7, 5, 'member', 'active', '2026-09-05 10:20:00'),
(13, 5, 'member', 'active', '2026-09-05 10:25:00'),
(15, 5, 'member', 'active', '2026-09-05 10:30:00'),
(20, 5, 'member', 'active', '2026-09-05 10:35:00'),

(9, 6, 'group_admin', 'active', '2026-09-06 10:15:00'),
(13, 6, 'member', 'active', '2026-09-06 10:20:00'),
(15, 6, 'member', 'active', '2026-09-06 10:25:00'),
(17, 6, 'member', 'active', '2026-09-06 10:30:00'),
(19, 6, 'member', 'active', '2026-09-06 10:35:00');


-- EXPERIENCES

INSERT INTO experiences
(name, description, start_date, end_date, group_id, user_id, experience_status, post_limit, created_at)
VALUES
('Sommerferie i Italien', 'En uge med familien i Italien', '2026-07-10', '2026-07-17', 1, 1, 'approved', 10, '2026-07-01 09:00:00'),
('Weekend i Aarhus', 'Weekendtur med familien', '2026-07-25', '2026-07-27', 1, 4, 'approved', 8, '2026-07-15 10:00:00'),
('Weekend i Barcelona', 'En weekend med vennerne i Barcelona', '2026-08-14', '2026-08-17', 2, 2, 'approved', 8, '2026-08-01 10:00:00'),
('Sommerhus ved Vesterhavet', 'Afslappende weekend ved havet', '2026-08-21', '2026-08-23', 2, 6, 'approved', 10, '2026-08-10 10:00:00'),
('Studietur til Berlin', 'Tur til Berlin med studiegruppen', '2026-09-20', '2026-09-23', 3, 3, 'approved', 10, '2026-09-10 11:00:00'),
('København weekend', 'Weekend i København med studievenner', '2026-09-05', '2026-09-07', 3, 12, 'approved', 8, '2026-08-25 11:00:00'),
('Roadtrip gennem Danmark', 'Roadtrip med venner gennem Danmark', '2026-08-01', '2026-08-05', 4, 4, 'approved', 12, '2026-07-20 12:00:00'),
('Paris med venner', 'En tur til Paris med venner', '2026-09-12', '2026-09-16', 5, 5, 'approved', 10, '2026-09-01 12:00:00'),
('Vandretur i Norge', 'Natur og vandring i Norge', '2026-08-10', '2026-08-15', 6, 9, 'approved', 12, '2026-07-25 13:00:00'),
('Efterårsferie i Spanien', 'Sol og afslapning i Spanien', '2026-10-12', '2026-10-19', 5, 20, 'pending', 10, '2026-09-25 14:00:00');


-- POSTS

INSERT INTO posts
(caption, location, created_at, experience_id, is_sticky)
VALUES
('Første dag i Italien! Vi er lige ankommet og nyder det gode vejr.', 'Rome, Italy', '2026-07-10 14:30:00', 1, 1),
('En hyggelig aften med god mad og masser af grin.', 'Rome, Italy', '2026-07-11 20:15:00', 1, 0),
('Vi fandt en fantastisk lille gade i Rom.', 'Rome, Italy', '2026-07-13 15:20:00', 1, 0),

('Så er vi ankommet til Aarhus!', 'Aarhus, Denmark', '2026-07-25 13:00:00', 2, 1),
('God mad og en tur rundt i byen.', 'Aarhus, Denmark', '2026-07-26 18:30:00', 2, 0),

('Så er vi landet i Barcelona!', 'Barcelona, Spain', '2026-08-14 16:00:00', 3, 1),
('Vi fandt den hyggeligste lille café.', 'Barcelona, Spain', '2026-08-15 12:30:00', 3, 0),
('Solnedgang over Barcelona.', 'Barcelona, Spain', '2026-08-16 20:00:00', 3, 0),

('Tid til en rolig weekend ved Vesterhavet.', 'Vejers Strand, Denmark', '2026-08-21 14:00:00', 4, 1),
('Morgenkaffe med udsigt til havet.', 'Vejers Strand, Denmark', '2026-08-22 09:30:00', 4, 0),

('Første dag i Berlin med studiegruppen.', 'Berlin, Germany', '2026-09-20 15:00:00', 5, 1),
('Vi har brugt hele dagen på at udforske byen.', 'Berlin, Germany', '2026-09-21 19:00:00', 5, 0),
('Sidste aften i Berlin.', 'Berlin, Germany', '2026-09-22 20:30:00', 5, 0),

('Klar til en weekend i København.', 'Copenhagen, Denmark', '2026-09-05 13:00:00', 6, 1),
('Nyder byen og det gode vejr.', 'Copenhagen, Denmark', '2026-09-06 15:30:00', 6, 0),

('Første stop på vores roadtrip!', 'Odense, Denmark', '2026-08-01 12:00:00', 7, 1),
('Videre mod Aalborg.', 'Aalborg, Denmark', '2026-08-03 16:00:00', 7, 0),
('Sidste dag på roadtrippen.', 'Skagen, Denmark', '2026-08-05 18:00:00', 7, 0),

('Bonjour Paris!', 'Paris, France', '2026-09-12 16:00:00', 8, 1),
('Vi fandt den perfekte café.', 'Paris, France', '2026-09-13 13:00:00', 8, 0),
('Aften ved Eiffeltårnet.', 'Paris, France', '2026-09-14 21:00:00', 8, 0),

('Vi er klar til vandretur i Norge.', 'Bergen, Norway', '2026-08-10 09:00:00', 9, 1),
('Fantastisk udsigt på toppen.', 'Bergen, Norway', '2026-08-12 16:00:00', 9, 0),
('Sidste dag i naturen.', 'Bergen, Norway', '2026-08-14 17:00:00', 9, 0),

('Planlægningen til Spanien er i gang!', 'Copenhagen, Denmark', '2026-09-25 14:30:00', 10, 1);


-- POST IMAGES

INSERT INTO post_images
(file_path, post_id, alt_text, sort_order)
VALUES
('italy-arrival.jpg', 1, 'View from the hotel in Italy', 1),
('italy-dinner.jpg', 2, 'Dinner in Rome', 1),
('rome-street.jpg', 3, 'A street in Rome', 1),

('aarhus-arrival.jpg', 4, 'Arrival in Aarhus', 1),
('aarhus-food.jpg', 5, 'Food in Aarhus', 1),

('barcelona-arrival.jpg', 6, 'Street in Barcelona', 1),
('barcelona-cafe.jpg', 7, 'Cafe in Barcelona', 1),
('barcelona-sunset.jpg', 8, 'Sunset in Barcelona', 1),

('vesterhavet.jpg', 9, 'The beach at Vesterhavet', 1),
('morning-coffee.jpg', 10, 'Morning coffee by the sea', 1),

('berlin-trip.jpg', 11, 'Study group in Berlin', 1),
('berlin-city.jpg', 12, 'Street in Berlin', 1),
('berlin-evening.jpg', 13, 'Evening in Berlin', 1),

('copenhagen.jpg', 14, 'Copenhagen city', 1),
('copenhagen-day.jpg', 15, 'A day in Copenhagen', 1),

('odense.jpg', 16, 'First stop in Odense', 1),
('aalborg.jpg', 17, 'Aalborg during the roadtrip', 1),
('skagen.jpg', 18, 'Skagen beach', 1),

('paris-arrival.jpg', 19, 'Paris street', 1),
('paris-cafe.jpg', 20, 'Cafe in Paris', 1),
('eiffel-tower.jpg', 21, 'Eiffel Tower at night', 1),

('norway-hike.jpg', 22, 'Hiking in Norway', 1),
('norway-view.jpg', 23, 'View from the mountain', 1),
('norway-nature.jpg', 24, 'Nature in Norway', 1),

('spain-planning.jpg', 25, 'Planning the trip to Spain', 1);


-- COMMENTS

INSERT INTO comments
(created_at, body, post_id, user_id, parent_comment_id)
VALUES
('2026-07-10 15:00:00', 'Det ser virkelig hyggeligt ud!', 1, 2, NULL),
('2026-07-10 15:15:00', 'God tur!', 1, 3, NULL),
('2026-07-10 15:30:00', 'Hvor ser det dejligt ud.', 1, 4, NULL),
('2026-07-10 15:45:00', 'Tak! Vi har det virkelig godt.', 1, 1, 1),

('2026-07-11 21:00:00', 'Det ser lækkert ud!', 2, 2, NULL),
('2026-07-11 21:10:00', 'Jeg er misundelig!', 2, 5, NULL),

('2026-07-13 16:00:00', 'Den gade ser virkelig hyggelig ud.', 3, 3, NULL),

('2026-07-25 14:00:00', 'God tur!', 4, 1, NULL),
('2026-07-25 14:15:00', 'Aarhus er så hyggelig.', 4, 3, NULL),

('2026-08-14 17:00:00', 'Glæder mig til at se mere!', 6, 1, NULL),
('2026-08-14 17:15:00', 'Barcelona ser fantastisk ud.', 6, 4, NULL),

('2026-08-15 13:00:00', 'Hvor ligger den café?', 7, 8, NULL),
('2026-08-15 13:10:00', 'Den ligger tæt på stranden.', 7, 6, 12),

('2026-08-21 15:00:00', 'Det ser så hyggeligt ud.', 9, 10, NULL),
('2026-08-22 10:00:00', 'Perfekt morgen!', 10, 14, NULL),

('2026-09-20 16:00:00', 'God tur til jer!', 11, 12, NULL),
('2026-09-20 16:15:00', 'Berlin er virkelig fed.', 11, 16, NULL),
('2026-09-21 20:00:00', 'Hvad har været jeres favorit indtil videre?', 12, 9, NULL),
('2026-09-21 20:15:00', 'Brandenburger Tor var min favorit.', 12, 3, 18),

('2026-09-05 14:00:00', 'København er altid en god idé.', 14, 11, NULL),
('2026-09-06 16:00:00', 'Det ser ud til at være en god weekend.', 15, 16, NULL),

('2026-08-01 13:00:00', 'Hvor går turen hen bagefter?', 16, 8, NULL),
('2026-08-01 13:15:00', 'Vi kører mod Aalborg.', 16, 4, 22),

('2026-08-05 19:00:00', 'Skagen ser flot ud!', 18, 10, NULL),

('2026-09-12 17:00:00', 'Paris!', 19, 13, NULL),
('2026-09-13 14:00:00', 'Den café ser hyggelig ud.', 20, 15, NULL),
('2026-09-14 22:00:00', 'Virkelig flot billede.', 21, 20, NULL),

('2026-08-10 10:00:00', 'God vandretur!', 22, 17, NULL),
('2026-08-12 17:00:00', 'Wow, hvilken udsigt.', 23, 19, NULL),
('2026-08-14 18:00:00', 'Det ser virkelig flot ud.', 24, 13, NULL),

('2026-09-25 15:00:00', 'Glæder mig til Spanien!', 25, 5, NULL);


-- LIKES

INSERT INTO likes
(user_id, post_id, created_at)
VALUES
(2, 1, '2026-07-10 15:05:00'),
(3, 1, '2026-07-10 15:20:00'),
(4, 1, '2026-07-10 15:25:00'),
(5, 1, '2026-07-10 15:30:00'),

(1, 2, '2026-07-11 21:05:00'),
(3, 2, '2026-07-11 21:10:00'),
(6, 2, '2026-07-11 21:15:00'),

(2, 3, '2026-07-13 16:10:00'),
(5, 3, '2026-07-13 16:20:00'),

(1, 4, '2026-07-25 14:05:00'),
(3, 4, '2026-07-25 14:10:00'),
(7, 4, '2026-07-25 14:20:00'),

(2, 5, '2026-07-26 19:00:00'),
(4, 5, '2026-07-26 19:10:00'),

(1, 6, '2026-08-14 17:05:00'),
(4, 6, '2026-08-14 17:10:00'),
(8, 6, '2026-08-14 17:15:00'),
(10, 6, '2026-08-14 17:20:00'),

(2, 7, '2026-08-15 13:05:00'),
(6, 7, '2026-08-15 13:10:00'),
(8, 7, '2026-08-15 13:15:00'),

(3, 8, '2026-08-16 20:10:00'),
(7, 8, '2026-08-16 20:20:00'),

(4, 9, '2026-08-21 15:10:00'),
(8, 9, '2026-08-21 15:20:00'),

(1, 11, '2026-09-20 16:00:00'),
(9, 11, '2026-09-20 16:10:00'),
(12, 11, '2026-09-20 16:20:00'),
(16, 11, '2026-09-20 16:30:00'),

(3, 12, '2026-09-21 20:00:00'),
(11, 12, '2026-09-21 20:10:00'),
(16, 12, '2026-09-21 20:20:00'),

(1, 14, '2026-09-05 14:10:00'),
(9, 14, '2026-09-05 14:20:00'),

(5, 16, '2026-08-01 13:00:00'),
(8, 16, '2026-08-01 13:10:00'),
(10, 16, '2026-08-01 13:20:00'),

(7, 19, '2026-09-12 17:10:00'),
(13, 19, '2026-09-12 17:20:00'),
(15, 19, '2026-09-12 17:30:00'),

(5, 22, '2026-08-10 10:10:00'),
(13, 22, '2026-08-10 10:20:00'),
(17, 22, '2026-08-10 10:30:00'),

(9, 23, '2026-08-12 17:00:00'),
(15, 23, '2026-08-12 17:10:00'),
(19, 23, '2026-08-12 17:20:00');


-- POST VIEWS

INSERT INTO post_views
(user_id, post_id, viewed_at)
VALUES
(2, 1, '2026-07-10 14:45:00'),
(3, 1, '2026-07-10 15:10:00'),
(4, 1, '2026-07-10 15:20:00'),
(5, 1, '2026-07-10 15:25:00'),
(6, 1, '2026-07-10 15:30:00'),

(1, 2, '2026-07-11 20:30:00'),
(2, 2, '2026-07-11 20:40:00'),
(3, 2, '2026-07-11 20:50:00'),
(5, 2, '2026-07-11 21:00:00'),

(1, 3, '2026-07-13 15:30:00'),
(4, 3, '2026-07-13 15:40:00'),

(1, 4, '2026-07-25 13:30:00'),
(2, 4, '2026-07-25 13:40:00'),
(3, 4, '2026-07-25 14:00:00'),

(1, 6, '2026-08-14 16:30:00'),
(4, 6, '2026-08-14 16:40:00'),
(6, 6, '2026-08-14 16:50:00'),
(8, 6, '2026-08-14 17:00:00'),

(2, 7, '2026-08-15 12:45:00'),
(4, 7, '2026-08-15 12:50:00'),
(6, 7, '2026-08-15 13:00:00'),

(1, 9, '2026-08-21 14:20:00'),
(6, 9, '2026-08-21 14:30:00'),
(8, 9, '2026-08-21 14:40:00'),

(3, 11, '2026-09-20 15:30:00'),
(9, 11, '2026-09-20 15:40:00'),
(12, 11, '2026-09-20 15:50:00'),
(16, 11, '2026-09-20 16:00:00'),

(3, 12, '2026-09-21 19:20:00'),
(9, 12, '2026-09-21 19:30:00'),
(11, 12, '2026-09-21 19:40:00'),

(1, 14, '2026-09-05 13:30:00'),
(3, 14, '2026-09-05 13:40:00'),
(12, 14, '2026-09-05 13:50:00'),

(4, 16, '2026-08-01 12:30:00'),
(8, 16, '2026-08-01 12:40:00'),
(10, 16, '2026-08-01 12:50:00'),

(5, 19, '2026-09-12 16:30:00'),
(13, 19, '2026-09-12 16:40:00'),
(15, 19, '2026-09-12 16:50:00'),

(5, 22, '2026-08-10 09:30:00'),
(13, 22, '2026-08-10 09:40:00'),
(17, 22, '2026-08-10 09:50:00'),
(19, 22, '2026-08-10 10:00:00');


-- USER TOKENS

INSERT INTO user_tokens
(user_id, token_hash, token_type, expires_at, used_at)
VALUES
(1, 'abc123abc123abc123abc123abc123abc123abc123abc123abc123abc123abcd', 'verify_email', '2026-10-01 12:00:00', NULL),
(5, 'def456def456def456def456def456def456def456def456def456def456defa', 'verify_email', '2026-10-05 12:00:00', NULL),
(10, '1111111111111111111111111111111111111111111111111111111111111111', 'reset_password', '2026-10-10 12:00:00', NULL),
(15, '2222222222222222222222222222222222222222222222222222222222222222', 'reset_password', '2026-10-12 12:00:00', NULL);


-- INVITATIONS

INSERT INTO invitations
(group_id, created_by, token_hash, expires_at, used_at, created_at)
VALUES
(1, 1, '3333333333333333333333333333333333333333333333333333333333333333', '2026-10-10 12:00:00', NULL, '2026-09-25 10:00:00'),
(2, 2, '4444444444444444444444444444444444444444444444444444444444444444', '2026-10-15 12:00:00', NULL, '2026-09-26 11:00:00'),
(3, 3, '5555555555555555555555555555555555555555555555555555555555555555', '2026-10-20 12:00:00', NULL, '2026-09-27 12:00:00'),
(5, 5, '6666666666666666666666666666666666666666666666666666666666666666', '2026-10-25 12:00:00', NULL, '2026-09-28 13:00:00');


-- SITE CONTENT

INSERT INTO site_content
(content_key, title, body, updated_by, updated_at)
VALUES
('welcome', 'Welcome to PostCard', 'Share your travel experiences with the people who matter.', 1, '2026-09-20 10:00:00'),
('about', 'About PostCard', 'PostCard is a private space for sharing travel moments.', 3, '2026-09-20 10:30:00'),
('privacy', 'Privacy', 'Your travel moments are shared only with selected groups.', 1, '2026-09-21 11:00:00'),
('contact', 'Contact', 'Contact the PostCard team if you need help.', 3, '2026-09-21 11:30:00');


-- SITE SETTINGS

INSERT INTO site_settings
(setting_key, setting_value, updated_at)
VALUES
('site_name', 'PostCard', '2026-09-20 10:00:00'),
('max_upload_size', '10MB', '2026-09-20 10:00:00'),
('default_post_limit', '10', '2026-09-20 10:00:00'),
('allow_comments', '1', '2026-09-20 10:00:00'),
('allow_likes', '1', '2026-09-20 10:00:00');