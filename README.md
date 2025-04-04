<h1 align="center">
API INVENTORY SALES
</h1>

## Sobre
API REST utilizando Laravel que gerencia um módulo simplificado de controle de estoque e vendas para um ERP.

## Rodando projeto
### Pré-requisitos
- Git
- Docker

### Passo a Passo
- 1- Clonar o repositório
```
https://github.com/nepogabriel/mini-ecommerce-laravel.git
```

- 2- Entre no diretório 
```bash
cd nome-do-diretorio
```

- 3- Configure variáveis de ambiente
```bash
cp .env.example .env
```

- 4- Instale as dependências
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

- 5- Inicie o container
```bash
./vendor/bin/sail up -d
```

- 6- Acesse o container
```bash
docker exec -it mini-ecommerce-laravel-laravel.test-1 bash
```
OU
```bash
docker exec -it {nome-diretorio}-laravel.test-1 bash
```

- 7- Dentro do container execute para gerar uma chave do laravel
```bash
php artisan key:generate
```

- 8- Dentro do container execute para criar as tabelas do banco de dados
```bash
php artisan migrate
```

- **Observação:** Caso apresente erro ao criar as tabelas do banco de dados, tente os comandos abaixo e execute novamente o comando para criação das tabelas. 
``` bash
# Primeiro comando
docker exec -it {nome-diretorio}-laravel.test-1 bash

# Segundo comando
composer update
```

- 9- Este projeto usa seeders, dentro do container use o comando abaixo
``` bash
php artisan db:seed --class=ProductSeeder
```

- 10- Para os testes unitários, dentro do container execute
``` bash
php artisan test
```

- 11- Link de acesso
```
http://localhost:8585/
```

### Banco de dados
- Porta externa: 33009
- Porta interna: 3306
- Banco de dados: bd_inventory
- Usuário: root
- Senha:

# Endpoints
- POST /api/inventory (Registrar entrada de produtos no estoque)
```json
{
  "product_id": 1,
  "quantity": 5,
  "last_updated": "2025-04-03 14:00:00" // Opcinal
}
```

- GET /api/inventory (Obter situação atual do estoque)

- POST /api/sales (Registrar uma nova venda)
```json
{
    "items": [
        {
            "product_id": 1,
            "quantity": 2,
            "unit_price": 737.04,
            "unit_cost": 160.85
        },
        {
            "product_id": 2,
            "quantity": 3,
            "unit_price": 2196.98,
            "unit_cost": 1717.82
        }
    ]
}
```

- GET /api/sales/{id} (Obter detalhes de uma venda específica)

## 👥 Contribuidor
Gabriel Ribeiro.
🌐 https://linkedin.com/in/gabriel-ribeiro-br/
