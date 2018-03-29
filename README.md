Service for sending mails via sendmail 

**Endpoint**:
 ```text
  POST /sendmail
 ``` 
**Form** :
```php
 ('subject', TextType::class, ['constraints' => [new Assert\NotBlank()]])
 ('from', TextType::class, ['constraints' => [new Assert\NotBlank(), new Assert\Email()]])
 ('to', TextType::class, ['constraints' => [new Assert\NotBlank(), new Assert\Email()]])
 ('body', TextType::class, ['constraints' => [new Assert\NotBlank()]])
```
**Content-Types**:
```text
json
```
**Responses** : 
```text
200 - OK
400 - BAD REQUEST
```

**Defaults**:
```text
default port on localhost - 8090, so endpoint will be localhost:8090/sendmail
default environment variables you could find in docker-compose yml and change them there
```