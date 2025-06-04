# 📘 inTime - Teste Técnico (Ticto)

Este repositório faz parte da entrega de um **teste técnico** para a empresa **Ticto**.

## 🎯 Objetivo do Teste

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

---

## 🧱 Arquitetura do Projeto

A arquitetura da aplicação é baseada no padrão **MVC com Service Layer**, contemplando os seguintes pontos:

* **Controllers** focados em lidar com a entrada e resposta HTTP;
* **Services** contendo a lógica de negócio de forma isolada e reutilizável;
* **Enums** organizando as permissões disponíveis para os tokens Sanctum;
* **Resources** usados para formatar as respostas de API (padrão JSON);
* **Middlewares** configurados para validar permissões via abilities do Sanctum;

### Por que Service Layer?

A separação por serviços permite uma organização clara da lógica de negócio e torna o projeto mais testável e manutenível.

### Por que não usar DDD, Hexagonal, etc?

Embora arquiteturas mais robustas como **DDD** ou **Arquitetura Hexagonal** sejam valiosas em projetos grandes e complexos, sua aplicação aqui resultaria em **over engineering** desnecessário. A escolha por uma abordagem mais simples atende completamente ao escopo e requisitos deste teste.

---

## 📌 Comentários no Código

A maioria dos comentários está em **português**, por dois motivos:

1. O teste foi redigido integralmente em português;
2. Comentários têm como objetivo facilitar a leitura dos avaliadores.

Os commits, no entanto, seguem o padrão em **inglês**, alinhados com boas práticas de versionamento.

---

## 📒 Sobre este README

Este é um **README provisório** com anotações e insights sobre o desenvolvimento. Uma versão final mais objetiva e organizada será disponibilizada ao término da implementação, contendo:

* Instruções de execução local;
* Estrutura completa de endpoints;
* Explicações de decisões técnicas;
* Cobertura de testes (se aplicável).

---