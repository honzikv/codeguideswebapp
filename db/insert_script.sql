-- role
INSERT INTO code_guides.role_lov (id, role) VALUES (1, 'author');
INSERT INTO code_guides.role_lov (id, role) VALUES (3, 'publisher');
INSERT INTO code_guides.role_lov (id, role) VALUES (2, 'reviewer');

-- stavy guides
INSERT INTO code_guides.guide_state_lov (id, state) VALUES (2, 'published');
INSERT INTO code_guides.guide_state_lov (id, state) VALUES (3, 'rejected');
INSERT INTO code_guides.guide_state_lov (id, state) VALUES (1, 'reviewed');

-- uzivatele
INSERT INTO code_guides.user (id, username, password, email, role_id, banned) VALUES (20, 'admin', '$2y$10$Dw9YgXabcLaCTmtQqkzv2uK7D9nt6ZxI75W5wm2bVMwxf1jRVKJGO', 'admin@admin.com', 3, 0);
INSERT INTO code_guides.user (id, username, password, email, role_id, banned) VALUES (35, 'reviewer1', '$2y$10$W.LGDxNxMcOwK3dj3jXpVu8jXJa7nZS3VgY37mdsjyQDZ.Q2zxPvi', 'reviewer1@review.com', 2, 0);
INSERT INTO code_guides.user (id, username, password, email, role_id, banned) VALUES (36, 'reviewer2', '$2y$10$heG2jMLNHopNyFjjsMxNHen3013We3d740XvrYWuLxJGQx2CYDtAi', 'reviewer2@review.com', 2, 0);
INSERT INTO code_guides.user (id, username, password, email, role_id, banned) VALUES (37, 'reviewer3', '$2y$10$TV6VipUUeGzIfX6Oz/s1tenlimX8IEeqaD81qgS0K6B4YCSiUNPnW', 'reviewer3@gmail.com', 2, 0);
INSERT INTO code_guides.user (id, username, password, email, role_id, banned) VALUES (38, 'reviewer4', '$2y$10$Ad9CxU8mIOz1Nr1cRjSjpelmo0L3EdErUzWlSKgs7UgJGUkVqLOSS', 'reviewer4@gmail.com', 2, 0);
INSERT INTO code_guides.user (id, username, password, email, role_id, banned) VALUES (40, 'author1', '$2y$10$18xLU6c2XEMMI7wKi586..68YpnIv5qy7pcEK9UwRoJ3X.20tISFG', 'author1@gmail.com', 1, 0);
INSERT INTO code_guides.user (id, username, password, email, role_id, banned) VALUES (41, 'author2', '$2y$10$gDaMRw5rOTxzzJK0LptIoePS/ARXP1.4.16Bq/5Y9v7tYILTCQcES', 'author2@gmail.com', 1, 0);
INSERT INTO code_guides.user (id, username, password, email, role_id, banned) VALUES (43, 'randomauthor33', '$2y$10$KgBuydOUlR2epBYPNiWFEOASZYEt1SDO1DmqdcPxPw0W3Ln7X2ljW', 'randomauthor33@authors.com', 1, 0);
INSERT INTO code_guides.user (id, username, password, email, role_id, banned) VALUES (46, 'author3', '$2y$10$/2RO3cYkqB57tVYv/0u4zOG2cx7xtqIr/cw8dDO9GC1Fbdf9w66DW', 'author3@gmail.com', 1, 0);

-- clanky
INSERT INTO code_guides.guide (id, name, abstract, filename, user_id, guide_state) VALUES (25, 'How to install Python with Anaconda', 'This guide shows how to install python with Anaconda and create virtual environment.', 'python.pdf', 41, 2);
INSERT INTO code_guides.guide (id, name, abstract, filename, user_id, guide_state) VALUES (26, 'Install pytorch with pip', 'Select your preferences and run the install command. Stable represents the most currently tested and supported version of PyTorch. This should be suitable for many users. Preview is available if you want the latest, not fully tested and supported, 1.8 builds that are generated nightly. Please ensure that you have met the prerequisites below (e.g., numpy), depending on your package manager. Anaconda is our recommended package manager since it installs all dependencies. You can also install previous versions of PyTorch. Note that LibTorch is only available for C++.', 'zoscv_01_2020.pdf', 41, 2);
INSERT INTO code_guides.guide (id, name, abstract, filename, user_id, guide_state) VALUES (28, 'How to get started with IntelliJ Idea', 'ntelliJ IDEA is an integrated development environment written in Java for developing computer software. It is developed by JetBrains, and is available as an Apache 2 Licensed community edition, and in a proprietary commercial edition. Both can be used for commercial development. - Wikipedia', '280558.pdf', 43, 2);
INSERT INTO code_guides.guide (id, name, abstract, filename, user_id, guide_state) VALUES (30, 'Test guide to remove', 'test', '8.+Webovy+server.pdf', 40, 1);
INSERT INTO code_guides.guide (id, name, abstract, filename, user_id, guide_state) VALUES (31, 'Introduction to PHP Regex', 'A regular expression is a sequence of characters that forms a search pattern. When you search for data in a text, you can use this search pattern to describe what you are searching for.&#13;&#10;&#13;&#10;A regular expression can be a single character, or a more complicated pattern.&#13;&#10;&#13;&#10;Regular expressions can be used to perform all types of text search and text replace operations.&#13;&#10;&#13;&#10;In PHP, regular expressions are strings composed of delimiters, a pattern and optional modifiers.&#13;&#10;$exp = &#34;/w3schools/i&#34;;&#13;&#10;&#13;&#10;More examples in the pdf!', 'zos03_2020.pdf', 46, 2);
INSERT INTO code_guides.guide (id, name, abstract, filename, user_id, guide_state) VALUES (32, 'guideName', 'guideAbstract', '1-s2.0-S1877050920306530-main.pdf', 20, 1);

-- recenze
INSERT INTO code_guides.review (id, reviewer_id, guide_id, efficiency_score, info_score, complexity_score, quality_score, overall_score, notes, is_finished) VALUES (36, 38, 25, 5, 5, 5, 5, 5, '', 1);
INSERT INTO code_guides.review (id, reviewer_id, guide_id, efficiency_score, info_score, complexity_score, quality_score, overall_score, notes, is_finished) VALUES (37, 36, 25, 9, 5, 5, 5, 7, '', 1);
INSERT INTO code_guides.review (id, reviewer_id, guide_id, efficiency_score, info_score, complexity_score, quality_score, overall_score, notes, is_finished) VALUES (38, 35, 25, 9, 9, 9, 4, 9, 'Very informative......', 1);
INSERT INTO code_guides.review (id, reviewer_id, guide_id, efficiency_score, info_score, complexity_score, quality_score, overall_score, notes, is_finished) VALUES (39, 35, 26, 5, 5, 5, 5, 5, '', 1);
INSERT INTO code_guides.review (id, reviewer_id, guide_id, efficiency_score, info_score, complexity_score, quality_score, overall_score, notes, is_finished) VALUES (40, 38, 26, 5, 5, 5, 5, 5, '', 1);
INSERT INTO code_guides.review (id, reviewer_id, guide_id, efficiency_score, info_score, complexity_score, quality_score, overall_score, notes, is_finished) VALUES (48, 37, 26, 5, 5, 3, 5, 9, 'ok it works i guess', 1);
INSERT INTO code_guides.review (id, reviewer_id, guide_id, efficiency_score, info_score, complexity_score, quality_score, overall_score, notes, is_finished) VALUES (49, 35, 28, 5, 5, 5, 5, 5, '', 1);
INSERT INTO code_guides.review (id, reviewer_id, guide_id, efficiency_score, info_score, complexity_score, quality_score, overall_score, notes, is_finished) VALUES (50, 36, 28, 5, 5, 5, 5, 5, 'ok', 1);
INSERT INTO code_guides.review (id, reviewer_id, guide_id, efficiency_score, info_score, complexity_score, quality_score, overall_score, notes, is_finished) VALUES (51, 37, 28, 5, 5, 5, 5, 5, '', 1);
INSERT INTO code_guides.review (id, reviewer_id, guide_id, efficiency_score, info_score, complexity_score, quality_score, overall_score, notes, is_finished) VALUES (52, 35, 31, 8, 6, 7, 9, 10, '', 1);
INSERT INTO code_guides.review (id, reviewer_id, guide_id, efficiency_score, info_score, complexity_score, quality_score, overall_score, notes, is_finished) VALUES (53, 36, 31, 7, 7, 7, 7, 7, '', 1);
INSERT INTO code_guides.review (id, reviewer_id, guide_id, efficiency_score, info_score, complexity_score, quality_score, overall_score, notes, is_finished) VALUES (54, 37, 31, 3, 2, 2, 3, 10, '', 1);
INSERT INTO code_guides.review (id, reviewer_id, guide_id, efficiency_score, info_score, complexity_score, quality_score, overall_score, notes, is_finished) VALUES (56, 38, 30, 5, 5, 5, 5, 5, null, 0);
INSERT INTO code_guides.review (id, reviewer_id, guide_id, efficiency_score, info_score, complexity_score, quality_score, overall_score, notes, is_finished) VALUES (57, 35, 30, 5, 5, 5, 5, 5, null, 0);
INSERT INTO code_guides.review (id, reviewer_id, guide_id, efficiency_score, info_score, complexity_score, quality_score, overall_score, notes, is_finished) VALUES (58, 36, 30, 5, 5, 5, 5, 5, null, 0);