# MyTransfer

**Aplicação de transferência de saldo entre usuários, simulando regras de fintech e comportamentos de carteiras digitais.**

Feito em Laravel 12, Livewire + Volt, AlpineJS, TailwindCSS, PostgreSQL, Sail.  

## Modelagem de Dados

### User
id  
name  
document  
email  
password  
type (enum)  
created_by  
timestamps  
deleted_at  

### Balance
id  
user_id  
amount (centavos)  
timestamps  

### Transactions
id  
sender_id  
receiver_id  
amount  
type (enum)  
status (enum)  
timestamps  


## Instalação e Execução

git clone ...  
cd ... 
cp .env.example .env  
sail up -d  
sail composer install  
sail artisan migrate   
sail npm install  
sail npm run dev -- --host  



## Dados de Acesso

Email: test@example.com  
Senha: password  


## Test 
# Rodar todos os testes 
sail php artisan test  

# Rodar meus testes 
sail php artisan test --filter=UserTest 
sail php artisan test --filter=BalanceTest 
sail php artisan test --filter=TransactionTest 
