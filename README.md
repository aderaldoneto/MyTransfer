# MyTransfer  

## Modelagem 
User  
ID  
Nome  
CPF_CNPJ (unique)  
Email (unique)   
Senha  
Type (enum: pessoa física, empresa)   
Created_by (user_id)  
created_at / updated_at / deleted_at   

Balance   
Id  
User_id   
balance (int) (para precisão, opto por usar int para valores, ai faço /100 e *100 para exibir/salvar em centavos).  

Transactions  
remetente_id  
destinatario_id  
valor  
type (enums)
status  (enums)
created_at / updated_at  
