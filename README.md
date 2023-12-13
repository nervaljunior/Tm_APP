# Tm_APP - Plataforma de Controle de Projetos de Pesquisa

Este projeto consiste em uma plataforma para o gerenciamento de projetos de pesquisa, administrando tarefas atribuídas aos alunos participantes.

![image](https://github.com/nervaljunior/Tm_APP/assets/108685222/4e8fa75a-9653-4cc0-9f28-388a76edd407)

## ✒️ Autores

* **Nerval de Jesus Santos Junior** - *Documentação - Dev - Engenheiro* - [Perfil do GitHub](https://github.com/nervaljunior)
* **Leandro Lisboa Matos** - *Documentação - Dev - Engenheiro* - [Perfil do GitHub]()
* **Leonardo Victor dos Santos Sa Menez** - *Documentação - Dev - Engenheiro* - [Perfil do GitHub]()
* **Luís Guilherme Freitas de Almeida Silva** - *Documentação - Dev - Engenheiro* - [Perfil do GitHub]()

## 📌 Planejamento

### DIAGRAMA DE SEQUÊNCIA
O diagrama mostra o detalhamento do caso de uso "Criar Usuário", no qual o professor (admin) executa métodos que se comunicam com o sistema e com o banco de dados para registro dos dados, verificação da existência ou não, confirmação de criação da conta ou retorno de que o usuário já está cadastrado no sistema. O diagrama mostra o detalhamento do caso de uso "Criar Projeto", no qual o professor (admin) executa métodos para criação dos projetos no sistema que comunica-se com o banco de dados para salvar os registros. De forma semelhante, é possível consultar as informações posteriormente e criar tarefas dentro do projeto.

### DIAGRAM DE CASO DE USO
No Diagrama de Caso de Uso, teremos três atores, são eles: Observador-alunos, Standard-aluno-Lider e Admin-Professor. O ator Observador-alunos é uma generalização de Standard-aluno-Líder que, por sua vez, é uma generalização de Admin-Professor. Vale ressaltar, portanto, que os casos de uso de Observador-alunos são comuns para as especializações subsequentes, assim como a funcionalidade de Standard-aluno-Lider é comum pra Admin-Professor. Dessa forma, Observador-alunos tem como funcionalidade Fazer Login e Visualizar Informações no Sistema, que pode ser Consultar Projetos, Visualizar Gráficos e/ou Consultar Tarefas. Standard-aluno-Lider pode, além das anteriores, Atualizar Andamento da Tarefa. Por fim, Admin-Professor é o ator que herda todas as funcionalidades anteriores e ainda tem as seguintes funcionalidades: Criar Usuários, Editar Dados dos Usuários, Criar Tarefas, Atribuir Nível de Prioridade, Editar Tarefas, Alocar Tarefas à Projetos, Criar Projetos, Alocar Usuários a Projetos e Editar Projetos.

### DIAGRAMA DE CLASSE
No Diagrama de Classes, teremos as classes: Pessoa, Observers, Standard, Admin, Projetos e Tarefas. Começando pela classe Admin, que é uma especialização da classe Standard, possui os seguintes métodos: criarProjeto(), editarProjeto(), criarTarefa(), criarProjeto(), editarTarefa(), alocarTarefa(), excluirTarefa(), excluirProjeto(), alocarUsuario(). Standard, por sua vez, é uma especialização de Observers e possui o método atualTarefa(), que atualiza tarefa. Observers é uma especialização de Pessoa e possui como métodos consultarTarefa(), consultarProjetos(), visualizarGrafico(). Pessoa é a classe genérica e possui como atributos id, nome, cpf, numphone e email; seus métodos são: visualizarProjeto(), visualizarTarefa(). A classe Tarefas que possui nomeTarefa, dataInicio e dataFim como atributos, tem o método cadastrarTarefa() e é uma composição de Projetos. Projetos tem os atributos idProjeto, nomeProjeto, nomeResponsavel, dataInicio e dataFim e o método cadastrarProjeto() e é uma composição de Admin.

## 🚀 Começando

![image](https://github.com/nervaljunior/Tm_APP/assets/108685222/3ba78979-8ccf-43c3-adff-0f2b4b1940f6)

## Diagramação 

#### Caso de Uso

No Diagrama de Caso de Uso, teremos três atores, são eles: Observador-alunos, Standard-aluno-Lider e Admin-Professor. O ator Observador-alunos é uma generalização de Standard-aluno-Líder que, por sua vez, é uma generalização de Admin-Professor. Vale ressaltar, portanto, que os casos de uso de Observador-alunos são comuns para as especializações subsequentes, assim como a funcionalidade de Standard-aluno-Lider é comum pra Admin-Professor. Dessa forma, Observador-alunos tem como funcionalidade Fazer Login e Visualizar Informações no Sistema, que pode ser Consultar Projetos, Visualizar Gráficos e/ou Consultar Tarefas. Standard-aluno-Lider pode, além das anteriores, Atualizar Andamento da Tarefa. Por fim, Admin-Professor é o ator que herda todas as funcionalidades anteriores e ainda tem as seguintes funcionalidades: Criar Usuários, Editar Dados dos Usuários, Criar Tarefas, Atribuir Nível de Prioridade, Editar Tarefas, Alocar Tarefas à Projetos, Criar Projetos, Alocar Usuários a Projetos e Editar Projetos.

![image](https://github.com/nervaljunior/Tm_APP/assets/108685222/e5595794-6143-4e0a-9d93-1520077fc02d)

#### Sequência

O diagrama mostra o detalhamento do caso de uso "Criar Usuário", no qual o professor (admin) executa metodos que se comunicam com o sistema e com o banco de dados para registro dos dados, verificação da existência ou não, confirmação de criação da conta ou retorno de que o usuario ja esta cadastrado no sistema.
O diagrama mostra o detalhamento do caso de uso "Criar Projeto", no qual o professor (admin) executa metodos para criação dos projetos no sistema que comunica-se com o banco de dados para salvar os registros. De forma semelhante é possivel consultar as informações posteriormente e criar tarefas dentro do projeto.

![image](https://github.com/nervaljunior/Tm_APP/assets/108685222/71fb01be-f888-475e-b75f-3e9519760788)
![image](https://github.com/nervaljunior/Tm_APP/assets/108685222/0f9872d0-f640-4062-9065-d5ea9fbfa8ec)

#### Atividade

O diagrama mostra o detalhamento do caso de uso "Criar Usuário", no qual o professor (admin) executa metodos que se comunicam com o sistema e com o banco de dados para registro dos dados, verificação da existência ou não, confirmação de criação da conta ou retorno de que o usuario ja esta cadastrado no sistema.

O diagrama mostra o detalhamento do caso de uso "Criar Projeto", no qual o professor (admin) executa metodos para criação dos projetos no sistema que comunica-se com o banco de dados para salvar os registros. De forma semelhante é possivel consultar as informações posteriormente e criar tarefas dentro do projeto.

![image](https://github.com/nervaljunior/Tm_APP/assets/108685222/d5e54d51-a00e-4fe9-bfdf-6ac87ce23c50)


#### Classe 

No Diagrama de Classes, teremos as classes: Pessoa, Observers, Standard, Admin, Projetos e Tarefas. Começando pela classe Admin, que é uma especialização da classe Standard, possui os seguintes métodos: criarProjeto(), editarProjeto(), criarTarefa(), criarProjeto(), editarTarefa(), alocarTarefa(), excluirTarefa(), excluirProjeto(), alocarUsuario(). Standard, por sua vez, é uma especialização de Observers e possui o método atualTarefa(), que atualiza tarefa. Observers é uma especialização de Pessoa e possui como métodos consultarTarefa(), consultarProjetos(), visualizarGrafico(). Pessoa é a classe genérica e possui como atributos id, nome, cpf, numphone e email; seus métodos são: visualizarProjeto(), visualizarTarefa(). A classe Tarefas que possui nomeTarefa, dataInicio e dataFim como atributos, tem o método cadastrarTarefa() e é uma composição de Projetos. Projetos tem os atributos idProjeto, nomeProjeto, nomeResponsavel, dataInicio e dataFim e o método cadastrarProjeto() e é uma composição de Admin.

![Imagem do WhatsApp de 2023-12-13 à(s) 11 42 58_17bc77dd](https://github.com/nervaljunior/Tm_APP/assets/108685222/67c14eca-cd57-42b5-a5a2-d4fc1c9db0ba)

### 📋 Pré-requisitos

- Servidor PHP
- Banco de dados PHPMyAdmin
- StarUML para visualização do UML

### 🔧 Instalação

1. Clone o repositório.
2. Configure o servidor PHP.
3. Importe o banco de dados utilizando o PHPMyAdmin.

## ⚙️ Executando os testes

## 📦 Implantação

## 🛠️ Construído com

- PHP
- Banco de Dados PHPMyAdmin
- StarUML para modelagem UML

## 📌 Versão

As Imagens a seguir mostram as formas de análise e visualização de gráficos que ajudam no gerenciamento dos projetos.

aqui temos a timeline de tarefas.
![Imagem do WhatsApp de 2023-12-01 à(s) 17 21 39_e4d96b17](https://github.com/nervaljunior/Tm_APP/assets/108685222/45a139b0-2028-4d51-b065-f204d8c4f0a5)
![Imagem do WhatsApp de 2023-12-01 à(s) 17 21 39_59b7eb53](https://github.com/nervaljunior/Tm_APP/assets/108685222/9a0598f8-f0c8-414e-8910-9b776ee54782)

aqui por ultimo podemos analisar os usuarios que estão cadastrados e qual o tipo de acesso e preferencias que eles têm.
![Imagem do WhatsApp de 2023-12-01 à(s) 17 21 39_c9d6a648](https://github.com/nervaljunior/Tm_APP/assets/108685222/863c2319-ee98-4ad1-9ec4-c0699201eaa3)

## Estrutura do Projeto

O projeto segue a arquitetura MVC (Model-View-Controller) para uma organização clara e eficiente do código. A estrutura do projeto é dividida em pacotes.

## Requisitos e Ferramentas

- PHP
- PHPMyAdmin
- StarUML

## Funcionalidades
# Documentação - Sistema de Gestão de Projetos

Bem-vindo à documentação do Sistema de Gestão de Projetos, construído com o framework Adiante Builder.

## Índice

1. [**Login**](#login)
2. [**Home**](#home)
3. [**Gestão**](#gestão)
    - [Cadastro de Projetos](#cadastro-de-projetos)
    - [Cadastro de Usuários](#cadastro-de-usuários)
4. [**Acompanhamento de Tarefas**](#acompanhamento-de-tarefas)
5. [**Outras Funcionalidades**](#outras-funcionalidades)

## **Login** <a name="login"></a>

Na tela de login, encontram-se os campos "usuário" e "senha", sendo necessário preenchê-los com as informações cadastradas no banco de dados. Em caso de omissão ou inserção de dados incorretos, o sistema emitirá mensagens de erro, tais como "usuário não encontrado", "o campo senha é obrigatório" e "senha incorreta".

![image](https://github.com/nervaljunior/Tm_APP/assets/108685222/719cff1b-b786-42de-992a-2134adcb5e64)
![image](https://github.com/nervaljunior/Tm_APP/assets/108685222/232394d8-ab7f-4efb-a645-08338f0e5d89)

## **Home** <a name="home"></a>

Na tela inicial, é possível visualizar os projetos já registrados no sistema. Além disso, encontra-se uma barra de pesquisa central, que permite buscar projetos pelo nome. Na barra superior, é apresentada a informação do usuário atualmente logado no sistema.

![image](https://github.com/nervaljunior/Tm_APP/assets/108685222/685a4749-99a4-462b-b007-eb9ab6004d37)

## **Gestão** <a name="gestão"></a>

No botão de "Gestão", temos acesso à aba de "Projetos", que exibe a lista completa de todos os projetos registrados no sistema. Através do botão "Cadastrar Projeto", podemos incluir novos projetos, utilizar o botão "Editar" para efetuar modificações, o botão "Apagar" para excluir projetos, e o botão "Participante" para gerenciar os colaboradores envolvidos nos projetos.

![image](https://github.com/nervaljunior/Tm_APP/assets/108685222/aec250ea-9720-40fe-a7de-855842af8250)

### Cadastro de Projetos <a name="cadastro-de-projetos"></a>

Nesta tela, é possível realizar o cadastro inicial dos projetos no sistema, registrando o nome e descrição detalhada do mesmo, informando seu escopo. Existem diversas opções de formatação de texto. Ao acessar a operação de edição, será mostrada esta mesma tela, possibilitando ao usuário editar as informações.

![image](https://github.com/nervaljunior/Tm_APP/assets/108685222/c3c95caf-cc73-4ae7-ae98-303fd1880929)

### Cadastro de Usuários <a name="cadastro-de-usuários"></a>

Nesta tela, é possível realizar o cadastro de usuários no sistema, registrando o nome e email. Existem diversas operações disponíveis. No botão "Edição", temos acesso aos campos com os dados cadastrados para o usuário, os quais podem ser alterados. O botão "Excluir" deleta o usuário e todos os seus dados do sistema. O botão "Clonar" permite duplicar os dados de um usuário. O botão "XXXX" permite desativar um usuário, dessa forma, o mesmo não pode ser atribuído a um projeto, porém seus dados serão mantidos. O controle de acesso permite ao admin definir o nível de acesso do usuário. A coluna "Status" mostra se o usuário está ativo ou inativo.

![image](https://github.com/nervaljunior/Tm_APP/assets/108685222/fbe70f19-a227-4ad6-9c91-4735b7394e4b)

## **Acompanhamento de Tarefas** <a name="acompanhamento-de-tarefas"></a>

Na tela kanban de tarefas, é possível realizar o acompanhamento de tarefas no sistema. As tarefas são classificadas em pendentes, em andamento e concluídas. Na aba tarefas, é possível monitorar o andamento de tarefas pelo gráfico de Gantt e o número de tarefas por status.

![image](https://github.com/nervaljunior/Tm_APP/assets/108685222/80a7b94a-5a55-4db5-a834-e0787e37cb42)

Abaixo a visualização dos gráficos possíveis de se visualizar.
![image](https://github.com/nervaljunior/Tm_APP/assets/108685222/0828544e-a716-470c-87aa-e5953611f943)

## **Outras Funcionalidades** <a name="outras-funcionalidades"></a>

Existem algumas funcionalidades disponíveis no sistema, como "Notificações", que mostram informações sobre os projetos para o usuário. "Mensagens" permite o envio de mensagens entre usuários e admin. No menu do usuário, é possível acessar o próprio perfil e seus dados, recarregar (atualizar) a página e fazer logout do sistema. Os códigos, banco de dados e demais ferramentas utilizadas na construção do sistema podem ser acessados pelo admin para implantação de futuras melhorias.

![image](https://github.com/nervaljunior/Tm_APP/assets/108685222/7e686aa0-423d-46f1-bb88-07d8f5463a14)

- Desenvolvemos as interações baseadas no modelo CRUD, que são as quatro operações básicas do desenvolvimento de uma aplicação. Essas operações são utilizadas em bases de dados relacionais fornecidas aos usuários do sistema.



Sinta-se à vontade para contribuir com melhorias ou correções neste projeto. Basta criar um fork, fazer as alterações desejadas e enviar um pull request.




## 📄 Licença

Este projeto é distribuído sob a licença MIT. Veja o arquivo [LICENSE.md](https://github.com/nervaljunior/Tm_APP/blob/main/LICENSE.md) para detalhes.

## 🎁 Expressões de gratidão

* Conte a outras pessoas sobre este projeto 📢;
* Convide alguém da equipe para uma cerveja 🍺 - eu sugiro [Nerval Junior](https://github.com/nervaljunior)!!;
* Um agradecimento público 🫂;

Licença: Creative Commons Attribution 4.0 International License

Este trabalho está licenciado sob a Creative Commons Attribution 4.0 International License. Para ver uma cópia desta licença, visite [CC BY 4.0](https://creativecommons.org/licenses/by/4.0/legalcode.en). Isso significa que você pode usar, compartilhar e adaptar este material, inclusive para fins comerciais, desde que forneça o crédito apropriado, forneça um link para a licença e indique se foram feitas alterações. Essa permissão é válida para todos os conteúdos contidos neste zip, e se estende ao uso e publicação no GitHub ou em outras plataformas.
