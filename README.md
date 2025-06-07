# 📘 inTime - Teste Técnico (Ticto)

Este repositório faz parte da entrega de um teste técnico para a empresa Ticto.

---

## 🌟 Objetivo do Teste

A proposta consiste em desenvolver uma aplicação de controle de ponto, permitindo que:

- Funcionários possam bater ponto (clock-in);
- Administradores possam gerenciar os funcionários e visualizar os registros de ponto;
- A autenticação e autorização sejam feitas utilizando Laravel Sanctum, com controle baseado em *abilities*.

---

## ⚙️ Tecnologias e Ferramentas Utilizadas

- PHP 8.4  
- Laravel 12  
- Laravel Sanctum para autenticação com tokens pessoais  
- Enum para centralização de permissões (abilities)  
- Service Layer para separar regras de negócio da camada de controle  
- Eloquent ORM  
- PestPHP para testes automatizados  
- Cache para otimização de requisições externas  
- Form Requests para validações reutilizáveis  
- API Resources para formatação das respostas  
- Custom Rules para validações como CPF e CEP  
- Traits utilitárias para testes

---

## 🧱 Arquitetura do Projeto

- Controllers focados na entrada e resposta HTTP;
- Services com a lógica de negócio isolada e reutilizável;
- Enums para controle de permissões dos tokens Sanctum;
- Resources para padronização de respostas;
- Middlewares para validar abilities dos tokens;
- Form Requests com regras de validação reutilizáveis;
- Rules customizadas como CPF e CEP.

> **Service Layer** foi adotado por facilitar a organização da lógica de negócio sem o peso de arquiteturas como DDD ou Hexagonal, que seriam overkill neste escopo.

---

## 🧪 Validações Customizadas

### 📌 CPF
Validação via `App\Rules\Cpf`, com algoritmo oficial. Dispensa pacotes externos.

### 📌 CEP
Validação via `App\Rules\ValidZipCode`, utilizando a API ViaCEP com cache de 1 dia.

- A regra só valida se o campo for alterado;
- Usa o `ZipCodeService` para centralizar a lógica e o cache.

---

## 🧰 Utilitários e Traits

- `App\Traits\GeneratesCpf`: gera CPF válido usado em testes e seeders, com o mesmo algoritmo da Rule.

---

## 🧑‍💻 Enumeração de Perfis e Permissões

### 🎭 `UserRole`
- `admin`: gerencia usuários e vê todos os registros;
- `employee`: funcionário comum.

### 🛡️ `TokenAbility`
Enum central com permissões como:
- `employee:clock-in`
- `admin:manage-employees`
- `employee:update-password`

> Os enums facilitam a associação com middleware de abilities e ajudam na consistência dos tokens.

---

## 🔍 Estratégia de Consulta de CEP

- Cache configurado para evitar chamadas repetidas;
- `ZipCodeService` centraliza chamadas à API externa;
- Resource de retorno padronizado para reuso nos formulários.

---

## 📌 Validação de CEP no Formulário

- A criação de funcionário valida o CEP e retorna seus dados.
- Atualização só revalida se o campo mudar.
- Caso o CEP não esteja em cache, ele é buscado novamente.

---

## 🗃️ Estrutura do Banco de Dados

### 🧑‍💼 `users`
Tabela única para admins e funcionários. Campos adicionais:

- `cpf`, `role`, `position`, `birth_date`
- Endereço completo
- `created_by` → indica quem criou o usuário
- `deleted_at` para soft delete

> Simples e eficaz para o escopo, sem necessidade de separar `employees`.

### ⏱️ `punches`

Registros de ponto com:

- `user_id`
- `type` (`in` ou `out`)
- `punched_at` → data real do ponto
- `created_by` → ID do admin (se lançamento manual)

---

## 📈 Logs e Observabilidade

### 🪵 Logs Estruturados

- Canal: `daily`  
- Nível: `debug`

```env
LOG_CHANNEL=daily
LOG_LEVEL=debug
```

### 🔐 Segurança

- Logs não armazenam senhas;
- Informações rastreáveis: ID do usuário, IP, rota, ação executada.

---

## 🧪 Testes de Integração

Testes completos com PestPHP:

- Login/logout
- Cadastro e edição de usuários
- Clock-in/out
- Validações customizadas (CPF/CEP)

> Os testes validam tanto o funcionamento como a emissão dos logs.

---

## 💡 Estratégia de Desenvolvimento

- TDD com PestPHP;
- Testes escritos antes das features;
- Organização por domínio (ex: `tests/Feature/Auth`);
- Documentação mantida neste README.

---

## 🧪 Commits e Versionamento

- Commits padronizados com `feat`, `test`, `fix`, etc;
- Commits pequenos e incrementais;
- Não foi necessário criar branches extras, mas seguimos boas práticas no histórico.

---

## 🔄 Considerações Técnicas

**Por que não usar Jobs ou Events neste projeto?**

- O escopo não exigia fluxos assíncronos nem tarefas agendadas;
- O Laravel já permite envio de e-mails e eventos simples inline com baixo acoplamento;
- Eventos, listeners e comandos Artisan foram evitados por não serem necessários no momento.

---

## 🚀 Experiência com Jobs e Filas

Apesar de não aplicados aqui, possuo experiência sólida com:

- `dispatch()`, `Bus::batch()`, `Bus::chain()`;
- Filas com Redis;
- Monitoramento com Laravel Horizon;
- Fallbacks, prioridades, timeouts e retries.

---

## ❓ Interpretações Necessárias

### 🛠 Atualização de Senha

O enunciado era ambíguo sobre quem poderia atualizar a senha. A solução aplicada foi:

- Usuários autenticados podem trocar a própria senha;
- Admins podem resetar senhas de outros usuários;

> Garante autonomia e controle administrativo.

---

## 📒 Sobre este README

Este é um README **provisório** e **documentado**, com anotações detalhadas da implementação atual.

Versão final incluirá:

- Instruções de execução local;
- Estrutura de endpoints;
- Explicações de decisões técnicas;
- Cobertura completa dos testes.

---