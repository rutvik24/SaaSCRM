@servers(['web' => 'ubuntu@129.154.232.195'])

@task('addDB', ['on' => 'web'])
    sudo /bin/bash /usr/bin/clpctl db:add --domainName=app.rutviknabhoya.me --databaseName={{ $db_name }} --databaseUserName={{ $db_username }} --databaseUserPassword={{ $db_password }}
@endtask

@task('delete-db', ['on' => 'web'])
    sudo /bin/bash /usr/bin/clpctl db:delete --databaseName={{ $db_name }}
@endtask
