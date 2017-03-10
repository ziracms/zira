(function($){
    var base;
    // emoji path
    var emoji_assets_url = 'assets/images/emoji/emoji';
    // known emoji
    var emoji_chars = ['1f4a5','1f554','1f478-1f3fd','1f42d','1f406','26c8','1f3a3','1f530','1f575-1f3ff','1f0cf','1f6b4-1f3fc','1f312','1f384','1f6b5','1f311','2935','1f1ec-1f1f8','1f41e','270d-1f3fd','2b05','1f627','1f1ec-1f1f7','1f339','1f51e','1f687','1f523','1f4d0','1f52e','1f44b-1f3fc','1f557','1f1f0-1f1ed','1f482-1f3fd','1f590','1f6cc','1f31b','1f635','1f385-1f3fc','1f37c','1f3f9','1f46e-1f3ff','1f1f8-1f1fd','1f1e6-1f1fc','1f1f2-1f1f8','2692','1f39f','1f6c2','1f467-1f3ff','1f386','1f6cf','1f4e5','1f30f','1f1e9-1f1ef','1f397','1f3b6','1f1ee-1f1f7','1f471-1f3fd','1f538','1f359','1f4db','1f448-1f3ff','1f45a','1f55e','1f43d','1f376','1f64d-1f3fc','1f1f9-1f1f3','1f532','1f23a','261d-1f3ff','1f6b6-1f3fd','1f36f','1f477-1f3ff','1f1ea-1f1ed','1f491','1f50b','1f39e','1f37b','23ef','1f517','25ab','1f3c4','1f982','1f1f9-1f1f7','1f522','1f4f0','1f64f-1f3fe','2668','1f3d5','1f47d','1f426','1f474-1f3fe','1f6a4','1f351','1f4aa-1f3fb','2197','1f431','1f698','23f3','270a-1f3fe','1f477-1f3fe','1f62c','1f1e7-1f1ef','1f318','1f1f7-1f1ea','1f3cb-1f3fe','1f1e8-1f1f3','1f4c1','1f458','1f457','1f405','1f472-1f3fe','25fd','1f1f8-1f1f1','1f1e6-1f1ff','1f1e7-1f1f1','1f463','1f4d1','1f4f1','1f478-1f3fe','1f1f7-1f1f8','1f462','1f63c','1f51b','1f1ec-1f1eb','1f1f5-1f1f1','274c','1f1e6-1f1f4','1f466-1f3fe','1f1f9-1f1f9','1f385-1f3fd','1f68b','1f437','1f487-1f3fe','1f4c9','1f5fb','1f354','1f5c2','1f1ec-1f1ee','1f4c5','1f440','1f370','1f194','1f626','1f1f1-1f1fe','1f3c9','1f202','2623','1f513','1f413','1f1ec-1f1fc','2797','1f45c','1f45e','1f31f','1f4e9','1f5b2','1f466-1f3fd','1f1ff-1f1f2','1f646','1f500','1f310','1f1f8-1f1e8','1f49f','2620','1f646-1f3ff','1f1ec-1f1f5','1f614','1f4e2','1f400','1f1f9-1f1e6','1f46a','1f31a','2753','2693','1f63b','1f337','270a-1f3fd','1f1f8-1f1f4','1f1f5-1f1f2','1f4c6','1f1e7-1f1ed','1f1f4-1f1f2','1f912','1f42f','1f58a','2652','1f1f9-1f1ed','1f634','1f1fa-1f1e6','1f1ed-1f1f2','1f473-1f3fe','1f4be','1f6a0','1f6f0','1f479','1f6c0-1f3fe','1f33f','1f1e6-1f1fa','1f6a6','1f338','1f1e7-1f1f9','1f48d','1f4aa','26cf','1f1f2-1f1f0','1f606','1f573','1f487-1f3fb','270c-1f3fb','1f632','1f642','1f3b2','1f6a1','1f4cb','1f3db','2721','1f62e','1f1ee-1f1f8','1f30a','261d-1f3fc','1f641','1f6e9','2199','1f446-1f3ff','1f1ec-1f1f6','1f468-1f468-1f466','1f1ea-1f1fa','1f4d7','1f1f2-1f1fa','1f1f8-1f1ec','270b-1f3fb','1f350','1f501','1f44c-1f3fc','1f55f','1f64d-1f3ff','1f465','1f1f8-1f1f7','2b07','1f4b4','1f4d2','1f331','1f445','1f477-1f3fb','1f447-1f3ff','1f1e8-1f1f4','1f198','1f64d','1f1e7-1f1fe','26f0','1f402','1f44d-1f3fd','1f1f2-1f1f9','1f69e','1f1f0-1f1f3','1f31d','1f471','1f191','1f1f3-1f1f1','1f482-1f3fb','1f6b5-1f3ff','1f64f-1f3fb','1f4ba','1f6cd','1f447-1f3fe','261d-1f3fe','1f647-1f3fc','1f596-1f3ff','1f4ca','1f30b','1f45d','1f6a5','1f46e-1f3fe','1f44a-1f3fd','269b','1f493','1f35c','1f4ec','303d','1f382','1f3c2','1f4b8','1f6b4-1f3ff','1f421','1f1e8-1f1f2','1f575-1f3fd','1f60d','1f47c','1f636','1f645-1f3fc','1f50e','23f1','1f6b7','1f481-1f3ff','1f447-1f3fc','1f550','1f1f2-1f1fd','1f4b0','1f47c-1f3fc','1f631','24c2','1f64e-1f3fc','1f469-1f469-1f467','1f326','2196','1f3a5','1f918-1f3ff','1f466-1f3fb','1f450','1f34f','1f38c','1f1f3-1f1eb','2198','270a-1f3fc','1f1f5-1f1f8','1f383','1f1f2-1f1eb','1f1f7-1f1fa','1f483','2b1b','1f4b9','1f643','1f3cb','1f6a3-1f3fe','1f3b3','1f3cb-1f3ff','1f468-1f469-1f467','261d-1f3fd','1f478-1f3fc','1f22f','1f5d1','1f1f0-1f1fc','26d4','1f495','1f477-1f3fc','1f4f5','1f415','0032-20e3','1f579','1f684','1f35d','1f435','1f490','1f1ee-1f1f2','1f1ee-1f1f4','1f44c-1f3fb','1f452','1f535','1f4b3','1f616','1f3fa','1f378','1f62d','1f480','1f1e7-1f1fc','1f5fa','1f334','1f4d5','1f391','1f6a8','270b-1f3fe','1f53d','1f1ec-1f1f9','1f52d','2600','1f6b5-1f3fd','2b55','1f1e7-1f1f4','1f913','1f4cc','1f196','1f439','2611','1f344','1f1e8-1f1ff','1f1fc-1f1eb','2934','1f68d','1f485-1f3fe','1f467-1f3fc','2712','1f436','1f575','1f3e0','1f4ef','1f3bd','1f372','1f1f2-1f1e6','1f473-1f3fc','1f32c','1f32d','1f534','1f52c','1f468-1f468-1f467-1f466','1f3f7','1f602','1f46f','1f4cf','1f356','1f49e','1f481-1f3fb','1f1ff-1f1e6','262a','2328','2194','1f1e6-1f1e8','1f1f5-1f1f3','264a','1f3c1','1f1f2-1f1f5','1f3ae','1f330','1f3a6','1f460','1f469-1f3fe','1f325','1f6e4','1f1e8-1f1f7','1f54a','1f595-1f3fb','1f590-1f3fb','1f443-1f3ff','1f484','1f476','1f58b','1f1ef-1f1ea','2699','1f401','1f475-1f3ff','1f64a','1f5a5','1f483-1f3fd','1f49a','1f301','1f916','1f4f3','1f48c','0039-20e3','26ea','1f443-1f3fc','26e9','1f3cc','1f1e8-1f1e9','1f60c','1f3cf','1f35e','1f69a','1f3ca-1f3fe','1f469-1f3fb','1f1ee-1f1f1','1f1eb-1f1ee','1f35b','1f567','270d-1f3ff','1f4f4','1f442-1f3fb','1f6a3-1f3fb','1f3bc','26f9-1f3ff','1f36c','1f1e8-1f1fb','26fd','2622','1f309','1f1f0-1f1f2','1f6b5-1f3fe','1f414','1f35a','1f6c0-1f3ff','1f361','1f390','1f1f1-1f1f9','1f467-1f3fe','1f482-1f3fc','1f1ec-1f1ec','1f468-1f468-1f467-1f467','1f537','1f1f8-1f1ff','1f44d-1f3fe','1f1e6-1f1ec','1f423','1f486-1f3fe','1f1eb-1f1ef','1f447-1f3fb','1f41a','2728','1f4b5','1f6b0','1f3d6','1f3ec','1f449-1f3fb','1f512','1f6b1','1f19a','1f1f9-1f1fb','1f4ad','1f201','1f448-1f3fe','1f449-1f3fc','1f3e5','1f1f8-1f1e9','1f54d','1f41b','1f64f','1f1e9-1f1ea','1f1f9-1f1f1','25fc','1f595-1f3fe','26c4','1f52a','1f3e9','1f373','1f64d-1f3fe','1f5d2','1f1f0-1f1ea','1f475-1f3fc','1f5ff','1f1e7-1f1e9','2716','1f352','1f1f9-1f1eb','270c-1f3fe','1f1f9-1f1ef','23ec','1f31c','1f553','1f40d','1f42a','1f511','2649','1f424','0038-20e3','262f','1f604','1f304','1f1e7-1f1e7','1f645-1f3fe','2049','1f348','1f307','1f58d','1f46d','270c-1f3ff','2626','1f1e6-1f1f8','1f58c','1f1f1-1f1fb','1f64b-1f3fb','2796','1f31e','1f685','1f6b6-1f3fb','1f3ee','1f4f2','1f51d','1f6b4-1f3fe','1f3ce','1f695','1f1f0-1f1f7','1f302','1f1ea-1f1e6','1f478','1f50d','1f3df','1f605','1f6af','1f1f3-1f1f7','1f3f0','1f37d','1f596-1f3fe','1f1f1-1f1e8','1f3a1','1f6e3','1f4c0','1f4b2','1f1e8-1f1fe','1f306','1f566','1f915','1f44f','1f6b5-1f3fc','1f1e7-1f1f8','1f4e6','1f54c','1f3e7','1f570','26a0','2614','1f303','1f448-1f3fc','1f1e6-1f1f1','1f4d3','1f3af','1f3c3-1f3ff','1f471-1f3fc','1f44a-1f3fc','1f47b','1f300','269c','1f3c3-1f3fe','1f377','1f482-1f3fe','1f1f7-1f1fc','1f3d0','1f3ca-1f3fc','1f233','2650','1f4a0','1f494','264c','1f1f1-1f1f7','1f4a7','1f61b','23ee','1f428','1f64e-1f3fd','1f1e8-1f1ee','1f1f8-1f1fe','270b-1f3ff','1f68c','1f474-1f3ff','1f64e-1f3ff','1f443','21a9','1f1fb-1f1e8','1f17e','1f441-1f5e8','1f4e8','1f496','1f470','1f4eb','1f1f3-1f1ff','1f477-1f3fd','1f34b','1f1f0-1f1ee','1f355','1f429','1f506','261d','1f170','1f4ac','1f6ae','1f404','1f353','26f3','1f3c3-1f3fb','1f1e9-1f1f0','1f552','1f68e','1f644','2638','1f645-1f3fd','1f44b-1f3fe','1f380','1f561','1f43c','1f476-1f3fc','1f62b','1f3f5','1f4ab','1f468-1f3fe','1f549','1f574','1f50a','1f1e8-1f1f1','1f610','270a-1f3fb','1f647','1f39a','25c0','1f44f-1f3fc','1f478-1f3ff','1f469-1f3ff','1f44a-1f3ff','1f1e8-1f1ec','1f17f','1f442-1f3ff','1f1ff-1f1fc','1f44e-1f3fc','1f472','1f39b','1f44d-1f3fb','1f6e5','1f5fd','1f474','1f1f9-1f1e8','1f63e','1f5e1','1f464','1f563','1f443-1f3fd','1f3d2','1f44e-1f3fb','1f64b-1f3fe','1f3fc','1f483-1f3fc','1f54e','1f1ee-1f1e9','1f30d','1f4af','1f1e8-1f1fc','1f1ec-1f1ea','3030','1f1ee-1f1f3','26c5','26f9-1f3fb','1f53a','1f6b5-1f3fb','1f47c-1f3ff','1f477','270c-1f3fc','1f60b','1f1ed-1f1f0','1f4a4','1f683','1f6b3','270d','1f502','1f37a','1f3c0','1f442-1f3fc','1f362','1f450-1f3fb','1f6e0','1f1f3-1f1e6','2666','1f471-1f3fb','23e9','1f3c4-1f3fb','1f3c4-1f3fd','1f469-2764-1f469','1f3e8','1f456','1f1fa-1f1f8','1f3dc','1f385-1f3fe','1f689','1f1f8-1f1ef','1f3ca-1f3ff','1f40e','1f1fe-1f1f9','1f63d','1f647-1f3fe','1f503','1f4d6','1f1f2-1f1ff','1f5fe','1f4bc','1f1e8-1f1f0','1f486-1f3fb','1f1fa-1f1ff','1f1e6-1f1ee','1f499','1f613','1f590-1f3fd','1f1e6-1f1eb','1f1f3-1f1ee','1f981','1f3c4-1f3fc','1f917','1f392','2763','1f688','1f46e-1f3fb','1f1e8-1f1fa','1f3ff','1f531','1f50c','1f1f5-1f1ed','2602','1f64b-1f3ff','1f473-1f3fd','27a1','2694','1f1ec-1f1ed','1f533','1f236','2764','26aa','1f4bf','1f438','1f539','1f1e7-1f1ec','1f443-1f3fb','1f1f5-1f1fe','1f409','270d-1f3fb','26ab','0035-20e3','1f32e','1f336','1f646-1f3fb','1f368','1f48b','1f4e7','1f1ed-1f1fa','1f6a9','1f3c7-1f3fe','1f637','0036-20e3','1f51f','1f3cb-1f3fd','1f6b4-1f3fd','1f483-1f3fe','1f1fe-1f1ea','1f64d-1f3fd','1f4e0','1f447','1f4a8','1f468-1f3ff','1f600','1f476-1f3fd','1f18e','1f3fe','1f433','1f6bf','1f646-1f3fd','270d-1f3fe','1f507','1f1ea-1f1f9','1f3b0','1f1f9-1f1e9','1f4a2','1f41d','1f416','1f647-1f3ff','1f346','1f623','27bf','1f1fb-1f1f3','1f418','1f485-1f3fd','1f42b','1f1f6-1f1e6','1f47c-1f3fe','1f4d4','1f4b1','25b6','1f317','1f1f5-1f1ea','23f9','1f44c-1f3fe','2697','1f1e9-1f1f4','1f1ee-1f1f6','1f3a4','1f33a','1f3fd','1f6c4','1f649','1f44e-1f3fd','1f3aa','1f3c7','1f6b4','1f44a-1f3fb','1f472-1f3fc','1f44e-1f3ff','1f315','1f4f9','1f595-1f3ff','1f63a','1f555','1f6ce','1f595','1f332','1f1fa-1f1fe','1f51c','1f1f8-1f1ea','1f52f','2757','1f363','1f3cd','1f1ec-1f1f3','1f3d9','1f510','1f420','26d1','1f619','1f485-1f3fc','1f199','1f625','1f1f5-1f1f9','25aa','1f6e2','1f33e','0031-20e3','1f469-1f469-1f467-1f467','1f1f1-1f1f8','1f3c3-1f3fc','2651','1f64e-1f3fb','1f620','00ae','1f1f2-1f1f2','1f1f0-1f1f5','1f1f2-1f1f6','1f1fb-1f1e6','1f349','1f389','1f1f9-1f1fc','1f468-1f469-1f467-1f466','1f595-1f3fd','1f468-1f468-1f467','261d-1f3fb','1f4aa-1f3fd','1f4aa-1f3fe','1f448-1f3fb','1f379','1f1f8-1f1f9','1f34c','1f53c','1f250','1f483-1f3fb','1f443-1f3fe','274e','1f69c','002a-20e3','1f469','1f60f','1f340','1f6c0-1f3fb','1f646-1f3fe','1f6d0','1f690','1f1f3-1f1f4','1f43b','1f43e','1f49d','1f3c4-1f3ff','1f1e6-1f1fd','1f1eb-1f1f2','1f1fa-1f1ec','1f611','1f3b5','1f21a','1f469-1f3fd','1f1ec-1f1e6','2696','1f442','1f6a3','1f3eb','1f30e','1f453','1f691','1f1ee-1f1e8','0033-20e3','1f37f','1f32a','1f408','1f371','1f6a3-1f3fc','1f3b8','1f44f-1f3ff','1f466-1f3fc','3299','1f466','1f5fc','231b','1f5a8','23fa','1f3b4','1f3ad','1f633','1f387','1f481-1f3fd','1f1f8-1f1f2','1f1ee-1f1ea','1f33b','1f30c','1f590-1f3fe','26fa','1f3da','1f4aa-1f3fc','1f48f','1f911','1f699','1f44c-1f3ff','270b','26d3','1f385-1f3fb','1f5dc','1f64c-1f3fd','1f1ea-1f1ea','1f3e3','1f481-1f3fc','25fb','1f680','1f467','1f61c','1f3d4','1f529','3297','1f3d8','1f33d','1f45f','1f430','1f645-1f3ff','1f470-1f3fd','1f335','26f9','1f32f','1f38e','1f364','231a','1f1ec-1f1f2','1f64c-1f3fc','1f38b','1f4a6','1f3bb','1f9c0','1f448-1f3fd','1f1fb-1f1ec','1f980','1f630','1f918','1f51a','1f485-1f3ff','1f575-1f3fb','1f694','2709','2660','1f536','1f487-1f3fd','1f314','1f396','1f692','1f46b','1f6a3-1f3ff','1f6c0-1f3fd','1f481','1f319','1f485-1f3fb','1f69b','2754','1f1ef-1f1f4','1f44a-1f3fe','26be','1f1f9-1f1f4','1f1fc-1f1f8','1f590-1f3ff','1f1e7-1f1ee','27b0','1f40c','1f1f2-1f1f1','1f1e7-1f1fb','1f556','23ed','1f910','1f442-1f3fd','1f1f3-1f1fa','1f1eb-1f1f4','1f5e3','1f646-1f3fc','1f6ba','1f44d-1f3fc','1f1f0-1f1fe','1f3de','264e','1f357','1f60a','1f44b','1f1f2-1f1f4','26f1','1f64c','1f5b1','1f3c5','1f44f-1f3fb','1f918-1f3fe','2603','2195','1f44c','1f442-1f3fe','1f558','1f333','1f468-1f469-1f467-1f467','1f6b4-1f3fb','1f3ac','1f3ca-1f3fd','2663','1f6c3','1f696','1f441','1f381','1f64e','1f32b','1f1e6-1f1f7','1f6c0','2747','1f645','1f621','1f1e6-1f1f6','260e','2b50','1f358','1f1ea-1f1f8','1f195','1f1eb-1f1f0','1f48a','270c','1f1f2-1f1ec','1f1f8-1f1f3','1f468-2764-1f468','1f3c4-1f3fe','1f342','1f410','1f235','1f1ec-1f1e7','2648','1f624','1f608','1f53b','1f486-1f3fd','1f918-1f3fc','1f5bc','1f403','1f3f8','1f444','1f1ec-1f1fa','1f5ef','270a','1f365','1f1f2-1f1ed','1f3dd','1f475-1f3fb','1f482','1f64c-1f3ff','1f1f1-1f1fa','1f192','1f3b7','1f1f0-1f1ff','1f471-1f3fe','1f49c','1f918-1f3fd','1f648','1f4da','1f4ae','1f1ee-1f1f9','1f44b-1f3fb','1f3e6','264f','1f489','1f449-1f3fd','1f49b','1f587','1f6bd','1f446-1f3fe','1f35f','1f5c3','270a-1f3ff','1f38f','1f171','1f6ec','1f1e8-1f1e8','1f47f','1f603','1f601','1f1f2-1f1fb','1f3a7','1f1f2-1f1f3','1f327','1f469-1f469-1f467-1f466','1f4ee','26f9-1f3fc','1f472-1f3ff','1f459','1f472-1f3fd','26a1','1f43a','1f595-1f3fc','1f497','26bd','1f1e7-1f1ff','1f61e','1f6b8','1f239','1f629','1f1e8-1f1f5','2734','1f425','1f4df','1f446-1f3fc','1f6c5','1f622','1f3ed','1f1e7-1f1eb','0037-20e3','1f1fb-1f1fa','1f3d3','1f234','26f2','26f7','1f4dd','1f407','1f64e-1f3fe','1f4fc','1f47c-1f3fd','1f52b','1f596-1f3fd','1f628','1f6c1','1f1f3-1f1f5','1f1f5-1f1fc','1f468-1f3fc','2705','1f432','1f449','2639','1f596-1f3fb','1f1ef-1f1f5','1f4a1','1f451','1f486','1f1e9-1f1ff','1f4f8','270b-1f3fc','1f1f5-1f1eb','1f388','1f1e6-1f1f2','1f64f-1f3fc','1f44a','1f3c7-1f3fc','1f446-1f3fb','1f528','1f520','1f43f','1f470-1f3fe','1f68a','1f1e8-1f1e6','1f4c4','1f434','1f450-1f3fe','1f461','1f487','263a','1f638','1f647-1f3fd','1f483-1f3ff','1f385-1f3ff','1f44e','1f525','1f469-1f469-1f466-1f466','1f6ac','1f468-1f3fd','1f399','1f450-1f3fd','1f596','1f419','1f4c2','1f341','1f470-1f3fc','1f6a2','1f61f','1f1ea-1f1f7','1f454','1f1e7-1f1e6','1f446','1f328','1f3a0','2b06','1f6b6-1f3fe','1f447-1f3fd','1f504','1f55c','2795','1f4b6','1f4ce','1f474-1f3fc','1f1e7-1f1ea','1f4d8','1f393','1f6bc','1f422','00a9','1f46c','1f1ed-1f1f3','1f1fd-1f1f0','1f3b9','1f64c-1f3fe','1f473','1f1f0-1f1ec','1f4b7','1f564','1f55b','1f1f8-1f1e7','1f45b','1f6b9','1f639','1f374','1f3ab','1f47a','1f486-1f3fc','1f1fb-1f1ee','1f3d1','2139','1f1e7-1f1f7','1f1ea-1f1ec','1f618','1f482-1f3ff','2604','1f476-1f3ff','1f4a3','1f469-1f469-1f466','270f','1f64f-1f3fd','1f1e8-1f1fd','1f647-1f3fb','1f984','1f1e7-1f1f3','1f1f2-1f1fe','1f46e-1f3fd','1f6b6-1f3ff','1f1f8-1f1fb','1f1eb-1f1f7','1f551','1f1e9-1f1ec','1f3ca','270b-1f3fd','1f3ca-1f3fb','1f1f3-1f1ea','1f44f-1f3fd','21aa','1f324','262e','1f69d','1f36b','1f3a9','1f375','1f40f','1f470-1f3fb','1f3be','1f197','1f63f','1f3a2','1f590-1f3fc','1f697','1f448','1f469-1f3fc','1f3a8','1f3e1','1f575-1f3fc','1f36a','1f471-1f3ff','1f470-1f3ff','1f1f3-1f1ec','1f5f3','1f34d','1f343','1f1f9-1f1f0','1f1e9-1f1f2','1f450-1f3ff','26f5','1f321','2755','1f617','1f467-1f3fb','1f411','1f193','1f313','1f449-1f3fe','1f40a','1f474-1f3fd','1f5de','2665','1f237','1f64f-1f3ff','1f475-1f3fd','1f486-1f3ff','1f1f1-1f1e7','1f1f5-1f1f0','1f367','1f41c','1f4c3','1f524','1f60e','1f4fb','1f4e3','1f481-1f3fe','26f9-1f3fe','23eb','1f3e2','1f609','1f6b2','1f329','1f518','1f565','1f466-1f3ff','1f469-2764-1f48b-1f469','1f64d-1f3fb','267b','1f4fd','1f4e4','1f42e','1f41f','1f521','1f450-1f3fc','1f55d','1f251','264b','2744','1f4c8','1f6cb','1f44b-1f3ff','23ea','1f505','1f50f','1f607','271d','1f4bd','1f1f2-1f1e9','1f1f2-1f1f7','1f44b-1f3fd','1f1e7-1f1f6','1f3ba','1f446-1f3fd','1f345','1f6be','1f5d3','26ce','1f4e1','1f3bf','1f5c4','23f0','1f578','1f360','1f1f9-1f1ff','1f4d9','1f686','1f3c3','1f693','1f476-1f3fb','1f4ed','1f3c6','1f42c','1f468-1f469-1f466-1f466','1f478-1f3fb','1f467-1f3fd','1f1e8-1f1ed','1f6b6','1f1f5-1f1ec','1f33c','1f1ed-1f1f7','1f366','26b0','1f61a','1f918-1f3fb','1f44c-1f3fd','1f577','1f612','1f347','1f1f9-1f1f2','1f6a7','1f455','1f562','1f4a9','1f305','0023-20e3','270d-1f3fc','1f1f2-1f1ea','1f1ef-1f1f2','26f4','1f1f2-1f1fc','26f8','1f1e7-1f1f2','1f1f8-1f1f0','1f308','1f64b','1f475-1f3fe','1f54b','1f3c8','1f44d','2618','1f508','1f1f7-1f1f4','270c-1f3fd','1f412','1f47c-1f3fb','1f3cb-1f3fc','1f6e1','1f468-1f468-1f466-1f466','1f6f3','1f64b-1f3fd','1f6b6-1f3fc','1f3ea','1f69f','1f44f-1f3fe','2702','1f472-1f3fb','1f645-1f3fb','1f4c7','1f55a','267f','1f4f7','1f6ad','1f914','1f640','1f3c7-1f3ff','1f1f2-1f1e8','26b1','1f487-1f3ff','1f473-1f3ff','1f4cd','1f519','1f983','1f473-1f3fb','26f9-1f3fd','1f6a3-1f3fd','1f3c3-1f3fd','1f1f1-1f1e6','1f475','1f509','1f6eb','1f3cb-1f3fb','2708','1f498','1f46e','1f44e-1f3fe','1f1f8-1f1f8','1f487-1f3fc','1f38d','1f004','1f64c-1f3fb','1f37e','1f1fa-1f1f2','1f476-1f3fe','203c','1f1f8-1f1ed','1f492','1f1ec-1f1e9','1f1e6-1f1e9','2733','1f1ec-1f1f1','2b1c','1f3c7-1f3fd','2714','1f4de','1f3c7-1f3fb','1f1f8-1f1e6','1f516','1f3b1','1f6bb','1f56f','1f46e-1f3fc','1f1f8-1f1ee','1f1f9-1f1ec','1f3f4','1f514','1f61d','264d','1f4dc','1f515','1f238','1f38a','1f449-1f3ff','1f64b-1f3fc','1f3fb','1f6aa','1f488','23f2','2653','1f3e4','1f1ed-1f1f9','1f468-1f3fb','1f62f','1f48e','2122','1f4bb','1f232','0030-20e3','1f4ea','1f596-1f3fc','1f47e','1f526','1f1f5-1f1f7','1f36e','1f6c0-1f3fc','1f1ec-1f1fe','1f4fa','1f1e6-1f1ea','1f1e6-1f1f9','1f1fb-1f1ea','0034-20e3','1f320','1f575-1f3fe','1f1f1-1f1ee','1f36d','1f559','1f4f6','1f682','1f4ff','1f44d-1f3ff','1f3d7','1f560','25fe','1f468-2764-1f48b-1f468','1f474-1f3fb','1f527','1f68f','1f6ab','1f34e','1f369','1f576','1f4aa-1f3ff','1f1f5-1f1e6','1f615','1f1f3-1f1e8','1f34a','1f1e8-1f1eb','1f3ef','1f468','1f427','2601','1f316','1f1ea-1f1e8','1f40b','1f385','1f1f1-1f1f0','1f417','1f62a','23f8','2615','1f5dd','1f3f3','1f485','1f681'];

    $(document).ready(function(){
        if (typeof(zira_parse_content)=="undefined") return;

        base = zira_base;
        if (base.substr(-1) == '/') {
            base = base.substr(0, base.length - 1);
        }
        emoji_parse('.parse-content');
        // add emoji parser to zira_parse_content hook
        if (typeof(zira_parse_content.extra)=="undefined") zira_parse_content.extra = [];
        zira_parse_content.extra.push(function(){
            emoji_parse('.parse-content');
        });
        if (supportContentEditable()) {
            emoji_paste.contentEditable = true;
        }
        // init textarea and replace it with contenteditable div if supported
        $('textarea.user-rich-input').each(function(){
            $(this).addClass('emoji-input');
            // adding buttons
            $(this).parent().prepend('<div class="emoji-buttons"></div>');
            $(this).parent().children('.emoji-buttons').append('<a href="javascript:void(0)" title="'+t('Emoji')+'" class="emoji-button emoji-smileys-button"><img src="'+base+'/assets/images/smileys/png/1f642.png" width="24" height="24" /></a>');
            $(this).parent().children('.emoji-buttons').children('.emoji-smileys-button').click(zira_bind(this, function(){
                emoji_open_smileys_wnd.call(this, zira_bind(this, function(emoji){
                    emoji_paste(this, emoji);
                }));
            }));
            $(this).parent().children('.emoji-buttons').append('<a href="javascript:void(0)" title="'+t('Quote')+'" class="emoji-button emoji-quote-button"></a>');
            $(this).parent().children('.emoji-buttons').children('.emoji-quote-button').click(zira_bind(this, emoji_input_quote));
            $(this).parent().children('.emoji-buttons').append('<a href="javascript:void(0)" title="'+t('Image')+'" class="emoji-button emoji-image-button"></a>');
            $(this).parent().children('.emoji-buttons').children('.emoji-image-button').click(zira_bind(this, emoji_input_image));
            $(this).parent().children('.emoji-buttons').append('<a href="javascript:void(0)" title="'+t('Bold')+'" class="emoji-button emoji-bold-button"></a>');
            $(this).parent().children('.emoji-buttons').children('.emoji-bold-button').click(zira_bind(this, emoji_input_bold));
            $(this).parent().children('.emoji-buttons').append('<a href="javascript:void(0)" title="'+t('Code')+'" class="emoji-button emoji-code-button"></a>');
            $(this).parent().children('.emoji-buttons').children('.emoji-code-button').click(zira_bind(this, emoji_input_code));
            // handle form submit
            if ($(this).parents('form').eq(0).hasClass('xhr-form')) {
                $(this).parents('form').eq(0).bind('xhr-submit-start', zira_bind(this, emoji_submit));
                $(this).parents('form').eq(0).bind('xhr-submit-success', zira_bind(this, emoji_submit_success));
                $(this).parents('form').eq(0).bind('xhr-submit-error', zira_bind(this, emoji_submit_error));
            } else {
                $(this).parents('form').eq(0).submit(zira_bind(this, emoji_submit));
            }
            // creating contenteditable
            if (typeof(emoji_paste.contentEditable)!="undefined" && emoji_paste.contentEditable) {
                $(this).after('<div class="emoji-editable contenteditable" id="'+$(this).attr('id')+'-editable" contenteditable="true"></div>');
                $(this).parent().children('.emoji-editable').css({
                    'height': $(this).outerHeight()
                }).keyup(zira_bind(this, function(e){
                    if (e.keyCode==39 || e.keyCode==40) {
                        closeEditableTag.call(this, e.keyCode==40);
                    }
                }));
                //.bind('paste', function(e) {
                //    try {
                //        var clipboardData = e.originalEvent.clipboardData || window.clipboardData;
                //        var pastedData = clipboardData.getData('text/html');
                //        if (pastedData.indexOf('<')>=0) {
                //            e.stopPropagation();
                //            e.preventDefault();
                //        }
                //    } catch(err) {}
                //});
                $(this).hide();
            }
            if (navigator.userAgent.indexOf('MSIE')<0) {
                $(this).parent().children('.emoji-buttons').find('.emoji-button').tooltip();
            }
        });
        
        zira_modal_create('emoji-modal-dialog', '', '', '', zira_modal_close_btn());
    });

    /**
     * Parse text and replace emoji with images
     */
    function emoji_parse(selector) {
        $(selector).each(function(){
            //if ($(this).hasClass('article')) return true;
            if ($(this).hasClass('emoji-parsed-content')) return true;
            var fs;
            var p1 = $(this).css('line-height');
            var p2 = $(this).css('font-size');
            if (p1) p1 = parseInt(p1);
            if (p2) p2 = parseInt(p2);
            if (p1 && p2) fs = Math.max(p1,p2);
            else fs = 20;
            if (fs<20) fs = 20;
            else if (fs>64) fs = 64;
            var content = $(this).html();
            content = emoji_parse_prepare(content);
            content = emoji_parse_replace(content, fs);
            $(this).html(content);
            $(this).addClass('emoji-parsed-content');
        });
    }

    /**
     * Find emoji in text
     */
    function emoji_parse_prepare(content) {
        var p = new RegExp('([\\uD800-\\uDBFF])([\\uDC00-\\uDFFF])', 'g');
        var m, c;
        var i = 0;
        while(m=p.exec(content)) {
            if (typeof(m)=="undefined" || typeof(m[0])=="undefined" || typeof(m[1])=="undefined" || typeof(m[2])=="undefined") continue;
            if (i>999) break;
            c = getEmojiCode(m[1], m[2]);
            content = content.replace(m[0], '[#x'+c+']');
            i++;
        }
        p = new RegExp('([\\u0030-\\u0039\\u0023\\u002a])([\u200B])([\\u20e3])', 'g');
        i = 0;
        while(m=p.exec(content)) {
            if (typeof(m)=="undefined" || typeof(m[0])=="undefined" || typeof(m[1])=="undefined" || typeof(m[2])=="undefined" || typeof(m[3])=="undefined") continue;
            if (i>999) break;
            content = content.replace(m[0], '[#x'+('000'+m[1].charCodeAt(0).toString(16)).slice(-4)+']'+m[2]+'[#x'+('000'+m[3].charCodeAt(0).toString(16)).slice(-4)+']');
            i++;
        }
        p = new RegExp('([\\u20a0-\\u32ff\\u2049\\u203c\\u00ae\\u00a9])', 'g');
        i = 0;
        while(m=p.exec(content)) {
            if (typeof(m)=="undefined" || typeof(m[0])=="undefined" || typeof(m[1])=="undefined") continue;
            if (i>999) break;
            content = content.replace(m[0], '[#x'+('000'+m[1].charCodeAt(0).toString(16)).slice(-4)+']');
            i++;
        }
        return content;
    }

    /**
     * Replace found emoji with image
     */
    function emoji_parse_replace(content, size) {
        var ext = supportsSVG() ? 'svg' : 'png';
        content = content.replace(/\][\u200B]\[#x/g,'-');
        var p = new RegExp('\\[#x([^\\]]+)\\]', 'g');
        var m, u;
        var i = 0;
        while(m=p.exec(content)) {
            if (typeof(m) == "undefined" || typeof(m[0]) == "undefined" || typeof(m[1]) == "undefined") continue;
            if (i>999) break;
            if ($.inArray(m[1], emoji_chars)>=0) {
                u = base + '/' + emoji_assets_url + '/' + ext + '/' + m[1] + '.' + ext;
                content = content.replace(m[0], '<img src="'+u+'" width="'+size+'" onerror="this.src=\''+base+'/assets/images/blank.png\';" height="'+size+'" alt="emoji-'+m[1]+'" class="emoji" />');
            } else {
                u = m[0].replace(/[-]/g,']&#x200B;[#x').replace(/\[#x([^\]]+)\]/g, '&#x$1;');
                content = content.replace(m[0], u);
            }
            i++;
        }
        return content;
    }

    /**
     * Get emoji hex code
     */
    function getEmojiCode(lead, trail){
        return surrogatePairToCodepoint(lead.charCodeAt(0), trail.charCodeAt(0)).toString(16);
    }

    /**
     * Taken from emoji_image_replace.js
     * https://gist.github.com/mwunsch/4710561
     */
    function surrogatePairToCodepoint(lead, trail) {
        return (lead - 0xD800) * 0x400 + (trail - 0xDC00) + 0x10000;
    }

    /**
     * Open emoji dialog
     */
    function emoji_open_smileys_wnd(callback) {
        if (typeof(emoji_paste.contentEditable)!="undefined" && emoji_paste.contentEditable) {
            $(this).parent().children('.emoji-editable').get(0).focus();
            emoji_open_smileys_wnd.selection_range = saveEditableSelection();
        }
        $('#emoji-modal-dialog').bind('shown.bs.modal', function(e) {
            // keep focus
            e.stopImmediatePropagation();
            $('#emoji-modal-dialog').unbind('shown.bs.modal');
            emoji_load('smileys');
        });
        zira_modal(t('Select emoji'), '<div class="emoji-loader-wrapper"><span class="zira-loader"></span></div>', function(){
            if (typeof(callback)!="undefined" && typeof(emoji_load.smile)!="undefined" && emoji_load.smile) {
                callback.call(null, emoji_load.smile);
            }
        }, true, 'emoji-modal-dialog');
    }

    /**
     * Load emoji by type
     */
    function emoji_load(typo) {
        emoji_load.smile = null;
        $.post(emoji_url,{
                'typo': typo
            }, function(response) {
                $('#emoji-modal-dialog').find('.emoji-loader-wrapper').replaceWith(response);
                $('#emoji-modal-dialog').find('.emoji-type-link').click(function(){
                    $('#emoji-modal-dialog').find('.emoji-modal-wrapper').replaceWith('<div class="emoji-loader-wrapper"><span class="zira-loader"></span></div>');
                    emoji_load($(this).attr('rel'));
                });
                $('#emoji-modal-dialog').find('.emoji-image-link').click(function(){
                    emoji_load.smile = {'char': emoji_char($(this).attr('rel')), 'image': $(this).html()};
                    $('#emoji-modal-dialog').modal('hide');
                });
            },
            'html'
        );
    }

    /**
     * Emoji entity
     */
    function emoji_char(code) {
        return '&#x'+code.split('-').join(';&#x200b;&#x')+';';
    }

    /**
     * Insert emoji
     */
    function emoji_paste(input, emoji) {
        var sel, val;
        if (typeof(emoji_paste.contentEditable)=="undefined" || !emoji_paste.contentEditable) {
            $(input).get(0).focus();
            sel = getSelection($(input).get(0));
            if (sel!==false) {
                val = $(input).val();
                $(input).val(val.substr(0, sel.start) + emoji.char + val.substr(sel.start));
            } else {
                $(input).val($(input).val() + emoji.char);
            }
        } else {
            $(input).parent().children('.emoji-editable').get(0).focus();
            restoreEditableSelection(emoji_open_smileys_wnd.selection_range);
            if (!pasteAtEditableSelection(emoji.image+'<span>&#x200c;</span>')) {
                $(input).parent().children('.emoji-editable').append(emoji.image);
            }
        }
    }

    /**
     * Form submit event handler
     */
    function emoji_submit() {
        if (typeof(emoji_paste.contentEditable)!="undefined" && emoji_paste.contentEditable) {
            var val = $(this).parent().children('.emoji-editable').html();
            val = val.replace(/[\r\n]/g,'');
            var p = new RegExp('<img [^>]*alt=(?:["\'])?([^"\'\x20]+)(?:["\'])?[^>]*>','i');
            var m;
            while (m=p.exec(val)) {
                if (typeof(m[0])=="undefined" || typeof(m[1])=="undefined") continue;
                if (m[0].indexOf(' class="emoji-image"')<0 && m[0].indexOf(' class=emoji-image')<0) {
                    var _p = new RegExp('[\\x20]class=(?:["])?(emoji[\\x20]*)(?:["])?');
                    var _r;
                    if (m[0].match(_p)) {
                        if (m[1].indexOf('emoji-')==0) m[1] = emoji_char(m[1].substr(6));
                        _r = m[1];
                    } else {
                        _r = m[0].replace(/alt=(?:["\'])?([^"\'\x20]+)(?:["\'])/,'');
                    }
                    val = val.replace(m[0], _r);
                    continue;
                }
                val = val.replace(m[0], emoji_char(m[1]));
            }
            p = new RegExp('<b(?:[\x20][^>]+)?>(.+?)</b>','i');
            while (m=p.exec(val)) {
                if (typeof(m[0])=="undefined" || typeof(m[1])=="undefined") continue;
                val = val.replace(m[0], emoji_bold(m[1]));
            }
            p = new RegExp('<q(?:[\x20][^>]+)?>(.+?)</q>','i');
            while (m=p.exec(val)) {
                if (typeof(m[0])=="undefined" || typeof(m[1])=="undefined") continue;
                val = val.replace(m[0], emoji_quote(m[1]));
            }
            p = new RegExp('<code(?:[\x20][^>]+)?>(.+?)</code>','i');
            while (m=p.exec(val)) {
                if (typeof(m[0])=="undefined" || typeof(m[1])=="undefined") continue;
                val = val.replace(m[0], emoji_code(m[1]));
            }
            var p = new RegExp('<img [^>]*src=(?:["\'])?([^"\'>]+)(?:["\'])?(?:[\x20][^>]*)?>','i');
            while (m=p.exec(val)) {
                if (typeof(m[0])=="undefined" || typeof(m[1])=="undefined") continue;
                if (m[1].indexOf('data:')==0) {
                    var title = '';
                    if (m[0].indexOf(' title=')>0) {
                        title = m[0].replace(/^<img [^>]*title=(?:["\'])?([^"\'>]+)(?:["\'])?(?:[\x20][^>]*)?>$/gi, '$1');
                    }
                    val = val.replace(m[0], ' ['+title+'] ');
                } else {
                    val = val.replace(m[0], emoji_image(m[1]));
                }
            }
            val = val.replace(/[\u200c]/g,'');
            val = val.replace(/&nbsp;/gi,' ');
            val = val.replace(/<br[\x20\/]*?>/gi, "\r\n");
            val = val.replace(/<div(?:[\x20][^>]+)?>(.*?)<\/div>/gi, "\r\n$1");
            val = val.replace(/<p(?:[\x20][^>]+)?>(.*?)<\/p>/gi, "\r\n$1");
            val = val.replace(/([\r][\n]){3,}/g,"\r\n\r\n");
            val = val.replace(/^[\s]+/,'').replace(/[\s]+$/,'');
            val = val.replace(/<[a-z\/].*?>/gi, "");
            $(this).val(val);
            $(this).parent().children('.emoji-editable').hide();
            $(this).show();
        }
    }

    /**
     * Form submit success event handler
     */
    function emoji_submit_success() {
        emoji_submit_end.call(this);
        if (typeof(emoji_paste.contentEditable)!="undefined" && emoji_paste.contentEditable) {
            $(this).parent().children('.emoji-editable').html('');
        }
    }

    /**
     * Form submit error event handler
     */
    function emoji_submit_error() {
        emoji_submit_end.call(this);
    }

    /**
     * Form submit end event handler
     */
    function emoji_submit_end() {
        if (typeof(emoji_paste.contentEditable)!="undefined" && emoji_paste.contentEditable) {
            $(this).parent().children('.emoji-editable').show();
            $(this).hide();
        }
    }

    /**
     * Insert bold text
     */
    function emoji_input_bold() {
        var sel, val;
        if (typeof(emoji_paste.contentEditable)=="undefined" || !emoji_paste.contentEditable) {
            $(this).get(0).focus();
            sel = getSelection($(this).get(0));
            if (sel!==false) {
                val = $(this).val();
                $(this).val(val.substr(0, sel.start) + emoji_bold(val.substr(sel.start, sel.end-sel.start)) + val.substr(sel.end));
                if (sel.start == sel.end) {
                    setSelectionRange($(this).get(0), sel.start+3, sel.start+3);
                }
            } else {
                $(this).val($(this).val() + emoji_bold(''));
                var p = $(this).val().length - 4;
                setSelectionRange($(this).get(0), p, p);
            }
        } else {
            $(this).parent().children('.emoji-editable').get(0).focus();
            var txt = getEditableSelectionHtml();
            if (txt.length==0) txt = '<b>'+'&#x200c;'+'</b>';
            else txt = '<b>'+txt+'</b>'+'<span>&#x200c;</span>';
            pasteAtEditableSelection(txt);
        }
    }

    /**
     * Bold text BB-code
     */
    function emoji_bold(text) {
        return '[b]'+text+'[/b]';
    }

    /**
     * Insert quote
     */
    function emoji_input_quote() {
        var sel, val;
        if (typeof(emoji_paste.contentEditable)=="undefined" || !emoji_paste.contentEditable) {
            $(this).get(0).focus();
            sel = getSelection($(this).get(0));
            if (sel!==false) {
                val = $(this).val();
                $(this).val(val.substr(0, sel.start) + emoji_quote(val.substr(sel.start, sel.end-sel.start)) + val.substr(sel.end));
                if (sel.start == sel.end) {
                    setSelectionRange($(this).get(0), sel.start+7, sel.start+7);
                }
            } else {
                $(this).val($(this).val() + emoji_quote(''));
                var p = $(this).val().length - 8;
                setSelectionRange($(this).get(0), p, p);
            }
        } else {
            $(this).parent().children('.emoji-editable').get(0).focus();
            var txt = getEditableSelectionHtml();
            if (txt.length==0) txt = '<q>'+'&#x200c;'+'</q>';
            else txt = '<q>'+txt+'</q>'+'<span>&#x200c;</span>';
            pasteAtEditableSelection(txt);
        }
    }

    /**
     * Quote BB-code
     */
    function emoji_quote(text) {
        return '[quote]'+text+'[/quote]';
    }

    /**
     * Insert code
     */
    function emoji_input_code() {
        var sel, val;
        if (typeof(emoji_paste.contentEditable)=="undefined" || !emoji_paste.contentEditable) {
            $(this).get(0).focus();
            sel = getSelection($(this).get(0));
            if (sel!==false) {
                val = $(this).val();
                $(this).val(val.substr(0, sel.start) + emoji_code(val.substr(sel.start, sel.end-sel.start)) + val.substr(sel.end));
                if (sel.start == sel.end) {
                    setSelectionRange($(this).get(0), sel.start+6, sel.start+6);
                }
            } else {
                $(this).val($(this).val() + emoji_code(''));
                var p = $(this).val().length - 7;
                setSelectionRange($(this).get(0), p, p);
            }
        } else {
            $(this).parent().children('.emoji-editable').get(0).focus();
            var txt = getEditableSelectionHtml();
            if (txt.length==0) txt = '<code>'+'&#x200c;'+'</code>';
            else txt = '<code>'+txt+'</code>'+'<span>&#x200c;</span>';
            pasteAtEditableSelection(txt);
        }
    }

    /**
     * Code BB-code
     */
    function emoji_code(text) {
        return '[code]'+text+'[/code]';
    }

    /**
     * Insert image
     */
    function emoji_input_image() {
        if (typeof(emoji_paste.contentEditable)!="undefined" && emoji_paste.contentEditable) {
            $(this).parent().children('.emoji-editable').get(0).focus();
            emoji_input_image.selection_range = saveEditableSelection();
        }
        zira_prompt(t('Enter image URL address'), zira_bind(this, function(url){
            url = url.replace(/<.*?>/g,'').replace(/[<>'"]/g,'');
            var sel, val;
            if (typeof(emoji_paste.contentEditable)=="undefined" || !emoji_paste.contentEditable) {
                $(this).get(0).focus();
                sel = getSelection($(this).get(0));
                if (sel!==false) {
                    val = $(this).val();
                    $(this).val(val.substr(0, sel.start) + emoji_image(url) + val.substr(sel.start));
                } else {
                    $(this).val($(this).val() + emoji_image(url));
                }
            } else {
                $(this).parent().children('.emoji-editable').get(0).focus();
                restoreEditableSelection(emoji_input_image.selection_range);
                var img = '<img src="'+url+'" />';
                if (!pasteAtEditableSelection(img+'<span>&#x200c;</span>')) {
                    $(this).parent().children('.emoji-editable').append(img);
                }
            }
        }));
    }

    /**
     * Image BB-code
     */
    function emoji_image(url) {
        return '[img]'+url+'[/img]';
    }

    /**
     * Move editable caret outside current tag
     */
    function closeEditableTag(block) {
        $(this).parent().children('.emoji-editable').get(0).focus();
        var info = getEditableSelectionInfo($(this).parent().children('.emoji-editable').get(0));
        if (info.end) {
            var b = typeof(block)!="undefined" && block ? '<div>&nbsp;</div><span>&nbsp;</span>' : '<span>&nbsp;</span>';
            if (typeof window.getSelection != "undefined" && typeof document.createRange != "undefined") {
                placeEditableCaretAtEnd($(this).parent().children('.emoji-editable').get(0));
                pasteAtEditableSelection(b);
            } else { // IE
                $(this).parent().children('.emoji-editable').append(b);
                placeEditableCaretAtEnd($(this).parent().children('.emoji-editable').get(0));
            }
        }
    }

    /**
     * Check if svg images are supported
     */
    function supportsSVG () {
        return !!document.createElementNS &&
                !!document.createElementNS('http://www.w3.org/2000/svg', "svg").createSVGRect &&
                !navigator.userAgent.match(/(android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini|mobi|palm)/i);
    }

    /**
     * Check if contenteditable attribute is supported
     */
    function supportContentEditable() {
        var div = document.createElement('div');
        return (typeof(div.contentEditable) != 'undefined' && !/(iPhone|iPod|iPad|Android|Mobi)/i.test(navigator.userAgent));
    }

    /**
     * Get textarea selection range
     */
    function getSelection(input) {
        if ("selectionStart" in input && document.activeElement == input) {
            return {
                start: input.selectionStart,
                end: input.selectionEnd
            };
        } else if (input.createTextRange) {
            var sel = document.selection.createRange();
            if (sel.parentElement() === input) {
                var rng = input.createTextRange();
                rng.moveToBookmark(sel.getBookmark());
                for (var len = 0;
                         rng.compareEndPoints("EndToStart", rng) > 0;
                         rng.moveEnd("character", -1)) {
                    len++;
                }
                rng.setEndPoint("StartToStart", input.createTextRange());
                for (var pos = { start: 0, end: len };
                         rng.compareEndPoints("EndToStart", rng) > 0;
                         rng.moveEnd("character", -1)) {
                    pos.start++;
                    pos.end++;
                }
                return pos;
            }
        }
        return false;
    }

    /**
     * Set textarea selection range
     */
    function setSelectionRange(input, selectionStart, selectionEnd) {
        if (input.setSelectionRange) {
            input.focus();
            input.setSelectionRange(selectionStart, selectionEnd);
        } else if (input.createTextRange) {
            var range = input.createTextRange();
            range.collapse(true);
            range.moveEnd('character', selectionEnd);
            range.moveStart('character', selectionStart);
            range.select();
        }
    }

    /**
     * Return focused editable selected range
     */
    function saveEditableSelection() {
        if (window.getSelection) {
            sel = window.getSelection();
            if (sel.getRangeAt && sel.rangeCount) {
                return sel.getRangeAt(0);
            }
        } else if (document.selection && document.selection.createRange) {
            return document.selection.createRange();
        }
        return null;
    }

    /**
     * Restore selected range of focused editable
     */
    function restoreEditableSelection(range) {
        if (range) {
            if (window.getSelection) {
                sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
            } else if (document.selection && range.select) {
                range.select();
            }
        }
    }

    /**
     * Get selected html of focused editable
     */
    function getEditableSelectionHtml() {
        var html = "";
        if (typeof window.getSelection != "undefined") {
            var sel = window.getSelection();
            if (sel.rangeCount) {
                var container = document.createElement("div");
                for (var i = 0, len = sel.rangeCount; i < len; ++i) {
                    container.appendChild(sel.getRangeAt(i).cloneContents());
                }
                html = container.innerHTML;
            }
        } else if (typeof document.selection != "undefined") {
            if (document.selection.type == "Text") {
                html = document.selection.createRange().htmlText;
            }
        }
        return html;
    }

    /**
     * Paste html to focused editable at selected range
     */
    function pasteAtEditableSelection(html) {
        var sel, range;
        if (window.getSelection) {
            // IE9 and non-IE
            sel = window.getSelection();
            if (sel.getRangeAt && sel.rangeCount) {
                range = sel.getRangeAt(0);
                range.deleteContents();
                var el = document.createElement("div");
                el.innerHTML = html;
                var frag = document.createDocumentFragment(), node, lastNode;
                while ( (node = el.firstChild) ) {
                    lastNode = frag.appendChild(node);
                }
                range.insertNode(frag);
                // setting carret
                if (lastNode) {
                    range = range.cloneRange();
                    range.selectNodeContents(lastNode);
                    range.collapse(false);
                    sel.removeAllRanges();
                    sel.addRange(range);
                }
            }
            return true;
        } else if (document.selection && document.selection.type != "Control") {
            // IE < 9
            //document.selection.createRange().pasteHTML(html);

            // paste and set caret
            var id = "marker_" + ("" + Math.random()).slice(2);
            html += '<span id="' + id + '"></span>';
            var textRange = document.selection.createRange();
            textRange.pasteHTML(html);
            var markerSpan = document.getElementById(id);
            textRange.moveToElementText(markerSpan);
            textRange.select();
            markerSpan.parentNode.removeChild(markerSpan);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Editable caret at start or at end ?
     */
    function getEditableSelectionInfo(el) {
        var atStart = false, atEnd = false;
        var selRange, testRange;
        if (window.getSelection) {
            var sel = window.getSelection();
            if (sel.rangeCount) {
                selRange = sel.getRangeAt(0);
                testRange = selRange.cloneRange();

                testRange.selectNodeContents(el);
                testRange.setEnd(selRange.startContainer, selRange.startOffset);
                atStart = (testRange.toString() == "");

                testRange.selectNodeContents(el);
                testRange.setStart(selRange.endContainer, selRange.endOffset);
                atEnd = (testRange.toString() == "");
            }
        } else if (document.selection && document.selection.type != "Control") {
            selRange = document.selection.createRange();
            testRange = selRange.duplicate();

            testRange.moveToElementText(el);
            testRange.setEndPoint("EndToStart", selRange);
            atStart = (testRange.text == "");

            testRange.moveToElementText(el);
            testRange.setEndPoint("StartToEnd", selRange);
            atEnd = (testRange.text == "");
        }

        return { start: atStart, end: atEnd };
    }

    /**
     * Place editable caret at end
     */
    function placeEditableCaretAtEnd(el) {
        el.focus();
        if (typeof window.getSelection != "undefined" && typeof document.createRange != "undefined") {
            var range = document.createRange();
            range.selectNodeContents(el);
            range.collapse(false);
            var sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        } else if (typeof document.body.createTextRange != "undefined") {
            var textRange = document.body.createTextRange();
            textRange.moveToElementText(el);
            textRange.collapse(false);
            textRange.select();
        }
    }
})(jQuery);