This config file 
```
Host st-*
    User admin
    IdentityFile ~/.ssh/test

Host st-test
    Hostname test.example.org
    LocalForward 3306 localhost:3306

Host st-live
    Hostname live.example.org
    LocalForward 3307 dbserver:3306
```


would create that output with the -d option
```
 --------- ------------------ --------------- ------- -------------- 
 alias     hostname           localforward    user    identityfile
 --------- ------------------ --------------- ------- -------------- 
 st-live   live.example.org   MySQL => 3307   admin   ~/.ssh/test   
 st-test   test.example.org   MySQL => 3306   admin   ~/.ssh/test
 --------- ------------------ --------------- ------- -------------- 
```

and would offer st-live and st-test as autocompletion values