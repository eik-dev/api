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

def getNewID(id):
    newC.execute("SELECT * FROM maps WHERE new_id=%s", (id,))
    result = newC.fetchone()
    if result:
        return result['old_id']
    else:
        #get max old id
        newC.execute("SELECT MAX(old_id) as max FROM maps")
        result = newC.fetchone()
        max = result['max']
        newC.execute("INSERT INTO maps (old_id, new_id) VALUES (%s, %s)", (max+1, id))
        new.commit()
        return max+1

def modifyNumber(number, id):
    number = number.split('/')
    number[2] = str(getNewID(id))
    return '/'.join(number)

# Select all users from DB1
originalC.execute("SELECT * FROM certificates WHERE created_at IS NOT NULL")
certificates = originalC.fetchall()

for certificate in certificates:
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