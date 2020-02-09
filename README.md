# deploiement de l'app 

### deployer en local 

consultez le repertoire env_exemple
Modifiez les variables suivantes afin de coller à votre environement local 
##### docker-compose.yaml
```dockerfile
      DB_HOST: "YourDbHost"
      DB_PORT: "3311"
      DB_USER: "YourDbUser"
      DB_PASS: "YourDbPassWord"
      DB_DATABASE: "YourDbName"
      NODE_TOKEN: "YourSecretForEnvFileRoot"

     'traefik.frontend.rule=Host:ws.yourDomain'
     'traefik.frontend.rule=Host:yourDomain'   

      MYSQL_ROOT_PASSWORD: "YourPassWord"
      MYSQL_DATABASE: "dbName"
      MYSQL_USER: "User"
      MYSQL_PASSWORD: "UserPassWord"   
      MAILER_URL: "smtp://YourSMTPHost?encryption=tls&auth_mode=login&username=user@domain&password=YourPassword"  
```
##### site.conf (nginx)

```
server_name Your domain;
add_header 'Access-Control-Allow-Origin' 'http//:ws.Your Domain';
```

##### .env root

```dotenv
APP_ENV=dev
APP_SECRET=keep it secret
MERCURE_PUBLISH_URL=https://ws.YourDomain
MERCURE_JWT_TOKEN=keep it secret
NODE_TOKEN= secret of NODE_TOKEN in docker-compose.yaml
```

###Proxy

Le proxy doit challenge les certs sur un wildcard de votre domaine . 
le websocket passe par : ws.yourdomain.tld le proxy doit donc challenge sur un protocol http pour le server node.
Celui-ci se charge du switch protocol . 
