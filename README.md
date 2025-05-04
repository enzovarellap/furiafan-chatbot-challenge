# 🐺 FURIA Fan Chatbot – Desafio Técnico FURIA

Este projeto foi desenvolvido como parte do **Challenge #1: Experiência Conversacional [NORMAL]** da FURIA. A proposta consiste na criação de um chatbot interativo para fãs do time de CS da FURIA, proporcionando uma experiência de acompanhamento em tempo real.

---

## 🔥 Objetivo

Criar uma aplicação web com chat interativo onde fãs da FURIA possam:

- Acompanhar jogos ao vivo com status em tempo real
- Receber informações sobre os jogadores e o time


---

## ✨ Funcionalidades

- **Live status dos jogos:** Atualizações em tempo real dos jogos de CS:GO (em formato simulado).
- **Landing Page amigável:** Interface responsiva e intuitiva com botão de início rápido para o chat.

---

## 🧰 Tecnologias Utilizadas

- **Laravel** (backend)
- **Laravel Livewire** (interatividade no frontend sem necessidade de JavaScript complexo)
- **Tailwind CSS** (estilização moderna e responsiva)
- **Blade** (template engine nativa do Laravel)

---

## 🚀 Como Executar Localmente

> Pré-requisitos: PHP ≥ 8.1, Composer, Node.js, NPM

1. Clone o repositório:

```bash
git clone https://github.com/enzovarellap/furiafan-chatbot-challenge.git
cd furiafan-chatbot-challenge
```

2. Instale as dependências do PHP:

```bash
composer install
npm install && npm run dev
```
3. Crie um arquivo `.env` a partir do `.env.example`:

```bash
cp .env.example .env
```
4. Gere a chave de aplicativo:

```bash
php artisan key:generate
```
5. Inicie o servidor local:

```bash
php artisan serve
```
6. Acesse a aplicação em `http://localhost:8000`

