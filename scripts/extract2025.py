import mysql.connector

db = mysql.connector.connect(
    host="localhost",
    user="sammy",
    password="sammy",
    database="laravel"
)

cursor = db.cursor(dictionary=True)

certificates = cursor.execute("SELECT DISTINCT * FROM certificates WHERE verified IS NOT NULL AND year = 2025")

print("Total certificates: ", len(certificates))
for certificate in certificates:
    cursor.execute("SELECT name, email, FROM users WHERE id = %s", (certificate['user_id'],))
    user = cursor.fetchone()
    print(user['name'], user['email'], certificate['number'])
    cursor.execute("SELECT * FROM education WHERE user_id = %s", (certificate['user_id'],))
    education = cursor.fetchall()
    for edu in education:
        print(edu['institution'], edu['course'], edu['year'])
    cursor.execute("SELECT * FROM files WHERE user_id = %s  AND folder = 'requirements'", (certificate['user_id'],))
    files = cursor.fetchall()
    for file in files:
        print(file['name'])
    print("--------------------------------")
