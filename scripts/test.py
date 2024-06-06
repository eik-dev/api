# mysql -u sammy -p -e "SET foreign_key_checks = 0; USE laravel; SOURCE users.sql; SET foreign_key_checks = 1;"
import os
import glob
import subprocess

# get all .sql files in the directory
sql_files = glob.glob("*.sql")
for sql_file in sql_files:
    print(sql_file)
    subprocess.run(['mysql', '-u', 'sammy', '-p', '-e', f"SET foreign_key_checks = 0; USE temp_eik; SOURCE {sql_file}; SET foreign_key_checks = 1;"])
    print("done")