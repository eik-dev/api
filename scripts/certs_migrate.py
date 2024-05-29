import mysql.connector
import pprint
import random

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
newC = new.cursor()

# Select all users from DB1
originalC.execute("SELECT * FROM certificates WHERE created_at IS NOT NULL")
certificates = originalC.fetchall()

for certificate in certificates:
    print(certificate)

TOTAL = len(certificates)
COUNT = 0
SUCCESS = 0
ERRORED = 0

print(TOTAL)