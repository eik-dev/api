import mysql.connector
import pprint

DB = mysql.connector.connect(
    host="localhost",
    user="sammy",
    password="sammy",
    database="laravel"
)

# Create cursors
cursor = DB.cursor(dictionary=True)
def getNumber(category):
    category = category.lower()
    if category == 'student':
        return 5
    if category == 'associate':
        return 2
    if category == 'fellow':
        return 7
    if category == 'honorary':
        return 6
    if category == 'affiliate':
        return 4
    if category == 'lead':
        return 1
    if category == 'corporate':
        return 3
    if category == 'firms':
        return 3
    else:
        raise Exception("Invalid category")

def modifyNumber(number, category):
    number = number.split('/')
    number[1] = str(getNumber(category))
    return '/'.join(number)

# Select all users from DB1
cursor.execute("SELECT * FROM certificates")
certificates = cursor.fetchall()

TOTAL = len(certificates)
COUNT = 0
SUCCESS = 0
ERRORED = 0

for certificate in certificates:
    original = certificate['number']
    COUNT += 1
    try:
        #get user category from users table using user_id
        cursor.execute("SELECT * FROM users WHERE id=%s", (certificate['user_id'],))
        user = cursor.fetchone()
        if not user:
            raise Exception("User not found")
        role = user['role']
        category = ''
        if role == 'Individual':
            #get user category from individual table using user_id
            cursor.execute("SELECT * FROM individuals WHERE user_id=%s", (certificate['user_id'],))
            individual = cursor.fetchone()
            category = individual['category']
        else:
            category = 'firms'
        #modify certificate number
        newNumber = modifyNumber(original, category)
        cursor.execute("UPDATE certificates SET number = %s WHERE id = %s", (newNumber, certificate['id']))
        DB.commit()
        print(f"{original}::{category} -> {newNumber}")
        # print(f"{COUNT}/{TOTAL} :: {round(COUNT/TOTAL*100, 2)}% :: {ERRORED} errored", end='\r')
        SUCCESS += 1
    except Exception as e:
        with open('certs_update_logs.txt', 'a') as logs:
                logs.write(f"failed to modify error :: {e}\n")
        ERRORED += 1

print(f"{COUNT}/{TOTAL} :: {SUCCESS}({round(SUCCESS/TOTAL*100, 2)}%) success :: {round(ERRORED/TOTAL*100, 2)}% errored")