### 📘 inTime - Teste Técnico (Ticto)

Este repositório faz parte da entrega de um teste técnico para a empresa Ticto.

## 🌟 Objetivo do Teste

A proposta consiste em desenvolver uma aplicação de controle de ponto, permitindo que:

-   Funcionários possam bater ponto (clock-in);

-   Administradores possam gerenciar os funcionários e visualizar os registros de ponto;

-   A autenticação e autorização sejam feitas utilizando perfis (admin e funcionário).

### 🧰 Tecnologias Principais

-   **PHP 8.4**
-   **Laravel 12**
-   **Laravel Sanctum** – autenticação com tokens pessoais
-   **Eloquent ORM** – comunicação com o banco de dados
-   **PestPHP** – escrita e execução de testes automatizados

## 🚀 Como Rodar a Aplicação

Siga os passos abaixo para configurar e rodar o projeto localmente:

### 1. Clone o repositório

```bash
git clone https://github.com/vitorpedroso283/intime-app.git
cd intime-app
```

### 2. Instale as dependências

```bash
composer install
```

### 3. Copie o arquivo `.env` e configure

```bash
cp .env.example .env
```

Edite o arquivo `.env` com as credenciais corretas do seu banco de dados.

### 4. Gere a chave da aplicação

```bash
php artisan key:generate
```

### 5. Crie o banco de dados

Crie um banco (por exemplo, `intime-app`) no seu MySQL/PostgreSQL:

```sql
CREATE DATABASE intime-app;
```

### 6. Execute as migrations e seeders

```bash
php artisan migrate --seed
```

Esse comando criará todas as tabelas do banco e populará os dados iniciais, incluindo um usuário administrador padrão para acesso ao sistema:

-   **Email: admin@intime.test**
-   **Senha: t0atr@sado**

Esse usuário pode ser utilizado para realizar os testes iniciais, acessar endpoints protegidos como administrador e cadastrar novos funcionários.

### 7. Rode a aplicação localmente

```bash
php artisan serve
```

Acesse no navegador: [http://localhost:8000](http://localhost:8000)

---

### ✅ Rodando os testes

Execute os testes com Pest:

```bash
./vendor/bin/pest
```

---

## 🔌 Endpoints da API

A aplicação expõe uma API RESTful protegida por autenticação via Laravel Sanctum. O token de acesso é obtido na rota `/login` e reutilizado automaticamente nos demais endpoints, respeitando as permissões definidas por abilities.

### 🧩 Grupos de Endpoints

#### 🔐 Autenticação

-   `POST /login`: Gera o token de acesso para o usuário.
-   `POST /logout`: Revoga o token atual.
-   `PATCH /me/password`: Atualiza a senha do próprio usuário logado (ability: `UPDATE_PASSWORD`).

#### 👤 Administração de Usuários (`/admin/users`)

-   `GET /users`: Lista todos os funcionários.
-   `POST /users`: Cria novo funcionário.
-   `GET /users/{id}`: Visualiza dados de um funcionário.
-   `PUT /users/{id}`: Atualiza dados de um funcionário.
-   `DELETE /users/{id}`: Remove um funcionário.
-   `PATCH /users/{id}/password`: Reseta a senha de um funcionário (apenas admin).

🔐 Todas as rotas acima exigem ability: `MANAGE_EMPLOYEES`.

#### ⏱️ Registro de Ponto (`/punches`)

-   `POST /clock-in`: Bate o ponto (entrada ou saída automática). Requer ability: `CLOCK_IN`.
-   `POST /manual`: Registra ponto manual (ex: esquecimento) — via admin.
-   `PUT /{id}`: Atualiza um registro de ponto (admin).
-   `DELETE /{id}`: Remove um registro de ponto (admin).

🔐 As três últimas rotas requerem ability: `MANAGE_EMPLOYEES`.

-   `GET /report`: Retorna relatório de registros de ponto com filtros avançados. Requer ability: `VIEW_ALL_CLOCKS`.

##### Filtros disponíveis para `/report`:

| Parâmetro  | Tipo   | Descrição                                                                                           |
| ---------- | ------ | --------------------------------------------------------------------------------------------------- |
| from       | date   | Data inicial (YYYY-MM-DD)                                                                           |
| to         | date   | Data final (YYYY-MM-DD)                                                                             |
| user_id    | int    | ID do funcionário                                                                                   |
| created_by | int    | ID do admin que criou o registro                                                                    |
| position   | string | Cargo do funcionário                                                                                |
| sort_by    | string | Campo de ordenação (`punched_at`, `employee_name`, `employee_role`, `employee_age`, `manager_name`) |
| sort_dir   | string | Direção da ordenação: `asc` ou `desc`                                                               |
| per_page   | int    | Quantidade por página (1–100)                                                                       |
| page       | int    | Página da listagem                                                                                  |

ℹ️ Para uso dos filtros, o token deve conter a ability: `FILTER_CLOCKS`.

#### 🧭 Consulta de CEP

-   `GET /zipcode/{cep}`: Retorna endereço completo utilizando a API do ViaCEP.

---

### 📥 Importação

O arquivo da collection já está disponível no repositório com o nome:

```
intime-app.postman_collection.json
```

Você pode importá-lo diretamente no Postman para testar e explorar os endpoints.

### 🧪 Informações úteis

-   **Autenticação:** Laravel Sanctum com token do tipo Bearer.
-   **Token automático:** o token (`access_token`) é salvo automaticamente no ambiente ao fazer login.
-   **Variáveis de ambiente esperadas:**
    -   `BASE_URL`: URL base da API (ex: `http://localhost:8000/api`)
    -   `access_token`: preenchido automaticamente após o login

> Acesse o Postman, importe a collection e inicie os testes. O token será gerenciado automaticamente após o login.

---

### 🧠 Estratégias de Implementação

-   **Enum** para centralização de permissões (abilities)
-   **Enum** para centralização de role (perfis admin e funcionário)
-   **Service Layer** para separar regras de negócio da camada de controle
-   **Cache** para otimização de requisições externas
-   **Form Requests** para validações padronizadas e reutilizáveis
-   **API Resources** para padronização e formatação das respostas
-   **Custom Rules** para validações como CPF e CEP
-   **Traits utilitárias** como geração de CPF válido para testes
-   **A separação por serviços permite uma organização clara da lógica de negócio e torna o projeto mais testável e manutenível.**

## 🧱 Arquitetura do Projeto

A arquitetura da aplicação foi pensada de forma pragmática, priorizando boas práticas, organização clara e padrões sólidos, sem adotar estruturas complexas como DDD ou Arquitetura Hexagonal, que seriam desnecessárias para o escopo deste projeto.

A escolha por uma abordagem simples e eficiente, baseada no padrão MVC com Service Layer, garante uma separação adequada de responsabilidades, tornando o projeto fácil de manter e evoluir.

A estrutura contempla:


-   **Controllers focados em lidar com a entrada e resposta HTTP;**

-   **Services contendo a lógica de negócio de forma isolada e reutilizável;**

-   **Enums organizando as permissões disponíveis para os tokens Sanctum, roles e filtros;**

-   **Resources usados para formatar as respostas de API (padrão JSON);**

-   **Middlewares configurados para validar permissões via abilities do Sanctum;**

-   **Form Requests responsáveis por encapsular regras de validação reutilizáveis;**

-   **Rules customizadas utilizadas para validações específicas como CPF e CEP.**


## 🧪 Validações Customizadas

Para garantir a consistência e controle sobre os dados, foram criadas regras próprias de validação (Rules):

## 📌 CPF

A regra `App\Rules\Cpf` valida o CPF com base no algoritmo oficial, dispensando bibliotecas externas não mantidas. Garante controle total e validação robusta dos dígitos verificadores.

## 📌 CEP (Zip Code)

A regra `App\Rules\ValidZipCode` valida se um CEP existe via API ViaCEP. A resposta é cacheada por 1 dia para evitar múltiplas requisições.

-   O `ZipCodeService` centraliza essa lógica;
-   A validação ocorre apenas se o campo for alterado;
-   O cache é utilizado tanto na validação quanto na aplicação.

## 🧰 Utilitários e Traits

Para testes e seeders, foi criada a trait `App\Traits\GeneratesCpf` que gera CPFs válidos com base no mesmo algoritmo de validação utilizado na regra `Cpf`. Essa trait é usada diretamente na `UserFactory`.

## 🧑‍💻 Enumeração de Perfis e Permissões

### 🎭 UserRole

Enum que representa os dois papéis possíveis:

-   `admin` → gerencia os funcionários, visualiza todos os pontos, etc;
-   `employee` → funcionário comum que registra seus próprios pontos.

O enum fornece métodos auxiliares como `->abilities()` e `->label()` para facilitar a associação com permissões e labels traduzidos.

### 🛡️ TokenAbility

Enum central que define as permissões utilizadas nos tokens Sanctum, como:

-   `employee:clock-in`
-   `admin:manage-employees`
-   `employee:update-password`

Esse enum garante consistência e documenta todas as abilities válidas do sistema.

## 🔍 Estratégia de Consulta de CEP

A API de consulta de CEP foi construída pensando na performance e reutilização:

-   As requisições à ViaCEP são armazenadas em cache com TTL configurável;
-   Um `ZipCodeService` centraliza a chamada e o cache, evitando acoplamento direto com a API externa;
-   Os dados são retornados via `Resource`, garantindo consistência de estrutura na API;
-   O recurso será reutilizado nos formulários de cadastro de funcionários, onde o CEP será validado automaticamente durante o `FormRequest` (via um custom validator).

### ➕ Validação de CEP na criação

Ao cadastrar um novo funcionário, o `FormRequest` verifica se o CEP informado é válido e retorna seus dados formatados. Caso não seja encontrado, o request falha com erro 404.

### 🛡️ A implementação também contempla fallback automático:

Se o CEP não estiver em cache, a API externa é consultada e o resultado é salvo, garantindo consistência e performance.

---

## 🗃️ Estrutura do Banco de Dados

O projeto possui duas tabelas principais:

### 🧑‍💼 `users`

Armazena tanto administradores quanto funcionários. Campos adicionais foram incluídos diretamente nessa tabela:

-   `cpf`, `role`, `position`, `birth_date`
-   Endereço completo (`zipcode`, `street`, `neighborhood`, `city`, `state`, `number`, `complement`)
-   `created_by` → indica quem cadastrou o usuário
-   `deleted_at` → permite soft delete com `SoftDeletes`

🔄 A opção de manter os campos adicionais na tabela `users`, sem criar uma tabela `employees` separada, foi tomada para manter a estrutura simples, já que todo `user` é um funcionário (ou ao menos precisa bater ponto).

### ⏱️ `punches`

Registra os batimentos de ponto com os campos:

-   `user_id` → referência ao usuário
-   `type` (`in` ou `out`)
-   `punched_at` → momento real do batimento (pode ser diferente de `created_at`)
-   `created_by` → identifica se foi um lançamento manual por um admin

📌 **Por que `punched_at` se já temos `created_at`?**

Para registrar batimentos manuais corretamente. O `created_at` indica quando o registro foi inserido, enquanto `punched_at` indica o momento real da batida.

📌 **Por que `created_by`?**

Para diferenciar batidas feitas pelo próprio funcionário de registros manuais adicionados por um administrador.

---

## 📌 Comentários no Código

A maioria dos comentários está em **português**, por dois motivos:

1. O teste foi redigido integralmente em português;
2. Comentários têm como objetivo facilitar a leitura dos avaliadores.

Os commits, no entanto, seguem o padrão em **inglês**, alinhados com boas práticas de versionamento.

---

## 📈 Observabilidade e Logs

O projeto foi pensado para facilitar a rastreabilidade das ações realizadas pelos usuários, especialmente em operações críticas como:

-   Login e logout;
-   Registro manual de ponto;
-   Criação, atualização e remoção de funcionários.

🪵 **Logs Estruturados**

-   Utiliza o canal `daily` (configurado no `.env`) para registrar logs diários separados por data.
-   O nível de log padrão é `debug`, permitindo rastrear informações detalhadas durante o desenvolvimento e testes.

```dotenv
LOG_CHANNEL=daily
LOG_LEVEL=debug
```

🔐 **Privacidade e Segurança**

-   Informações sensíveis como senhas não são logadas;
-   Os logs focam em eventos e contexto de requisição, como: ID do usuário autenticado, IP, rota acessada, tipo de ação executada.

🧪 **Cobertura de Testes de Integração**

Todos os endpoints principais foram testados com **testes de integração completos**:

-   Autenticação e autorização;
-   Cadastro, atualização e remoção de funcionários;
-   Registro de ponto (clock-in/out);
-   Validações customizadas como CPF e CEP.

💡 Os testes garantem que, além de respostas corretas, os logs esperados são emitidos sem gerar exceções ou vazamentos.

---

## 🗃️ **Relatório de Registros com SQL Puro**

A listagem de registros de ponto exigida no desafio foi implementada utilizando **consulta SQL nativa**, sem Eloquent, conforme solicitado.

A consulta inclui:

-   ID do Registro
-   Nome e Cargo do Funcionário
-   Nome do Gestor
-   Idade do Funcionário (calculada na query)
-   Data e Hora Completa do Registro (com segundos)

### A consulta está disponível no método `report()` do `PunchController`, garantindo performance e clareza conforme os critérios de avaliação.

## 📒 Estratégia de Desenvolvimento

Para organizar o desenvolvimento desta aplicação, estou utilizando a seguinte abordagem:

-   Uso de **TDD (Test Driven Development)** sempre que possível, com o framework **PestPHP**;
-   Criação de **testes antes das features** para garantir a integridade da lógica;
-   Execução local dos testes via `./vendor/bin/pest`;
-   Estrutura de testes separada por domínio (ex: `tests/Feature/Auth`, `tests/Feature/Clock`, etc);
-   Atualização constante da documentação neste README.

Essa abordagem garante maior confiança na evolução do sistema e ajuda a manter o código limpo e funcional.

---

## 🧪 Commits e Versionamento

Os commits seguem convenções claras (feat, test, fix, docs, etc), garantindo rastreabilidade. Apesar do uso de uma única branch, o histórico foi mantido limpo e incremental, permitindo fácil revisão do progresso e decisões tomadas.

## 🔄 Considerações Técnicas Adicionais

Durante a implementação deste teste, optei por não utilizar Jobs, Events, Listeners ou comandos Artisan customizados, e listo abaixo os motivos:

O escopo do desafio foi bem definido e direto, com foco em controle de ponto e gestão de usuários;

A criação de jobs para processos como envio de e-mail de boas-vindas, embora possível, não se justificava, já que o Laravel provê isso de forma trivial com notificações ou Mail::to()->send() inline;

O uso de events e listeners, bem como comandos Artisan customizados, foi evitado por não haver fluxo reativo, tarefas agendadas ou rotinas de longa duração que demandassem esse tipo de arquitetura.

---

## ❓Dúvidas de Interpretação

Durante a análise do teste, surgiram algumas dúvidas quanto ao escopo funcional. Seguem abaixo os pontos em que foram feitas interpretações técnicas para garantir a entrega da funcionalidade de forma coerente:

### Atualização de senha

O enunciado não deixava claro se a funcionalidade de troca de senha deveria ser feita pelo usuário autenticado (por exemplo, no painel pessoal) ou se deveria existir uma funcionalidade de reset de senha feito por um administrador.

Considerando o contexto de controle de ponto, onde normalmente o gestor é quem define ou reseta a senha dos funcionários, optamos por:

-   Criar uma rota para o usuário autenticado trocar sua própria senha (caso o sistema precise ser mais autônomo ou tenha um painel de autoatendimento);

-   Adicionar uma rota exclusiva para administradores resetarem a senha de qualquer outro usuário (funcionário ou outro admin), conforme seria esperado em um sistema corporativo tradicional.

Isso garante flexibilidade e cobre ambos os cenários com segurança.

---

## 📒 Sobre este README

Este é um **README provisório** com anotações e insights sobre o desenvolvimento. Uma versão final mais objetiva e organizada será disponibilizada ao término da implementação, contendo:

-   Instruções de execução local;
-   Estrutura completa de endpoints;
-   Explicações de decisões técnicas;
-   Cobertura de testes (se aplicável).
