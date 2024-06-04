import mysql.connector
import pprint

original = mysql.connector.connect(
    host="localhost",
    user="sammy",
    password="sammy",
    database="original_eik"
)

new = mysql.connector.connect(
    host="localhost",
    user="sammy",
    password="sammy",
    database="laravel"
)

# Create cursors
originalC = original.cursor(dictionary=True)
newC = new.cursor(dictionary=True)
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
    number[2] = str(getNumber(category))
    return '/'.join(number)

# Select all users from DB1
originalC.execute("SELECT * FROM certificates WHERE created_at IS NOT NULL")
certificates = originalC.fetchall()

for certificate in certificates:
    #get user category from users table using user_id
    originalC.execute("SELECT * FROM users WHERE id=%s", (certificate['user_id'],))
    user = originalC.fetchone()
    if not user:
        continue
    category = user['category']
    #modify certificate numbber
    newNumber = modifyNumber(certificate['number'], certificate['user_id'])
    newC.execute("INSERT INTO certificates (user_id, number, created_at, updated_at, expiry, verified) VALUES (%s, %s, %s, %s, %s, %s)", (certificate['user_id'], newNumber, certificate['created_at'], certificate['updated_at'], certificate['expiry'], certificate['verified']))
    new.commit()
    print(f"{certificate['number']} - {newNumber}")

TOTAL = len(certificates)
COUNT = 0
SUCCESS = 0
ERRORED = 0

print(TOTAL)