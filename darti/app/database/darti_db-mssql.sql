CREATE TABLE produtos_dart( 
      id  INT IDENTITY    NOT NULL  , 
      projeto_id int   NOT NULL  , 
      nome_do_produto varchar  (255)   NOT NULL  , 
      system_users_id int   NOT NULL  , 
      descricao nvarchar(max)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE projeto_dart( 
      id  INT IDENTITY    NOT NULL  , 
      nome_projeto varchar  (255)   NOT NULL  , 
      cor varchar  (55)   , 
      descricao nvarchar(max)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE status_tarefa( 
      id  INT IDENTITY    NOT NULL  , 
      descricao varchar  (255)   NOT NULL  , 
      color varchar  (255)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE tarefas_dart( 
      id  INT IDENTITY    NOT NULL  , 
      produtos_dart_id int   NOT NULL  , 
      status_tarefa_id int   NOT NULL  , 
      nome_da_tarefa varchar  (255)   NOT NULL  , 
      data_de_inicio datetime2     DEFAULT NULL, 
      data_prevista datetime2     DEFAULT NULL, 
      data_de_termino datetime2     DEFAULT NULL, 
      system_users_id int   NOT NULL  , 
      descricao nvarchar(max)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE user_permission_projeto( 
      id  INT IDENTITY    NOT NULL  , 
      projeto_dart_id int   NOT NULL  , 
      user int   NOT NULL  , 
 PRIMARY KEY (id)) ; 

 
  
 ALTER TABLE produtos_dart ADD CONSTRAINT fk_produtos_dart_1 FOREIGN KEY (projeto_id) references projeto_dart(id); 
ALTER TABLE tarefas_dart ADD CONSTRAINT fk_tarefas_dart_1 FOREIGN KEY (status_tarefa_id) references status_tarefa(id); 
ALTER TABLE tarefas_dart ADD CONSTRAINT fk_tarefas_dart_2 FOREIGN KEY (produtos_dart_id) references produtos_dart(id); 
ALTER TABLE user_permission_projeto ADD CONSTRAINT fk_user_permission_projeto_1 FOREIGN KEY (projeto_dart_id) references projeto_dart(id); 

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
 
