CREATE TABLE produtos_dart( 
      id number(10)    NOT NULL , 
      projeto_id number(10)    NOT NULL , 
      nome_do_produto varchar  (255)    NOT NULL , 
      system_users_id number(10)    NOT NULL , 
      descricao varchar(3000)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE projeto_dart( 
      id number(10)    NOT NULL , 
      nome_projeto varchar  (255)    NOT NULL , 
      cor varchar  (55)   , 
      descricao varchar(3000)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE status_tarefa( 
      id number(10)    NOT NULL , 
      descricao varchar  (255)    NOT NULL , 
      color varchar  (255)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE tarefas_dart( 
      id number(10)    NOT NULL , 
      produtos_dart_id number(10)    NOT NULL , 
      status_tarefa_id number(10)    NOT NULL , 
      nome_da_tarefa varchar  (255)    NOT NULL , 
      data_de_inicio timestamp(0)    DEFAULT NULL , 
      data_prevista timestamp(0)    DEFAULT NULL , 
      data_de_termino timestamp(0)    DEFAULT NULL , 
      system_users_id number(10)    NOT NULL , 
      descricao varchar(3000)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE user_permission_projeto( 
      id number(10)    NOT NULL , 
      projeto_dart_id number(10)    NOT NULL , 
      user number(10)    NOT NULL , 
 PRIMARY KEY (id)) ; 

 
  
 ALTER TABLE produtos_dart ADD CONSTRAINT fk_produtos_dart_1 FOREIGN KEY (projeto_id) references projeto_dart(id); 
ALTER TABLE tarefas_dart ADD CONSTRAINT fk_tarefas_dart_1 FOREIGN KEY (status_tarefa_id) references status_tarefa(id); 
ALTER TABLE tarefas_dart ADD CONSTRAINT fk_tarefas_dart_2 FOREIGN KEY (produtos_dart_id) references produtos_dart(id); 
ALTER TABLE user_permission_projeto ADD CONSTRAINT fk_user_permission_projeto_1 FOREIGN KEY (projeto_dart_id) references projeto_dart(id); 
 CREATE SEQUENCE produtos_dart_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER produtos_dart_id_seq_tr 

BEFORE INSERT ON produtos_dart FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT produtos_dart_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE projeto_dart_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER projeto_dart_id_seq_tr 

BEFORE INSERT ON projeto_dart FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT projeto_dart_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE status_tarefa_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER status_tarefa_id_seq_tr 

BEFORE INSERT ON status_tarefa FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT status_tarefa_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE tarefas_dart_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER tarefas_dart_id_seq_tr 

BEFORE INSERT ON tarefas_dart FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT tarefas_dart_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE user_permission_projeto_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER user_permission_projeto_id_seq_tr 

BEFORE INSERT ON user_permission_projeto FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT user_permission_projeto_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
 
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
 
