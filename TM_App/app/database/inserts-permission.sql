INSERT INTO system_group (id, name, uuid) VALUES( (SELECT max(g.id) + 1 FROM system_group g) , 'Gestão', '03814c9b-e536-454b-995d-a6794c90957b');
INSERT INTO system_user_group (id, system_group_id, system_user_id) VALUES((SELECT max(ug.id) + 1 FROM system_user_group ug), (SELECT max(g.id) FROM system_group g), 1);
INSERT INTO system_program (id,name,controller) VALUES( (SELECT max(p.id) + 1 FROM system_program p) , 'Kanban', 'TarefasDartKanbanView');
INSERT INTO system_group_program (id, system_group_id, system_program_id) VALUES( (SELECT max(gp.id) + 1 FROM system_group_program gp), (SELECT max(g.id) FROM system_group g) , (SELECT max(p.id) FROM system_program p where p.controller = 'TarefasDartKanbanView'));
INSERT INTO system_program (id,name,controller) VALUES( (SELECT max(p.id) + 1 FROM system_program p) , 'Produtos', 'ProdutosDartList');
INSERT INTO system_group_program (id, system_group_id, system_program_id) VALUES( (SELECT max(gp.id) + 1 FROM system_group_program gp), (SELECT max(g.id) FROM system_group g) , (SELECT max(p.id) FROM system_program p where p.controller = 'ProdutosDartList'));
INSERT INTO system_program (id,name,controller) VALUES( (SELECT max(p.id) + 1 FROM system_program p) , 'Tarefas(Form)', 'TarefasDartGanttForm');
INSERT INTO system_group_program (id, system_group_id, system_program_id) VALUES( (SELECT max(gp.id) + 1 FROM system_group_program gp), (SELECT max(g.id) FROM system_group g) , (SELECT max(p.id) FROM system_program p where p.controller = 'TarefasDartGanttForm'));
INSERT INTO system_program (id,name,controller) VALUES( (SELECT max(p.id) + 1 FROM system_program p) , 'Tarefas(View)', 'TarefasDartGanttFormView');
INSERT INTO system_group_program (id, system_group_id, system_program_id) VALUES( (SELECT max(gp.id) + 1 FROM system_group_program gp), (SELECT max(g.id) FROM system_group g) , (SELECT max(p.id) FROM system_program p where p.controller = 'TarefasDartGanttFormView'));
INSERT INTO system_program (id,name,controller) VALUES( (SELECT max(p.id) + 1 FROM system_program p) , 'Grafico', 'ProdutoTarefasDartForm');
INSERT INTO system_group_program (id, system_group_id, system_program_id) VALUES( (SELECT max(gp.id) + 1 FROM system_group_program gp), (SELECT max(g.id) FROM system_group g) , (SELECT max(p.id) FROM system_program p where p.controller = 'ProdutoTarefasDartForm'));
INSERT INTO system_program (id,name,controller) VALUES( (SELECT max(p.id) + 1 FROM system_program p) , 'Cadastro de produto', 'ProdutosDartForm');
INSERT INTO system_group_program (id, system_group_id, system_program_id) VALUES( (SELECT max(gp.id) + 1 FROM system_group_program gp), (SELECT max(g.id) FROM system_group g) , (SELECT max(p.id) FROM system_program p where p.controller = 'ProdutosDartForm'));
INSERT INTO system_program (id,name,controller) VALUES( (SELECT max(p.id) + 1 FROM system_program p) , 'Cadastro de projeto', 'ProjetoDartForm');
INSERT INTO system_group_program (id, system_group_id, system_program_id) VALUES( (SELECT max(gp.id) + 1 FROM system_group_program gp), (SELECT max(g.id) FROM system_group g) , (SELECT max(p.id) FROM system_program p where p.controller = 'ProjetoDartForm'));
INSERT INTO system_program (id,name,controller) VALUES( (SELECT max(p.id) + 1 FROM system_program p) , 'Projetos', 'ProjetoDartList');
INSERT INTO system_group_program (id, system_group_id, system_program_id) VALUES( (SELECT max(gp.id) + 1 FROM system_group_program gp), (SELECT max(g.id) FROM system_group g) , (SELECT max(p.id) FROM system_program p where p.controller = 'ProjetoDartList'));
INSERT INTO system_group (id, name, uuid) VALUES( (SELECT max(g.id) + 1 FROM system_group g) , 'Home', '3aae5546-8b5c-416f-a8f5-75c7774baa76');
INSERT INTO system_user_group (id, system_group_id, system_user_id) VALUES((SELECT max(ug.id) + 1 FROM system_user_group ug), (SELECT max(g.id) FROM system_group g), 1);
INSERT INTO system_program (id,name,controller) VALUES( (SELECT max(p.id) + 1 FROM system_program p) , 'Home', 'ProjetoDartCardList');
INSERT INTO system_group_program (id, system_group_id, system_program_id) VALUES( (SELECT max(gp.id) + 1 FROM system_group_program gp), (SELECT max(g.id) FROM system_group g) , (SELECT max(p.id) FROM system_program p where p.controller = 'ProjetoDartCardList'));