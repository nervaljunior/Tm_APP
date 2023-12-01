SELECT setval('produtos_dart_id_seq', coalesce(max(id),0) + 1, false) FROM produtos_dart;
SELECT setval('projeto_dart_id_seq', coalesce(max(id),0) + 1, false) FROM projeto_dart;
SELECT setval('status_tarefa_id_seq', coalesce(max(id),0) + 1, false) FROM status_tarefa;
SELECT setval('tarefas_dart_id_seq', coalesce(max(id),0) + 1, false) FROM tarefas_dart;
SELECT setval('user_permission_projeto_id_seq', coalesce(max(id),0) + 1, false) FROM user_permission_projeto;