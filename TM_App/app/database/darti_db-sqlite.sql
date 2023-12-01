PRAGMA foreign_keys=OFF; 

CREATE TABLE produtos_dart( 
      id  INTEGER    NOT NULL  , 
      projeto_id int   NOT NULL  , 
      nome_do_produto varchar  (255)   NOT NULL  , 
      system_users_id int   NOT NULL  , 
      descricao text   , 
 PRIMARY KEY (id),
FOREIGN KEY(projeto_id) REFERENCES projeto_dart(id)) ; 

CREATE TABLE projeto_dart( 
      id  INTEGER    NOT NULL  , 
      nome_projeto varchar  (255)   NOT NULL  , 
      cor varchar  (55)   , 
      descricao text   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE status_tarefa( 
      id  INTEGER    NOT NULL  , 
      descricao varchar  (255)   NOT NULL  , 
      color varchar  (255)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE tarefas_dart( 
      id  INTEGER    NOT NULL  , 
      produtos_dart_id int   NOT NULL  , 
      status_tarefa_id int   NOT NULL  , 
      nome_da_tarefa varchar  (255)   NOT NULL  , 
      data_de_inicio datetime   , 
      data_prevista datetime   , 
      data_de_termino datetime   , 
      system_users_id int   NOT NULL  , 
      descricao text   , 
 PRIMARY KEY (id),
FOREIGN KEY(status_tarefa_id) REFERENCES status_tarefa(id),
FOREIGN KEY(produtos_dart_id) REFERENCES produtos_dart(id)) ; 

CREATE TABLE user_permission_projeto( 
      id  INTEGER    NOT NULL  , 
      projeto_dart_id int   NOT NULL  , 
      user int   NOT NULL  , 
 PRIMARY KEY (id),
FOREIGN KEY(projeto_dart_id) REFERENCES projeto_dart(id)) ; 

 
 
 CREATE VIEW produto_tarefas_dart AS SELECT 
    produtos_dart.id as "id",
    produtos_dart.nome_do_produto as "nome_do_produto",
    status_tarefa.id as "id_status_tarefa",
    status_tarefa.descricao as "descricao",
    status_tarefa.color as "color",
    tarefas_dart.id as "id_tarefas_dart",
    tarefas_dart.status_tarefa_id as "status_tarefa_id",
    tarefas_dart.nome_da_tarefa as "nome_da_tarefa",
    tarefas_dart.data_de_inicio as "data_de_inicio",
    tarefas_dart.data_prevista as "data_prevista",
    tarefas_dart.data_de_termino as "data_de_termino"
FROM 
    produtos_dart, 
    status_tarefa, 
    tarefas_dart
WHERE 
    tarefas_dart.status_tarefa_id = status_tarefa.id AND 
    tarefas_dart.produtos_dart_id = produtos_dart.id; 
 
