-- =====================================================================
-- PostCard test data (run after schema.sql)
--
-- TEST ACCOUNTS — every password is: test123
--   mia@example.com     Mia Nielsen: admin of "Familien Nielsen", member of
--                       "Webudvikling 3. semester". Has an ACTIVE experience
--                       (Sommer i Norge, 6 of 20 posts) and a PENDING request.
--   sofie@example.com   Sofie Jensen: admin of "Webudvikling 3. semester"
--                       (permission group) — reviews Mia's pending request.
--   noah@example.com    Noah Thomsen: admin of "Løbeklubben Esbjerg".
--   oksana@example.com  Oksana Kovalenko: admin of "Kollegerne" (permission
--                       group) — reviews Lucas' pending request.
--   oliver@example.com  Oliver Hansen: experience with a FULL quota (3 of 3).
--   anna@example.com    Anna Møller: new user, email NOT verified, no groups.
--   emil@example.com    Emil Mortensen: BLOCKED by the operator.
--   laura@example.com   Laura Christiansen: DELETED account.
--   admin@postcard.example  Platform operator (is_operator = 1).
--
-- RAW TOKENS for testing links (the database only stores their SHA-256 hash):
--   reset password (valid until 2026-12-31): reset-anna-valid
--   reset password (expired):                reset-ida-expired
--   verify email (Anna, valid until 2026-10-17): verify-anna-pending
--   invitation to Familien Nielsen (valid):  invite-familien-nielsen
--   invitation to Kollegerne (valid):        invite-kollegerne
--   invitation to Webudvikling (expired):    invite-studievenner-expired
--   invitation to Løbeklubben (already used): invite-loebeklubben-used
--
-- Images point to the sample photos in public/assets/images/sample/
-- (real uploads will be stored on Cloudinary, see post_images).
-- All likes, views and comments come from members of the post's own group.
-- "Today" in this data is around 2026-10-15.
-- =====================================================================

USE postcard;
SET NAMES utf8mb4;


-- USERS
-- password_hash = password_hash('test123', PASSWORD_DEFAULT) — a new salt per user.

INSERT INTO users
(last_name, first_name, username, password_hash, profile_image, bio, email, birthdate, user_status, is_operator, email_verified_at, created_at)
VALUES
('Nielsen', 'Mia', 'mia_nielsen', '$2y$12$ABdc7VzEBdJHFaChkYuJMuEQdApxDJFC2f/SsWOc36k3TQ1Z4TEgW', NULL, 'Rejser, fotograferer og samler minder med familien.', 'mia@example.com', '2001-05-12', 'active', 0, '2026-03-01 10:05:00', '2026-03-01 10:00:00'),
('Nielsen', 'Peter', 'peter_nielsen', '$2y$12$MKdSmujzQirzw8QywOoSFO.VJhmTmJ8Dem0zW2GuG.sBkV2Sc5QUa', NULL, 'Mias far. Elsker fjorde og lange gåture.', 'peter@example.com', '1968-03-02', 'active', 0, '2026-03-02 19:02:00', '2026-03-02 19:00:00'),
('Nielsen', 'Karen', 'karen_nielsen', '$2y$12$U8cI1MKRpM.b.ViRWYTmR.7eQmYFZVvFzz7hgtCfofe/tbTvPHgj6', NULL, 'Mor, lærer og altid den, der husker madpakkerne.', 'karen@example.com', '1970-11-19', 'active', 0, '2026-03-02 19:32:00', '2026-03-02 19:30:00'),
('Nielsen', 'Jonas', 'jonas_nielsen', '$2y$12$DcaKACf.mj5jGeMPzWC5vu6GToIT2exB3qSoMv0470bw4eisNb.Iu', NULL, 'Lillebror. Løber, når jeg ikke spiller fodbold.', 'jonas@example.com', '2004-07-08', 'active', 0, '2026-03-03 16:03:00', '2026-03-03 16:00:00'),
('Jensen', 'Sofie', 'sofie_jensen', '$2y$12$4z2830gdvw9R2fR94w5P3u3pcba9SBoU2./u12sHSjJSNwMgKtGNC', NULL, 'Studerer webudvikling. Kaffe først, kode bagefter.', 'sofie@example.com', '2000-08-21', 'active', 0, '2026-06-10 09:04:00', '2026-06-10 09:00:00'),
('Hansen', 'Oliver', 'oliver_hansen', '$2y$12$qIknl0hqombvYun.JVk8Xes7l20CHyuytCBPVYasorx1MPrqoZ2P2', NULL, 'Bjerge, telte og dårligt vejr – jeg elsker det hele.', 'oliver@example.com', '1999-03-15', 'active', 0, '2026-03-18 20:05:00', '2026-03-18 20:00:00'),
('Madsen', 'Emma', 'emma_madsen', '$2y$12$YapOel1LjUiZ205Zly8o5eXMaIEJZ/ysXkyVwrD3qoIz3g9YaWKne', NULL, 'Roadtrips, solnedgange og en god playliste.', 'emma@example.com', '2002-11-02', 'active', 0, '2026-06-10 12:03:00', '2026-06-10 12:00:00'),
('Pedersen', 'Lucas', 'lucas_pedersen', '$2y$12$7t75wQAkabyHr4TlUpC5Q.01kp4TrBZiEGkthiK5AfuVUTg1fZJLW', NULL, 'Frontend-udvikler med kameraet i tasken.', 'lucas@example.com', '2001-06-18', 'active', 0, '2026-06-11 08:33:00', '2026-06-11 08:30:00'),
('Larsen', 'Freja', 'freja_larsen', '$2y$12$BraQHttwAK0TrEpLCf.8aOWx.LAaU25OybmemrBoPbxPMqkGcwWBq', NULL, 'Bylivet er mit. Kaffebarer og loppemarkeder.', 'freja@example.com', '2002-01-30', 'active', 0, '2026-08-26 09:15:00', '2026-08-26 09:00:00'),
('Thomsen', 'Noah', 'noah_thomsen', '$2y$12$v/JqfjYQ90M1xJXln0HyFujgaiQ2vCk02a28thqQBToh23g.nop5a', NULL, 'Træner i Løbeklubben Esbjerg. Trail er bedst.', 'noah@example.com', '1995-10-11', 'active', 0, '2026-03-15 18:04:00', '2026-03-15 18:00:00'),
('Rasmussen', 'Ida', 'ida_rasmussen', '$2y$12$RectAtjKImTrpMEhtvZD0OON5J/WbjscwgE.WavOWBfyGD344MDua', NULL, 'Løber om morgenen, bager om aftenen.', 'ida@example.com', '1997-07-07', 'active', 0, '2026-03-16 07:03:00', '2026-03-16 07:00:00'),
('Kovalenko', 'Oksana', 'oksana_kovalenko', '$2y$12$pUMevcqZGfqpBZYRm/XtU.EB2vuqlxxnIK8oTDj7A3v3OGLBGXKfe', NULL, 'Fra Lviv, bor i Esbjerg. Projektleder og weekendbager.', 'oksana@example.com', '1992-04-24', 'active', 0, '2026-08-31 08:04:00', '2026-08-31 08:00:00'),
('Møller', 'Anna', 'anna_moller', '$2y$12$Y1VYYZXJ2qb4Pcjlzt3ta.2rhhAMF3O4dlzUJdSqxyUzS4SDy/jKW', NULL, NULL, 'anna@example.com', '2003-02-11', 'active', 0, NULL, '2026-10-14 21:00:00'),
('Mortensen', 'Emil', 'emil_mortensen', '$2y$12$MEw0qXwbXMfn04qTww5Uz.BOPgnwNSlx4yJPxCU6YHgVZFICZsPG2', NULL, 'Fotograf.', 'emil@example.com', '1999-12-05', 'blocked', 0, '2026-06-12 10:04:00', '2026-06-12 10:00:00'),
('Christiansen', 'Laura', 'laura_christiansen', '$2y$12$wu9AWsGWRISe9iMRLuCvzeipfOCJXyMYLU4d6Rq11fN6blJCSXA8a', NULL, NULL, 'laura@example.com', '2003-03-28', 'deleted', 0, '2026-06-12 11:03:00', '2026-06-12 11:00:00'),
('Admin', 'PostCard', 'postcard_admin', '$2y$12$uAtCwtuLPXvBWL5QFARQ8.FP5.HdXVNE/RyOlfwjLy0qSEPE0UZn.', NULL, NULL, 'admin@postcard.example', '1988-06-14', 'active', 1, '2026-02-15 09:01:00', '2026-02-15 09:00:00');


-- USER GROUPS
-- 1 and 3 are open groups; 2 and 4 use permission-based posting.

INSERT INTO user_groups
(name, description, posting_mode, default_post_limit, style, created_at)
VALUES
('Familien Nielsen', 'Vores familiealbum: ferier, fødselsdage og hverdagen imellem.', 'open', 20, 'postcard', '2026-03-02 18:00:00'),
('Webudvikling 3. semester', 'Holdet fra webudvikling: studieture, projektuger og sommerens eventyr.', 'permission', 15, 'postcard', '2026-06-10 09:30:00'),
('Løbeklubben Esbjerg', 'Løb, trail og konkurrencer – i Danmark og ude i verden.', 'open', 10, 'postcard', '2026-03-15 18:30:00'),
('Kollegerne', 'Os fra kontoret. Del jeres ferier og firmaturen.', 'permission', 20, 'postcard', '2026-08-31 08:30:00');


-- GROUP MEMBERS

INSERT INTO group_members
(user_id, group_id, member_role, member_status, joined_at)
VALUES
-- Familien Nielsen
(1, 1, 'group_admin', 'active', '2026-03-02 18:00:00'),
(2, 1, 'member', 'active', '2026-03-02 19:10:00'),
(3, 1, 'member', 'active', '2026-03-02 19:40:00'),
(4, 1, 'member', 'active', '2026-03-03 16:10:00'),

-- Webudvikling 3. semester
(5, 2, 'group_admin', 'active', '2026-06-10 09:30:00'),
(1, 2, 'member', 'active', '2026-06-10 10:00:00'),
(7, 2, 'member', 'active', '2026-06-10 12:10:00'),
(8, 2, 'member', 'active', '2026-06-11 08:40:00'),
(6, 2, 'member', 'active', '2026-06-11 20:00:00'),
(14, 2, 'member', 'removed', '2026-06-12 10:10:00'),
(15, 2, 'member', 'left', '2026-06-12 11:10:00'),
(9, 2, 'member', 'active', '2026-08-26 09:30:00'),

-- Løbeklubben Esbjerg (Oliver joined through an invitation link)
(10, 3, 'group_admin', 'active', '2026-03-15 18:30:00'),
(11, 3, 'member', 'active', '2026-03-16 07:10:00'),
(6, 3, 'member', 'active', '2026-03-20 19:00:00'),
(4, 3, 'member', 'active', '2026-03-21 17:00:00'),

-- Kollegerne
(12, 4, 'group_admin', 'active', '2026-08-31 08:30:00'),
(8, 4, 'member', 'active', '2026-09-01 09:00:00'),
(11, 4, 'member', 'active', '2026-09-01 12:00:00'),
(9, 4, 'member', 'left', '2026-09-02 10:00:00');


-- EXPERIENCES
-- One of each state: active, ended, closed, pending, rejected, not started yet,
-- and one with the post quota used up.

INSERT INTO experiences
(name, description, start_date, end_date, group_id, user_id, experience_status, post_limit, created_at)
VALUES
-- 1: ACTIVE now, 6 of 20 posts used
('Sommer i Norge', 'Fem uger i bil gennem Norge – fra fjordene i syd til kysten ved Ålesund.', '2026-09-10', '2026-10-31', 1, 1, 'approved', 20, '2026-09-05 20:00:00'),
-- 2: closed (ended in April)
('Påske på Isle of Skye', 'Påskeferie i det skotske højland med hele familien.', '2026-04-02', '2026-04-09', 1, 3, 'closed', 20, '2026-03-25 19:00:00'),
-- 3: closed
('Trekking til Everest Base Camp', 'Tre uger til fods i Himalaya.', '2026-05-01', '2026-05-20', 3, 6, 'closed', 10, '2026-04-25 18:00:00'),
-- 4: approved, but the end date has passed (the app must check dates, not only the status)
('Roadtrip langs Californiens kyst', 'To uger på Highway 1 med en afstikker til Yosemite.', '2026-08-01', '2026-08-14', 2, 7, 'approved', 15, '2026-07-25 10:00:00'),
-- 5: PENDING request in a permission group (Sofie must review it)
('Studietur til Berlin', 'Holdets studietur med besøg hos to webbureauer.', '2026-11-05', '2026-11-08', 2, 1, 'pending', 15, '2026-10-12 21:00:00'),
-- 6: REJECTED request
('Weekend i København', 'En hurtig weekend med kaffebarer og loppemarked.', '2026-10-24', '2026-10-26', 2, 9, 'rejected', 15, '2026-10-08 12:00:00'),
-- 7: approved, ended
('Trailløb på Mallorca', 'Træningslejr og maratonløb i Serra de Tramuntana.', '2026-09-24', '2026-09-28', 3, 10, 'approved', 10, '2026-09-20 17:00:00'),
-- 8: ACTIVE, but the quota is used up (3 of 3)
('Efterårsvandring i Skotland', 'Ti dage på West Highland Way.', '2026-10-10', '2026-10-20', 3, 6, 'approved', 3, '2026-10-08 19:00:00'),
-- 9: approved, has NOT started yet
('Juleferie i Lviv', 'Jul hjemme hos familien i Lviv.', '2026-12-20', '2027-01-03', 4, 12, 'approved', 20, '2026-10-01 09:00:00'),
-- 10: PENDING request (Oksana must review it)
('Firmatur til Bornholm', 'Kontorets weekendtur med cykler og røgede sild.', '2026-11-13', '2026-11-15', 4, 8, 'pending', 20, '2026-10-14 09:30:00');


-- POSTS
-- The author of a post is the owner of its experience (experiences.user_id).

INSERT INTO posts
(caption, location, created_at, experience_id, is_sticky)
VALUES
-- Sommer i Norge (Mia)
('Første stop: Prækestolen! Turen op tog to timer, men udsigten over Lysefjorden var det hele værd.', 'Prækestolen, Norge', '2026-09-12 14:30:00', 1, 1),
('Regn hele formiddagen – så blev vandfaldene til gengæld enorme.', 'Hardanger, Norge', '2026-09-15 11:10:00', 1, 0),
('Frokostpause ved floden. Far mener, det er det smukkeste sted på turen indtil videre.', 'Jotunheimen, Norge', '2026-09-19 18:45:00', 1, 0),
('Vi har kørt Trollstigen i tåge. Far holdt øjnene lukket i alle 11 hårnålesving.', 'Trollstigen, Norge', '2026-09-24 20:00:00', 1, 0),
('Sidste dag i fjordene. Vi gik op til Prækestolen igen for at sige farvel til udsigten.', 'Prækestolen, Norge', '2026-09-30 10:15:00', 1, 0),
('Kysten ved Ålesund. Blæsten tager pusten fra én, men lyset er helt fantastisk.', 'Ålesund, Norge', '2026-10-06 16:40:00', 1, 0),

-- Påske på Isle of Skye (Karen)
('Vejen op til Quiraing. Vi stoppede hvert femte minut for at tage billeder.', 'Quiraing, Skotland', '2026-04-03 15:00:00', 2, 0),
('Vandfaldet i Glen Brittle – vi fik våde sko, men det var det værd.', 'Glen Brittle, Skotland', '2026-04-05 12:20:00', 2, 0),
('Sidste aften ved kysten. Jonas påstår, han så en sæl.', 'Neist Point, Skotland', '2026-04-08 17:30:00', 2, 0),

-- Trekking til Everest Base Camp (Oliver)
('Nat i telt på 4.400 meter. Det sneede hele natten, men morgenlyset over bjergene var magisk.', 'Dingboche, Nepal', '2026-05-06 07:30:00', 3, 0),
('Base Camp nået! Benene er færdige, men humøret er i top.', 'Everest Base Camp, Nepal', '2026-05-12 19:00:00', 3, 0),

-- Roadtrip langs Californiens kyst (Emma)
('Highway 1 fra San Francisco mod syd. Klipperne ved kysten er vildere end på billederne.', 'Big Sur, Californien', '2026-08-02 18:00:00', 4, 1),
('Afstikker til Yosemite. Vi badede i floden under de store granitklipper.', 'Yosemite, Californien', '2026-08-06 20:30:00', 4, 0),
('Yosemite Falls efter en lang vandretur. Sidste aften, inden vi kører hjem.', 'Yosemite, Californien', '2026-08-09 21:30:00', 4, 0),

-- Trailløb på Mallorca (Noah)
('Træningsløb i bjergene: 900 højdemeter før frokost.', 'Serra de Tramuntana, Mallorca', '2026-09-25 13:00:00', 7, 0),
('Løbet er i hus! 42 km langs kysten – og i mål på under seks timer.', 'Cala Figuera, Mallorca', '2026-09-27 17:00:00', 7, 0),

-- Efterårsvandring i Skotland (Oliver) — quota 3 of 3 used
('Første dag på West Highland Way. Tåge, får og ingen mobildækning.', 'Glencoe, Skotland', '2026-10-11 18:00:00', 8, 0),
('Vandfald i regnvejr – hvad havde vi ellers forventet?', 'Glen Nevis, Skotland', '2026-10-12 17:15:00', 8, 0),
('Skov og en flod helt for os selv. Sidste opslag – nu er kvoten brugt.', 'Loch Lomond, Skotland', '2026-10-13 19:40:00', 8, 0);


-- POST IMAGES
-- Max 5 per post. sort_order decides the order in the photo set.
-- Real uploads get a Cloudinary URL and public_id. These test photos live in
-- public/assets/images/sample/, so they have a local path and no public_id
-- (replace them with Cloudinary URLs once the sample photos are uploaded there).

INSERT INTO post_images
(image_url, post_id, alt_text, sort_order)
VALUES
('assets/images/sample/photo-4.jpg', 1, 'Udsigt fra et klippeplateau ned over en lang, blå fjord', 1),
('assets/images/sample/photo-3.jpg', 2, 'Højt vandfald i en grøn skov med tåge over træerne', 1),
('assets/images/sample/photo-1.jpg', 3, 'Høje granitklipper og grantræer, der spejler sig i en rolig flod', 1),
('assets/images/sample/photo-5.jpg', 4, 'Smal vej gennem grønne bjerge og stejle klipper under en grå himmel', 1),
('assets/images/sample/photo-4.jpg', 5, 'Udsigt fra et klippeplateau ned over en lang, blå fjord', 1),
('assets/images/sample/photo-3.jpg', 5, 'Højt vandfald i en grøn skov med tåge over træerne', 2),
('assets/images/sample/photo-1.jpg', 5, 'Høje granitklipper og grantræer ved en rolig flod', 3),
('assets/images/sample/photo-6.jpg', 6, 'Lyse klipper og store sten i et turkis hav', 1),

('assets/images/sample/photo-5.jpg', 7, 'Smal vej gennem grønne bjerge og stejle klipper under en grå himmel', 1),
('assets/images/sample/photo-3.jpg', 8, 'Højt vandfald i en grøn skov med tåge over træerne', 1),
('assets/images/sample/photo-6.jpg', 9, 'Lyse klipper og store sten i et turkis hav', 1),

('assets/images/sample/photo-2.jpg', 10, 'Gult telt i sneen foran snedækkede bjergtoppe', 1),
('assets/images/sample/photo-2.jpg', 11, 'Telt og stengærde i sneen med høje bjerge i baggrunden', 1),

('assets/images/sample/photo-5.jpg', 12, 'Smal vej gennem grønne bjerge og stejle klipper', 1),
('assets/images/sample/photo-6.jpg', 12, 'Lyse klipper og store sten i et turkis hav', 2),
('assets/images/sample/photo-1.jpg', 13, 'Høje granitklipper og grantræer, der spejler sig i en rolig flod', 1),
('assets/images/sample/photo-3.jpg', 14, 'Højt vandfald i en grøn skov med tåge over træerne', 1),

('assets/images/sample/photo-1.jpg', 15, 'Klipper og grantræer i bjergene', 1),
('assets/images/sample/photo-6.jpg', 16, 'Lyse klipper og store sten i et turkis hav', 1),

('assets/images/sample/photo-5.jpg', 17, 'Smal vej gennem grønne bjerge under en grå himmel', 1),
('assets/images/sample/photo-3.jpg', 18, 'Højt vandfald i en grøn skov med tåge over træerne', 1),
('assets/images/sample/photo-1.jpg', 19, 'Grantræer ved en rolig flod med klipper i baggrunden', 1);


-- COMMENTS
-- Only members of the post's group comment. Replies use parent_comment_id.
-- Emil's comment (27) is from before he was removed and blocked.

INSERT INTO comments
(created_at, body, post_id, user_id, parent_comment_id)
VALUES
('2026-09-12 15:02:00', 'Hvor er det flot! Pas nu på ved kanten.', 1, 3, NULL),
('2026-09-12 15:20:00', 'Vi holdt god afstand, mor!', 1, 1, 1),
('2026-09-12 18:40:00', 'Misundelig. Her regner det bare.', 1, 4, NULL),
('2026-09-15 13:10:00', 'Husk regntøjet! Jeg kan se, I bliver våde.', 2, 3, NULL),
('2026-09-24 21:00:00', 'Haha, typisk far.', 4, 4, NULL),
('2026-09-24 21:15:00', 'Jeg kørte jo ikke, så det var tilladt.', 4, 2, 5),
('2026-09-30 11:00:00', 'Det bliver et flot billede til albummet.', 5, 3, NULL),
('2026-09-30 12:30:00', 'Kommer I hjem til min fødselsdag?', 5, 4, NULL),
('2026-09-30 13:00:00', 'Selvfølgelig! Vi er hjemme den 31.', 5, 1, 8),
('2026-10-06 17:30:00', 'Ålesund er så smuk. Hils far.', 6, 3, NULL),

('2026-04-03 16:00:00', 'Den vej ser vild ud!', 7, 1, NULL),
('2026-04-03 16:30:00', 'Den er smallere, end den ser ud.', 7, 3, 11),
('2026-04-08 18:00:00', 'Jonas og hans sæler …', 9, 2, NULL),

('2026-05-06 09:00:00', 'Vildt, Oliver! Hvordan har benene det?', 10, 10, NULL),
('2026-05-06 12:00:00', 'Ømme, men glade.', 10, 6, 14),
('2026-05-12 20:00:00', 'Tillykke! Det er en drøm.', 11, 11, NULL),
('2026-05-12 20:30:00', 'Respekt!', 11, 4, NULL),

('2026-08-02 19:00:00', 'Big Sur står på min liste!', 12, 1, NULL),
('2026-08-02 19:30:00', 'Du skal derover, Mia. Det er helt vildt.', 12, 7, 18),
('2026-08-06 21:00:00', 'Var det koldt at bade?', 13, 8, NULL),
('2026-08-06 21:20:00', 'Iskoldt, men det var det værd.', 13, 7, 20),
('2026-08-09 22:00:00', 'Smukt billede, Emma.', 14, 5, NULL),

('2026-09-27 19:00:00', 'Sejt løb, Noah!', 16, 11, NULL),
('2026-09-27 19:30:00', 'Under seks timer – imponerende.', 16, 6, NULL),
('2026-10-11 20:00:00', 'God tur! Husk at strække ud.', 17, 10, NULL),
('2026-10-13 21:00:00', 'Det ser så fredeligt ud.', 19, 11, NULL),

('2026-08-03 08:05:00', 'Fedt billede!', 12, 14, NULL);


-- LIKES
-- Only group members, never the author, always after they viewed the post.

INSERT INTO likes
(user_id, post_id, created_at)
VALUES
(2, 1, '2026-09-12 14:36:00'),
(3, 1, '2026-09-12 15:01:00'),
(4, 1, '2026-09-12 18:31:00'),
(3, 2, '2026-09-15 13:06:00'),
(3, 3, '2026-09-19 19:31:00'),
(4, 3, '2026-09-19 20:11:00'),
(3, 4, '2026-09-24 20:41:00'),
(4, 4, '2026-09-24 20:56:00'),
(2, 5, '2026-09-30 10:21:00'),
(3, 5, '2026-09-30 10:56:00'),
(4, 5, '2026-09-30 12:26:00'),
(3, 6, '2026-10-06 17:26:00'),
(4, 6, '2026-10-06 19:01:00'),

(1, 7, '2026-04-03 15:56:00'),
(2, 7, '2026-04-03 16:21:00'),
(4, 7, '2026-04-03 17:01:00'),
(1, 8, '2026-04-05 13:01:00'),
(1, 9, '2026-04-08 17:51:00'),
(2, 9, '2026-04-08 17:56:00'),
(4, 9, '2026-04-08 19:01:00'),

(10, 10, '2026-05-06 08:56:00'),
(11, 10, '2026-05-06 09:31:00'),
(4, 10, '2026-05-06 15:01:00'),
(10, 11, '2026-05-12 19:21:00'),
(11, 11, '2026-05-12 19:56:00'),
(4, 11, '2026-05-12 20:26:00'),

(1, 12, '2026-08-02 18:56:00'),
(5, 12, '2026-08-02 19:11:00'),
(8, 12, '2026-08-02 20:31:00'),
(9, 12, '2026-08-27 10:01:00'),
(1, 13, '2026-08-06 20:51:00'),
(8, 13, '2026-08-06 20:56:00'),
(5, 13, '2026-08-06 21:06:00'),
(6, 13, '2026-08-06 21:31:00'),
(9, 13, '2026-08-27 10:06:00'),
(5, 14, '2026-08-09 21:51:00'),
(6, 14, '2026-08-09 23:01:00'),

(6, 15, '2026-09-25 14:01:00'),
(11, 15, '2026-09-25 15:31:00'),
(6, 16, '2026-09-27 18:01:00'),
(11, 16, '2026-09-27 18:46:00'),
(4, 16, '2026-09-27 20:01:00'),

(10, 17, '2026-10-11 19:31:00'),
(11, 17, '2026-10-11 20:31:00'),
(11, 18, '2026-10-12 19:11:00'),
(4, 19, '2026-10-13 20:01:00'),
(10, 19, '2026-10-13 20:31:00'),
(11, 19, '2026-10-13 20:51:00');


-- POST VIEWS
-- One row per member per post (distinct viewers), never the author.

INSERT INTO post_views
(user_id, post_id, viewed_at)
VALUES
(2, 1, '2026-09-12 14:35:00'),
(3, 1, '2026-09-12 15:00:00'),
(4, 1, '2026-09-12 18:30:00'),
(2, 2, '2026-09-15 11:15:00'),
(3, 2, '2026-09-15 13:05:00'),
(4, 2, '2026-09-15 16:00:00'),
(2, 3, '2026-09-19 18:50:00'),
(3, 3, '2026-09-19 19:30:00'),
(4, 3, '2026-09-19 20:10:00'),
(2, 4, '2026-09-24 20:05:00'),
(3, 4, '2026-09-24 20:40:00'),
(4, 4, '2026-09-24 20:55:00'),
(2, 5, '2026-09-30 10:20:00'),
(3, 5, '2026-09-30 10:55:00'),
(4, 5, '2026-09-30 12:25:00'),
(3, 6, '2026-10-06 17:25:00'),
(4, 6, '2026-10-06 19:00:00'),

(1, 7, '2026-04-03 15:55:00'),
(2, 7, '2026-04-03 16:20:00'),
(4, 7, '2026-04-03 17:00:00'),
(1, 8, '2026-04-05 13:00:00'),
(2, 8, '2026-04-05 14:10:00'),
(4, 8, '2026-04-05 18:00:00'),
(1, 9, '2026-04-08 17:50:00'),
(2, 9, '2026-04-08 17:55:00'),
(4, 9, '2026-04-08 19:00:00'),

(10, 10, '2026-05-06 08:55:00'),
(11, 10, '2026-05-06 09:30:00'),
(4, 10, '2026-05-06 15:00:00'),
(10, 11, '2026-05-12 19:20:00'),
(11, 11, '2026-05-12 19:55:00'),
(4, 11, '2026-05-12 20:25:00'),

(1, 12, '2026-08-02 18:55:00'),
(5, 12, '2026-08-02 19:10:00'),
(6, 12, '2026-08-02 20:00:00'),
(8, 12, '2026-08-02 20:30:00'),
(14, 12, '2026-08-03 08:00:00'),
(9, 12, '2026-08-27 10:00:00'),
(1, 13, '2026-08-06 20:50:00'),
(8, 13, '2026-08-06 20:55:00'),
(5, 13, '2026-08-06 21:05:00'),
(6, 13, '2026-08-06 21:30:00'),
(9, 13, '2026-08-27 10:05:00'),
(5, 14, '2026-08-09 21:50:00'),
(1, 14, '2026-08-09 22:10:00'),
(6, 14, '2026-08-09 23:00:00'),

(6, 15, '2026-09-25 14:00:00'),
(11, 15, '2026-09-25 15:30:00'),
(4, 15, '2026-09-25 18:00:00'),
(6, 16, '2026-09-27 18:00:00'),
(11, 16, '2026-09-27 18:45:00'),
(4, 16, '2026-09-27 20:00:00'),

(10, 17, '2026-10-11 19:30:00'),
(11, 17, '2026-10-11 20:30:00'),
(4, 17, '2026-10-11 21:00:00'),
(10, 18, '2026-10-12 18:00:00'),
(11, 18, '2026-10-12 19:10:00'),
(4, 19, '2026-10-13 20:00:00'),
(10, 19, '2026-10-13 20:30:00'),
(11, 19, '2026-10-13 20:50:00');


-- USER TOKENS
-- token_hash = SHA-256 of the raw token (see the list at the top of this file).

INSERT INTO user_tokens
(user_id, token_hash, token_type, expires_at, used_at)
VALUES
(13, '13a5a92763aed30711b96b5f1a6ba9be0928ffc2db91fe105bff5a06ea9cf2a8', 'verify_email', '2026-10-17 21:00:00', NULL),
(9, 'd2b1a34898fde199eae2da0f12b5e53458259d9db98ee837234ceb504502a4a4', 'verify_email', '2026-08-29 09:00:00', '2026-08-26 09:15:00'),
(13, '6da974d5fd0e946f17fd5c3fade359ddec3fa980e00b419e99526e43610de8f6', 'reset_password', '2026-12-31 23:59:59', NULL),
(11, '9482276ddcb5bb5fad9ea5d77f16eb78f224ad708e62774f5b1162942aefeaf4', 'reset_password', '2026-09-01 12:00:00', NULL);


-- INVITATIONS
-- token_hash = SHA-256 of the raw token (see the list at the top of this file).

INSERT INTO invitations
(group_id, created_by, token_hash, expires_at, used_at, created_at)
VALUES
(1, 1, 'b387ccf86313a543a59822da6e7a5cff434fb623dca73768962dc046a4c2fd4b', '2026-12-31 23:59:59', NULL, '2026-10-10 18:00:00'),
(2, 5, 'bb5a8aefab79dca1b0aa19732cd8e758fc4a431b5f7d62935937d5e54c2ff51c', '2026-09-08 09:00:00', NULL, '2026-09-01 09:00:00'),
(3, 10, '73316b5d426cc0ceb0c4ff77b33aceecfee7f84b0504b3afd08da5bdb82c2978', '2026-03-25 18:00:00', '2026-03-20 19:00:00', '2026-03-18 18:00:00'),
(4, 12, '77c5e6e3bfd83f2158c80ed800f5e309d1b44f7c4abf222502a1be634cc2624f', '2026-12-31 23:59:59', NULL, '2026-10-13 09:00:00');


-- SITE CONTENT
-- Keys match the operator's content page (admin-content.php).

INSERT INTO site_content
(content_key, title, body, updated_by, updated_at)
VALUES
('welcome', 'Del din oplevelse – ikke hele dit liv', 'Et privat sted at dele rejseminder med de mennesker, der betyder noget.', 16, '2026-09-21 10:00:00'),
('rules', 'Regler og retningslinjer', 'PostCard er et privat sted for grupper, der kender hinanden. Del kun billeder, du selv har taget eller har lov til at dele. Spørg, før du deler billeder af andre – især af børn. Det, der deles i en gruppe, bliver i gruppen. Intet krænkende, voldeligt eller ulovligt indhold. Skriv venligt og respektfuldt i kommentarer.', 16, '2026-09-21 10:30:00'),
('privacy', 'Dine data', 'Du kan altid slette din bruger. Så bliver dine oplysninger og dine opslag slettet.', 16, '2026-09-21 11:00:00'),
('contact', 'Kontakt', 'Har du spørgsmål, eller vil du anmelde indhold, der bryder reglerne? Skriv til kontakt@postcard.example, så svarer vi inden for to hverdage.', 16, '2026-09-21 11:30:00');


-- SITE SETTINGS
-- Keys match the operator's settings page (admin-settings.php).

INSERT INTO site_settings
(setting_key, setting_value, updated_at)
VALUES
('site_name', 'PostCard', '2026-09-20 10:00:00'),
('default_post_limit', '20', '2026-09-20 10:00:00'),
('max_upload_size', '10MB', '2026-09-20 10:00:00'),
('allow_likes', '1', '2026-09-20 10:00:00'),
('allow_comments', '1', '2026-09-20 10:00:00');
