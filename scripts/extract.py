import mysql.connector

new = mysql.connector.connect(
    host="localhost",
    user="sammy",
    password="sammy",
    database="laravel"
)

newC = new.cursor(dictionary=True)

newC.execute("SELECT * FROM t_w_g_s")
twgs = newC.fetchall()

for twg in twgs:
    user = newC.execute(f"SELECT name, email FROM users WHERE id = {twg['user_id']}")
    user = newC.fetchone()
    print(f"{user['name']}, {user['email']}, {(twg['twgs'].decode('utf-8'))[1:-1]}")
    with open("twgs.csv", "a") as f:
        f.write(f"{user['name']}, {user['email']}, {(twg['twgs'].decode('utf-8'))[1:-1]}\n")