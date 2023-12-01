INSERT INTO produtos_dart (id,projeto_id,nome_do_produto,system_users_id,descricao) VALUES (1,1,'Produto 1',1,null); 

INSERT INTO projeto_dart (id,nome_projeto,cor,descricao) VALUES (1,'Cientistas de Alcântara ','#00d084','O SoftExpert Projeto é um software 100% web que permite gerenciar facilmente projetos, produtos, pessoas, serviços e finanças através de um conjunto completo de funcionalidades que simplificam as melhores práticas do gerenciamento de projeto com um baixo custo total de propriedade.'); 

INSERT INTO projeto_dart (id,nome_projeto,cor,descricao) VALUES (2,'Projeto 2','#00d001','TESTE'); 

INSERT INTO status_tarefa (id,descricao,color) VALUES (1,'PENDENTE','#ffc107'); 

INSERT INTO status_tarefa (id,descricao,color) VALUES (2,'EM ANDAMENTO','#17a2b8'); 

INSERT INTO status_tarefa (id,descricao,color) VALUES (3,'CONCLUIDO','#28a745'); 

INSERT INTO status_tarefa (id,descricao,color) VALUES (4,'ATRASADA','#dc3545'); 

INSERT INTO tarefas_dart (id,produtos_dart_id,status_tarefa_id,nome_da_tarefa,data_de_inicio,data_prevista,data_de_termino,system_users_id,descricao) VALUES (1,1,1,'Tarefa 1','2023-09-12','2023-09-16',null,1,null); 

INSERT INTO tarefas_dart (id,produtos_dart_id,status_tarefa_id,nome_da_tarefa,data_de_inicio,data_prevista,data_de_termino,system_users_id,descricao) VALUES (2,1,3,'Tarefa 2','2023-09-12','2023-09-13','2023-09-13',1,null); 

INSERT INTO tarefas_dart (id,produtos_dart_id,status_tarefa_id,nome_da_tarefa,data_de_inicio,data_prevista,data_de_termino,system_users_id,descricao) VALUES (3,1,2,'Tarefa 3','2023-09-12','2023-09-19',null,1,null); 

INSERT INTO tarefas_dart (id,produtos_dart_id,status_tarefa_id,nome_da_tarefa,data_de_inicio,data_prevista,data_de_termino,system_users_id,descricao) VALUES (4,1,2,'Tarefa 4','2023-09-12','2023-09-19',null,1,null); 

INSERT INTO user_permission_projeto (id,projeto_dart_id,user) VALUES (1,1,1); 

INSERT INTO user_permission_projeto (id,projeto_dart_id,user) VALUES (2,2,1); 

INSERT INTO user_permission_projeto (id,projeto_dart_id,user) VALUES (3,1,2); 
