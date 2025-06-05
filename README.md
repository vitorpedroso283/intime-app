# 📘 inTime - Teste Técnico (Ticto)

Este repositório faz parte da entrega de um **teste técnico** para a empresa **Ticto**.

---

## 🌟 Objetivo do Teste

A proposta consiste em desenvolver uma aplicação de controle de ponto, permitindo que:

* Funcionários possam bater ponto (clock-in);
* Administradores possam gerenciar os funcionários e visualizar os registros de ponto;
* A autenticação e autorização sejam feitas utilizando Laravel Sanctum, com controle baseado em "abilities".

---

## ⚙️ Tecnologias e Ferramentas Utilizadas

* **PHP 8.4**
* **Laravel 12**
* **Laravel Sanctum** para autenticação com tokens pessoais
* **Enum** para centralização de permissões (abilities)
* **Service Layer** para separar regras de negócio da camada de controle
* **Eloquent ORM** para comunicação com o banco de dados
* **PestPHP** para escrita e execução de testes automatizados
* **Cache** para otimização de requisições externas

---

## 🧱 Arquitetura do Projeto

A arquitetura da aplicação é baseada no padrão **MVC com Service Layer**, contemplando os seguintes pontos:

* **Controllers** focados em lidar com a entrada e resposta HTTP;
* **Services** contendo a lógica de negócio de forma isolada e reutilizável;
* **Enums** organizando as permissões disponíveis para os tokens Sanctum;
* **Resources** usados para formatar as respostas de API (padrão JSON);
* **Middlewares** configurados para validar permissões via abilities do Sanctum.

### Por que Service Layer?

A separação por serviços permite uma organização clara da lógica de negócio e torna o projeto mais testável e manutenível.

### Por que não usar DDD, Hexagonal, etc?

Embora arquiteturas mais robustas como **DDD** ou **Arquitetura Hexagonal** sejam valiosas em projetos grandes e complexos, sua aplicação aqui resultaria em **over engineering** desnecessário. A escolha por uma abordagem mais simples atende completamente ao escopo e requisitos deste teste.

---

## 🔍 Estratégia de Consulta de CEP

A API de consulta de CEP foi construída pensando na performance e reutilização:

* As requisições à [ViaCEP](https://viacep.com.br) são **armazenadas em cache** com TTL configurável;
* Um `ZipCodeService` centraliza a chamada e o cache, evitando acoplamento direto com a API externa;
* Os dados são retornados via `Resource`, garantindo consistência de estrutura na API;
* O recurso será reutilizado nos formulários de cadastro de funcionários, onde o CEP será **validado automaticamente** durante o `FormRequest` (via um custom validator).

#### ➕ Validação de CEP na criação

Ao cadastrar um novo funcionário, o `FormRequest` verifica se o CEP informado é válido e retorna seus dados formatados. Caso não seja encontrado, o request falha com erro 404.

#### 🔁 Considerações sobre atualização

Para `updates`, a validação só será reexecutada caso o campo `cep` seja alterado. Isso evita falhas desnecessárias caso o CEP anterior tenha expirado no cache, mas ainda seja válido.

🛡️ A implementação também contempla **fallback automático**: se o CEP não estiver em cache, a API externa é consultada e o resultado é salvo, garantindo consistência e performance.

---

## 🗃️ Estrutura do Banco de Dados

O projeto possui duas tabelas principais:

### 🧑‍💼 `users`

Armazena tanto administradores quanto funcionários. Campos adicionais foram incluídos diretamente nessa tabela:

- `cpf`, `role`, `position`, `birth_date`
- Endereço completo (`zipcode`, `street`, `neighborhood`, `city`, `state`, `number`, `complement`)
- `created_by` → indica quem cadastrou o usuário
- `deleted_at` → permite soft delete com `SoftDeletes`

🔄 A opção de manter os campos adicionais na tabela `users`, sem criar uma tabela `employees` separada, foi tomada para manter a estrutura simples, já que todo `user` é um funcionário (ou ao menos precisa bater ponto).

### ⏱️ `punches`

Registra os batimentos de ponto com os campos:

- `user_id` → referência ao usuário
- `type` (`in` ou `out`)
- `punched_at` → momento real do batimento (pode ser diferente de `created_at`)
- `created_by` → identifica se foi um lançamento manual por um admin

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

## 📒 Estratégia de Desenvolvimento

Para organizar o desenvolvimento desta aplicação, estou utilizando a seguinte abordagem:

* Uso de **TDD (Test Driven Development)** sempre que possível, com o framework **PestPHP**;
* Criação de **testes antes das features** para garantir a integridade da lógica;
* Execução local dos testes via `./vendor/bin/pest`;
* Estrutura de testes separada por domínio (ex: `tests/Feature/Auth`, `tests/Feature/Clock`, etc);
* Atualização constante da documentação neste README.

Essa abordagem garante maior confiança na evolução do sistema e ajuda a manter o código limpo e funcional.

---

## 🧪 Commits e Versionamento

* Os commits seguem convenções claras (`feat`, `test`, `fix`, `docs`, etc);
* A frequência de entregas parciais está refletida nos commits pequenos e incrementais;
* Apesar de não termos utilizado múltiplas **branches** neste projeto, essa foi uma decisão consciente para manter o fluxo simples. Caso necessário, adotaríamos convenções como:

* feature/nome-da-feature
* fix/ajuste-especifico
* docs/atualiza-readme

A ausência de branches não comprometeu a legibilidade nem o controle do histórico, que segue boas práticas de versionamento.

---

## 📒 Sobre este README

Este é um **README provisório** com anotações e insights sobre o desenvolvimento. Uma versão final mais objetiva e organizada será disponibilizada ao término da implementação, contendo:

* Instruções de execução local;
* Estrutura completa de endpoints;
* Explicações de decisões técnicas;
* Cobertura de testes (se aplicável).

---