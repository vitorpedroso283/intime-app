# 📘 inTime – Teste Técnico (Ticto)

Bem-vindo(a)! Este repositório representa a entrega do teste técnico solicitado pela empresa **Ticto**.

---

💡 **Sobre o desafio**  
O objetivo foi desenvolver uma API moderna e robusta para controle de ponto, aplicando boas práticas de arquitetura, segurança e organização de código.

---

✨ **O que você encontrará aqui:**

- 📚 **API 100% documentada** via Postman (collection incluída no repositório);
- 🧱 **Backend sólido**, com estrutura real de projeto — pronto para evoluir;
- 🛠️ **Código limpo**, testes automatizados e separação clara de responsabilidades;
- ☕ **E sim...** alguns litros de café foram consumidos para deixar tudo no capricho.

---

> _"Clean code always looks like it was written by someone who cares."_  
> — **Robert C. Martin (Uncle Bob)**

---

🧾 **Nota pessoal:**  
> Esta entrega foi feita com dedicação, atenção aos detalhes e aquele toque artesanal que todo projeto técnico merece.  
> O README foi escrito com o mesmo cuidado aplicado ao código — com seções organizadas, explicações diretas e linguagem acessível, para que qualquer pessoa desenvolvedora ou avaliadora possa entender com clareza as decisões e estratégias adotadas.  
> Mesmo sem frontend, a API foi pensada como base sólida para qualquer tipo de expansão futura — com ou sem botãozinho de 'bater ponto'.

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

### 🔧 Requisitos do Ambiente

Antes de tudo, certifique-se de ter os seguintes requisitos instalados:

-   **PHP >= 8.2** (a aplicação foi testada com PHP 8.4)
-   **Composer** – para gerenciar as dependências PHP
-   **MySQL** (ou outro banco compatível com Laravel)
-   **Postman** – para testar os endpoints utilizando a collection disponível no repositório

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

### 🧱 Arquitetura e Estratégias de Implementação

A arquitetura da aplicação foi pensada de forma pragmática, priorizando boas práticas, clareza e padrões consistentes. A estrutura é baseada em **MVC com Service Layer**, evitando complexidades desnecessárias como DDD ou Arquitetura Hexagonal, que não se justificariam para o escopo deste projeto.

As principais estratégias e decisões adotadas incluem:

-   **Service Layer** para isolar e reutilizar regras de negócio;
-   **Enum** para centralizar permissões (abilities) e roles (admin e funcionário);
-   **Form Requests** para validações padronizadas e reaproveitáveis;
-   **Custom Rules** específicas como CPF e CEP;
-   **API Resources** para padronização das respostas JSON;
-   **Cache** para otimizar requisições externas, como as da API ViaCEP;
-   **Traits utilitárias**, como a geração de CPF válido para testes;
-   **Middlewares** para controle de acesso com base nas abilities;
-   **Controllers enxutos**, focados apenas em entrada e saída HTTP.

> 💡 A separação por serviços contribui diretamente para a manutenibilidade, testabilidade e legibilidade do projeto como um todo.

## 🧪 Validações Customizadas

Para garantir a consistência e controle sobre os dados, foram criadas regras próprias de validação (Rules):

### 📌 CPF

A regra `App\Rules\Cpf` valida o CPF com base no algoritmo oficial, sem depender de bibliotecas externas não mantidas.
Como não existe uma lib oficial do Laravel para validação de CPF, optou-se por uma implementação própria, garantindo controle total e validação robusta dos dígitos verificadores.

### 📌 CEP (Zip Code)

A regra `App\Rules\ValidZipCode` valida se um CEP existe via API ViaCEP. A resposta é cacheada por 1 dia para evitar múltiplas requisições.

A API de consulta de CEP foi construída pensando na performance e reutilização:

-   As requisições à ViaCEP são armazenadas em cache com TTL configurável;
-   Um `ZipCodeService` centraliza a chamada e o cache, evitando acoplamento direto com a API externa;
-   Os dados são retornados via `Resource`, garantindo consistência de estrutura na API;
-   O recurso será reutilizado nos formulários de cadastro de funcionários, onde o CEP será validado automaticamente durante o `FormRequest` (via um custom validator).

#### ➕ Validação de CEP na criação

Ao cadastrar um novo funcionário, o `FormRequest` verifica se o CEP informado é válido e retorna seus dados formatados. Caso não seja encontrado, o request falha com erro 404.

#### 🔁 Considerações sobre atualização

A validação só será reexecutada caso o campo `cep` seja alterado. Isso evita falhas desnecessárias caso o CEP anterior tenha expirado no cache, mas ainda seja válido.

#### 🛡️ Fallback automático

Se o CEP não estiver em cache, a API externa é consultada e o resultado é salvo automaticamente, garantindo consistência e performance.

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

## 📒 Sobre esta entrega

Essa entrega foi feita com muito carinho, foco em boas práticas e movida a algumas boas xícaras de café — **talvez mais do que o recomendado 😅**.

Foi um daqueles projetos que a gente realmente se diverte desenvolvendo: simples, bem estruturado, com espaço pra pensar em melhorias e aplicar decisões técnicas com propósito. Tudo está organizado de forma objetiva, mas com profundidade suficiente pra mostrar o cuidado por trás de cada escolha.

Apesar de não ter incluído um frontend, a decisão foi consciente: o foco aqui era demonstrar uma API robusta, bem estruturada e alinhada com boas práticas. Criar um frontend corrido apenas para cumprir tabela não agregaria valor real à proposta da vaga — especialmente sendo para uma posição back-end. Mas vale reforçar que tenho familiaridade com frontend e, se necessário, entregaria essa camada sem problemas.

Se surgir qualquer dúvida, estou por aqui — e prometo que o café não afetou a qualidade do código. Só ajudou mesmo! ☕🚀
